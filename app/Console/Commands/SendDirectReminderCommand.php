<?php

namespace App\Console\Commands;

use App\Models\Box;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDirectReminderCommand extends Command
{
    protected $signature = 'app:send-direct-reminder';
    protected $description = 'Send reminder notification to direct box customers 5 days before 1-month deadline';

    public function handle(): int
    {
        // Find direct boxes OPEN for 25+ days (5 days before 1-month deadline)
        $cutoffDate = now()->subDays(25);

        $boxes = Box::where('type', 'direct')
            ->whereIn('status', [Box::STATUS_OPEN, Box::STATUS_REQUEST_TO_CLOSE])
            ->where('created_at', '<=', $cutoffDate)
            ->whereNull('reminder_sent_at')
            ->with('customer')
            ->get();

        $count = 0;

        foreach ($boxes as $box) {
            if (!$box->customer) {
                continue;
            }

            // Calculate days remaining
            $deadline = $box->created_at->addMonth();
            $daysRemaining = now()->diffInDays($deadline, false);

            if ($daysRemaining <= 5 && $daysRemaining >= 0) {
                // Send notification via database notification
                $box->customer->notify(new \App\Notifications\DirectBoxReminder($box, $daysRemaining));

                // Mark reminder as sent
                $box->update(['reminder_sent_at' => now()]);

                $count++;
                $this->info("Reminder sent to {$box->customer->email} for box {$box->display_name} ({$daysRemaining} days remaining)");
            }
        }

        $this->info("Done. {$count} reminder(s) sent.");
        return self::SUCCESS;
    }
}
