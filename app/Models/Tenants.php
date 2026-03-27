<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenants extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Tell stancl/tenancy which attributes are REAL database columns.
     * Without this, BaseTenant puts everything into the `data` JSON column.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'church_name',
            'church_address',
            'pastor_name',
            'pastor_email',
            'subdomain',
            'subscription_plan',
            'status',
            'subscription_started_at',
            'subscription_due_at',
        ];
    }

    protected $casts = [
        'subscription_started_at' => 'datetime',
        'subscription_due_at' => 'datetime',
    ];

    // Relationships to central DB models
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function billingCycles()
    {
        return $this->hasMany(BillingCycle::class);
    }

    // Convenience: check current plan features
    public function canAddDisciple(int $currentCount): bool
    {
        $max = $this->subscriptionPlan->max_disciples;
        return $max === null || $currentCount < $max; // null = unlimited
    }

    public function canAddLeader(int $currentCount): bool
    {
        $max = $this->subscriptionPlan->max_leaders;
        return $max === null || $currentCount < $max;
    }
}