<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Incident;
use App\Models\User;
use App\Models\Category;
use App\Models\Zone;
use App\Models\ZoneAlert;
use App\Observers\AuditLogObserver;

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
        // Register AuditLogObserver for critical models
        Incident::observe(AuditLogObserver::class);
        User::observe(AuditLogObserver::class);
        Category::observe(AuditLogObserver::class);
        Zone::observe(AuditLogObserver::class);
        ZoneAlert::observe(AuditLogObserver::class);
    }
}
