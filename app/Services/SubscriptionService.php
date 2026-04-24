<?php

namespace App\Services;

use App\Models\Church;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Feature matrix — source of truth for all subscription gating.
     *
     * Plan slugs: free, basic, standard, premium
     * Limit value 0 = unlimited.
     * Boolean features use true/false.
     */
    protected array $matrix = [
        'free' => [
            'max_programs'        => 1,
            'max_disciples'       => 20,
            'max_leaders'         => 5,
            'devotional_tracking' => false,
            'export_sheets'       => false,
            'bulk_import'         => false,
            'historical_reports'  => false,
            'priority_support'    => false,
        ],
        'basic' => [
            'max_programs'        => 4,
            'max_disciples'       => 50,
            'max_leaders'         => 5,
            'devotional_tracking' => true,
            'export_sheets'       => false,
            'bulk_import'         => false,
            'historical_reports'  => false,
            'priority_support'    => false,
        ],
        'standard' => [
            'max_programs'        => 0, // unlimited
            'max_disciples'       => 0, // unlimited
            'max_leaders'         => 10,
            'devotional_tracking' => true,
            'export_sheets'       => true,
            'bulk_import'         => false,
            'historical_reports'  => false,
            'priority_support'    => false,
        ],
        'premium' => [
            'max_programs'        => 0, // unlimited
            'max_disciples'       => 0, // unlimited
            'max_leaders'         => 0, // unlimited
            'devotional_tracking' => true,
            'export_sheets'       => true,
            'bulk_import'         => true,
            'historical_reports'  => true,
            'priority_support'    => true,
        ],
    ];

    /**
     * Map middleware feature slugs to their matrix keys.
     * This lets routes use friendly names like 'subscription:manage-programs'
     * while the matrix stores 'max_programs'.
     */
    protected array $featureAliases = [
        'manage-programs'      => 'max_programs',
        'manage-lessons'       => 'max_programs',      // lessons gated by program limit
        'add_disciple'         => 'max_disciples',
        'max_programs'         => 'max_programs',
        'max_disciples'        => 'max_disciples',
        'max_leaders'          => 'max_leaders',
        'devotional_tracking'  => 'devotional_tracking',
        'devotional'           => 'devotional_tracking', // legacy alias
        'export_sheets'        => 'export_sheets',
        'bulk_import'          => 'bulk_import',
        'historical_reports'   => 'historical_reports',
        'priority_support'     => 'priority_support',
    ];

    /**
     * Get the current tenant's subscription plan slug.
     */
    public function getPlan(): string
    {
        try {
            $tenant = tenancy()->tenant;
            if (! $tenant) {
                return 'free';
            }

            // Read from the central DB tenants table
            $plan = DB::connection('central')
                ->table('tenants')
                ->where('id', $tenant->getTenantKey())
                ->value('subscription_plan');

            return strtolower((string) ($plan ?? 'free'));
        } catch (\Throwable) {
            return 'free';
        }
    }

    /**
     * Check if the current tenant's plan allows a given feature.
     *
     * For boolean features (devotional_tracking, export_sheets, etc.):
     *   Returns true if the feature flag is true.
     *
     * For count-based limits (max_programs, max_disciples, max_leaders):
     *   Returns true (the limit exists but actual count checking is done
     *   via checkLimit() when needed). isAllowed() just confirms the
     *   feature is not hard-blocked.
     *
     * Accepts both matrix keys and route aliases.
     */
    public function isAllowed(?Church $church, string $feature): bool
    {
        $plan = $church
            ? strtolower((string) ($church->subscription_plan ?? 'free'))
            : $this->getPlan();

        $planConfig = $this->matrix[$plan] ?? $this->matrix['free'];

        // Resolve alias to matrix key
        $matrixKey = $this->featureAliases[$feature] ?? $feature;

        if (! array_key_exists($matrixKey, $planConfig)) {
            // Unknown feature — deny by default
            return false;
        }

        $value = $planConfig[$matrixKey];

        // Boolean features
        if (is_bool($value)) {
            return $value;
        }

        // Count-based limits: 0 = unlimited (always allowed), >0 = allowed (count checked separately)
        // isAllowed() returns true as long as the feature exists in the plan.
        // Actual count enforcement is done by checkLimit().
        return true;
    }

    /**
     * Check if the current count is under the plan's limit for a given key.
     *
     * @param  string  $limitKey  One of: max_programs, max_disciples, max_leaders
     * @param  int     $currentCount  The current count of the resource
     * @return bool    True if under the limit (or unlimited), false if at/over limit
     */
    public function checkLimit(string $limitKey, int $currentCount): bool
    {
        $plan = $this->getPlan();
        $planConfig = $this->matrix[$plan] ?? $this->matrix['free'];

        // Resolve alias
        $matrixKey = $this->featureAliases[$limitKey] ?? $limitKey;

        if (! array_key_exists($matrixKey, $planConfig)) {
            return false;
        }

        $max = (int) $planConfig[$matrixKey];

        // 0 = unlimited
        if ($max === 0) {
            return true;
        }

        return $currentCount < $max;
    }

    /**
     * Get the full plan config for the current tenant.
     */
    public function getPlanConfig(): array
    {
        $plan = $this->getPlan();
        return $this->matrix[$plan] ?? $this->matrix['free'];
    }
}
