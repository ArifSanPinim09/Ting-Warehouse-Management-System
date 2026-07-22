<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\GenerateInvoice;
use App\Livewire\Admin\VerificationIndex;
use App\Models\Box;
use App\Models\DendaClaim;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DendaInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Box $box;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $this->box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => Box::STATUS_ARRIVED_INA,
            'type' => 'sharing',
            'method' => 'air',
        ]);

        // Seed minimal rates for FeeCalculationService
        $this->seedRates();
    }

    // ─── Test: denda tagged saat generate invoice ────────────────

    public function test_generate_invoice_tags_pending_denda_claims(): void
    {
        $this->actingAs($this->admin);

        // Create 2 pending denda claims for customer
        $item1 = Item::factory()->create(['customer_id' => $this->customer->id]);
        $item2 = Item::factory()->create(['customer_id' => $this->customer->id]);
        $denda1 = DendaClaim::factory()->create([
            'customer_id' => $this->customer->id,
            'item_id' => $item1->id,
            'jumlah_denda' => 5000,
            'status' => DendaClaim::STATUS_PENDING,
            'invoice_id' => null,
        ]);
        $denda2 = DendaClaim::factory()->create([
            'customer_id' => $this->customer->id,
            'item_id' => $item2->id,
            'jumlah_denda' => 5000,
            'status' => DendaClaim::STATUS_PENDING,
            'invoice_id' => null,
        ]);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', 100)
            ->set('length', 50)
            ->set('width', 40)
            ->set('height', 30)
            ->call('generateInvoice');

        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertEquals(10000, $invoice->denda_total); // 5000 + 5000
        $this->assertEquals(Invoice::STATUS_WAITING_PAYMENT, $invoice->status);

        // Reload and check denda claims are tagged
        $denda1->refresh();
        $denda2->refresh();
        $this->assertEquals(DendaClaim::STATUS_TAGGED, $denda1->status);
        $this->assertEquals($invoice->id, $denda1->invoice_id);
        $this->assertEquals(DendaClaim::STATUS_TAGGED, $denda2->status);
        $this->assertEquals($invoice->id, $denda2->invoice_id);

        // Box status updated
        $this->box->refresh();
        $this->assertEquals(Box::STATUS_INVOICE, $this->box->status);
    }

    // ─── Test: invoice tanpa denda → denda_total = 0 ────────────

    public function test_generate_invoice_without_denda_has_zero_denda_total(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', 100)
            ->set('length', 50)
            ->set('width', 40)
            ->set('height', 30)
            ->call('generateInvoice');

        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertEquals(0, $invoice->denda_total);
    }

    // ─── Test: grand total = fee_tax + fee_wh + fee_packing + add_on + denda_total ─

    public function test_grand_total_includes_denda_total(): void
    {
        $this->actingAs($this->admin);

        $item = Item::factory()->create(['customer_id' => $this->customer->id]);
        DendaClaim::factory()->create([
            'customer_id' => $this->customer->id,
            'item_id' => $item->id,
            'jumlah_denda' => 5000,
            'status' => DendaClaim::STATUS_PENDING,
            'invoice_id' => null,
        ]);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', 100)
            ->set('length', 50)
            ->set('width', 40)
            ->set('height', 30)
            ->set('addOn', 10000) // admin manual add on
            ->call('generateInvoice');

        $invoice = Invoice::first();
        $expectedGrandTotal = $invoice->fee_tax + $invoice->fee_wh + $invoice->fee_packing + $invoice->add_on + $invoice->denda_total;
        $this->assertEquals($expectedGrandTotal, $invoice->grand_total);
        $this->assertEquals(10000, $invoice->add_on);     // admin manual
        $this->assertEquals(5000, $invoice->denda_total);  // denda
    }

    // ─── Test: customer A denda tidak masuk invoice customer B ──

    public function test_denda_does_not_leak_to_other_customer(): void
    {
        $this->actingAs($this->admin);

        $customerB = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $boxB = Box::factory()->create([
            'customer_id' => $customerB->id,
            'status' => Box::STATUS_ARRIVED_INA,
            'type' => 'sharing',
            'method' => 'air',
        ]);

        // Denda for customer A
        $itemA = Item::factory()->create(['customer_id' => $this->customer->id]);
        DendaClaim::factory()->create([
            'customer_id' => $this->customer->id,
            'item_id' => $itemA->id,
            'jumlah_denda' => 5000,
            'status' => DendaClaim::STATUS_PENDING,
        ]);

        // Generate invoice for customer B
        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $boxB->id)
            ->set('weight', 50)
            ->set('length', 30)
            ->set('width', 20)
            ->set('height', 10)
            ->call('generateInvoice');

        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertEquals(0, $invoice->denda_total);

        // Customer A's denda still pending
        $this->assertEquals(1, DendaClaim::where('customer_id', $this->customer->id)
            ->where('status', DendaClaim::STATUS_PENDING)
            ->whereNull('invoice_id')
            ->count());
    }

    // ─── Test: denda tagged → paid saat invoice verified ────────

    public function test_denda_becomes_paid_when_invoice_verified(): void
    {
        $this->actingAs($this->admin);

        // Create denda and generate invoice
        $item = Item::factory()->create(['customer_id' => $this->customer->id]);
        DendaClaim::factory()->create([
            'customer_id' => $this->customer->id,
            'item_id' => $item->id,
            'jumlah_denda' => 5000,
            'status' => DendaClaim::STATUS_PENDING,
            'invoice_id' => null,
        ]);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', 100)
            ->set('length', 50)
            ->set('width', 40)
            ->set('height', 30)
            ->call('generateInvoice');

        $invoice = Invoice::first();
        $this->assertEquals(DendaClaim::STATUS_TAGGED, DendaClaim::where('invoice_id', $invoice->id)->first()->status);

        // Customer pays
        $invoice->status = Invoice::STATUS_WAITING_VERIFICATION;
        $invoice->save();

        // Admin verifies
        Livewire::test(VerificationIndex::class)
            ->set('selectedId', $invoice->id)
            ->call('verifyPayment');

        // Denda should now be paid
        $invoice->refresh();
        $this->assertEquals(Invoice::STATUS_VERIFIED, $invoice->status);
        $denda = DendaClaim::where('invoice_id', $invoice->id)->first();
        $this->assertEquals(DendaClaim::STATUS_PAID, $denda->status);
    }

    // ─── Test: sudah tagged denda tidak masuk invoice baru ──────

    public function test_tagged_denda_not_included_in_new_invoice(): void
    {
        $this->actingAs($this->admin);

        $item = Item::factory()->create(['customer_id' => $this->customer->id]);
        DendaClaim::factory()->create([
            'customer_id' => $this->customer->id,
            'item_id' => $item->id,
            'jumlah_denda' => 5000,
            'status' => DendaClaim::STATUS_TAGGED,
            'invoice_id' => Invoice::factory(), // already tagged to another invoice
        ]);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', 100)
            ->set('length', 50)
            ->set('width', 40)
            ->set('height', 30)
            ->call('generateInvoice');

        $invoice = Invoice::orderByDesc('id')->first();
        $this->assertEquals(0, $invoice->denda_total);
    }

    // ─── Preview shows denda ────────────────────────────────────

    public function test_preview_includes_pending_denda(): void
    {
        $this->actingAs($this->admin);

        $item = Item::factory()->create(['customer_id' => $this->customer->id]);
        DendaClaim::factory()->create([
            'customer_id' => $this->customer->id,
            'item_id' => $item->id,
            'jumlah_denda' => 5000,
            'status' => DendaClaim::STATUS_PENDING,
            'invoice_id' => null,
        ]);

        $component = Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', 100)
            ->set('length', 50)
            ->set('width', 40)
            ->set('height', 30);

        $preview = $component->get('preview');
        $this->assertNotNull($preview);
        $this->assertEquals(5000, $preview['denda_total']);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    private function seedRates(): void
    {
        $defaults = [
            'rate_sharing_air_berat' => 100,
            'rate_sharing_air_volume' => 230,
            'rate_sharing_sea_berat' => 80,
            'rate_sharing_sea_volume' => 180,
            'rate_sharing_sensitive_air_berat' => 150,
            'rate_sharing_sensitive_air_volume' => 350,
            'rate_sharing_sensitive_sea_berat' => 120,
            'rate_sharing_sensitive_sea_volume' => 270,
            'rate_direct_air_berat' => 120,
            'rate_direct_air_volume' => 280,
            'rate_direct_sea_berat' => 95,
            'rate_direct_sea_volume' => 220,
            'fee_packing_150' => 5000,
            'fee_packing_1000' => 6500,
            'fee_packing_2000' => 8000,
            'fee_packing_extra_per_kg' => 1500,
        ];

        foreach ($defaults as $key => $value) {
            Setting::create(['key' => $key, 'value' => $value]);
        }
    }
}
