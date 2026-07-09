<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\CheckoutIndex;
use App\Livewire\Customer\CreateInvoice;
use App\Models\Box;
use App\Models\Checkout;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhChinaData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FlexibleInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $admin;
    private Box $box;
    private Item $item1;
    private Item $item2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $this->box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'sharing',
            'method' => 'air',
            'status' => Box::STATUS_OPEN,
        ]);

        $this->item1 = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'RESI-001',
            'name' => 'Item A',
            'quantity' => 2,
            'is_sensitive' => false,
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);

        $this->item2 = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'RESI-002',
            'name' => 'Item B',
            'quantity' => 3,
            'is_sensitive' => false,
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);

        // WH China data with weight
        WhChinaData::create(['resi_number' => 'RESI-001', 'berat' => 2.5, 'ukuran_box' => '30x30x30', 'input_by' => $this->admin->id, 'item_id' => $this->item1->id, 'matched_at' => now()]);
        WhChinaData::create(['resi_number' => 'RESI-002', 'berat' => 3.0, 'ukuran_box' => '40x40x40', 'input_by' => $this->admin->id, 'item_id' => $this->item2->id, 'matched_at' => now()]);

        // Seed rate settings for FeeCalculationService
        Setting::create(['key' => 'rate_sharing_air_berat', 'value' => '200', 'group' => 'rate_sharing']);
        Setting::create(['key' => 'rate_sharing_air_volume', 'value' => '150', 'group' => 'rate_sharing']);
        Setting::create(['key' => 'fee_packing_150', 'value' => '5000', 'group' => 'fee_packing']);
        Setting::create(['key' => 'fee_packing_1000', 'value' => '6500', 'group' => 'fee_packing']);
        Setting::create(['key' => 'fee_packing_2000', 'value' => '8000', 'group' => 'fee_packing']);
        Setting::create(['key' => 'fee_packing_extra_per_kg', 'value' => '1500', 'group' => 'fee_packing']);
    }

    // ─── Page Access ───────────────────────────────────────────

    public function test_customer_can_view_create_invoice_page(): void
    {
        $this->actingAs($this->customer)
            ->get('/create-invoice')
            ->assertOk();
    }

    public function test_create_invoice_shows_available_items(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->assertSee('Item A')
            ->assertSee('Item B')
            ->assertSee('RESI-001')
            ->assertSee('RESI-002');
    }

    // ─── Item Filtering ────────────────────────────────────────

    public function test_only_shows_items_not_in_any_invoice(): void
    {
        // Put item1 in an invoice
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => null,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ]);
        $invoice->items()->attach($this->item1->id);

        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->assertDontSee('Item A')
            ->assertSee('Item B');
    }

    public function test_only_shows_arrived_items(): void
    {
        $this->item2->update(['arrived_indonesia' => false]);

        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->assertSee('Item A')
            ->assertDontSee('Item B');
    }

    // ─── Selection + Preview ───────────────────────────────────

    public function test_select_items_and_calculate_preview(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->call('toggleItem', $this->item1->id)
            ->call('toggleItem', $this->item2->id)
            ->set('length', 60)
            ->set('width', 40)
            ->set('height', 50)
            ->assertSet('preview.item_count', 2)
            ->assertSet('preview.total_weight', 5.5) // 2.5 + 3.0
            ->assertSee('Rp'); // fee is calculated
    }

    public function test_toggle_all(): void
    {
        $this->actingAs($this->customer);

        $component = Livewire::test(CreateInvoice::class)
            ->call('toggleAll');

        $selected = $component->get('selectedItems');
        sort($selected);
        $this->assertEquals([$this->item1->id, $this->item2->id], $selected);

        $component->call('toggleAll')
            ->assertSet('selectedItems', []);
    }

    // ─── Invoice Creation ──────────────────────────────────────

    public function test_create_flexible_invoice(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->call('toggleItem', $this->item1->id)
            ->call('toggleItem', $this->item2->id)
            ->set('length', 60)
            ->set('width', 40)
            ->set('height', 50)
            ->call('createInvoice');

        $invoice = Invoice::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($invoice);
        $this->assertNull($invoice->box_id); // flexible invoice
        $this->assertEquals(Invoice::STATUS_WAITING_PAYMENT, $invoice->status);
        $this->assertEquals(2, $invoice->items()->count());
        $this->assertTrue($invoice->isFlexible());
        $this->assertGreaterThan(0, $invoice->grand_total);
    }

    public function test_cannot_create_with_zero_items(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->set('length', 60)
            ->set('width', 40)
            ->set('height', 50)
            ->call('createInvoice')
            ->assertDispatched('toast');

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_cannot_create_without_dimensions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->call('toggleItem', $this->item1->id)
            ->call('createInvoice')
            ->assertHasErrors(['length', 'width', 'height']);
    }

    // ─── Backward Compatibility ────────────────────────────────

    public function test_old_invoice_with_box_id_still_works(): void
    {
        $invoice = Invoice::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ]);

        $this->assertNotNull($invoice->box);
        $this->assertFalse($invoice->isFlexible());
        $this->assertNotEmpty($invoice->box_info);
    }

    public function test_flexible_invoice_box_info_from_items(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->call('toggleItem', $this->item1->id)
            ->set('length', 60)
            ->set('width', 40)
            ->set('height', 50)
            ->call('createInvoice');

        $invoice = Invoice::where('customer_id', $this->customer->id)->first();
        $this->assertNull($invoice->box_id);
        $this->assertTrue($invoice->isFlexible());
        // box_info should resolve from items' box
        $this->assertNotEmpty($invoice->box_info);
        $this->assertNotEquals('-', $invoice->box_info);
    }

    // ─── Checkout Sender Fields ────────────────────────────────

    public function test_checkout_dropship_requires_sender_fields(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => Invoice::STATUS_VERIFIED,
        ]);

        $this->actingAs($this->customer);

        Livewire::test(CheckoutIndex::class)
            ->set('invoiceId', $invoice->id)
            ->set('addressType', 'dropship')
            ->set('recipientName', 'John Doe')
            ->set('recipientPhone', '08123456789')
            ->set('address', 'Jl. Test No. 123, Jakarta')
            ->set('confirmation', true)
            ->call('submit')
            ->assertHasErrors(['senderName', 'senderPhone']);
    }

    public function test_checkout_personal_does_not_require_sender_fields(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => Invoice::STATUS_VERIFIED,
        ]);

        $this->actingAs($this->customer);

        Livewire::test(CheckoutIndex::class)
            ->set('invoiceId', $invoice->id)
            ->set('addressType', 'personal')
            ->set('recipientName', 'John Doe')
            ->set('recipientPhone', '08123456789')
            ->set('address', 'Jl. Test No. 123, Jakarta')
            ->set('confirmation', true)
            ->call('submit');

        $checkout = Checkout::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($checkout);
        $this->assertNull($checkout->sender_name);
        $this->assertNull($checkout->sender_phone);
    }

    public function test_checkout_dropship_saves_sender_fields(): void
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => Invoice::STATUS_VERIFIED,
        ]);

        $this->actingAs($this->customer);

        Livewire::test(CheckoutIndex::class)
            ->set('invoiceId', $invoice->id)
            ->set('addressType', 'dropship')
            ->set('recipientName', 'John Doe')
            ->set('recipientPhone', '08123456789')
            ->set('address', 'Jl. Test No. 123, Jakarta')
            ->set('senderName', 'Customer Name')
            ->set('senderPhone', '08987654321')
            ->set('confirmation', true)
            ->call('submit');

        $checkout = Checkout::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($checkout);
        $this->assertEquals('Customer Name', $checkout->sender_name);
        $this->assertEquals('08987654321', $checkout->sender_phone);
    }
}
