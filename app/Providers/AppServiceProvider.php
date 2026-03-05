<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SubscriptionService;
use App\Services\AttendanceService;
use App\Services\DevotionalService;
use App\Services\ProgressService;
use App\Services\ProgramEnrollmentService;
use App\Services\ReportExportService;
use App\Policies\DisciplePolicy;
use App\Policies\AttendancePolicy;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Services
        $this->app->singleton(AttendanceService::class);
        $this->app->singleton(DevotionalService::class);
        $this->app->singleton(ProgramEnrollmentService::class);
        $this->app->singleton(ReportExportService::class);

        // ProgressService depends on Attendance + Devotional services
        $this->app->singleton(ProgressService::class, fn($app) =>
            new ProgressService(
                $app->make(AttendanceService::class),
                $app->make(DevotionalService::class),
            )
        );
    }

    public function boot(): void
    {
        // Register Policies
        Gate::policy(User::class, DisciplePolicy::class);
    }
}
