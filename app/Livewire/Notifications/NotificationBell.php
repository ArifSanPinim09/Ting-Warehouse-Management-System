<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Notification Bell — shows unread count + dropdown with notification list.
 *
 * PRD §8.4: "Notifikasi | Kanan | Notifikasi terbaru"
 * PRD §8.11: "Notifikasi | Verification, Checkout, Complain requests"
 * PRD §8.15: "Notifikasi | Deadline, komplain baru"
 * PRD §16: Empty state: "Tidak ada notifikasi"
 * PRD §17: Loading state: "Notifikasi (3 skeleton row)"
 */
class NotificationBell extends Component
{
    /** @var int Unread notification count */
    public int $unreadCount = 0;

    /** @var bool Whether the dropdown is open */
    public bool $showDropdown = false;

    /** @var bool Whether we're loading notifications */
    public bool $loading = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->loadUnreadCount();
    }

    /**
     * Load unread notification count.
     */
    public function loadUnreadCount(): void
    {
        $this->unreadCount = Notification::where('user_id', auth()->id())
            ->unread()
            ->count();
    }

    /**
     * Toggle dropdown visibility.
     */
    public function toggleDropdown(): void
    {
        $this->showDropdown = ! $this->showDropdown;

        if ($this->showDropdown) {
            $this->loadUnreadCount();
        }
    }

    /**
     * Close dropdown.
     */
    public function closeDropdown(): void
    {
        $this->showDropdown = false;
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $notificationId)
            ->first();

        if ($notification && ! $notification->isRead()) {
            $notification->markAsRead();
            $this->loadUnreadCount();
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

        $this->loadUnreadCount();
    }

    /**
     * Refresh unread count (can be called from other components).
     */
    #[On('notification-received')]
    public function refreshUnread(): void
    {
        $this->loadUnreadCount();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(15)
            ->get();

        return view('livewire.notifications.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
