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
use App\Policies\DevotionalPolicy;
use App\Policies\SupportTicketPolicy;
use App\Models\User;
use App\Models\Devotional;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Gate;
use App\Services\TenantDatabaseManager;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Services
        $this->app->singleton(AttendanceService::class);
        $this->app->singleton(DevotionalService::class);
        $this->app->singleton(ProgramEnrollmentService::class);
        $this->app->singleton(ReportExportService::class);
        $this->app->singleton(TenantDatabaseManager::class);
        $this->app->singleton(SubscriptionService::class);

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

        // Migration paths
        $this->loadMigrationsFrom([
            database_path('migrations/central-app'),
        ]);

        // Register Policies
        Gate::policy(User::class, DisciplePolicy::class);
        Gate::policy(Devotional::class, DevotionalPolicy::class);
        Gate::policy(SupportTicket::class, SupportTicketPolicy::class);

        Gate::before(function (User $user, string $ability): ?bool {
            if (! function_exists('tenancy') || ! tenancy()->initialized || ! str_contains($ability, '.')) {
                return null;
            }

            if ($ability === 'reports.export') {
                return $user->hasPermission($ability)
                    && app(SubscriptionService::class)->isAllowed($user->church, 'export_sheets');
            }

            return $user->hasPermission($ability);
        });
    }
}
