<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantSignup;
use Illuminate\View\View;

class CentralDashboardController extends Controller
{
    public function __invoke(): View
    {
        $pendingSignups = TenantSignup::pending()->count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalTenants = Tenant::count();

        $byPlan = Tenant::query()
            ->selectRaw('subscription_plan, count(*) as c')
            ->groupBy('subscription_plan')
            ->pluck('c', 'subscription_plan')
            ->all();

        $planRows = collect(config('subscription.plans', []))
            ->map(fn ($meta, $key) => [
                'key'   => $key,
                'label' => $meta['label'] ?? ucfirst((string) $key),
                'count' => (int) ($byPlan[$key] ?? 0),
            ])
            ->values();

        return view('admin.dashboard', [
            'pendingSignups' => $pendingSignups,
            'activeTenants'  => $activeTenants,
            'totalTenants'   => $totalTenants,
            'planRows'       => $planRows,
        ]);
    }
}
