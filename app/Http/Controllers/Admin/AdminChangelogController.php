<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantReleaseAcknowledgment;
use App\Models\VersionHistory;
use App\Notifications\SystemUpdateAvailable;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminChangelogController extends Controller
{
    /**
     * List all product releases with acknowledgment stats.
     */
    public function index(): View
    {
        $releases = VersionHistory::with('acknowledgments')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        $totalTenants  = Tenant::count();
        $latestRelease = VersionHistory::published()->latestFirst()->first();

        $totalReleases  = VersionHistory::count();
        $publishedCount = VersionHistory::where('status', 'published')->count();
        $draftCount     = VersionHistory::where('status', 'draft')->count();
        $pushedCount    = VersionHistory::whereNotNull('pushed_at')->count();

        return view('admin.changelog.index', compact(
            'releases',
            'totalTenants',
            'latestRelease',
            'totalReleases',
            'publishedCount',
            'draftCount',
            'pushedCount',
        ));
    }

    /**
     * Show create form for a new release draft.
     */
    public function create(): View
    {
        return view('admin.changelog.create');
    }

    /**
     * Store a new release as draft.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'version'      => 'required|string|max:20|unique:central.version_history,version',
            'title'        => 'required|string|max:150',
            'release_notes'=> 'required|string',
            'features'     => 'nullable|array',
            'features.*'   => 'string|max:200',
            'bug_fixes'    => 'nullable|array',
            'bug_fixes.*'  => 'string|max:200',
            'is_major'     => 'boolean',
        ]);

        // Filter out empty entries from dynamic lists
        $validated['features']  = array_values(array_filter($validated['features']  ?? []));
        $validated['bug_fixes'] = array_values(array_filter($validated['bug_fixes'] ?? []));
        $validated['is_major']  = $request->boolean('is_major');
        $validated['status']    = 'draft';

        VersionHistory::create($validated);

        return redirect()
            ->route('admin.changelog.index')
            ->with('success', "Release v{$validated['version']} saved as draft.");
    }

    /**
     * Publish a draft release (AJAX).
     */
    public function publish(int $id): JsonResponse
    {
        $release = VersionHistory::findOrFail($id);

        if ($release->status === 'published') {
            return response()->json(['success' => false, 'message' => 'Already published.'], 422);
        }

        $release->status       = 'published';
        $release->published_at = now();
        $release->save();

        return response()->json([
            'success' => true,
            'message' => "v{$release->version} published.",
        ]);
    }

    /**
     * Broadcast a published release to all tenant church_admins (AJAX).
     */
    public function broadcast(int $id): JsonResponse
    {
        $release = VersionHistory::findOrFail($id);

        if ($release->status !== 'published') {
            return response()->json(['success' => false, 'message' => 'Release must be published first.'], 422);
        }

        $tenants      = Tenant::all();
        $tenantCount  = $tenants->count();

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            try {
                $admins = User::whereIn('role', ['church_admin', 'pastor'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SystemUpdateAvailable($release));
                }
            } finally {
                tenancy()->end();
            }
        }

        $release->pushed_at = now();
        $release->pushed_by = auth()->guard('admin')->id();
        $release->save();

        return response()->json([
            'success' => true,
            'message' => "Pushed to {$tenantCount} tenants.",
            'tenant_count' => $tenantCount,
        ]);
    }

    /**
     * Show per-tenant acknowledgment status for a release.
     */
    public function tenantStatus(int $id): View
    {
        $release = VersionHistory::with('acknowledgments')->findOrFail($id);
        $tenants = Tenant::all();

        $acknowledgedTenantIds = $release->acknowledgments
            ->pluck('tenant_id')
            ->flip();

        $tenantsWithStatus = $tenants->map(function ($tenant) use ($acknowledgedTenantIds, $release) {
            $ack = $release->acknowledgments
                ->firstWhere('tenant_id', $tenant->id);
            return [
                'tenant'          => $tenant,
                'acknowledged'    => $ack !== null,
                'acknowledged_at' => $ack?->acknowledged_at,
            ];
        });

        $acknowledgedCount = $tenantsWithStatus->where('acknowledged', true)->count();
        $pendingCount      = $tenantsWithStatus->where('acknowledged', false)->count();

        return view('admin.changelog.tenants', compact(
            'release',
            'tenantsWithStatus',
            'acknowledgedCount',
            'pendingCount',
        ));
    }
}
