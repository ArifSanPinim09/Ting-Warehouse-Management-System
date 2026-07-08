<?php

namespace Tests\Unit\Models;

use App\Models\Box;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_belongs_to_box(): void
    {
        $box = Box::factory()->create();
        $item = Item::factory()->create(['box_id' => $box->id, 'customer_id' => $box->customer_id]);

        $this->assertInstanceOf(Box::class, $item->box);
        $this->assertEquals($box->id, $item->box->id);
    }

    public function test_item_belongs_to_customer(): void
    {
        $user = User::factory()->create();
        $box = Box::factory()->create(['customer_id' => $user->id]);
        $item = Item::factory()->create(['customer_id' => $user->id, 'box_id' => $box->id]);

        $this->assertInstanceOf(User::class, $item->customer);
        $this->assertEquals($user->id, $item->customer->id);
    }

    public function test_boolean_casts(): void
    {
        $box = Box::factory()->create();
        $item = Item::factory()->create([
            'box_id' => $box->id,
            'customer_id' => $box->customer_id,
            'is_sensitive' => true,
            'arrived_china' => false,
            'arrived_indonesia' => false,
        ]);

        $this->assertIsBool($item->is_sensitive);
        $this->assertTrue($item->is_sensitive);
        $this->assertFalse($item->arrived_china);
    }
}
