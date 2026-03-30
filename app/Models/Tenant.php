<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'name', 'email', 'subdomain', 'db_name',
        'status', 'subscription_plan',
        'trial_ends_at', 'activated_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'activated_at'  => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id', 'name', 'email', 'subdomain', 'db_name',
            'status', 'subscription_plan',
            'trial_ends_at', 'activated_at',
        ];
    }

    // ← THIS tells stancl to use church_baptistchurch instead of tenant{uuid}
    public function getTenancyDatabaseName(): string
    {
        return $this->db_name;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}