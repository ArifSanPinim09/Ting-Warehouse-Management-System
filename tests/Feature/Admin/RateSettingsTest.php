<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\SettingsIndex;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RateSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);

        // Seed default rates
        Setting::create(['key' => 'kurs_yuan_idr', 'value' => '2460', 'group' => 'currency']);
        Setting::create(['key' => 'rate_sharing_air_berat', 'value' => '255', 'group' => 'rate_sharing']);
        Setting::create(['key' => 'fee_packing_150', 'value' => '5000', 'group' => 'fee_packing']);
    }

    /**
     * PRD §4.12: Admin can update rates
     */
    public function test_admin_can_update_rates(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SettingsIndex::class)
            ->set('kurs_yuan_idr', '2500')
            ->call('saveCurrency');

        $this->assertDatabaseHas('settings', [
            'key' => 'kurs_yuan_idr',
            'value' => '2500',
        ]);
    }

    /**
     * PRD §4.12: Rate change does NOT affect existing invoices (snapshot)
     */
    public function test_rate_change_does_not_affect_existing_invoices(): void
    {
        // Create an invoice with old rates
        $customer = User::factory()->create(['role' => 'customer']);
        $box = \App\Models\Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'fee_tax' => 1000000,
            'fee_wh' => 5000,
            'fee_packing' => 5000,
            'grand_total' => 1010000,
        ]);

        // Update rate
        $this->actingAs($this->admin);
        Livewire::test(SettingsIndex::class)
            ->set('kurs_yuan_idr', '3000')
            ->call('saveCurrency');

        // Invoice values unchanged (snapshot)
        $invoice->refresh();
        $this->assertEquals(1000000, $invoice->fee_tax);
        $this->assertEquals(5000, $invoice->fee_wh);
        $this->assertEquals(1010000, $invoice->grand_total);
    }
}
