<?php

namespace App\Jobs;

use App\Models\FeatureUpdate;
use App\Models\Tenant;
use App\Models\TenantFeatureInstallation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class InstallFeatureUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout in seconds — prevents hanging on bad migrations.
     */
    public int $timeout = 120;

    /**
     * Do NOT retry automatically — failed installs should be manually retried
     * by the pastor via the UI to avoid silent data corruption.
     */
    public int $tries = 1;

    public function __construct(
        public Tenant $tenant,
        public FeatureUpdate $featureUpdate,
    ) {}

    public function handle(): void
    {
        $installation = TenantFeatureInstallation::where('tenant_id', $this->tenant->id)
            ->where('feature_update_id', $this->featureUpdate->id)
            ->first();

        if (! $installation) {
            Log::warning("InstallFeatureUpdateJob: No installation record for tenant [{$this->tenant->id}], feature [{$this->featureUpdate->id}].");
            return;
        }

        // Guard: skip if already installed (idempotency for duplicate dispatches)
        if ($installation->status === 'installed') {
            Log::info("InstallFeatureUpdateJob: Feature [{$this->featureUpdate->slug}] already installed for tenant [{$this->tenant->id}]. Skipping.");
            return;
        }

        $installation->update(['status' => 'installing']);

        // Save previous tenancy state — critical for QUEUE_CONNECTION=sync
        // where this job runs inside an already-initialized tenant HTTP request.
        $previousTenant = tenancy()->initialized ? tenancy()->tenant : null;

        try {
            tenancy()->initialize($this->tenant);

            // ── 1) Run tenant migration ───────────────────────────
            $migrationFile = $this->featureUpdate->payload['migration_file'] ?? null;
            if ($migrationFile) {
                $migrationPath = "database/migrations/tenant/{$migrationFile}.php";

                Artisan::call('migrate', [
                    '--path'    => $migrationPath,
                    '--force'   => true,
                    '--realpath' => false,
                ]);

                Log::info("InstallFeatureUpdateJob: Migration applied for tenant [{$this->tenant->id}]: {$migrationFile}");
            }

            // ── 2) Run seeder ─────────────────────────────────────
            $seederClass = $this->featureUpdate->payload['seeder_class'] ?? null;
            if ($seederClass) {
                $fqcn = "Database\\Seeders\\{$seederClass}";

                Artisan::call('db:seed', [
                    '--class' => $fqcn,
                    '--force' => true,
                ]);

                Log::info("InstallFeatureUpdateJob: Seeded '{$seederClass}' for tenant [{$this->tenant->id}].");
            }

            // ── 3) Mark installed ─────────────────────────────────
            $installation->update([
                'status'       => 'installed',
                'installed_at' => now(),
                'error_log'    => null,
            ]);

            Log::info("InstallFeatureUpdateJob: Feature [{$this->featureUpdate->slug}] successfully installed for tenant [{$this->tenant->id}].");

        } catch (\Throwable $e) {
            Log::error("InstallFeatureUpdateJob: Failed for tenant [{$this->tenant->id}], feature [{$this->featureUpdate->id}]: {$e->getMessage()}");

            $installation->update([
                'status'    => 'failed',
                'error_log' => $e->getMessage(),
            ]);
        } finally {
            // End the tenancy context we created, then restore the previous
            // one if it existed (sync queue running inside a tenant request).
            tenancy()->end();

            if ($previousTenant) {
                tenancy()->initialize($previousTenant);
            }
        }
    }
}
