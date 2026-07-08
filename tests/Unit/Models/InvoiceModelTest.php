<?php

namespace Tests\Unit\Models;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_belongs_to_box(): void
    {
        $box = Box::factory()->create();
        $invoice = Invoice::factory()->create(['box_id' => $box->id, 'customer_id' => $box->customer_id]);

        $this->assertInstanceOf(Box::class, $invoice->box);
        $this->assertEquals($box->id, $invoice->box->id);
    }

    public function test_invoice_belongs_to_customer(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);
        $invoice = Invoice::factory()->create(['customer_id' => $user->id, 'box_id' => $box->id]);

        $this->assertInstanceOf(User::class, $invoice->customer);
        $this->assertEquals($user->id, $invoice->customer->id);
    }

    public function test_invoice_has_many_checkouts(): void
    {
        $box = Box::factory()->create();
        $invoice = Invoice::factory()->create(['box_id' => $box->id, 'customer_id' => $box->customer_id]);
        Checkout::factory()->count(2)->create(['invoice_id' => $invoice->id, 'customer_id' => $invoice->customer_id]);

        $this->assertCount(2, $invoice->checkouts);
    }

    public function test_decimal_casts(): void
    {
        $box = Box::factory()->create();
        $invoice = Invoice::factory()->create([
            'box_id' => $box->id,
            'customer_id' => $box->customer_id,
            'weight' => 123.45,
            'volume' => 67.89,
            'fee_tax' => 1000000.50,
            'grand_total' => 1500000.75,
        ]);

        $invoice->refresh();
        $this->assertIsString($invoice->weight); // decimal cast returns string
        $this->assertEquals('123.45', $invoice->weight);
        $this->assertEquals('67.89', $invoice->volume);
    }

    public function test_status_constants(): void
    {
        $this->assertEquals('waiting_payment', Invoice::STATUS_WAITING_PAYMENT);
        $this->assertEquals('waiting_verification', Invoice::STATUS_WAITING_VERIFICATION);
        $this->assertEquals('verified', Invoice::STATUS_VERIFIED);
    }

    public function test_get_valid_statuses(): void
    {
        $statuses = Invoice::getValidStatuses();

        $this->assertCount(3, $statuses);
        $this->assertContains('waiting_payment', $statuses);
        $this->assertContains('verified', $statuses);
    }
}
