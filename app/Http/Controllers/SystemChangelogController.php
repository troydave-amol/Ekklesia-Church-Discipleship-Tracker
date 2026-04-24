<?php

namespace App\Http\Controllers;

use App\Jobs\InstallFeatureUpdateJob;
use App\Models\FeatureUpdate;
use App\Models\Tenant;
use App\Models\TenantFeatureInstallation;
use App\Models\TenantReleaseAcknowledgment;
use App\Models\VersionHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemChangelogController extends Controller
{
    /**
     * Show all published releases with per-tenant acknowledgment status.
     */
    public function index(): View
    {
        $tenantId = tenancy()->tenant->getTenantKey();

        $releases = VersionHistory::published()
            ->latestFirst()
            ->get();

        $acknowledgedIds = TenantReleaseAcknowledgment::on('central')
            ->where('tenant_id', $tenantId)
            ->pluck('version_history_id')
            ->toArray();

        $latestRelease = $releases->first();

        $latestUnacknowledged = $releases->first(
            fn ($r) => !in_array($r->id, $acknowledgedIds)
        );

        return view('system.changelog', compact(
            'releases',
            'acknowledgedIds',
            'latestRelease',
            'latestUnacknowledged',
        ));
    }

    /**
     * Acknowledge (dismiss) a release for the current tenant (AJAX).
     */
    public function acknowledge(int $id): JsonResponse
    {
        $release = VersionHistory::findOrFail($id);

        if ($release->status !== 'published') {
            return response()->json(['success' => false, 'message' => 'Release not published.'], 422);
        }

        $tenantId = tenancy()->tenant->getTenantKey();

        TenantReleaseAcknowledgment::updateOrCreate(
            [
                'version_history_id' => $id,
                'tenant_id'          => $tenantId,
            ],
            ['acknowledged_at' => now()]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Show all published feature updates with the current tenant's install status.
     */
    public function updates(): View
    {
        $tenantId = tenancy()->tenant->getTenantKey();

        $featureUpdates = FeatureUpdate::published()
            ->orderByDesc('published_at')
            ->get();

        // Eager-load this tenant's installation records
        $installations = TenantFeatureInstallation::where('tenant_id', $tenantId)
            ->get()
            ->keyBy('feature_update_id');

        return view('system.updates', compact('featureUpdates', 'installations'));
    }

    /**
     * Install a feature update for the current tenant.
     * Atomically transitions pending/failed → installing to prevent race conditions.
     */
    public function install(FeatureUpdate $featureUpdate): RedirectResponse
    {
        $tenantId = tenancy()->tenant->getTenantKey();

        $installation = TenantFeatureInstallation::where('tenant_id', $tenantId)
            ->where('feature_update_id', $featureUpdate->id)
            ->first();

        if (! $installation) {
            return redirect()->back()->with('error', 'No installation record found for this update.');
        }

        // Prevent duplicate dispatches — only allow from pending or failed states
        if (! in_array($installation->status, ['pending', 'failed'])) {
            $msg = $installation->status === 'installed'
                ? 'This update is already installed.'
                : 'This update is currently being installed. Please wait.';

            return redirect()->back()->with('info', $msg);
        }

        // Atomically set to installing (race condition guard)
        $updated = TenantFeatureInstallation::where('id', $installation->id)
            ->whereIn('status', ['pending', 'failed'])
            ->update(['status' => 'installing', 'error_log' => null]);

        if (! $updated) {
            return redirect()->back()->with('info', 'Installation already in progress.');
        }

        // Resolve the full Tenant model from central DB
        $tenant = Tenant::find($tenantId);

        InstallFeatureUpdateJob::dispatch($tenant, $featureUpdate);

        return redirect()->back()->with('success', 'Update installation started. This may take a minute — refresh the page to check status.');
    }
}

