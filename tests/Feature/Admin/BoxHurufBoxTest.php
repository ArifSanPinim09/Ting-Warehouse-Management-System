<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ManageBox;
use App\Models\Box;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BoxHurufBoxTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    /** @test */
    public function huruf_box_is_fillable_on_box_model(): void
    {
        $box = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'batch_name' => '126',
            'huruf_box' => 'H',
            'status' => 'OPEN',
        ]);

        $this->assertEquals('H', $box->huruf_box);
        $this->assertEquals('126', $box->batch_name);
    }

    /** @test */
    public function box_code_attribute_returns_batch_plus_huruf(): void
    {
        $box = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'batch_name' => '126',
            'huruf_box' => 'H',
            'status' => 'OPEN',
        ]);

        $this->assertEquals('126-H', $box->box_code);
    }

    /** @test */
    public function box_code_returns_batch_only_when_no_huruf(): void
    {
        $box = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'batch_name' => '126',
            'status' => 'OPEN',
        ]);

        $this->assertEquals('126', $box->box_code);
    }

    /** @test */
    public function box_code_returns_box_id_when_no_batch(): void
    {
        $box = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'status' => 'OPEN',
        ]);

        $this->assertEquals('Box #' . $box->id, $box->box_code);
    }

    /** @test */
    public function display_name_prefers_tracking_number(): void
    {
        $box = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'tracking_number' => 'JNT123456',
            'batch_name' => '126',
            'huruf_box' => 'H',
            'status' => 'OPEN',
        ]);

        $this->assertEquals('JNT123456', $box->display_name);
    }

    /** @test */
    public function display_name_uses_batch_plus_huruf_when_no_tracking(): void
    {
        $box = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'batch_name' => '126',
            'huruf_box' => 'H',
            'status' => 'OPEN',
        ]);

        $this->assertEquals('126-H', $box->display_name);
    }

    /** @test */
    public function admin_can_create_box_with_huruf_box(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ManageBox::class)
            ->set('newType', 'sharing')
            ->set('newMethod', 'air')
            ->set('newBatchName', '200')
            ->set('newHurufBox', 'A')
            ->call('createBox');

        $box = Box::latest()->first();
        $this->assertEquals('200', $box->batch_name);
        $this->assertEquals('A', $box->huruf_box);
    }

    /** @test */
    public function admin_can_edit_huruf_box(): void
    {
        $this->actingAs($this->admin);

        $box = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'batch_name' => '126',
            'huruf_box' => 'H',
            'status' => 'OPEN',
        ]);

        Livewire::test(ManageBox::class)
            ->call('selectBox', $box->id)
            ->call('openEditModal')
            ->assertSet('editHurufBox', 'H')
            ->set('editHurufBox', 'B')
            ->call('saveBoxEdit');

        $box->refresh();
        $this->assertEquals('B', $box->huruf_box);
    }

    /** @test */
    public function huruf_box_is_searchable(): void
    {
        $this->actingAs($this->admin);

        Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'batch_name' => '126',
            'huruf_box' => 'X',
            'status' => 'OPEN',
        ]);

        Livewire::test(ManageBox::class)
            ->set('search', 'X')
            ->assertDontSee('Tidak ada box')
            ->assertSee('126-X');
    }

    /** @test */
    public function huruf_box_validates_max_length(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ManageBox::class)
            ->set('newType', 'sharing')
            ->set('newMethod', 'air')
            ->set('newHurufBox', str_repeat('A', 11)) // exceeds 10
            ->call('createBox')
            ->assertHasErrors(['newHurufBox']);
    }
}
