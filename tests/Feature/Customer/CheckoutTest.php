<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\CheckoutIndex;
use App\Models\Box;
use App\Models\Checkout;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $box = Box::factory()->create(['customer_id' => $this->customer->id]);
        $this->invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $box->id,
            'status' => Invoice::STATUS_VERIFIED,
        ]);
    }

    /**
     * PRD §4.6: Customer can request checkout
     */
    public function test_customer_can_request_checkout(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(CheckoutIndex::class)
            ->call('openForm')
            ->set('invoiceId', $this->invoice->id)
            ->set('addressType', 'personal')
            ->set('recipientName', 'John Doe')
            ->set('recipientPhone', '081234567890')
            ->set('address', 'Jl. Test No. 123, Jakarta Selatan')
            ->set('confirmation', true)
            ->call('submit');

        $this->assertDatabaseHas('checkouts', [
            'invoice_id' => $this->invoice->id,
            'customer_id' => $this->customer->id,
            'address_type' => 'personal',
            'recipient_name' => 'John Doe',
            'status' => 'request',
        ]);
    }

    /**
     * Unverified invoice rejected
     */
    public function test_unverified_invoice_rejected(): void
    {
        $this->invoice->update(['status' => Invoice::STATUS_WAITING_PAYMENT]);
        $this->actingAs($this->customer);

        Livewire::test(CheckoutIndex::class)
            ->call('openForm')
            ->set('invoiceId', $this->invoice->id)
            ->set('addressType', 'personal')
            ->set('recipientName', 'John Doe')
            ->set('recipientPhone', '081234567890')
            ->set('address', 'Jl. Test No. 123, Jakarta Selatan')
            ->set('confirmation', true)
            ->call('submit');

        $this->assertDatabaseMissing('checkouts', [
            'invoice_id' => $this->invoice->id,
        ]);
    }

    /**
     * Duplicate checkout for same invoice rejected
     */
    public function test_duplicate_checkout_rejected(): void
    {
        Checkout::factory()->create([
            'invoice_id' => $this->invoice->id,
            'customer_id' => $this->customer->id,
        ]);

        $this->actingAs($this->customer);

        Livewire::test(CheckoutIndex::class)
            ->call('openForm')
            ->set('invoiceId', $this->invoice->id)
            ->set('addressType', 'personal')
            ->set('recipientName', 'John Doe')
            ->set('recipientPhone', '081234567890')
            ->set('address', 'Jl. Test No. 123, Jakarta Selatan')
            ->set('confirmation', true)
            ->call('submit');

        $this->assertEquals(1, Checkout::where('invoice_id', $this->invoice->id)->count());
    }
}
