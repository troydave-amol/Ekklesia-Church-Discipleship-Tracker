<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BroadcastFeatureUpdateJob;
use App\Jobs\ReseedFeatureJob;
use App\Jobs\ScaffoldFeatureJob;
use App\Models\AdminAuditLog;
use App\Models\FeatureUpdate;
use App\Models\Tenant;
use App\Models\TenantFeatureInstallation;
use App\Models\VersionHistory;
use App\Services\FeaturePackageInspector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FeatureUpdateController extends Controller
{
    /**
     * Roots that are safe for managed scaffold-file cleanup.
     *
     * @var array<int, string>
     */
    private const MANAGED_ARTIFACT_ROOTS = [
        'app/Models',
        'app/Http/Controllers',
        'app/Http/Requests',
        'database/factories',
        'database/seeders',
        'database/migrations/tenant',
    ];

    /**
     * List all feature updates and app versions, newest first.
     */
    public function index()
    {
        $featureUpdates = FeatureUpdate::withCount([
            'installations as installed_count' => function ($query) {
                $query->where('status', 'installed');
            },
        ])->orderByDesc('created_at')->get();
        $versionHistories = VersionHistory::withCount('acknowledgments')->orderByDesc('created_at')->get();
        $totalTenants = Tenant::active()->count();

        return view('admin.feature-updates.index', compact('featureUpdates', 'versionHistories', 'totalTenants'));
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:feature_updates,slug'],
            'version' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:feature,patch,data'],
            'fields' => ['nullable', 'array'],
            'fields.*.name' => ['required', 'string'],
            'fields.*.type' => ['required', 'string'],
            'fields.*.nullable' => ['nullable', 'boolean'],
            'scaffold' => ['nullable', 'array'],
            'seed_count' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $payload = [
            'fields' => $validated['fields'] ?? [],
            'scaffold' => $validated['scaffold'] ?? [],
            'seed_count' => $validated['seed_count'] ?? 50,
        ];

        $featureUpdate = FeatureUpdate::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'version' => $validated['version'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'status' => 'draft',
            'payload' => $payload,
        ]);

        ScaffoldFeatureJob::dispatch($featureUpdate);

        return redirect()
            ->route('admin.feature-updates.show', $featureUpdate)
            ->with('success', 'Scaffolding started. Files will be generated shortly.');
    }

    /**
     * Show a single feature update and its per-tenant installation statuses.
     */
    public function show(FeatureUpdate $featureUpdate, FeaturePackageInspector $inspector)
    {
        $featureUpdate->load('installations.tenant');
        $inspection = $inspector->inspect($featureUpdate);
        $deleteImpact = $this->buildDeleteImpact($featureUpdate, $inspection);

        return view('admin.feature-updates.show', compact('featureUpdate', 'inspection', 'deleteImpact'));
    }

    /**
     * Publish the feature: mark it published, create pending installation
     * records for every active tenant, and notify their church admins.
     */
    public function publish(FeatureUpdate $featureUpdate, FeaturePackageInspector $inspector)
    {
        $inspection = $inspector->inspect($featureUpdate);

        if (!$inspection['is_complete']) {
            $missingLabels = implode(', ', $inspection['missing_labels']);

            return redirect()
                ->back()
                ->with('error', "Publish blocked. Missing or incomplete components: {$missingLabels}.");
        }

        $featureUpdate->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $activeTenants = Tenant::active()->get();
        foreach ($activeTenants as $tenant) {
            TenantFeatureInstallation::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
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

    /**
     * Permanently delete a feature package and its managed scaffold files.
     */
    public function destroy(FeatureUpdate $featureUpdate, FeaturePackageInspector $inspector)
    {
        $featureUpdate->load('installations.tenant');

        $inspection = $inspector->inspect($featureUpdate);
        $deleteImpact = $this->buildDeleteImpact($featureUpdate, $inspection);

        if ($deleteImpact['has_installing_installations']) {
            return redirect()
                ->back()
                ->with('error', 'Delete blocked. One or more tenant installations are currently installing this package.');
        }

        $cleanup = $this->cleanupSelectedArtifacts($inspection['selected_components']);

        if ($cleanup['issues'] !== []) {
            return redirect()
                ->back()
                ->with('error', 'Delete blocked. Could not safely clean up package files: '.implode('; ', $cleanup['issues']));
        }

        $packageSnapshot = [
            'id' => $featureUpdate->id,
            'name' => $featureUpdate->name,
            'slug' => $featureUpdate->slug,
            'version' => $featureUpdate->version,
            'status' => $featureUpdate->status,
            'type' => $featureUpdate->type,
            'selected_component_count' => $inspection['selected_count'],
            'tracked_tenant_count' => $deleteImpact['tracked_tenant_count'],
            'installation_status_counts' => $deleteImpact['installation_status_counts'],
        ];

        try {
            DB::connection($featureUpdate->getConnectionName())->transaction(function () use ($featureUpdate, $packageSnapshot, $cleanup) {
                $featureUpdate->delete();

                AdminAuditLog::logChange(
                    'feature_update',
                    $packageSnapshot['id'],
                    auth('admin')->id(),
                    'deleted',
                    "Feature package '{$packageSnapshot['name']}' deleted.",
                    $packageSnapshot,
                    [
                        'deleted' => true,
                        'cleanup' => [
                            'deleted_files' => $cleanup['deleted_paths'],
                            'skipped_missing_files' => $cleanup['skipped_paths'],
                        ],
                    ],
                );
            });
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->with('error', 'Feature package cleanup finished, but the package record could not be deleted. Please retry or inspect the audit logs.');
        }

        return redirect()
            ->route('admin.feature-updates.index')
            ->with('success', "Feature package \"{$packageSnapshot['name']}\" was permanently deleted.");
    }

    /**
     * @param  array<int, array{label:string,relative_path:?string}>  $components
     * @return array{
     *     deleted_paths: array<int, string>,
     *     skipped_paths: array<int, string>,
     *     issues: array<int, string>
     * }
     */
    private function cleanupSelectedArtifacts(array $components): array
    {
        $deletedPaths = [];
        $skippedPaths = [];
        $issues = [];

        foreach ($components as $component) {
            $validation = $this->validateCleanupPath($component['relative_path'] ?? null);

            if (!$validation['valid']) {
                $issues[] = "{$component['label']}: {$validation['reason']}";
                continue;
            }

            $absolutePath = $validation['absolute_path'];
            $relativePath = $validation['relative_path'];

            if (is_dir($absolutePath)) {
                $issues[] = "{$component['label']}: cleanup target is a directory ({$relativePath})";
                continue;
            }

            if (!is_file($absolutePath)) {
                $skippedPaths[] = $relativePath;
                continue;
            }

            if (!File::delete($absolutePath)) {
                $issues[] = "{$component['label']}: failed to delete {$relativePath}";
                continue;
            }

            $deletedPaths[] = $relativePath;
        }

        return [
            'deleted_paths' => $deletedPaths,
            'skipped_paths' => $skippedPaths,
            'issues' => $issues,
        ];
    }

    /**
     * @return array{valid:bool,relative_path:?string,absolute_path:?string,reason:?string}
     */
    private function validateCleanupPath(?string $relativePath): array
    {
        if (!is_string($relativePath) || trim($relativePath) === '') {
            return [
                'valid' => false,
                'relative_path' => null,
                'absolute_path' => null,
                'reason' => 'missing path metadata',
            ];
        }

        $normalizedRelativePath = preg_replace('#/+#', '/', str_replace('\\', '/', trim($relativePath))) ?? '';

        if ($normalizedRelativePath === ''
            || preg_match('/^[A-Za-z]:/', $normalizedRelativePath)
            || Str::startsWith($normalizedRelativePath, '/')
            || str_contains($normalizedRelativePath, '..')) {
            return [
                'valid' => false,
                'relative_path' => $normalizedRelativePath,
                'absolute_path' => null,
                'reason' => "invalid cleanup path ({$normalizedRelativePath})",
            ];
        }

        $isManagedPath = collect(self::MANAGED_ARTIFACT_ROOTS)->contains(
            fn (string $root): bool => $normalizedRelativePath === $root || Str::startsWith($normalizedRelativePath, $root.'/')
        );

        if (!$isManagedPath) {
            return [
                'valid' => false,
                'relative_path' => $normalizedRelativePath,
                'absolute_path' => null,
                'reason' => "path is outside managed scaffold roots ({$normalizedRelativePath})",
            ];
        }

        $absolutePath = base_path(str_replace('/', DIRECTORY_SEPARATOR, $normalizedRelativePath));
        $normalizedBasePath = str_replace('\\', '/', rtrim(base_path(), '\\/')).'/';
        $normalizedAbsolutePath = str_replace('\\', '/', $absolutePath);

        if (!Str::startsWith($normalizedAbsolutePath, $normalizedBasePath)) {
            return [
                'valid' => false,
                'relative_path' => $normalizedRelativePath,
                'absolute_path' => $absolutePath,
                'reason' => "path resolves outside the repository ({$normalizedRelativePath})",
            ];
        }

        return [
            'valid' => true,
            'relative_path' => $normalizedRelativePath,
            'absolute_path' => $absolutePath,
            'reason' => null,
        ];
    }

    /**
     * @param  array{selected_count:int}  $inspection
     * @return array{
     *     selected_component_count:int,
     *     tracked_tenant_count:int,
     *     installation_status_counts:array<string, int>,
     *     has_installing_installations:bool
     * }
     */
    private function buildDeleteImpact(FeatureUpdate $featureUpdate, array $inspection): array
    {
        $featureUpdate->loadMissing('installations');

        $statusCounts = $featureUpdate->installations
            ->groupBy(fn (TenantFeatureInstallation $installation): string => $installation->status)
            ->map(fn ($installations): int => $installations->count())
            ->sortKeys()
            ->all();

        return [
            'selected_component_count' => $inspection['selected_count'],
            'tracked_tenant_count' => $featureUpdate->installations->count(),
            'installation_status_counts' => $statusCounts,
            'has_installing_installations' => ($statusCounts['installing'] ?? 0) > 0,
        ];
    }
}
