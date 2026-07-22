<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\GenerateInvoice;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use App\Services\FeeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GenerateInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Box $box;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $this->box = Box::factory()->create([
            'customer_id' => $customer->id,
            'status' => Box::STATUS_ARRIVED_INA,
            'type' => 'sharing',
            'method' => 'air',
        ]);

        // Seed default rates
        $this->seedDefaultRates();
    }

    /**
     * PRD §4.10: Admin can generate invoice
     */
    public function test_admin_can_generate_invoice(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', 100)
            ->set('length', 50)
            ->set('width', 40)
            ->set('height', 30)
            ->set('addOn', 0)
            ->call('generateInvoice');

        $invoice = Invoice::where('box_id', $this->box->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(Invoice::STATUS_WAITING_PAYMENT, $invoice->status);
        $this->assertEquals($this->box->customer_id, $invoice->customer_id);
        $this->assertNotNull($invoice->invoice_number);

        // Box status updated
        $this->box->refresh();
        $this->assertEquals(Box::STATUS_INVOICE, $this->box->status);
    }

    /**
     * PRD §4.10: Invoice uses FeeCalculationService
     */
    public function test_invoice_uses_fee_calculation_service(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', 100)
            ->set('length', 50)
            ->set('width', 40)
            ->set('height', 30)
            ->set('addOn', 50000)
            ->call('generateInvoice');

        $invoice = Invoice::where('box_id', $this->box->id)->first();
        $this->assertNotNull($invoice);

        // Verify against FeeCalculationService directly
        $feeService = new FeeCalculationService();
        $expected = $feeService->calculate(
            type: 'sharing',
            method: 'air',
            weight: 100,
            length: 50,
            width: 40,
            height: 30,
            isSensitive: false,
            addOn: 50000,
        );

        $this->assertEquals($expected['fee_tax'], $invoice->fee_tax);
        $this->assertEquals($expected['fee_wh'], $invoice->fee_wh);
        $this->assertEquals($expected['fee_packing'], $invoice->fee_packing);
        $this->assertEquals($expected['grand_total'], $invoice->grand_total);
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
