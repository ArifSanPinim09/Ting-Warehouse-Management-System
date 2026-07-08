<?php

namespace Tests\Unit\Models;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // ─── Relationships ────────────────────────────────────────────

    public function test_user_has_many_boxes(): void
    {
        $user = User::factory()->create();
        Box::factory()->count(3)->create(['customer_id' => $user->id]);

        $this->assertCount(3, $user->boxes);
    }

    public function test_user_has_many_items(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);
        Item::factory()->count(2)->create(['customer_id' => $user->id, 'box_id' => $box->id]);

        $this->assertCount(2, $user->items);
    }

    public function test_user_has_many_invoices(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);
        Invoice::factory()->count(2)->create(['customer_id' => $user->id, 'box_id' => $box->id]);

        $this->assertCount(2, $user->invoices);
    }

    public function test_user_has_many_checkouts(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);
        $invoice = Invoice::factory()->create(['customer_id' => $user->id, 'box_id' => $box->id]);
        Checkout::factory()->count(2)->create(['customer_id' => $user->id, 'invoice_id' => $invoice->id]);

        $this->assertCount(2, $user->checkouts);
    }

    public function test_user_has_many_complains(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);
        Complain::factory()->count(2)->create(['customer_id' => $user->id, 'box_id' => $box->id]);

        $this->assertCount(2, $user->complains);
    }

    // ─── Role Checks ─────────────────────────────────────────────

    public function test_is_owner_returns_true_for_owner(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $this->assertTrue($user->isOwner());
    }

    public function test_is_owner_returns_false_for_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->assertFalse($user->isOwner());
    }

    public function test_is_owner_returns_false_for_customer(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->assertFalse($user->isOwner());
    }

    public function test_is_admin_returns_true_for_owner(): void
    {
        $user = User::factory()->create(['role' => 'owner']);
        $this->assertTrue($user->isAdmin());
    }

    public function test_is_admin_returns_true_for_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->assertTrue($user->isAdmin());
    }

    public function test_is_admin_returns_false_for_customer(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->assertFalse($user->isAdmin());
    }

    public function test_is_customer_returns_true_for_customer(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->assertTrue($user->isCustomer());
    }

    public function test_is_customer_returns_false_for_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->assertFalse($user->isCustomer());
    }

    // ─── Status Constants ────────────────────────────────────────

    public function test_status_constants(): void
    {
        $this->assertEquals('pending', User::STATUS_PENDING);
        $this->assertEquals('active', User::STATUS_ACTIVE);
        $this->assertEquals('inactive', User::STATUS_INACTIVE);
    }
}
