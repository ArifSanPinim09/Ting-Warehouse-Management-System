<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\KursHistoryIndex;
use App\Livewire\Admin\SettingsIndex;
use App\Models\Invoice;
use App\Models\KursHistory;
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

        // Seed default rates (no longer kurs_yuan_idr — that's now in kurs_history)
        Setting::create(['key' => 'rate_sharing_air_berat', 'value' => '255', 'group' => 'rate_sharing']);
        Setting::create(['key' => 'fee_packing_150', 'value' => '5000', 'group' => 'fee_packing']);
    }

    /**
     * Revisi §2.2: Admin can input kurs via history page
     */
    public function test_admin_can_input_kurs_via_history_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(KursHistoryIndex::class)
            ->set('kurs_value', '2660')
            ->set('effective_date', '2026-07-09')
            ->call('saveKurs');

        $this->assertDatabaseHas('kurs_history', [
            'input_by' => $this->admin->id,
        ]);
        $kurs = KursHistory::where('input_by', $this->admin->id)->first();
        $this->assertNotNull($kurs);
        $this->assertEqualsWithDelta(2660.0, (float) $kurs->kurs_value, 0.01);
        $this->assertEquals('2026-07-09', $kurs->effective_date->format('Y-m-d'));
    }

    /**
     * Revisi §8.1: Duplicate (kurs_value, effective_date) rejected
     */
    public function test_duplicate_kurs_date_rejected(): void
    {
        $this->actingAs($this->admin);

        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-07-09',
            'input_by' => $this->admin->id,
        ]);

        Livewire::test(KursHistoryIndex::class)
            ->set('kurs_value', '2660')
            ->set('effective_date', '2026-07-09')
            ->call('saveKurs')
            ->assertDispatched('toast');

        // Should not create a duplicate
        $this->assertEquals(1, KursHistory::where('kurs_value', 2660)->whereDate('effective_date', '2026-07-09')->count());
    }

    /**
     * Revisi §2.2: Same kurs value on different date is allowed
     */
    public function test_same_kurs_value_different_date_allowed(): void
    {
        $this->actingAs($this->admin);

        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-07-01',
            'input_by' => $this->admin->id,
        ]);

        Livewire::test(KursHistoryIndex::class)
            ->set('kurs_value', '2660')
            ->set('effective_date', '2026-07-09')
            ->call('saveKurs');

        $this->assertEquals(2, KursHistory::where('kurs_value', 2660)->count());
    }

    /**
     * Revisi §7.2: Validation — kurs_value required
     */
    public function test_kurs_value_required(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(KursHistoryIndex::class)
            ->set('kurs_value', '')
            ->set('effective_date', '2026-07-09')
            ->call('saveKurs')
            ->assertHasErrors(['kurs_value']);
    }

    /**
     * Revisi §7.2: Validation — effective_date required
     */
    public function test_effective_date_required(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(KursHistoryIndex::class)
            ->set('kurs_value', '2660')
            ->set('effective_date', '')
            ->call('saveKurs')
            ->assertHasErrors(['effective_date']);
    }

    /**
     * Revisi §7.2: Validation — effective_date cannot be in the future
     */
    public function test_effective_date_cannot_be_future(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(KursHistoryIndex::class)
            ->set('kurs_value', '2660')
            ->set('effective_date', now()->addDays(5)->format('Y-m-d'))
            ->call('saveKurs')
            ->assertHasErrors(['effective_date']);
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

        // Update sharing rate
        $this->actingAs($this->admin);
        Livewire::test(SettingsIndex::class)
            ->set('rate_sharing_air_berat', '300')
            ->call('confirmSave', 'sharing')
            ->call('saveSharingRates');

        // Invoice values unchanged (snapshot)
        $invoice->refresh();
        $this->assertEquals(1000000, $invoice->fee_tax);
        $this->assertEquals(5000, $invoice->fee_wh);
        $this->assertEquals(1010000, $invoice->grand_total);
    }

    /**
     * Revisi §7.2: Validation — kurs_value must be numeric
     */
    public function test_kurs_value_must_be_numeric(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(KursHistoryIndex::class)
            ->set('kurs_value', 'abc')
            ->set('effective_date', '2026-07-09')
            ->call('saveKurs')
            ->assertHasErrors(['kurs_value']);
    }

    /**
     * KursHistoryIndex renders successfully for admin
     */
    public function test_kurs_history_page_renders(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(KursHistoryIndex::class)
            ->assertStatus(200)
            ->assertSee('History Kurs');
    }

    /**
     * Customer cannot access kurs history page
     */
    public function test_customer_cannot_access_kurs_history(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);

        $this->actingAs($customer);

        $this->get('/admin/kurs-history')->assertForbidden();
    }
}
