<?php

namespace App\Services;

use App\Models\Church;

class SubscriptionService
{
    // Plan limits per feature
    protected array $limits = [
        'basic'   => ['members' => 50,  'attendance' => true,  'finance' => false, 'reports' => false],
        'standard'=> ['members' => 200, 'attendance' => true,  'finance' => true,  'reports' => false],
        'premium' => ['members' => null,'attendance' => true,  'finance' => true,  'reports' => true],
    ];

    public function isAllowed(Church $church, string $feature): bool
    {
        $plan = $this->limits[$church->plan] ?? $this->limits['basic'];

        if (!isset($plan[$feature])) return false;

        // Boolean gate
        if (is_bool($plan[$feature])) return $plan[$feature];

        // Count gate (e.g. members)
        return $church->members()->count() < $plan[$feature];
    }
}