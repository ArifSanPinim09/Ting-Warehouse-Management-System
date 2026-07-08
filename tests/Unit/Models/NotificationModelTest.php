<?php

namespace Tests\Unit\Models;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationModelTest extends TestCase
{
    use RefreshDatabase;

    private function createNotification(array $overrides = []): Notification
    {
        $user = User::factory()->create();
        return Notification::create(array_merge([
            'id' => (string) Str::uuid(),
            'type' => 'test_notification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['title' => 'Test', 'message' => 'Test message'],
        ], $overrides));
    }

    public function test_data_is_cast_to_array(): void
    {
        $notif = $this->createNotification();

        $this->assertIsArray($notif->data);
        $this->assertEquals('Test', $notif->data['title']);
    }

    public function test_read_at_is_cast_to_datetime(): void
    {
        $notif = $this->createNotification(['read_at' => now()]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $notif->read_at);
    }

    public function test_is_read_returns_false_when_unread(): void
    {
        $notif = $this->createNotification();

        $this->assertFalse($notif->isRead());
    }

    public function test_is_read_returns_true_when_read(): void
    {
        $notif = $this->createNotification(['read_at' => now()]);

        $this->assertTrue($notif->isRead());
    }

    public function test_mark_as_read_sets_read_at(): void
    {
        $notif = $this->createNotification();

        $this->assertNull($notif->read_at);

        $notif->markAsRead();

        $this->assertNotNull($notif->fresh()->read_at);
    }

    public function test_scope_unread(): void
    {
        $this->createNotification(); // unread
        $this->createNotification(['read_at' => now()]); // read

        $this->assertEquals(1, Notification::unread()->count());
    }

    public function test_scope_read(): void
    {
        $this->createNotification(); // unread
        $this->createNotification(['read_at' => now()]); // read

        $this->assertEquals(1, Notification::read()->count());
    }
}
