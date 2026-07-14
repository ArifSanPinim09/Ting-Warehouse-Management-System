<?php

namespace Tests\Unit\Services;

use App\Models\Invoice;
use App\Models\KursHistory;
use App\Models\Setting;
use App\Models\User;
use App\Services\FeeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Snapshot Rule Test — CLAUDE.md §3.3 / Revisi §2.2
 *
 * Memverifikasi bahwa invoice yang SUDAH VERIFIED sebelum kurs_history
 * diperkenalkan tetap menghasilkan angka yang SAMA PERSIS.
 *
 * FeeCalculationService::calculate() TIDAK menggunakan kurs — ia hanya
 * membaca 16 rate keys dari tabel settings. Kurs hanya dipakai untuk
 * tampilan (dashboard) dan generate invoice (Yuan→IDR conversion).
 *
 * Volume formula (client-verified): (P × L × T) / 6000
 *
 * Test ini membuktikan bahwa:
 * 1. calculate() menghasilkan angka yang sama meski kurs_history ada
 * 2. Invoice yang sudah dibuat tidak berubah (snapshot)
 * 3. KursHistory adalah layer terpisah, bukan bagian dari fee engine
 */
class SnapshotRuleTest extends TestCase
{
    use RefreshDatabase;

    private FeeCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default rates (PRD §4.12)
        $this->seedDefaultRates();

