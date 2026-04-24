<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureUpdate extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'name',
        'slug',
        'version',
        'description',
        'type',
        'status',
        'payload',
        'published_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'published_at' => 'datetime',
    ];

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /* ── Relationships ──────────────────────────────────────── */

    public function installations(): HasMany
    {
        return $this->hasMany(TenantFeatureInstallation::class);
    }

    /* ── Helpers ────────────────────────────────────────────── */

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getTenantInstallation(string $tenantId): ?TenantFeatureInstallation
    {
        return $this->installations()->where('tenant_id', $tenantId)->first();
    }
}
