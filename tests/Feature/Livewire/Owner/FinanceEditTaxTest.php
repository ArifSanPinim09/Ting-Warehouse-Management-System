<?php

namespace Tests\Feature\Livewire\Owner;

use App\Livewire\Owner\FinanceIndex;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceEditTaxTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'role' => 'owner',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $this->invoice = Invoice::factory()->create([
            'invoice_number' => 'INV-TEST-001',
            'customer_id' => $customer->id,
            'fee_tax' => 5000,
            'fee_wh' => 3000,
            'fee_packing' => 2000,
            'add_on' => 0,
            'denda_total' => 0,
            'grand_total' => 10000,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ]);
    }

    public function test_owner_can_see_edit_tax_button(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(FinanceIndex::class)
            ->assertSee('Edit Fee Tax');
    }

    public function test_owner_can_open_edit_tax_modal(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(FinanceIndex::class)
            ->call('openEditTax', $this->invoice->id)
            ->assertSet('showEditTax', true)
            ->assertSet('editInvoiceId', $this->invoice->id)
            ->assertSet('editFeeTax', (string) number_format($this->invoice->fee_tax, 2, '.', ''))
            ->assertSee('Edit Fee Tax');
    }

    public function test_owner_can_save_new_fee_tax_and_grand_total_recalculates(): void
    {
        $this->actingAs($this->owner);

        $newTax = 9999;
        $expectedGrandTotal = $newTax + 3000 + 2000 + 0 + 0; // fee_wh + fee_packing + add_on + denda

        Livewire::test(FinanceIndex::class)
            ->call('openEditTax', $this->invoice->id)
            ->set('editFeeTax', $newTax)
            ->call('saveTax')
            ->assertSet('showEditTax', false)
            ->assertSet('editInvoiceId', null);

        $this->invoice->refresh();
        $this->assertEquals($newTax, (float) $this->invoice->fee_tax);
        $this->assertEquals($expectedGrandTotal, (float) $this->invoice->grand_total);

        // Verify audit log
        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => 'App\\Models\\Invoice',
            'subject_id' => $this->invoice->id,
            'event' => 'updated',
        ]);
    }

    public function test_fee_tax_validation_rejects_negative_values(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(FinanceIndex::class)
            ->call('openEditTax', $this->invoice->id)
            ->set('editFeeTax', -100)
            ->call('saveTax')
            ->assertHasErrors(['editFeeTax']);
    }

    public function test_fee_tax_validation_rejects_non_numeric_values(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(FinanceIndex::class)
            ->call('openEditTax', $this->invoice->id)
            ->set('editFeeTax', 'abc')
            ->call('saveTax')
            ->assertHasErrors(['editFeeTax']);
    }

    public function test_non_owner_cannot_access_finance_page(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($customer);

        $this->get('/owner/finance')->assertForbidden();
    }
}
