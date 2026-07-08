<?php

namespace Tests\Feature\Owner;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageAdminTest extends TestCase
{
    use RefreshDatabase;

    private function createOwner(): User
    {
        return User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);
    }

    private function createAdmin(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'status' => User::STATUS_ACTIVE,
        ], $overrides));
    }

    public function test_owner_can_access_manage_admin(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        $response = $this->get('/owner/manage-admin');

        $response->assertOk();
    }

    public function test_admin_cannot_access_manage_admin(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);

        $response = $this->get('/owner/manage-admin');

        $response->assertForbidden();
    }

    public function test_manage_admin_shows_empty_state(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->assertSee('Tidak ada admin ditemukan');
    }

    public function test_manage_admin_lists_admins(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin(['name' => 'Admin Satu', 'email' => 'admin1@test.com']);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->assertSee('Admin Satu')
            ->assertSee('admin1@test.com');
    }

    public function test_manage_admin_search_filter(): void
    {
        $owner = $this->createOwner();
        $admin1 = $this->createAdmin(['name' => 'Admin Alpha']);
        $admin2 = $this->createAdmin(['name' => 'Admin Beta']);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->set('search', 'Alpha')
            ->assertSee('Admin Alpha');
    }

    public function test_manage_admin_status_filter(): void
    {
        $owner = $this->createOwner();
        $activeAdmin = $this->createAdmin(['name' => 'Active Admin', 'status' => User::STATUS_ACTIVE]);
        $inactiveAdmin = $this->createAdmin(['name' => 'Inactive Admin', 'status' => User::STATUS_INACTIVE]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->set('filterStatus', 'inactive')
            ->assertSee('Inactive Admin');
    }

    public function test_manage_admin_activate_admin(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin(['status' => User::STATUS_INACTIVE]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->call('confirmActivate', $admin->id)
            ->assertSet('showConfirm', true)
            ->assertSet('confirmAction', 'activate')
            ->assertSee('Aktifkan Admin')
            ->call('executeConfirm');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_manage_admin_deactivate_admin(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin(['status' => User::STATUS_ACTIVE]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->call('confirmDeactivate', $admin->id)
            ->assertSet('showConfirm', true)
            ->assertSet('confirmAction', 'deactivate')
            ->assertSee('Nonaktifkan Admin')
            ->call('executeConfirm');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'status' => User::STATUS_INACTIVE,
        ]);
    }

    public function test_manage_admin_activate_creates_audit_log(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin(['status' => User::STATUS_INACTIVE]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->call('confirmActivate', $admin->id)
            ->call('executeConfirm');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => 'activated',
        ]);
    }

    public function test_manage_admin_deactivate_creates_audit_log(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin(['status' => User::STATUS_ACTIVE]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->call('confirmDeactivate', $admin->id)
            ->call('executeConfirm');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => 'deactivated',
        ]);
    }

    public function test_manage_admin_cancel_confirm(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin(['status' => User::STATUS_INACTIVE]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->call('confirmActivate', $admin->id)
            ->assertSet('showConfirm', true)
            ->call('cancelConfirm')
            ->assertSet('showConfirm', false)
            ->assertSet('confirmAction', '');

        // Admin should NOT be activated
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'status' => User::STATUS_INACTIVE,
        ]);
    }

    public function test_manage_admin_detail_panel(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin(['name' => 'Detail Admin', 'phone' => '081234567890']);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->call('selectAdmin', $admin->id)
            ->assertSet('showDetail', true)
            ->assertSee('Detail Admin')
            ->assertSee('081234567890');
    }

    public function test_manage_admin_close_detail(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->call('closeDetail')
            ->assertSet('showDetail', false)
            ->assertSet('selectedId', null);
    }

    public function test_manage_admin_shows_activity_history(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin();

        $this->actingAs($owner);

        ActivityLog::create([
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => 'updated',
            'old_values' => ['status' => 'active'],
            'new_values' => ['status' => 'inactive'],
        ]);

        Livewire::test(\App\Livewire\Owner\ManageAdminIndex::class)
            ->call('selectAdmin', $admin->id)
            ->assertSee('Aktivitas Terbaru')
            ->assertSee('updated');
    }
}
