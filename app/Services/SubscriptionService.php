<?php

namespace App\Services;

use App\Models\Church;

class SubscriptionService
{
    public function isAllowed(Church $church, string $feature): bool
    {
        $planKey = strtolower((string) ($church->subscription_plan ?? 'free'));
        $plans  = config('subscription.plans', []);
        $plan   = $plans[$planKey] ?? $plans['free'];

        return match ($feature) {
            'add_disciple' => $this->underDiscipleCap($church, $plan),
            'bulk_import'  => in_array($planKey, ['premium'], true),
            'devotional'   => in_array($planKey, ['free', 'basic', 'standard', 'premium'], true),
            'export_sheets'=> in_array($planKey, ['standard', 'premium'], true),
            default        => false,
        };
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    protected function underDiscipleCap(Church $church, array $plan): bool
    {
        $max = $plan['disciples_max'] ?? null;
        if ($max === null) {
            return true;
        }

        return $church->members()->count() < (int) $max;
    }
}
