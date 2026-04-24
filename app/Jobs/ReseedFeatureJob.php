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

class ReseedFeatureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public FeatureUpdate $featureUpdate,
        public Tenant $tenant,
    ) {}

    public function handle(): void
    {
        $seederClass = $this->featureUpdate->payload['seeder_class'] ?? null;

        if (! $seederClass) {
            Log::warning("ReseedFeatureJob: No seeder_class in payload for feature [{$this->featureUpdate->id}].");
            return;
        }

        $fqcn = "Database\\Seeders\\{$seederClass}";

        // Save previous tenancy state for sync queue safety
        $previousTenant = tenancy()->initialized ? tenancy()->tenant : null;

        try {
            tenancy()->initialize($this->tenant);

            Artisan::call('db:seed', [
                '--class' => $fqcn,
                '--force' => true,
            ]);

            Log::info("ReseedFeatureJob: Seeded '{$seederClass}' for tenant [{$this->tenant->id}].");

        } catch (\Throwable $e) {
            Log::error("ReseedFeatureJob: Failed for tenant [{$this->tenant->id}]: {$e->getMessage()}");

            // Mark the installation as failed
            TenantFeatureInstallation::where('tenant_id', $this->tenant->id)
                ->where('feature_update_id', $this->featureUpdate->id)
                ->update([
                    'status'    => 'failed',
                    'error_log' => $e->getMessage(),
                ]);

            throw $e;
        } finally {
            tenancy()->end();

            if ($previousTenant) {
                tenancy()->initialize($previousTenant);
            }
        }
    }
}
