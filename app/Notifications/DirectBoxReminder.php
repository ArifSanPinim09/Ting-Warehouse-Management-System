<?php

namespace App\Notifications;

use App\Models\Box;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DirectBoxReminder extends Notification
{
    use Queueable;

    public function __construct(
        public Box $box,
        public int $daysRemaining,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Reminder: Direct Batch Hampir Expired',
            'message' => "Batch \"{$this->box->display_name}\" akan otomatis di-close dalam {$this->daysRemaining} hari lagi. Silakan proses pengiriman Anda sebelum batas waktu.",
            'box_id' => $this->box->id,
            'days_remaining' => $this->daysRemaining,
            'action_url' => '/box/direct',
        ];
    }
}
