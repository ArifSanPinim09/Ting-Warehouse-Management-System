<?php

namespace Tests\Unit\Models;

use App\Models\DendaClaim;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DendaClaimModelTest extends TestCase
{
    use RefreshDatabase;

    // ─── Relationship Tests ─────────────────────────────────────────

    public function test_belongs_to_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item = Item::factory()->create(['customer_id' => $customer->id]);
        $denda = DendaClaim::factory()->create([
            'customer_id' => $customer->id,
            'item_id' => $item->id,
        ]);

        $this->assertInstanceOf(User::class, $denda->customer);
        $this->assertEquals($customer->id, $denda->customer->id);
    }

    public function test_belongs_to_item(): void
    {
        $item = Item::factory()->create();
        $denda = DendaClaim::factory()->create(['item_id' => $item->id]);

        $this->assertInstanceOf(Item::class, $denda->item);
        $this->assertEquals($item->id, $denda->item->id);
    }

    public function test_belongs_to_invoice_nullable(): void
    {
        $denda = DendaClaim::factory()->create(['invoice_id' => null]);

        $this->assertNull($denda->invoice);
    }

    public function test_belongs_to_invoice_when_tagged(): void
    {
        $invoice = Invoice::factory()->create();
        $denda = DendaClaim::factory()->tagged($invoice)->create();

        $this->assertInstanceOf(Invoice::class, $denda->invoice);
        $this->assertEquals($invoice->id, $denda->invoice->id);
    }

    // ─── Fillable & Cast Tests ──────────────────────────────────────

    public function test_fillable_attributes(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item = Item::factory()->create(['customer_id' => $customer->id]);
        $denda = DendaClaim::create([
            'customer_id' => $customer->id,
            'item_id' => $item->id,
            'jumlah_denda' => 5000,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('denda_claims', [
            'customer_id' => $customer->id,
            'item_id' => $item->id,
            'status' => 'pending',
        ]);
        $this->assertEqualsWithDelta(5000.0, (float) $denda->jumlah_denda, 0.01);
    }

    public function test_jumlah_denda_decimal_cast(): void
    {
        $denda = DendaClaim::factory()->create(['jumlah_denda' => 5000.50]);

        $this->assertIsString($denda->jumlah_denda);
        $this->assertEquals('5000.50', $denda->jumlah_denda);
    }

    public function test_created_at_is_datetime_cast(): void
    {
        $denda = DendaClaim::factory()->create();

        $this->assertNotNull($denda->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $denda->created_at);
    }

    public function test_no_updated_at_column(): void
    {
        $this->assertFalse(
            \Illuminate\Support\Facades\Schema::hasColumn('denda_claims', 'updated_at'),
            'denda_claims should not have updated_at — claims are immutable records'
        );
    }

    // ─── Status Constants Tests ─────────────────────────────────────

    public function test_status_constants(): void
    {
        $this->assertEquals('pending', DendaClaim::STATUS_PENDING);
        $this->assertEquals('tagged', DendaClaim::STATUS_TAGGED);
        $this->assertEquals('paid', DendaClaim::STATUS_PAID);
    }

    public function test_get_valid_statuses(): void
    {
        $statuses = DendaClaim::getValidStatuses();

        $this->assertCount(3, $statuses);
        $this->assertContains('pending', $statuses);
        $this->assertContains('tagged', $statuses);
        $this->assertContains('paid', $statuses);
    }

    // ─── Cascade Delete Tests ───────────────────────────────────────

    public function test_cascade_delete_when_customer_deleted(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item = Item::factory()->create(['customer_id' => $customer->id]);
        DendaClaim::factory()->create([
            'customer_id' => $customer->id,
            'item_id' => $item->id,
        ]);

        $customer->delete();

        $this->assertEquals(0, DendaClaim::count());
    }

    public function test_cascade_delete_when_item_deleted(): void
    {
        $item = Item::factory()->create();
        DendaClaim::factory()->create(['item_id' => $item->id]);

        $item->delete();

        $this->assertEquals(0, DendaClaim::count());
    }

    public function test_invoice_null_on_delete(): void
    {
        $invoice = Invoice::factory()->create();
        $denda = DendaClaim::factory()->tagged($invoice)->create();

        $invoice->delete();

        $denda->refresh();
        $this->assertNull($denda->invoice_id);
    }

    // ─── Factory State Tests ────────────────────────────────────────

    public function test_factory_default_is_pending(): void
    {
        $denda = DendaClaim::factory()->create();

        $this->assertEquals('pending', $denda->status);
        $this->assertNull($denda->invoice_id);
        $this->assertEqualsWithDelta(5000.0, (float) $denda->jumlah_denda, 0.01);
    }

    public function test_factory_tagged_state(): void
    {
        $invoice = Invoice::factory()->create();
        $denda = DendaClaim::factory()->tagged($invoice)->create();

        $this->assertEquals('tagged', $denda->status);
        $this->assertEquals($invoice->id, $denda->invoice_id);
    }

    public function test_factory_paid_state(): void
    {
        $invoice = Invoice::factory()->create();
        $denda = DendaClaim::factory()->paid($invoice)->create();

        $this->assertEquals('paid', $denda->status);
        $this->assertEquals($invoice->id, $denda->invoice_id);
    }

    // ─── Default Value Tests ────────────────────────────────────────

    public function test_jumlah_denda_default_is_5000(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item = Item::factory()->create(['customer_id' => $customer->id]);
        DendaClaim::create([
            'customer_id' => $customer->id,
            'item_id' => $item->id,
        ]);

        // Database default applies — verify via DB directly
        $this->assertDatabaseHas('denda_claims', [
            'customer_id' => $customer->id,
            'item_id' => $item->id,
            'jumlah_denda' => 5000.00,
        ]);
    }

    public function test_status_default_is_pending(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item = Item::factory()->create(['customer_id' => $customer->id]);
        DendaClaim::create([
            'customer_id' => $customer->id,
            'item_id' => $item->id,
        ]);

        // Database default applies — verify via DB directly
        $this->assertDatabaseHas('denda_claims', [
            'customer_id' => $customer->id,
            'item_id' => $item->id,
            'status' => 'pending',
        ]);
    }

    // ─── User Relationship Test ─────────────────────────────────────

    public function test_user_has_many_denda_claims(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item1 = Item::factory()->create(['customer_id' => $customer->id]);
        $item2 = Item::factory()->create(['customer_id' => $customer->id]);
        DendaClaim::factory()->create(['customer_id' => $customer->id, 'item_id' => $item1->id]);
        DendaClaim::factory()->create(['customer_id' => $customer->id, 'item_id' => $item2->id]);

        $this->assertCount(2, $customer->dendaClaims);
    }
}
