<?php

namespace Tests\Feature\Notification;

use App\Livewire\Notifications\NotificationBell;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationBellTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
    }

    private function createNotification(bool $read = false): Notification
    {
        return Notification::create([
            'id' => (string) Str::uuid(),
            'type' => 'test_notification',
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'data' => ['title' => 'Test Notif', 'message' => 'Test message'],
            'read_at' => $read ? now() : null,
        ]);
    }

    /**
     * Mark single notification as read
     */
    public function test_mark_as_read(): void
    {
        $notif = $this->createNotification();
        $this->actingAs($this->user);

        Livewire::test(NotificationBell::class)
            ->call('markAsRead', $notif->id);

        $notif->refresh();
        $this->assertNotNull($notif->read_at);
    }

    /**
     * Mark all notifications as read
     */
    public function test_mark_all_as_read(): void
    {
        $this->createNotification();
        $this->createNotification();
        $this->createNotification(read: true); // already read
        $this->actingAs($this->user);

        Livewire::test(NotificationBell::class)
            ->call('markAllAsRead');

        $this->assertEquals(0, Notification::where('notifiable_id', $this->user->id)->unread()->count());
    }

    /**
     * Unread count displayed correctly
     */
    public function test_unread_count(): void
    {
        $this->createNotification();
        $this->createNotification();
        $this->createNotification(read: true);
        $this->actingAs($this->user);

        Livewire::test(NotificationBell::class)
            ->assertSee('2');
    }
}
