<?php

namespace Tests\Unit\Models;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $log = ActivityLog::create([
            'user_id' => $user->id,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'event' => 'created',
        ]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }

    public function test_old_new_values_cast_to_array(): void
    {
        $user = User::factory()->create();
        $log = ActivityLog::create([
            'user_id' => $user->id,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'event' => 'updated',
            'old_values' => ['status' => 'active'],
            'new_values' => ['status' => 'inactive'],
        ]);

        $this->assertIsArray($log->old_values);
        $this->assertIsArray($log->new_values);
        $this->assertEquals('active', $log->old_values['status']);
        $this->assertEquals('inactive', $log->new_values['status']);
    }

    public function test_scope_for_event(): void
    {
        $user = User::factory()->create();
        ActivityLog::create(['user_id' => $user->id, 'subject_type' => User::class, 'subject_id' => 1, 'event' => 'created']);
        ActivityLog::create(['user_id' => $user->id, 'subject_type' => User::class, 'subject_id' => 2, 'event' => 'updated']);

        $this->assertEquals(1, ActivityLog::forEvent('created')->count());
    }

    public function test_scope_for_subject_type(): void
    {
        $user = User::factory()->create();
        ActivityLog::create(['user_id' => $user->id, 'subject_type' => User::class, 'subject_id' => 1, 'event' => 'created']);

        $this->assertEquals(1, ActivityLog::forSubjectType(User::class)->count());
    }
}
