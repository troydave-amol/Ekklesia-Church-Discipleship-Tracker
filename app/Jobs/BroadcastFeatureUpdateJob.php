<?php

namespace App\Jobs;

use App\Models\FeatureUpdate;
use App\Models\Tenant;
use App\Models\TenantFeatureInstallation;
use App\Notifications\FeatureUpdateAvailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BroadcastFeatureUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public FeatureUpdate $featureUpdate) {}

    public function handle(): void
    {
        // Save previous tenancy state for sync queue safety
        $previousTenant = tenancy()->initialized ? tenancy()->tenant : null;

        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            try {
                // Ensure a pending installation record exists
                TenantFeatureInstallation::firstOrCreate(
                    [
                        'tenant_id'         => $tenant->id,
                        'feature_update_id' => $this->featureUpdate->id,
                    ],
                    ['status' => 'pending']
                );

                // Switch into the tenant's isolated database
                tenancy()->initialize($tenant);

                // Notify every church_admin inside this tenant
                $admins = \App\Models\User::where('role', 'church_admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new FeatureUpdateAvailable($this->featureUpdate));
                }

                tenancy()->end();

            } catch (\Throwable $e) {
                Log::error("BroadcastFeatureUpdateJob: Failed for tenant [{$tenant->id}]: {$e->getMessage()}");
                tenancy()->end();
            }
        }

        // Restore previous tenancy context if it existed
        if ($previousTenant) {
            tenancy()->initialize($previousTenant);
        }

        Log::info("BroadcastFeatureUpdateJob: Notified {$tenants->count()} tenant(s) about '{$this->featureUpdate->name}'.");
    }
}
