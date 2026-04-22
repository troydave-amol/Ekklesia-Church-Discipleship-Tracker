<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Church extends Model
{
    protected $fillable = [
        'name',
        'address',
        'subscription_plan',
        'is_active',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function members()
    {
        return $this->hasMany(User::class)->where('role', 'disciple');
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    // ── Multi-tenancy Scope ────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        // Restrict to current tenant's church
        static::addGlobalScope(function (Builder $query) {
            if (tenant()) {
                $query->where('id', tenant()->id);
            }
        });
    }
}