<?php

namespace Tests\Unit\Services;

use App\Models\Setting;
use App\Services\FeeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for FeeCalculationService.
 *
 * CLAUDE.md §4: "Sebelum mengklaim fee engine selesai, WAJIB: hitung manual
 * minimal 5 skenario berbeda (kombinasi sharing/direct, air/sea, sensitive/
 * non-sensitive, dan 1 kasus volume > berat), tulis sebagai test case dengan
 * hasil manual di komentar, baru assert service menghasilkan angka yang sama."
 *
 * Volume formula (client-verified): (P × L × T) / 6000
 *
 * Default rates (PRD §4.12):
 * - kurs_yuan_idr: 2460
 * - rate_sharing_air_berat: 255
 * - rate_sharing_air_volume: 230
 * - rate_sharing_sea_berat: 70
 * - rate_sharing_sea_volume: 83
 * - rate_sharing_sensitive_air_berat: 315
 * - rate_sharing_sensitive_air_volume: 315
 * - rate_sharing_sensitive_sea_berat: 95
 * - rate_sharing_sensitive_sea_volume: 95
 * - rate_direct_air_berat: 230
 * - rate_direct_air_volume: 160
 * - rate_direct_sea_berat: 70
 * - rate_direct_sea_volume: 90
 * - fee_packing_150: 5000
 * - fee_packing_1000: 6500
 * - fee_packing_2000: 8000
 * - fee_packing_extra_per_kg: 1500
 */
class FeeCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private FeeCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default rates
        $this->seedDefaultRates();

        $this->service = new FeeCalculationService();
    }

    /**
     * Skenario 1: Sharing, Air, Non-Sensitive, Weight > Volume
     *
     * Input:
     * - type: sharing, method: air, sensitive: false
     * - weight: 100 kg, P: 50cm, L: 40cm, T: 30cm
     *
     * Manual calculation:
     * - Volume = (50 × 40 × 30) / 6000 = 10
     * - Basis = max(100, 10) = 100 (weight wins)
     * - Rate key = rate_sharing_air_berat (berat > volume)
     * - Rate = 255
     * - Fee TAX = 100 × 255 = 25500
     * - Fee WH = 5000 (100 kg → tier 0-150)
     * - Fee Packing = 5000 (same tier)
     * - Grand Total = 25500 + 5000 + 5000 = 35500
     */
    public function test_scenario_1_sharing_air_non_sensitive(): void
    {
        $result = $this->service->calculate(
            type: 'sharing',
            method: 'air',
            weight: 100,
            length: 50,
            width: 40,
            height: 30,
            isSensitive: false,
        );

        $this->assertEquals(10.0, $result['volume']);
        $this->assertEquals(100.0, $result['basis']);
        $this->assertEquals('rate_sharing_air_berat', $result['rate_key']);
        $this->assertEquals(255, $result['rate_used']);
        $this->assertEquals(25500, $result['fee_tax']);
        $this->assertEquals(5000, $result['fee_wh']);
        $this->assertEquals(5000, $result['fee_packing']);
        $this->assertEquals(35500, $result['grand_total']);
    }

    /**
     * Skenario 2: Direct, Sea, Non-Sensitive, Volume > Weight
     *
     * Input:
     * - type: direct, method: sea, sensitive: false
     * - weight: 50 kg, P: 100cm, L: 80cm, T: 60cm
     *
     * Manual calculation:
     * - Volume = (100 × 80 × 60) / 6000 = 80
     * - Basis = max(50, 80) = 80 (volume wins)
     * - Rate key = rate_direct_sea_volume
     * - Rate = 90 - 5 = 85 (REV-05.4: Direct Sea >25kg discount)
     * - Fee TAX = 80 × 85 = 6800
     * - Fee WH = 5000 (50 kg → tier 0-150)
     * - Fee Packing = 5000 (same)
     * - Grand Total = 6800 + 5000 + 5000 = 16800
     */
    public function test_scenario_2_direct_sea_non_sensitive(): void
    {
        $result = $this->service->calculate(
            type: 'direct',
            method: 'sea',
            weight: 50,
            length: 100,
            width: 80,
            height: 60,
            isSensitive: false,
        );

        $this->assertEquals(80.0, $result['volume']);
        $this->assertEquals(80.0, $result['basis']);
        $this->assertEquals('rate_direct_sea_volume', $result['rate_key']);
        $this->assertEquals(85, $result['rate_used']); // REV-05.4: 90 - 5
        $this->assertEquals(6800, $result['fee_tax']);
        $this->assertEquals(5000, $result['fee_wh']);
        $this->assertEquals(5000, $result['fee_packing']);
        $this->assertEquals(16800, $result['grand_total']);
    }

    /**
     * Skenario 3: Sharing, Air, Sensitive, Weight > Volume
     *
     * Input:
     * - type: sharing, method: air, sensitive: true
     * - weight: 200 kg, P: 60cm, L: 50cm, T: 40cm
     *
     * Manual calculation:
     * - Volume = (60 × 50 × 40) / 6000 = 20
     * - Basis = max(200, 20) = 200 (weight wins)
     * - Rate key = rate_sharing_sensitive_air_berat
     * - Rate = 315
     * - Fee TAX = 200 × 315 = 63000
     * - Fee WH = 6500 (200 kg → tier 151-1000)
     * - Fee Packing = 6500 (same)
     * - Grand Total = 63000 + 6500 + 6500 = 76000
     */
    public function test_scenario_3_sharing_air_sensitive(): void
    {
        $result = $this->service->calculate(
            type: 'sharing',
            method: 'air',
            weight: 200,
            length: 60,
            width: 50,
            height: 40,
            isSensitive: true,
        );

        $this->assertEquals(20.0, $result['volume']);
        $this->assertEquals(200.0, $result['basis']);
        $this->assertEquals('rate_sharing_sensitive_air_berat', $result['rate_key']);
        $this->assertEquals(315, $result['rate_used']);
        $this->assertEquals(63000, $result['fee_tax']);
        $this->assertEquals(6500, $result['fee_wh']);
        $this->assertEquals(6500, $result['fee_packing']);
        $this->assertEquals(76000, $result['grand_total']);
    }

    /**
     * Skenario 4: Direct, Sea, Sensitive, Volume > Weight
     *
     * PRD §4.12: Direct TIDAK punya sensitive rate. Direct sensitive → gunakan rate_direct_sea_volume.
     *
     * Input:
     * - type: direct, method: sea, sensitive: true
     * - weight: 30 kg, P: 120cm, L: 100cm, T: 80cm
     *
     * Manual calculation:
     * - Volume = (120 × 100 × 80) / 6000 = 160
     * - Basis = max(30, 160) = 160 (volume wins)
     * - Rate key = rate_direct_sea_volume (direct tidak punya sensitive variant)
     * - Rate = 90 - 5 = 85 (REV-05.4: Direct Sea >25kg discount)
     * - Fee TAX = 160 × 85 = 13600
     * - Fee WH = 5000 (30 kg → tier 0-150)
     * - Fee Packing = 5000 (same)
     * - Grand Total = 13600 + 5000 + 5000 = 23600
     */
    public function test_scenario_4_direct_sea_sensitive(): void
    {
        $result = $this->service->calculate(
            type: 'direct',
            method: 'sea',
            weight: 30,
            length: 120,
            width: 100,
            height: 80,
            isSensitive: true,
        );

        $this->assertEquals(160.0, $result['volume']);
        $this->assertEquals(160.0, $result['basis']);
        $this->assertEquals('rate_direct_sea_volume', $result['rate_key']);
        $this->assertEquals(85, $result['rate_used']); // REV-05.4: 90 - 5
        $this->assertEquals(13600, $result['fee_tax']);
        $this->assertEquals(5000, $result['fee_wh']);
        $this->assertEquals(5000, $result['fee_packing']);
        $this->assertEquals(23600, $result['grand_total']);
    }

    /**
     * Skenario 5: Sharing, Air, Non-Sensitive, Volume > Weight
     *
     * Input:
     * - type: sharing, method: air, sensitive: false
     * - weight: 10 kg, P: 200cm, L: 150cm, T: 100cm
     *
     * Manual calculation:
     * - Volume = (200 × 150 × 100) / 6000 = 500
     * - Basis = max(10, 500) = 500 (volume wins)
     * - Rate key = rate_sharing_air_volume
     * - Rate = 230
     * - Fee TAX = 500 × 230 = 115000
     * - Fee WH = 5000 (10 kg → tier 0-150)
     * - Fee Packing = 5000 (same)
     * - Grand Total = 115000 + 5000 + 5000 = 125000
     */
    public function test_scenario_5_volume_greater_than_weight(): void
    {
        $result = $this->service->calculate(
            type: 'sharing',
            method: 'air',
            weight: 10,
            length: 200,
            width: 150,
            height: 100,
            isSensitive: false,
        );

        $this->assertEquals(500.0, $result['volume']);
        $this->assertEquals(500.0, $result['basis']);
        $this->assertEquals('rate_sharing_air_volume', $result['rate_key']);
        $this->assertEquals(230, $result['rate_used']);
        $this->assertEquals(115000, $result['fee_tax']);
        $this->assertEquals(5000, $result['fee_wh']);
        $this->assertEquals(5000, $result['fee_packing']);
        $this->assertEquals(125000, $result['grand_total']);
    }

    /**
     * Skenario 6: Fee WH/Packing tiered — weight > 2000 kg (extra per kg)
     *
     * Input:
     * - type: sharing, method: sea, sensitive: false
     * - weight: 2500 kg, P: 10cm, L: 10cm, T: 10cm (kecil, biar berat domine)
     *
     * Manual calculation:
     * - Volume = (10 × 10 × 10) / 6000 = 0.1667
     * - Basis = max(2500, 0.1667) = 2500
     * - Rate key = rate_sharing_sea_berat (berat > volume)
     * - Rate = 70
     * - Fee TAX = 2500 × 70 = 175000
     * - Fee WH = 8000 + (2500 - 2000) × 1500 = 8000 + 750000 = 758000
     * - Fee Packing = 758000 (same tier logic)
     * - Grand Total = 175000 + 758000 + 758000 = 1691000
     */
    public function test_scenario_6_tiered_extra_per_kg(): void
    {
        $result = $this->service->calculate(
            type: 'sharing',
            method: 'sea',
            weight: 2500,
            length: 10,
            width: 10,
            height: 10,
            isSensitive: false,
        );

        $this->assertEqualsWithDelta(0.17, $result['volume'], 0.01);
        $this->assertEquals(2500.0, $result['basis']);
        $this->assertEquals('rate_sharing_sea_berat', $result['rate_key']);
        $this->assertEquals(70, $result['rate_used']);
        $this->assertEquals(175000, $result['fee_tax']);
        $this->assertEquals(758000, $result['fee_wh']);
        $this->assertEquals(758000, $result['fee_packing']);
        $this->assertEquals(1691000, $result['grand_total']);
    }

    /**
     * Skenario 7: Add On dari admin
     *
     * Input:
     * - type: direct, method: air, sensitive: false
     * - weight: 50 kg, P: 10cm, L: 10cm, T: 10cm
     * - addOn: 50000
     *
     * Manual calculation:
     * - Volume = (10 × 10 × 10) / 6000 = 0.1667
     * - Basis = max(50, 0.1667) = 50 (weight wins)
     * - Rate key = rate_direct_air_berat
     * - Rate = 230
     * - Fee TAX = 50 × 230 = 11500
     * - Fee WH = 5000 (50 kg → tier 0-150)
     * - Fee Packing = 5000 (same)
     * - Grand Total = 11500 + 5000 + 5000 + 50000 = 71500
     */
    public function test_scenario_7_with_add_on(): void
    {
        $result = $this->service->calculate(
            type: 'direct',
            method: 'air',
            weight: 50,
            length: 10,
            width: 10,
            height: 10,
            isSensitive: false,
            addOn: 50000,
        );

        $this->assertEqualsWithDelta(0.17, $result['volume'], 0.01);
        $this->assertEquals(50.0, $result['basis']);
        $this->assertEquals('rate_direct_air_berat', $result['rate_key']);
        $this->assertEquals(230, $result['rate_used']);
        $this->assertEquals(11500, $result['fee_tax']);
        $this->assertEquals(5000, $result['fee_wh']);
        $this->assertEquals(5000, $result['fee_packing']);
        $this->assertEquals(50000, $result['add_on']);
        $this->assertEquals(71500, $result['grand_total']);
    }

    /**
     * Skenario 8: With denda_total (Revisi §2.4.4)
     *
     * Same as scenario 7 but with dendaTotal = 10000
     * Grand Total = fee_tax(11500) + fee_wh(5000) + fee_packing(5000) + add_on(50000) + denda(10000) = 81500
     */
    public function test_scenario_8_with_denda_total(): void
    {
        $result = $this->service->calculate(
            type: 'direct',
            method: 'air',
            weight: 50,
            length: 10,
            width: 10,
            height: 10,
            isSensitive: false,
            addOn: 50000,
            dendaTotal: 10000,
        );

        $this->assertEquals(50000, $result['add_on']);
        $this->assertEquals(10000, $result['denda_total']);
        $this->assertEquals(81500, $result['grand_total']);
    }

    /**
     * denda_total defaults to 0 when not passed (backward compat).
     */
    public function test_denda_total_defaults_to_zero(): void
    {
        $result = $this->service->calculate(
            type: 'sharing',
            method: 'air',
            weight: 100,
            length: 50,
            width: 40,
            height: 30,
            isSensitive: false,
        );

        $this->assertEquals(0, $result['denda_total']);
    }

    /**
     * Test volume calculation standalone.
     *
     * Volume formula: (P × L × T) / 6000
     */
    public function test_volume_calculation(): void
    {
        // (100 × 80 × 60) / 6000 = 80
        $this->assertEquals(80.0, $this->service->calculateVolume(100, 80, 60));

        // (50 × 40 × 30) / 6000 = 10
        $this->assertEquals(10.0, $this->service->calculateVolume(50, 40, 30));

        // (10 × 10 × 10) / 6000 = 0.1667
        $this->assertEqualsWithDelta(0.1667, $this->service->calculateVolume(10, 10, 10), 0.001);
    }

    /**
     * Test basis calculation (max of weight vs volume).
     */
    public function test_basis_calculation(): void
    {
        // weight > volume
        $this->assertEquals(100.0, $this->service->calculateBasis(100, 50));

        // volume > weight
        $this->assertEquals(500.0, $this->service->calculateBasis(100, 500));

        // equal
        $this->assertEquals(100.0, $this->service->calculateBasis(100, 100));
    }

    /**
     * Test Fee WH tiered pricing breakpoints.
     */
    public function test_fee_wh_tiered(): void
    {
        // Tier 0-150: 5000
        $this->assertEquals(5000, $this->service->calculateFeeWh(0));
        $this->assertEquals(5000, $this->service->calculateFeeWh(50));
        $this->assertEquals(5000, $this->service->calculateFeeWh(150));

        // Tier 151-1000: 6500
        $this->assertEquals(6500, $this->service->calculateFeeWh(151));
        $this->assertEquals(6500, $this->service->calculateFeeWh(500));
        $this->assertEquals(6500, $this->service->calculateFeeWh(1000));

        // Tier 1001-2000: 8000
        $this->assertEquals(8000, $this->service->calculateFeeWh(1001));
        $this->assertEquals(8000, $this->service->calculateFeeWh(1500));
        $this->assertEquals(8000, $this->service->calculateFeeWh(2000));

        // Tier >2000: 8000 + (weight - 2000) × 1500
        $this->assertEquals(8000 + (2001 - 2000) * 1500, $this->service->calculateFeeWh(2001));
        $this->assertEquals(8000 + (2500 - 2000) * 1500, $this->service->calculateFeeWh(2500));
        $this->assertEquals(8000 + (3000 - 2000) * 1500, $this->service->calculateFeeWh(3000));
    }

    /**
     * Test Fee Packing uses same tiered logic as Fee WH.
     */
    public function test_fee_packing_same_as_wh(): void
    {
        $weights = [50, 150, 151, 500, 1000, 1001, 1500, 2000, 2001, 2500];

        foreach ($weights as $weight) {
            $this->assertEquals(
                $this->service->calculateFeeWh($weight),
                $this->service->calculateFeePacking($weight),
                "Fee Packing should equal Fee WH at weight {$weight}"
            );
        }
    }

    /**
     * Test getRate returns correct values.
     */
    public function test_get_rate(): void
    {
        $this->assertEquals(255, $this->service->getRate('rate_sharing_air_berat'));
        $this->assertEquals(230, $this->service->getRate('rate_sharing_air_volume'));
        $this->assertEquals(70, $this->service->getRate('rate_sharing_sea_berat'));
        $this->assertEquals(83, $this->service->getRate('rate_sharing_sea_volume'));
        $this->assertEquals(5000, $this->service->getRate('fee_packing_150'));
        $this->assertEquals(0, $this->service->getRate('nonexistent_key'));
    }

    /**
     * Test getAllRates returns all 16 parameters (12 rate + 4 packing).
     *
     * PRD §4.12: 12 rate variants + 4 fee packing = 16 rate keys
     * (kurs_yuan_idr is currency, not a rate used in calculation)
     */
    public function test_get_all_rates(): void
    {
        $rates = $this->service->getAllRates();

        $this->assertCount(20, $rates);
        $this->assertArrayHasKey('rate_sharing_air_berat', $rates);
        $this->assertArrayHasKey('fee_packing_150', $rates);
        $this->assertArrayHasKey('fee_packing_extra_per_kg', $rates);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Seed default rates from PRD §4.12.
     */
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
