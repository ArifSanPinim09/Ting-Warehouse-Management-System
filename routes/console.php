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
