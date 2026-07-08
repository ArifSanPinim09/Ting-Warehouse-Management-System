<?php

namespace Tests\Feature\Owner;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private function createOwner(): User
    {
        return User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);
    }

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
    }

    public function test_owner_can_access_audit_log(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        $response = $this->get('/owner/audit-log');

        $response->assertOk();
    }

    public function test_admin_cannot_access_audit_log(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);

        $response = $this->get('/owner/audit-log');

        $response->assertForbidden();
    }

    public function test_audit_log_shows_empty_state(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\AuditLogIndex::class)
            ->assertSee('Belum ada log aktivitas');
    }

    public function test_audit_log_displays_entries(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        ActivityLog::create([
            'user_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'event' => 'updated',
            'old_values' => ['status' => 'active'],
            'new_values' => ['status' => 'inactive'],
        ]);

        Livewire::test(\App\Livewire\AuditLogIndex::class)
            ->assertSee($owner->name)
            ->assertSee('updated')
            ->assertSee('User');
    }

    public function test_audit_log_search_filter(): void
    {
        $owner = $this->createOwner();
        $customer = User::factory()->create(['name' => 'JohnDoe', 'role' => 'customer']);

        $this->actingAs($owner);

        ActivityLog::create([
            'user_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'event' => 'created',
        ]);

        ActivityLog::create([
            'user_id' => $customer->id,
            'subject_type' => User::class,
            'subject_id' => $customer->id,
            'event' => 'updated',
        ]);

        Livewire::test(\App\Livewire\AuditLogIndex::class)
            ->set('search', 'JohnDoe')
            ->assertSee('JohnDoe');
    }

    public function test_audit_log_event_filter(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        ActivityLog::create([
            'user_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'event' => 'created',
        ]);

        ActivityLog::create([
            'user_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'event' => 'deleted',
        ]);

        Livewire::test(\App\Livewire\AuditLogIndex::class)
            ->set('filterEvent', 'created')
            ->assertSee('created');
    }

    public function test_audit_log_reset_filters(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\AuditLogIndex::class)
            ->set('search', 'test')
            ->set('filterEvent', 'created')
            ->set('filterSubject', User::class)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('filterEvent', '')
            ->assertSet('filterSubject', '');
    }

    public function test_audit_log_detail_panel(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        $log = ActivityLog::create([
            'user_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'event' => 'updated',
            'old_values' => ['status' => 'active'],
            'new_values' => ['status' => 'inactive'],
        ]);

        Livewire::test(\App\Livewire\AuditLogIndex::class)
            ->call('selectLog', $log->id)
            ->assertSee('Detail Log')
            ->assertSee($owner->name)
            ->assertSee('updated');
    }

    public function test_audit_log_close_detail(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\AuditLogIndex::class)
            ->call('closeDetail')
            ->assertSet('selectedLogId', null);
    }
}
