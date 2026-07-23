<?php

use App\Console\Commands\CheckDeadlinesCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Revisi §2.10.5: Check deadlines daily at 08:00
Schedule::command(CheckDeadlinesCommand::class)->dailyAt('08:00');

// REV-04.7: Send direct box reminder daily at 09:00
Schedule::command(\App\Console\Commands\SendDirectReminderCommand::class)->dailyAt('09:00');

// BUG-006/007: Process denda (Rp5.000/day after 5 days) and notuan lelang (15 days) daily at 00:30
Schedule::command(\App\Console\Commands\ProcessDendaAndLelang::class)->dailyAt('00:30');
