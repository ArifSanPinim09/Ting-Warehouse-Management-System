<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\InvoiceIndex;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use App\Services\FeeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InvoicePayTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        // Create admin so notifyAdmins() has a target
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $box = Box::factory()->create(['customer_id' => $this->customer->id]);
        $this->invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ]);
    }

    /**
     * PRD §4.5: Customer can pay invoice
     */
    public function test_customer_can_pay_invoice(): void
    {
        Storage::fake('public');
        $this->actingAs($this->customer);

        Livewire::test(InvoiceIndex::class)
            ->call('openPayModal', $this->invoice->id)
            ->set('paymentMethod', 'transfer')
            ->set('paymentProof', UploadedFile::fake()->image('proof.jpg', 100, 100))
            ->call('submitPayment');

        $this->invoice->refresh();
        $this->assertEquals(Invoice::STATUS_WAITING_VERIFICATION, $this->invoice->status);
        $this->assertNotNull($this->invoice->payment_proof);
        $this->assertEquals('transfer', $this->invoice->payment_method);
    }

    /**
     * Already paid invoice rejected
     */
    public function test_already_paid_invoice_rejected(): void
    {
        Storage::fake('public');
        $this->invoice->update(['status' => Invoice::STATUS_VERIFIED]);
        $this->actingAs($this->customer);

        Livewire::test(InvoiceIndex::class)
            ->call('openPayModal', $this->invoice->id)
            ->set('paymentMethod', 'transfer')
            ->set('paymentProof', UploadedFile::fake()->image('proof.jpg', 100, 100))
            ->call('submitPayment');

        $this->invoice->refresh();
        $this->assertEquals(Invoice::STATUS_VERIFIED, $this->invoice->status);
    }

    /**
     * Payment proof validated (mimes)
     */
    public function test_payment_proof_validated(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(InvoiceIndex::class)
            ->call('openPayModal', $this->invoice->id)
            ->set('paymentMethod', 'transfer')
            ->set('paymentProof', UploadedFile::fake()->image('proof.gif', 100, 100))
            ->call('submitPayment')
            ->assertHasErrors(['paymentProof']);
    }
}
