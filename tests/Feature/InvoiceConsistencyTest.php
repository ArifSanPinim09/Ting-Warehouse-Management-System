<?php

namespace Tests\Feature;

use App\Livewire\Admin\GenerateInvoice;
use App\Livewire\Customer\CreateInvoice;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhChinaData;
use App\Services\FeeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Goal verification: 3 conditions for invoice consistency.
 *
 * (1) Flexible invoice produces correct totals for cross-box items
 * (2) All old invoices (pre-revision, 1-box model) remain unchanged
 * (3) Old way (admin) and new way (flexible) produce consistent numbers
 *     for equivalent cases (1 full box selected by customer == admin generate)
 */
class InvoiceConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private FeeCalculationService $feeService;
    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDefaultRates();
        $this->feeService = new FeeCalculationService();
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
    }

    // ═══════════════════════════════════════════════════════════════
    // CONDITION 1: Flexible invoice correct totals for cross-box items
    // ═══════════════════════════════════════════════════════════════

    /**
     * Cross-box scenario: items from sharing/air and direct/sea boxes.
     * Rate should come from FIRST item's box (sharing/air).
     *
     * Manual calculation:
     * - Item A: WH China berat = 3.0 kg, from sharing/air box
     * - Item B: WH China berat = 5.0 kg, from direct/sea box
     * - Total weight = 8.0 kg
     * - Dimensions: 60x40x50 → volume = (60*40*50)/6000 = 20
     * - Basis = max(8.0, 20000) = 20000
     * - Rate key = rate_sharing_air_volume (first box is sharing/air, volume > weight)
     * - Rate = 230
     * - Fee TAX = 20000 × 230 = 4,600,000
     * - Fee WH = 5000 (8.0 kg → tier 0-150)
     * - Fee Packing = 5000 (same tier)
     * - Grand Total = 4,600,000 + 5000 + 5000 = 4,610,000
     */
    public function test_condition_1_cross_box_flexible_invoice_correct_total(): void
    {
        $box1 = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'sharing',
            'method' => 'air',
            'status' => Box::STATUS_OPEN,
        ]);
        $box2 = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'direct',
            'method' => 'sea',
            'status' => Box::STATUS_OPEN,
        ]);

        $itemA = Item::factory()->create([
            'box_id' => $box1->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'CROSS-001',
            'is_sensitive' => false,
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);
        $itemB = Item::factory()->create([
            'box_id' => $box2->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'CROSS-002',
            'is_sensitive' => false,
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);

        WhChinaData::create(['resi_number' => 'CROSS-001', 'berat' => 3.0, 'ukuran_box' => '30x30x30', 'input_by' => $this->admin->id, 'item_id' => $itemA->id, 'matched_at' => now()]);
        WhChinaData::create(['resi_number' => 'CROSS-002', 'berat' => 5.0, 'ukuran_box' => '40x40x40', 'input_by' => $this->admin->id, 'item_id' => $itemB->id, 'matched_at' => now()]);

        // Manual expected calculation
        $expected = $this->feeService->calculate(
            type: 'sharing',   // first box's type
            method: 'air',     // first box's method
            weight: 8.0,       // 3.0 + 5.0 aggregated
            length: 60,
            width: 40,
            height: 50,
            isSensitive: false,
        );

        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->call('toggleItem', $itemA->id)
            ->call('toggleItem', $itemB->id)
            ->set('length', 60)
            ->set('width', 40)
            ->set('height', 50)
            ->call('createInvoice');

        $invoice = Invoice::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($invoice);
        $this->assertNull($invoice->box_id);

        // Condition 1: totals match manual calculation
        $this->assertEquals($expected['fee_tax'], (float) $invoice->fee_tax, 'fee_tax mismatch');
        $this->assertEquals($expected['fee_wh'], (float) $invoice->fee_wh, 'fee_wh mismatch');
        $this->assertEquals($expected['fee_packing'], (float) $invoice->fee_packing, 'fee_packing mismatch');
        $this->assertEquals($expected['grand_total'], (float) $invoice->grand_total, 'grand_total mismatch');
        $this->assertEquals(8.0, (float) $invoice->weight, 'aggregated weight mismatch');
        $this->assertEquals($expected['volume'], (float) $invoice->volume, 'volume mismatch');
    }

    /**
     * Cross-box with sensitive item: one item is sensitive → rate should use sensitive variant.
     */
    public function test_condition_1_cross_box_with_sensitive_item(): void
    {
        $box1 = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'sharing',
            'method' => 'air',
            'status' => Box::STATUS_OPEN,
        ]);

        $itemA = Item::factory()->create([
            'box_id' => $box1->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'SENS-001',
            'is_sensitive' => false,
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);
        $itemB = Item::factory()->create([
            'box_id' => $box1->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'SENS-002',
            'is_sensitive' => true,
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);

        WhChinaData::create(['resi_number' => 'SENS-001', 'berat' => 2.0, 'ukuran_box' => '20x20x20', 'input_by' => $this->admin->id, 'item_id' => $itemA->id, 'matched_at' => now()]);
        WhChinaData::create(['resi_number' => 'SENS-002', 'berat' => 3.0, 'ukuran_box' => '30x30x30', 'input_by' => $this->admin->id, 'item_id' => $itemB->id, 'matched_at' => now()]);

        // Manual: total weight = 5.0, isSensitive = true (one item is sensitive)
        // Volume = (60*40*50)/6000 = 20, basis = 20
        // Rate key = rate_sharing_sensitive_air_volume = 315
        $expected = $this->feeService->calculate(
            type: 'sharing',
            method: 'air',
            weight: 5.0,
            length: 60,
            width: 40,
            height: 50,
            isSensitive: true,
        );

        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->call('toggleItem', $itemA->id)
            ->call('toggleItem', $itemB->id)
            ->set('length', 60)
            ->set('width', 40)
            ->set('height', 50)
            ->call('createInvoice');

        $invoice = Invoice::where('customer_id', $this->customer->id)->first();
        $this->assertEquals($expected['rate_key'], 'rate_sharing_sensitive_air_volume');
        $this->assertEquals($expected['fee_tax'], (float) $invoice->fee_tax);
        $this->assertEquals($expected['grand_total'], (float) $invoice->grand_total);
    }

    // ═══════════════════════════════════════════════════════════════
    // CONDITION 2: Old invoices remain unchanged
    // ═══════════════════════════════════════════════════════════════

    /**
     * Create an old-style invoice (admin generate, box_id set), record all values,
     * then verify they're unchanged after flexible invoice code exists.
     */
    public function test_condition_2_old_invoice_unchanged_after_flexible_exists(): void
    {
        $box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'sharing',
            'method' => 'air',
            'status' => Box::STATUS_ARRIVED_INA,
        ]);

        // Admin generates invoice the old way
        $this->actingAs($this->admin);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $box->id)
            ->set('weight', 150)
            ->set('length', 80)
            ->set('width', 60)
            ->set('height', 50)
            ->set('addOn', 25000)
            ->call('generateInvoice');

        $invoice = Invoice::where('box_id', $box->id)->first();
        $this->assertNotNull($invoice);

        // Record all values
        $savedValues = [
            'invoice_number' => $invoice->invoice_number,
            'box_id' => $invoice->box_id,
            'customer_id' => $invoice->customer_id,
            'weight' => (float) $invoice->weight,
            'volume' => (float) $invoice->volume,
            'fee_tax' => (float) $invoice->fee_tax,
            'fee_wh' => (float) $invoice->fee_wh,
            'fee_packing' => (float) $invoice->fee_packing,
            'add_on' => (float) $invoice->add_on,
            'grand_total' => (float) $invoice->grand_total,
            'status' => $invoice->status,
        ];

        // Now create a flexible invoice for same customer (simulates coexistence)
        $box2 = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'direct',
            'method' => 'sea',
            'status' => Box::STATUS_OPEN,
        ]);
        $item = Item::factory()->create([
            'box_id' => $box2->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'FLEX-001',
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);
        WhChinaData::create(['resi_number' => 'FLEX-001', 'berat' => 10, 'ukuran_box' => '50x50x50', 'input_by' => $this->admin->id, 'item_id' => $item->id, 'matched_at' => now()]);

        Livewire::test(CreateInvoice::class)
            ->call('toggleItem', $item->id)
            ->set('length', 50)
            ->set('width', 50)
            ->set('height', 50)
            ->call('createInvoice');

        // Condition 2: old invoice values MUST be identical
        $invoice->refresh();
        $this->assertEquals($savedValues['invoice_number'], $invoice->invoice_number);
        $this->assertEquals($savedValues['box_id'], $invoice->box_id);
        $this->assertEquals($savedValues['weight'], (float) $invoice->weight);
        $this->assertEquals($savedValues['fee_tax'], (float) $invoice->fee_tax, 'old invoice fee_tax changed!');
        $this->assertEquals($savedValues['fee_wh'], (float) $invoice->fee_wh, 'old invoice fee_wh changed!');
        $this->assertEquals($savedValues['fee_packing'], (float) $invoice->fee_packing, 'old invoice fee_packing changed!');
        $this->assertEquals($savedValues['grand_total'], (float) $invoice->grand_total, 'old invoice grand_total changed!');
        $this->assertEquals($savedValues['status'], $invoice->status);

        // Old invoice still has box relationship
        $this->assertNotNull($invoice->box);
        $this->assertFalse($invoice->isFlexible());
    }

    /**
     * Old invoice: box_info accessor still returns box tracking number.
     */
    public function test_condition_2_old_invoice_box_info_accessor(): void
    {
        $box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'sharing',
            'method' => 'air',
            'tracking_number' => 'TRK-OLD-001',
            'status' => Box::STATUS_ARRIVED_INA,
        ]);

        $invoice = Invoice::factory()->create([
            'box_id' => $box->id,
            'customer_id' => $this->customer->id,
        ]);

        $this->assertStringContainsString('TRK-OLD-001', $invoice->box_info);
    }

    // ═══════════════════════════════════════════════════════════════
    // CONDITION 3: Old way == New way for equivalent case
    // ═══════════════════════════════════════════════════════════════

    /**
     * Equivalent case: 1 box with 2 items, admin generates invoice for the box,
     * then customer selects ALL items from that box in a flexible invoice.
     *
     * For equivalence: WH China berat per item must sum to admin's box weight.
     * Same dimensions used.
     *
     * Expected: fee_tax, fee_wh, fee_packing, grand_total are IDENTICAL.
     */
    public function test_condition_3_old_and_new_produce_same_totals(): void
    {
        // ─── Setup: box with 2 items ────────────────────────────
        $box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'sharing',
            'method' => 'air',
            'status' => Box::STATUS_ARRIVED_INA,
        ]);

        $item1 = Item::factory()->create([
            'box_id' => $box->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'EQ-001',
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);
        $item2 = Item::factory()->create([
            'box_id' => $box->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'EQ-002',
            'arrived_indonesia' => true,
            'status' => Item::STATUS_ACTIVE,
        ]);

        // WH China berat: 40 + 60 = 100 kg (matches admin's box weight)
        WhChinaData::create(['resi_number' => 'EQ-001', 'berat' => 40, 'ukuran_box' => '30x30x30', 'input_by' => $this->admin->id, 'item_id' => $item1->id, 'matched_at' => now()]);
        WhChinaData::create(['resi_number' => 'EQ-002', 'berat' => 60, 'ukuran_box' => '40x40x40', 'input_by' => $this->admin->id, 'item_id' => $item2->id, 'matched_at' => now()]);

        $weight = 100; // same as sum of WH China berat
        $length = 80;
        $width = 60;
        $height = 50;

        // ─── OLD WAY: Admin generates invoice ───────────────────
        $this->actingAs($this->admin);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $box->id)
            ->set('weight', $weight)
            ->set('length', $length)
            ->set('width', $width)
            ->set('height', $height)
            ->set('addOn', 0)
            ->call('generateInvoice');

        $oldInvoice = Invoice::where('box_id', $box->id)->first();
        $this->assertNotNull($oldInvoice);

        $oldTotals = [
            'fee_tax' => (float) $oldInvoice->fee_tax,
            'fee_wh' => (float) $oldInvoice->fee_wh,
            'fee_packing' => (float) $oldInvoice->fee_packing,
            'grand_total' => (float) $oldInvoice->grand_total,
            'weight' => (float) $oldInvoice->weight,
            'volume' => (float) $oldInvoice->volume,
        ];

        // ─── NEW WAY: Customer creates flexible invoice ─────────
        // Need a fresh box for the items (old box is now UP_INVOICE)
        $newBox = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'sharing',
            'method' => 'air',
            'status' => Box::STATUS_OPEN,
        ]);
        $item1->update(['box_id' => $newBox->id]);
        $item2->update(['box_id' => $newBox->id]);

        $this->actingAs($this->customer);

        Livewire::test(CreateInvoice::class)
            ->call('toggleItem', $item1->id)
            ->call('toggleItem', $item2->id)
            ->set('length', $length)
            ->set('width', $width)
            ->set('height', $height)
            ->call('createInvoice');

        $newInvoice = Invoice::where('customer_id', $this->customer->id)->whereNull('box_id')->first();
        $this->assertNotNull($newInvoice);

        // Condition 3: totals must be IDENTICAL
        $this->assertEquals($oldTotals['fee_tax'], (float) $newInvoice->fee_tax,
            "fee_tax differs: old={$oldTotals['fee_tax']} new=" . (float) $newInvoice->fee_tax);
        $this->assertEquals($oldTotals['fee_wh'], (float) $newInvoice->fee_wh,
            "fee_wh differs: old={$oldTotals['fee_wh']} new=" . (float) $newInvoice->fee_wh);
        $this->assertEquals($oldTotals['fee_packing'], (float) $newInvoice->fee_packing,
            "fee_packing differs: old={$oldTotals['fee_packing']} new=" . (float) $newInvoice->fee_packing);
        $this->assertEquals($oldTotals['grand_total'], (float) $newInvoice->grand_total,
            "grand_total differs: old={$oldTotals['grand_total']} new=" . (float) $newInvoice->grand_total);
        $this->assertEquals($oldTotals['weight'], (float) $newInvoice->weight,
            "weight differs: old={$oldTotals['weight']} new=" . (float) $newInvoice->weight);
        $this->assertEquals($oldTotals['volume'], (float) $newInvoice->volume,
            "volume differs: old={$oldTotals['volume']} new=" . (float) $newInvoice->volume);
    }

    /**
     * Verify FeeCalculationService itself produces consistent results
     * regardless of how it's called (same inputs → same outputs).
     */
    public function test_condition_3_fee_service_deterministic(): void
    {
        $params = [
            'type' => 'sharing',
            'method' => 'air',
            'weight' => 100,
            'length' => 80,
            'width' => 60,
            'height' => 50,
            'isSensitive' => false,
            'addOn' => 0,
            'dendaTotal' => 0,
        ];

        $result1 = $this->feeService->calculate(...$params);
        $result2 = $this->feeService->calculate(...$params);

        $this->assertEquals($result1, $result2, 'FeeCalculationService is not deterministic');
    }

    // ─── Helper ────────────────────────────────────────────────

    private function seedDefaultRates(): void
    {
        $settings = [
            ['key' => 'kurs_yuan_idr', 'value' => '2460', 'group' => 'currency'],
            ['key' => 'rate_sharing_air_berat', 'value' => '255', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_air_volume', 'value' => '230', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sea_berat', 'value' => '70', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sea_volume', 'value' => '83', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_air_berat', 'value' => '315', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_air_volume', 'value' => '315', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_sea_berat', 'value' => '95', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_sea_volume', 'value' => '95', 'group' => 'rate_sharing'],
            ['key' => 'rate_direct_air_berat', 'value' => '230', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_air_volume', 'value' => '160', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_sea_berat', 'value' => '70', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_sea_volume', 'value' => '90', 'group' => 'rate_direct'],
            ['key' => 'fee_packing_150', 'value' => '5000', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_1000', 'value' => '6500', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_2000', 'value' => '8000', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_extra_per_kg', 'value' => '1500', 'group' => 'fee_packing'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
