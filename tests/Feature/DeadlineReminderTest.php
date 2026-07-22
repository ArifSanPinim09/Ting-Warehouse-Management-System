<?php

namespace Tests\Feature;

use App\Console\Commands\CheckDeadlinesCommand;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Deadline Reminder Tests — Revisi §2.10, §2.11.
 *
 * Tests for:
 * - Invoice payment_deadline auto-setting
 * - Box arrival storage_deadline auto-setting
 * - Payment reminders (H-3, H-1, H-0)
 * - Storage deadline expiry
 * - 2-week overdue hold
 * - Idempotency (no duplicate reminders)
 */
class DeadlineReminderTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Box $box;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => Box::STATUS_ARRIVED_INA,
        ]);
    }

    // ─── Invoice Creation Tests ─────────────────────────────────

    public function test_invoice_creation_sets_payment_deadline(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => null,
        ]);

        // Observer should set payment_deadline = created_at + 7 days
        $this->assertNotNull($invoice->fresh()->payment_deadline);
        $this->assertTrue(
            $invoice->fresh()->payment_deadline->isSameDay($invoice->created_at->addDays(7))
        );
    }

    public function test_invoice_with_explicit_payment_deadline_not_overwritten(): void
    {
        $customDeadline = now()->addDays(14);

        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => $customDeadline,
        ]);

        $this->assertTrue(
            $invoice->fresh()->payment_deadline->isSameDay($customDeadline)
        );
    }

    // ─── Box Arrival Tests ──────────────────────────────────────

    public function test_box_arrival_sets_arrived_indonesia_on_items(): void
    {
        $items = Item::factory()->count(3)->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'arrived_indonesia' => false,
        ]);

        $this->box->update(['status' => Box::STATUS_INVOICE]);

        foreach ($items as $item) {
            $this->assertTrue($item->fresh()->arrived_indonesia);
        }
    }

    public function test_box_arrival_sets_storage_deadline_on_box_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'storage_deadline' => null,
        ]);

        $this->box->update(['status' => Box::STATUS_INVOICE]);

        $this->assertNotNull($invoice->fresh()->storage_deadline);
        $this->assertTrue(
            $invoice->fresh()->storage_deadline->isSameDay(now()->addDays(30))
        );
    }

    public function test_box_arrival_sets_storage_deadline_on_flexible_invoice(): void
    {
        $items = Item::factory()->count(2)->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'arrived_indonesia' => false,
        ]);

        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => null, // flexible invoice
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'storage_deadline' => null,
        ]);

        $invoice->items()->attach($items->pluck('id'));

        $this->box->update(['status' => Box::STATUS_INVOICE]);

        $this->assertNotNull($invoice->fresh()->storage_deadline);
    }

    // ─── Payment Reminder Tests ─────────────────────────────────

    public function test_h3_reminder_sent_3_days_before_deadline(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => now()->addDays(3)->startOfDay(),
            'reminder_sent' => null,
        ]);

        $this->artisan('deadlines:check')
            ->assertExitCode(0);

        $invoice->refresh();
        $this->assertTrue($invoice->hasReminderBeenSent('h3'));
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_h1_reminder_sent_1_day_before_deadline(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => now()->addDay()->startOfDay(),
            'reminder_sent' => null,
        ]);

        $this->artisan('deadlines:check')
            ->assertExitCode(0);

        $invoice->refresh();
        $this->assertTrue($invoice->hasReminderBeenSent('h1'));
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_h0_reminder_sent_on_deadline_day(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => now()->startOfDay(),
            'reminder_sent' => null,
        ]);

        $this->artisan('deadlines:check')
            ->assertExitCode(0);

        $invoice->refresh();
        $this->assertTrue($invoice->hasReminderBeenSent('h0'));
        $this->assertDatabaseCount('notifications', 1);
    }

    // ─── Storage Expiry Tests ───────────────────────────────────

    public function test_storage_expired_sends_notification_and_holds_items(): void
    {
        $items = Item::factory()->count(2)->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_ACTIVE,
        ]);

        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'storage_deadline' => now()->subDay()->startOfDay(),
            'reminder_sent' => null,
        ]);

        $this->artisan('deadlines:check')
            ->assertExitCode(0);

        $invoice->refresh();
        $this->assertTrue($invoice->hasReminderBeenSent('storage_expired'));
        $this->assertDatabaseCount('notifications', 1);

        foreach ($items as $item) {
            $this->assertEquals(Item::STATUS_HOLD, $item->fresh()->status);
        }
    }

    // ─── 2-Week Overdue Tests ──────────────────────────────────

    public function test_2week_overdue_sends_lelang_warning_and_holds_items(): void
    {
        $items = Item::factory()->count(2)->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_ACTIVE,
        ]);

        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => now()->subWeeks(3)->startOfDay(),
            'reminder_sent' => null,
        ]);

        $this->artisan('deadlines:check')
            ->assertExitCode(0);

        $invoice->refresh();
        $this->assertTrue($invoice->hasReminderBeenSent('2week'));
        $this->assertDatabaseCount('notifications', 1);

        foreach ($items as $item) {
            $this->assertEquals(Item::STATUS_HOLD, $item->fresh()->status);
        }
    }

    // ─── Idempotency Tests ─────────────────────────────────────

    public function test_command_is_idempotent_no_duplicate_reminders(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => now()->addDays(3)->startOfDay(),
            'reminder_sent' => null,
        ]);

        // Run command twice
        $this->artisan('deadlines:check')->assertExitCode(0);
        $this->artisan('deadlines:check')->assertExitCode(0);

        // Should only have 1 notification, not 2
        $this->assertDatabaseCount('notifications', 1);
        $this->assertTrue($invoice->fresh()->hasReminderBeenSent('h3'));
    }

    public function test_command_skips_verified_invoices(): void
    {
        Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_VERIFIED,
            'payment_deadline' => now()->subDays(3)->startOfDay(),
            'reminder_sent' => null,
        ]);

        $this->artisan('deadlines:check')
            ->assertExitCode(0);

        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_command_skips_invoices_with_reminder_already_sent(): void
    {
        Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $this->box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => now()->addDays(3)->startOfDay(),
            'reminder_sent' => ['h3'],
        ]);

        $this->artisan('deadlines:check')
            ->assertExitCode(0);

        $this->assertDatabaseCount('notifications', 0);
    }

    // ─── Model Helper Tests ─────────────────────────────────────

    public function test_invoice_markReminderSent_and_hasReminderBeenSent(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'reminder_sent' => null,
        ]);

        $this->assertFalse($invoice->hasReminderBeenSent('h3'));

        $invoice->markReminderSent('h3');

        $this->assertTrue($invoice->hasReminderBeenSent('h3'));
        $this->assertFalse($invoice->hasReminderBeenSent('h1'));
    }

    public function test_invoice_isPaymentOverdue(): void
    {
        $overdueInvoice = Invoice::factory()->create([
            'payment_deadline' => now()->subDay(),
        ]);
        $currentInvoice = Invoice::factory()->create([
            'payment_deadline' => now()->addDay(),
        ]);

        $this->assertTrue($overdueInvoice->isPaymentOverdue());
        $this->assertFalse($currentInvoice->isPaymentOverdue());
    }

    public function test_invoice_isStorageExpired(): void
    {
        $expiredInvoice = Invoice::factory()->create([
            'storage_deadline' => now()->subDay(),
        ]);
        $currentInvoice = Invoice::factory()->create([
            'storage_deadline' => now()->addDay(),
        ]);

        $this->assertTrue($expiredInvoice->isStorageExpired());
        $this->assertFalse($currentInvoice->isStorageExpired());
    }

    // ─── Item Status Tests ─────────────────────────────────────

    public function test_item_has_hold_status(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        $this->assertContains(Item::STATUS_HOLD, Item::getValidStatuses());
    }
}
