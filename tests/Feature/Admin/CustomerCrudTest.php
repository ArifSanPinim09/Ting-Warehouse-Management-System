<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\CustomerIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    /** @test */
    public function admin_can_open_edit_modal(): void
    {
        $this->actingAs($this->admin);

        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '08123456789',
        ]);

        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $customer->id)
            ->call('openEditModal')
            ->assertSet('showEditModal', true)
            ->assertSet('editName', 'Test Customer')
            ->assertSet('editEmail', 'test@example.com')
            ->assertSet('editPhone', '08123456789');
    }

    /** @test */
    public function admin_can_edit_customer(): void
    {
        $this->actingAs($this->admin);

        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
            'name' => 'Old Name',
        ]);

        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $customer->id)
            ->call('openEditModal')
            ->set('editName', 'New Name')
            ->set('editEmail', 'new@example.com')
            ->set('editPhone', '08999999999')
            ->call('saveCustomer');

        $customer->refresh();
        $this->assertEquals('New Name', $customer->name);
        $this->assertEquals('new@example.com', $customer->email);
        $this->assertEquals('08999999999', $customer->phone);
    }

    /** @test */
    public function edit_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $customer->id)
            ->call('openEditModal')
            ->set('editName', '')
            ->set('editEmail', '')
            ->call('saveCustomer')
            ->assertHasErrors(['editName', 'editEmail']);
    }

    /** @test */
    public function edit_validates_unique_email(): void
    {
        $this->actingAs($this->admin);

        $other = User::factory()->create(['role' => 'customer', 'email' => 'taken@example.com']);
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $customer->id)
            ->call('openEditModal')
            ->set('editEmail', 'taken@example.com')
            ->call('saveCustomer')
            ->assertHasErrors(['editEmail']);
    }

    /** @test */
    public function admin_can_delete_customer_without_active_data(): void
    {
        $this->actingAs($this->admin);

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $customer->id)
            ->call('openDeleteConfirm')
            ->assertSet('showDeleteConfirm', true)
            ->call('deleteCustomer');

        $this->assertDatabaseMissing('users', ['id' => $customer->id]);
    }

    /** @test */
    public function admin_cannot_delete_customer_with_active_boxes(): void
    {
        $this->actingAs($this->admin);

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        // Create an active box for this customer
        \App\Models\Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'customer_id' => $customer->id,
            'status' => 'OPEN',
        ]);

        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $customer->id)
            ->call('openDeleteConfirm')
            ->call('deleteCustomer');

        // Customer should still exist
        $this->assertDatabaseHas('users', ['id' => $customer->id]);
    }

    /** @test */
    public function admin_can_change_customer_status_via_edit(): void
    {
        $this->actingAs($this->admin);

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $customer->id)
            ->call('openEditModal')
            ->set('editStatus', 'inactive')
            ->call('saveCustomer');

        $customer->refresh();
        $this->assertEquals('inactive', $customer->status);
    }

    /** @test */
    public function admin_can_edit_customer_address(): void
    {
        $this->actingAs($this->admin);

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $customer->id)
            ->call('openEditModal')
            ->set('editAddress', 'Jl. Sudirman No. 123, Jakarta')
            ->call('saveCustomer');

        $customer->refresh();
        $this->assertEquals('Jl. Sudirman No. 123, Jakarta', $customer->address);
    }
}
