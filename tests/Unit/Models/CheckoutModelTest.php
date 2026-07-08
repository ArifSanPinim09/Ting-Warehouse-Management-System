<?php

namespace Tests\Unit\Models;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_belongs_to_invoice(): void
    {
        $box = Box::factory()->create();
        $invoice = Invoice::factory()->create(['box_id' => $box->id, 'customer_id' => $box->customer_id]);
        $checkout = Checkout::factory()->create(['invoice_id' => $invoice->id, 'customer_id' => $invoice->customer_id]);

        $this->assertInstanceOf(Invoice::class, $checkout->invoice);
        $this->assertEquals($invoice->id, $checkout->invoice->id);
    }

    public function test_checkout_belongs_to_customer(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);
        $invoice = Invoice::factory()->create(['customer_id' => $user->id, 'box_id' => $box->id]);
        $checkout = Checkout::factory()->create(['customer_id' => $user->id, 'invoice_id' => $invoice->id]);

        $this->assertInstanceOf(User::class, $checkout->customer);
        $this->assertEquals($user->id, $checkout->customer->id);
    }

    public function test_status_constants(): void
    {
        $this->assertEquals('request', Checkout::STATUS_REQUEST);
        $this->assertEquals('on_process', Checkout::STATUS_ON_PROCESS);
        $this->assertEquals('sent', Checkout::STATUS_SENT);
    }

    public function test_get_valid_statuses(): void
    {
        $statuses = Checkout::getValidStatuses();

        $this->assertCount(3, $statuses);
        $this->assertContains('request', $statuses);
        $this->assertContains('on_process', $statuses);
        $this->assertContains('sent', $statuses);
    }
}
