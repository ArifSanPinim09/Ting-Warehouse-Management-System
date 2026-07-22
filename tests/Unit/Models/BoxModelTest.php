<?php

namespace Tests\Unit\Models;

use App\Models\Box;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoxModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_box_belongs_to_customer(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);

        $this->assertInstanceOf(User::class, $box->customer);
        $this->assertEquals($user->id, $box->customer->id);
    }

    public function test_box_has_many_items(): void
    {
        $box = Box::factory()->create();
        Item::factory()->count(3)->create(['box_id' => $box->id, 'customer_id' => $box->customer_id]);

        $this->assertCount(3, $box->items);
    }

    public function test_box_has_many_invoices(): void
    {
        $box = Box::factory()->create();
        Invoice::factory()->count(2)->create(['box_id' => $box->id, 'customer_id' => $box->customer_id]);

        $this->assertCount(2, $box->invoices);
    }

    public function test_box_has_many_complains(): void
    {
        $box = Box::factory()->create();
        Complain::factory()->count(2)->create(['box_id' => $box->id, 'customer_id' => $box->customer_id]);

        $this->assertCount(2, $box->complains);
    }

    public function test_status_constants(): void
    {
        $this->assertEquals('OPEN', Box::STATUS_OPEN);
        $this->assertEquals('SEND_TO_CARGO', Box::STATUS_SEND_TO_CARGO);
        $this->assertEquals('ARRIVED_INA', Box::STATUS_ARRIVED_INA);
        $this->assertEquals('INVOICE', Box::STATUS_INVOICE);
        $this->assertEquals('DONE', Box::STATUS_DONE);
    }

    public function test_get_valid_statuses(): void
    {
        $statuses = Box::getValidStatuses();

        $this->assertCount(15, $statuses);
        $this->assertContains('OPEN', $statuses);
        $this->assertContains('CLOSED', $statuses);
        $this->assertContains('LAST_CLAIM', $statuses);
        $this->assertContains('DONE', $statuses);
        $this->assertContains('REQUEST_TO_CLOSE', $statuses);
    }

    public function test_etd_eta_cast_to_date(): void
    {
        $box = Box::factory()->create([
            'etd' => '2026-08-01',
            'eta' => '2026-08-15',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $box->etd);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $box->eta);
    }
}
