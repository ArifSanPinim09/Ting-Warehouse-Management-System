<?php

namespace Tests\Feature;

use App\Livewire\Notifications\NotificationBell;
use App\Livewire\Notifications\NotificationIndex;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Notification System Tests — Revisi §2.11, §3.3, §4.3.
 *
 * Tests for:
 * - Schema migration (user_id, title, message, is_read)
 * - NotificationService new types
 * - /notifications page
 * - Bell component
 */
class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $admin;
    private NotificationService $notifService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->notifService = app(NotificationService::class);
    }

    // ─── Schema Tests ───────────────────────────────────────────

    public function test_notification_has_required_columns(): void
    {
        $notification = Notification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->customer->id,
            'user_id' => $this->customer->id,
            'type' => 'test',
            'data' => ['title' => 'Test', 'message' => 'Test message'],
            'title' => 'Test',
            'message' => 'Test message',
            'is_read' => false,
        ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'user_id' => $this->customer->id,
            'title' => 'Test',
            'message' => 'Test message',
            'is_read' => false,
        ]);
    }

    public function test_notification_is_read_scope(): void
    {
        Notification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->customer->id,
            'user_id' => $this->customer->id,
            'type' => 'test',
            'data' => ['title' => 'Unread'],
            'title' => 'Unread',
            'message' => 'Test',
            'is_read' => false,
        ]);

        Notification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->customer->id,
            'user_id' => $this->customer->id,
            'type' => 'test',
            'data' => ['title' => 'Read'],
            'title' => 'Read',
            'message' => 'Test',
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->assertCount(1, Notification::unread()->get());
        $this->assertCount(1, Notification::read()->get());
    }

    // ─── NotificationService New Types Tests ────────────────────

    public function test_item_arrived_wh_notification(): void
    {
        $box = Box::factory()->create(['customer_id' => $this->customer->id]);

        $notification = $this->notifService->itemArrivedWH($box);

        $this->assertEquals(NotificationService::TYPE_ITEM_ARRIVED_WH, $notification->type);
        $this->assertEquals($this->customer->id, $notification->user_id);
        $this->assertStringContainsString('WH Jakarta', $notification->message);
    }

    public function test_box_closed_notification(): void
    {
        $box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'tracking_number' => 'TRK-001',
        ]);

        $notification = $this->notifService->boxClosed($box);

        $this->assertEquals(NotificationService::TYPE_BOX_CLOSED, $notification->type);
        $this->assertEquals($this->customer->id, $notification->user_id);
        $this->assertStringContainsString('TRK-001', $notification->message);
    }

    public function test_claim_successful_notification(): void
    {
        $item = Item::factory()->create([
            'customer_id' => $this->customer->id,
            'name' => 'iPhone 15',
        ]);

        $notification = $this->notifService->claimSuccessful($item);

        $this->assertEquals(NotificationService::TYPE_CLAIM_SUCCESSFUL, $notification->type);
        $this->assertEquals($this->customer->id, $notification->user_id);
        $this->assertStringContainsString('5.000', $notification->message);
    }

    public function test_storage_deadline_7day_notification(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'storage_deadline' => now()->addDays(7),
        ]);

        $notification = $this->notifService->storageDeadline7Day($invoice);

        $this->assertEquals(NotificationService::TYPE_STORAGE_DEADLINE_7DAY, $notification->type);
        $this->assertEquals($this->customer->id, $notification->user_id);
        $this->assertStringContainsString('7 hari', $notification->message);
    }

    public function test_notification_types_include_all_revisi_types(): void
    {
        $types = NotificationService::getValidTypes();

        $this->assertContains(NotificationService::TYPE_ITEM_ARRIVED_WH, $types);
        $this->assertContains(NotificationService::TYPE_BOX_CLOSED, $types);
        $this->assertContains(NotificationService::TYPE_CLAIM_SUCCESSFUL, $types);
        $this->assertContains(NotificationService::TYPE_STORAGE_DEADLINE_7DAY, $types);
        $this->assertCount(20, $types);
    }

    // ─── Notification Index Page Tests ──────────────────────────

    public function test_customer_can_access_notifications_page(): void
    {
        $this->actingAs($this->customer);

        $this->get(route('notifications'))
            ->assertStatus(200)
            ->assertSee('Notifikasi');
    }

    public function test_admin_can_access_notifications_page(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('notifications'))
            ->assertStatus(200);
    }

    public function test_notifications_page_shows_user_notifications(): void
    {
        // Create notification for customer
        Notification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->customer->id,
            'user_id' => $this->customer->id,
            'type' => 'test',
            'data' => ['title' => 'Test Title', 'message' => 'Test Message'],
            'title' => 'Test Title',
            'message' => 'Test Message',
            'is_read' => false,
        ]);

        $this->actingAs($this->customer);

        Livewire::test(NotificationIndex::class)
            ->assertSee('Test Title')
            ->assertSee('Test Message');
    }

    public function test_notifications_page_does_not_show_other_users_notifications(): void
    {
        $otherCustomer = User::factory()->create(['role' => 'customer']);

        Notification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $otherCustomer->id,
            'user_id' => $otherCustomer->id,
            'type' => 'test',
            'data' => ['title' => 'Other User'],
            'title' => 'Other User',
            'message' => 'Test',
            'is_read' => false,
        ]);

        $this->actingAs($this->customer);

        Livewire::test(NotificationIndex::class)
            ->assertDontSee('Other User');
    }

    public function test_mark_as_read_on_notification_page(): void
    {
        $notification = Notification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->customer->id,
            'user_id' => $this->customer->id,
            'type' => 'test',
            'data' => ['title' => 'Test'],
            'title' => 'Test',
            'message' => 'Test',
            'is_read' => false,
        ]);

        $this->actingAs($this->customer);

        Livewire::test(NotificationIndex::class)
            ->call('markAsRead', $notification->id);

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_mark_all_as_read_on_notification_page(): void
    {
        for ($i = 0; $i < 3; $i++) {
            Notification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $this->customer->id,
                'user_id' => $this->customer->id,
                'type' => 'test',
                'data' => ['title' => "Test {$i}"],
                'title' => "Test {$i}",
                'message' => 'Test',
                'is_read' => false,
            ]);
        }

        $this->actingAs($this->customer);

        Livewire::test(NotificationIndex::class)
            ->call('markAllAsRead');

        $this->assertEquals(0, Notification::where('user_id', $this->customer->id)->unread()->count());
    }

    // ─── Bell Component Tests ───────────────────────────────────

    public function test_bell_shows_unread_count(): void
    {
        for ($i = 0; $i < 3; $i++) {
            Notification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $this->customer->id,
                'user_id' => $this->customer->id,
                'type' => 'test',
                'data' => ['title' => "Unread {$i}"],
                'title' => "Unread {$i}",
                'message' => 'Test',
                'is_read' => false,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            Notification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $this->customer->id,
                'user_id' => $this->customer->id,
                'type' => 'test',
                'data' => ['title' => "Read {$i}"],
                'title' => "Read {$i}",
                'message' => 'Test',
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        $this->actingAs($this->customer);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 3);
    }

    public function test_bell_does_not_show_other_users_notifications(): void
    {
        $otherCustomer = User::factory()->create(['role' => 'customer']);

        for ($i = 0; $i < 5; $i++) {
            Notification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $otherCustomer->id,
                'user_id' => $otherCustomer->id,
                'type' => 'test',
                'data' => ['title' => "Other {$i}"],
                'title' => "Other {$i}",
                'message' => 'Test',
                'is_read' => false,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            Notification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $this->customer->id,
                'user_id' => $this->customer->id,
                'type' => 'test',
                'data' => ['title' => "My {$i}"],
                'title' => "My {$i}",
                'message' => 'Test',
                'is_read' => false,
            ]);
        }

        $this->actingAs($this->customer);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 2);
    }

    public function test_bell_mark_as_read(): void
    {
        $notification = Notification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->customer->id,
            'user_id' => $this->customer->id,
            'type' => 'test',
            'data' => ['title' => 'Test'],
            'title' => 'Test',
            'message' => 'Test',
            'is_read' => false,
        ]);

        $this->actingAs($this->customer);

        Livewire::test(NotificationBell::class)
            ->call('markAsRead', $notification->id)
            ->assertSet('unreadCount', 0);

        $this->assertTrue($notification->fresh()->is_read);
    }

    // ─── Admin Bell Tests ──────────────────────────────────────

    public function test_admin_bell_shows_notifications(): void
    {
        for ($i = 0; $i < 3; $i++) {
            Notification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $this->admin->id,
                'user_id' => $this->admin->id,
                'type' => 'test',
                'data' => ['title' => "Admin {$i}"],
                'title' => "Admin {$i}",
                'message' => 'Test',
                'is_read' => false,
            ]);
        }

        $this->actingAs($this->admin);

        Livewire::test(NotificationBell::class)
            ->assertSet('unreadCount', 3);
    }
}
