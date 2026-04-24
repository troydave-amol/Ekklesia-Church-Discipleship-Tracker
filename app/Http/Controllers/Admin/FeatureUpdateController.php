<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BroadcastFeatureUpdateJob;
use App\Jobs\ReseedFeatureJob;
use App\Jobs\ScaffoldFeatureJob;
use App\Models\FeatureUpdate;
use App\Models\Tenant;
use App\Models\TenantFeatureInstallation;
use Illuminate\Http\Request;

class FeatureUpdateController extends Controller
{
    /**
     * List all feature updates, newest first.
     */
    public function index()
    {
        $featureUpdates = FeatureUpdate::orderByDesc('created_at')->get();

        return view('admin.feature-updates.index', compact('featureUpdates'));
    }

    /**
     * Show the create form for a new feature.
     */
    public function create()
    {
        return view('admin.feature-updates.create');
    }

    /**
     * Validate, create a draft feature, and dispatch the scaffold job.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'unique:feature_updates,slug'],
            'version'     => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:feature,patch,data'],
        ]);

        $featureUpdate = FeatureUpdate::create(array_merge($validated, [
            'status'  => 'draft',
            'payload' => [],
        ]));

        ScaffoldFeatureJob::dispatch($featureUpdate);

        return redirect()
            ->route('admin.feature-updates.show', $featureUpdate)
            ->with('success', 'Scaffolding started. Files will be generated shortly.');
    }

    /**
     * Show a single feature update and its per-tenant installation statuses.
     */
    public function show(FeatureUpdate $featureUpdate)
    {
        $featureUpdate->load('installations.tenant');

        return view('admin.feature-updates.show', compact('featureUpdate'));
    }

    /**
     * Publish the feature: mark it published, create pending installation
     * records for every active tenant, and notify their church admins.
     */
    public function publish(FeatureUpdate $featureUpdate)
    {
        $featureUpdate->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        // Create a pending installation row for every active tenant
        $activeTenants = Tenant::active()->get();
        foreach ($activeTenants as $tenant) {
            TenantFeatureInstallation::firstOrCreate(
                [
                    'tenant_id'         => $tenant->id,
                    'feature_update_id' => $featureUpdate->id,
                ],
                ['status' => 'pending']
            );
        }

        BroadcastFeatureUpdateJob::dispatch($featureUpdate);

        return redirect()
            ->back()
            ->with('success', 'Update published and tenants notified.');
    }

    /**
     * Re-run the factory seeder for all tenants that have already installed this feature.
     */
    public function seed(FeatureUpdate $featureUpdate)
    {
        $installedTenants = $featureUpdate
            ->installations()
            ->where('status', 'installed')
            ->with('tenant')
            ->get();

        foreach ($installedTenants as $installation) {
            ReseedFeatureJob::dispatch($featureUpdate, $installation->tenant);
        }

        return redirect()
            ->back()
            ->with('success', "Re-seed dispatched for {$installedTenants->count()} tenant(s).");
    }
}
