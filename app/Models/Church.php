<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Church extends Model
{
    protected $fillable = [
        'name',
        'address',
        'subscription_plan',
        'is_active',
        'logo_path',
        'primary_color',
    ];

    // ── Customization Defaults ──────────────────────────────────

    /**
     * Return the tenant's brand color, falling back to Ekklesia Gold.
     */
    public function getBrandColorAttribute(): string
    {
        return $this->primary_color ?? '#D4A853';
    }

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

    // ── Multi-tenancy Note ─────────────────────────────────────
    // Each tenant database is fully isolated (one church per DB).
    // No global scope is needed here — stancl/tenancy handles
    // database-level isolation automatically.
}