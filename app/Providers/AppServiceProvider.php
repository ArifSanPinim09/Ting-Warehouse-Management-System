<?php

namespace App\Providers;

use App\Models\Box;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use App\Observers\BoxObserver;
use App\Observers\InvoiceObserver;
use App\Observers\SettingObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Eloquent Observers for audit trail (CLAUDE.md §3.3, PRD §1.2 poin 7)
        User::observe(UserObserver::class);
        Box::observe(BoxObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Setting::observe(SettingObserver::class);

        if ($this->app->environment('production') || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }
    }
}
