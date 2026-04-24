<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantFeatureInstallation extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'tenant_id',
        'feature_update_id',
        'status',
        'error_log',
        'installed_at',
    ];

    protected $casts = [
        'installed_at' => 'datetime',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    public function featureUpdate(): BelongsTo
    {
        return $this->belongsTo(FeatureUpdate::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
