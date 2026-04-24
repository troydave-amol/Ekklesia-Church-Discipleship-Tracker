<?php

namespace App\Http\Controllers;

use App\Models\TenantReleaseAcknowledgment;
use App\Models\VersionHistory;
use Illuminate\Http\JsonResponse;
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
}
