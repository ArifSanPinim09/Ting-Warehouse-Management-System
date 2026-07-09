<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Notification Index — List all notifications for the current user.
 *
 * Revisi §2.11.3, §4.3: Halaman /notifications — daftar semua notifikasi.
 */
#[Layout('layouts.app')]
#[Title('Notifikasi — Ting Warehouse')]
class NotificationIndex extends Component
{
    use WithPagination;

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $notificationId)
            ->first();

        if ($notification && !$notification->isRead()) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->dispatch('notification-read');
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        $unreadCount = Notification::where('user_id', auth()->id())
            ->unread()
            ->count();

        return view('livewire.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