        $this->service = new FeeCalculationService();
    }

    /**
     * Pre-R1 snapshot: Invoice #1 — Sharing, Air, Non-Sensitive
     *
     * Dibuat SEBELUM kurs_history diperkenalkan.
     * Volume = (50×40×30)/6000 = 10, basis = max(100,10) = 100
     */
    public function test_pre_r1_invoice_1_sharing_air_unchanged(): void
    {
        $snapshot = [
            'type' => 'sharing',
            'method' => 'air',
            'weight' => 100.0,
            'length' => 50.0,
            'width' => 40.0,
            'height' => 30.0,
            'is_sensitive' => false,
            'add_on' => 0.0,
        ];
        $expectedFees = [
            'volume' => 10.0,
            'basis' => 100.0,
            'fee_tax' => 25500,
            'fee_wh' => 5000,
            'fee_packing' => 5000,
            'grand_total' => 35500,
        ];

        // Introduce kurs_history — should NOT affect fee calculation
        $admin = User::factory()->create(['role' => 'admin']);
        KursHistory::create([
            'kurs_value' => 2700.00,
            'effective_date' => '2026-07-01',
            'input_by' => $admin->id,
        ]);
        KursHistory::create([
            'kurs_value' => 2800.00,
            'effective_date' => '2026-07-09',
            'input_by' => $admin->id,
        ]);

        // Recalculate with same inputs
        $result = $this->service->calculate(
            type: $snapshot['type'],
            method: $snapshot['method'],
            weight: $snapshot['weight'],
            length: $snapshot['length'],
            width: $snapshot['width'],
            height: $snapshot['height'],
            isSensitive: $snapshot['is_sensitive'],
            addOn: $snapshot['add_on'],
        );

        // Assert EXACT match with pre-R1 values
        $this->assertEquals($expectedFees['volume'], $result['volume']);
        $this->assertEquals($expectedFees['basis'], $result['basis']);
        $this->assertEquals($expectedFees['fee_tax'], $result['fee_tax']);
        $this->assertEquals($expectedFees['fee_wh'], $result['fee_wh']);
        $this->assertEquals($expectedFees['fee_packing'], $result['fee_packing']);
        $this->assertEquals($expectedFees['grand_total'], $result['grand_total']);
    }

    /**
     * Pre-R1 snapshot: Invoice #2 — Direct, Sea, Non-Sensitive
     * Volume = (100×80×60)/6000 = 80, basis = max(50,80) = 80
     */
    public function test_pre_r1_invoice_2_direct_sea_unchanged(): void
    {
        $snapshot = [
            'type' => 'direct',
            'method' => 'sea',
            'weight' => 50.0,
            'length' => 100.0,
            'width' => 80.0,
            'height' => 60.0,
            'is_sensitive' => false,
            'add_on' => 0.0,
        ];
        $expectedFees = [
            'volume' => 80.0,
            'basis' => 80.0,
            'fee_tax' => 6800, // REV-05.4: Direct Sea >25kg → rate -5
            'fee_wh' => 5000,
            'fee_packing' => 5000,
            'grand_total' => 16800,
        ];

        // Introduce kurs_history
        $admin = User::factory()->create(['role' => 'admin']);
        KursHistory::create([
            'kurs_value' => 2500.00,
            'effective_date' => '2026-06-15',
            'input_by' => $admin->id,
        ]);

        $result = $this->service->calculate(
            type: $snapshot['type'],
            method: $snapshot['method'],
            weight: $snapshot['weight'],
            length: $snapshot['length'],
            width: $snapshot['width'],
            height: $snapshot['height'],
            isSensitive: $snapshot['is_sensitive'],
            addOn: $snapshot['add_on'],
        );

        $this->assertEquals($expectedFees['volume'], $result['volume']);
        $this->assertEquals($expectedFees['basis'], $result['basis']);
        $this->assertEquals($expectedFees['fee_tax'], $result['fee_tax']);
        $this->assertEquals($expectedFees['fee_wh'], $result['fee_wh']);
        $this->assertEquals($expectedFees['fee_packing'], $result['fee_packing']);
        $this->assertEquals($expectedFees['grand_total'], $result['grand_total']);
    }

    /**
     * Pre-R1 snapshot: Invoice #3 — Sharing, Air, Sensitive
     * Volume = (60×50×40)/6000 = 20, basis = max(200,20) = 200
     */
    public function test_pre_r1_invoice_3_sharing_air_sensitive_unchanged(): void
    {
        $snapshot = [
            'type' => 'sharing',
            'method' => 'air',
            'weight' => 200.0,
            'length' => 60.0,
            'width' => 50.0,
            'height' => 40.0,
            'is_sensitive' => true,
            'add_on' => 0.0,
        ];
        $expectedFees = [
            'volume' => 20.0,
            'basis' => 200.0,
            'fee_tax' => 63000,
            'fee_wh' => 6500,
            'fee_packing' => 6500,
            'grand_total' => 76000,
        ];

        // Introduce kurs_history with different kurs values
        $admin = User::factory()->create(['role' => 'admin']);
        KursHistory::create([
            'kurs_value' => 2000.00,
            'effective_date' => '2026-01-01',
            'input_by' => $admin->id,
        ]);
        KursHistory::create([
            'kurs_value' => 3000.00,
            'effective_date' => '2026-07-09',
            'input_by' => $admin->id,
        ]);

        $result = $this->service->calculate(
            type: $snapshot['type'],
            method: $snapshot['method'],
            weight: $snapshot['weight'],
            length: $snapshot['length'],
            width: $snapshot['width'],
            height: $snapshot['height'],
            isSensitive: $snapshot['is_sensitive'],
            addOn: $snapshot['add_on'],
        );

        $this->assertEquals($expectedFees['volume'], $result['volume']);
        $this->assertEquals($expectedFees['basis'], $result['basis']);
        $this->assertEquals($expectedFees['fee_tax'], $result['fee_tax']);
        $this->assertEquals($expectedFees['fee_wh'], $result['fee_wh']);
        $this->assertEquals($expectedFees['fee_packing'], $result['fee_packing']);
        $this->assertEquals($expectedFees['grand_total'], $result['grand_total']);
    }

    /**
     * Pre-R1 snapshot: Invoice #4 — Direct, Sea, Sensitive
     * Volume = (120×100×80)/6000 = 160, basis = max(30,160) = 160
     */
    public function test_pre_r1_invoice_4_direct_sea_sensitive_unchanged(): void
    {
        $snapshot = [
            'type' => 'direct',
            'method' => 'sea',
            'weight' => 30.0,
            'length' => 120.0,
            'width' => 100.0,
            'height' => 80.0,
            'is_sensitive' => true,
            'add_on' => 0.0,
        ];
        $expectedFees = [
            'volume' => 160.0,
            'basis' => 160.0,
            'fee_tax' => 13600, // REV-05.4: Direct Sea >25kg → rate -5
            'fee_wh' => 5000,
            'fee_packing' => 5000,
            'grand_total' => 23600,
        ];

        $admin = User::factory()->create(['role' => 'admin']);
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-07-09',
            'input_by' => $admin->id,
        ]);

        $result = $this->service->calculate(
            type: $snapshot['type'],
            method: $snapshot['method'],
            weight: $snapshot['weight'],
            length: $snapshot['length'],
            width: $snapshot['width'],
            height: $snapshot['height'],
            isSensitive: $snapshot['is_sensitive'],
            addOn: $snapshot['add_on'],
        );

        $this->assertEquals($expectedFees['volume'], $result['volume']);
        $this->assertEquals($expectedFees['basis'], $result['basis']);
        $this->assertEquals($expectedFees['fee_tax'], $result['fee_tax']);
        $this->assertEquals($expectedFees['fee_wh'], $result['fee_wh']);
        $this->assertEquals($expectedFees['fee_packing'], $result['fee_packing']);
        $this->assertEquals($expectedFees['grand_total'], $result['grand_total']);
    }

    /**
     * Pre-R1 snapshot: Invoice #5 — Volume > Weight, with Add On
     * Volume = (200×150×100)/6000 = 500, basis = max(10,500) = 500
     */
    public function test_pre_r1_invoice_5_volume_weight_with_addon_unchanged(): void
    {
        $snapshot = [
            'type' => 'sharing',
            'method' => 'air',
            'weight' => 10.0,
            'length' => 200.0,
            'width' => 150.0,
            'height' => 100.0,
            'is_sensitive' => false,
            'add_on' => 50000.0,
        ];
        $expectedFees = [
            'volume' => 500.0,
            'basis' => 500.0,
            'fee_tax' => 115000,
            'fee_wh' => 5000,
            'fee_packing' => 5000,
            'add_on' => 50000,
            'grand_total' => 175000,
        ];

        $admin = User::factory()->create(['role' => 'admin']);
        KursHistory::create([
            'kurs_value' => 2460.00,
            'effective_date' => '2026-06-01',
            'input_by' => $admin->id,
        ]);

        $result = $this->service->calculate(
            type: $snapshot['type'],
            method: $snapshot['method'],
            weight: $snapshot['weight'],
            length: $snapshot['length'],
            width: $snapshot['width'],
            height: $snapshot['height'],
            isSensitive: $snapshot['is_sensitive'],
            addOn: $snapshot['add_on'],
        );

        $this->assertEquals($expectedFees['volume'], $result['volume']);
        $this->assertEquals($expectedFees['basis'], $result['basis']);
        $this->assertEquals($expectedFees['fee_tax'], $result['fee_tax']);
        $this->assertEquals($expectedFees['fee_wh'], $result['fee_wh']);
        $this->assertEquals($expectedFees['fee_packing'], $result['fee_packing']);
        $this->assertEquals($expectedFees['add_on'], $result['add_on']);
        $this->assertEquals($expectedFees['grand_total'], $result['grand_total']);
    }

    /**
     * Pre-R1 snapshot: Invoice #6 — Tiered extra per kg (>2000kg)
     * Volume = (10×10×10)/6000 = 0.17, basis = max(2500,0.17) = 2500
     */
    public function test_pre_r1_invoice_6_tiered_extra_unchanged(): void
    {
        $snapshot = [
            'type' => 'sharing',
            'method' => 'sea',
            'weight' => 2500.0,
            'length' => 10.0,
            'width' => 10.0,
            'height' => 10.0,
            'is_sensitive' => false,
            'add_on' => 0.0,
        ];
        $expectedFees = [
            'fee_tax' => 175000,
            'fee_wh' => 758000,
            'fee_packing' => 758000,
            'grand_total' => 1691000,
        ];

        $admin = User::factory()->create(['role' => 'admin']);
        KursHistory::create([
            'kurs_value' => 2700.00,
            'effective_date' => '2026-07-09',
            'input_by' => $admin->id,
        ]);

        $result = $this->service->calculate(
            type: $snapshot['type'],
            method: $snapshot['method'],
            weight: $snapshot['weight'],
            length: $snapshot['length'],
            width: $snapshot['width'],
            height: $snapshot['height'],
            isSensitive: $snapshot['is_sensitive'],
            addOn: $snapshot['add_on'],
        );

        $this->assertEquals($expectedFees['fee_tax'], $result['fee_tax']);
        $this->assertEquals($expectedFees['fee_wh'], $result['fee_wh']);
        $this->assertEquals($expectedFees['fee_packing'], $result['fee_packing']);
        $this->assertEquals($expectedFees['grand_total'], $result['grand_total']);
    }

    /**
     * Snapshot rule: Invoice DB record is immutable after verification.
     *
     * Changing rates in settings should NOT alter existing invoice values.
     */
    public function test_invoice_snapshot_immutable_after_rate_change(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = \App\Models\Box::factory()->create(['customer_id' => $customer->id]);

        // Create invoice with original rates
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'fee_tax' => 25500,
            'fee_wh' => 5000,
            'fee_packing' => 5000,
            'add_on' => 0,
            'grand_total' => 35500,
            'status' => Invoice::STATUS_VERIFIED,
        ]);

        // Record snapshot
        $snapshotTax = $invoice->fee_tax;
        $snapshotWh = $invoice->fee_wh;
        $snapshotPacking = $invoice->fee_packing;
        $snapshotTotal = $invoice->grand_total;

        // Change rates in settings
        Setting::where('key', 'rate_sharing_air_volume')->update(['value' => '999']);
        Setting::where('key', 'fee_packing_150')->update(['value' => '99999']);

        // Introduce kurs_history
        $admin = User::factory()->create(['role' => 'admin']);
        KursHistory::create([
            'kurs_value' => 9999.00,
            'effective_date' => '2026-07-09',
            'input_by' => $admin->id,
        ]);

        // Invoice values MUST be unchanged
        $invoice->refresh();
        $this->assertEquals($snapshotTax, $invoice->fee_tax);
        $this->assertEquals($snapshotWh, $invoice->fee_wh);
        $this->assertEquals($snapshotPacking, $invoice->fee_packing);
        $this->assertEquals($snapshotTotal, $invoice->grand_total);
        $this->assertEquals(Invoice::STATUS_VERIFIED, $invoice->status);
    }

    /**
     * FeeCalculationService does NOT use kurs_history for fee calculation.
     * Kurs is a separate layer (currency conversion), not part of fee engine.
     */
    public function test_calculate_does_not_depend_on_kurs_history(): void
    {
        // Run calculate WITHOUT any kurs_history records
        $resultWithoutKurs = $this->service->calculate(
            type: 'sharing',
            method: 'air',
            weight: 100,
            length: 50,
            width: 40,
            height: 30,
        );

        // Add kurs_history records
        $admin = User::factory()->create(['role' => 'admin']);
        KursHistory::create([
            'kurs_value' => 2000.00,
            'effective_date' => '2026-01-01',
            'input_by' => $admin->id,
        ]);
        KursHistory::create([
            'kurs_value' => 5000.00,
            'effective_date' => '2026-07-09',
            'input_by' => $admin->id,
        ]);

        // Run calculate again — should produce IDENTICAL results
        $resultWithKurs = $this->service->calculate(
            type: 'sharing',
            method: 'air',
            weight: 100,
            length: 50,
            width: 40,
            height: 30,
        );

        $this->assertEquals($resultWithoutKurs, $resultWithKurs);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    private function seedDefaultRates(): void
    {
        $settings = [
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
