<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\Kalkulator;
use App\Models\Setting;
use App\Models\User;
use App\Services\FeeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Feature test for Customer Kalkulator.
 *
 * Verifies FeeCalculationService injection in the Livewire component.
 * CLAUDE.md §3.2: "Satu Source of Truth untuk Fee Calculation"
 * Fee engine itself already tested in Fase 3 (FeeCalculationServiceTest).
 */
class KalkulatorTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $this->seedDefaultRates();
    }

    /**
     * PRD §4.8: Kalkulator uses FeeCalculationService (not inline calculation).
     */
    public function test_kalkulator_uses_fee_calculation_service(): void
    {
        $this->actingAs($this->customer);

        // Verify result matches FeeCalculationService output directly
        $feeService = new FeeCalculationService();
        $expected = $feeService->calculate(
            type: 'sharing',
            method: 'air',
            weight: 100,
            length: 50,
            width: 40,
            height: 30,
            isSensitive: false,
        );

        Livewire::test(Kalkulator::class)
            ->set('type', 'sharing')
            ->set('method', 'air')
            ->set('weight', '100')
            ->set('length', '50')
            ->set('width', '40')
            ->set('height', '30')
            ->set('isSensitive', false)
            ->call('calculate')
            ->assertSet('calculated', true)
            ->assertSet('result.fee_tax', $expected['fee_tax'])
            ->assertSet('result.fee_wh', $expected['fee_wh'])
            ->assertSet('result.fee_packing', $expected['fee_packing'])
            ->assertSet('result.grand_total', $expected['grand_total']);
    }

    /**
     * PRD §4.8: Kalkulator works for direct + sea + sensitive.
     */
    public function test_kalkulator_direct_sea_sensitive(): void
    {
        $this->actingAs($this->customer);

        $feeService = new FeeCalculationService();
        $expected = $feeService->calculate(
            type: 'direct',
            method: 'sea',
            weight: 50,
            length: 100,
            width: 80,
            height: 60,
            isSensitive: true,
        );

        Livewire::test(Kalkulator::class)
            ->set('type', 'direct')
            ->set('method', 'sea')
            ->set('weight', '50')
            ->set('length', '100')
            ->set('width', '80')
            ->set('height', '60')
            ->set('isSensitive', true)
            ->call('calculate')
            ->assertSet('calculated', true)
            ->assertSet('result.grand_total', $expected['grand_total'])
            ->assertSet('result.rate_key', $expected['rate_key']);
    }

    /**
     * PRD §4.8: Validation errors on invalid input.
     */
    public function test_kalkulator_validates_required_fields(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(Kalkulator::class)
            ->set('weight', '')
            ->set('length', '')
            ->set('width', '')
            ->set('height', '')
            ->call('calculate')
            ->assertHasErrors(['weight', 'length', 'width', 'height']);
    }

    /**
     * PRD §4.8: Validation rejects non-numeric input.
     */
    public function test_kalkulator_validates_numeric(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(Kalkulator::class)
            ->set('weight', 'abc')
            ->set('length', 'xyz')
            ->set('width', '50')
            ->set('height', '30')
            ->call('calculate')
            ->assertHasErrors(['weight', 'length']);
    }

    /**
     * PRD §4.8: Reset clears all fields and result.
     */
    public function test_kalkulator_reset(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(Kalkulator::class)
            ->set('weight', '100')
            ->set('length', '50')
            ->set('width', '40')
            ->set('height', '30')
            ->call('calculate')
            ->assertSet('calculated', true)
            ->call('resetForm')
            ->assertSet('weight', '')
            ->assertSet('length', '')
            ->assertSet('width', '')
            ->assertSet('height', '')
            ->assertSet('calculated', false)
            ->assertSet('result', null);
    }

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
