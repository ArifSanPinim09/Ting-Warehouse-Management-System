<?php

namespace Tests\Unit\Models;

use App\Models\Box;
use App\Models\Complain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplainModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_complain_belongs_to_customer(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);
        $complain = Complain::factory()->create(['customer_id' => $user->id, 'box_id' => $box->id]);

        $this->assertInstanceOf(User::class, $complain->customer);
        $this->assertEquals($user->id, $complain->customer->id);
    }

    public function test_complain_belongs_to_box(): void
    {
        $box = Box::factory()->create();
        $complain = Complain::factory()->create(['box_id' => $box->id, 'customer_id' => $box->customer_id]);

        $this->assertInstanceOf(Box::class, $complain->box);
        $this->assertEquals($box->id, $complain->box->id);
    }

    public function test_status_constants(): void
    {
        $this->assertEquals('open', Complain::STATUS_OPEN);
        $this->assertEquals('in_review', Complain::STATUS_IN_REVIEW);
        $this->assertEquals('processing', Complain::STATUS_PROCESSING);
        $this->assertEquals('resolved', Complain::STATUS_RESOLVED);
    }

    public function test_get_valid_statuses(): void
    {
        $statuses = Complain::getValidStatuses();

        $this->assertCount(4, $statuses);
        $this->assertContains('open', $statuses);
        $this->assertContains('in_review', $statuses);
        $this->assertContains('processing', $statuses);
        $this->assertContains('resolved', $statuses);
    }
}
