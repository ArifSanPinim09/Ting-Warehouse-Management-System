<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\VerificationIndex;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $box = Box::factory()->create(['customer_id' => $this->customer->id]);
        $this->invoice = Invoice::factory()->create([
            'customer_id' => $this->customer->id,
            'box_id' => $box->id,
            'status' => Invoice::STATUS_WAITING_VERIFICATION,
            'payment_method' => 'transfer',
            'payment_proof' => 'payment-proof/test.jpg',
        ]);
    }

    /**
     * PRD §4.11: Admin can verify payment
     */
    public function test_admin_can_verify_payment(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(VerificationIndex::class)
            ->call('selectInvoice', $this->invoice->id)
            ->call('verifyPayment');

        $this->invoice->refresh();
        $this->assertEquals(Invoice::STATUS_VERIFIED, $this->invoice->status);
    }

    /**
     * PRD §4.11: Admin can reject payment with reason
     */
    public function test_admin_can_reject_payment(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(VerificationIndex::class)
            ->call('selectInvoice', $this->invoice->id)
            ->call('openRejectModal')
            ->set('rejectReason', 'Bukti transfer tidak jelas, silakan upload ulang')
            ->call('rejectPayment');

        $this->invoice->refresh();
        $this->assertEquals(Invoice::STATUS_WAITING_PAYMENT, $this->invoice->status);
        $this->assertNull($this->invoice->payment_proof);
        $this->assertNull($this->invoice->payment_method);
    }
}
