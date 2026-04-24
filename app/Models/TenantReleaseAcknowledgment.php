<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantReleaseAcknowledgment extends Model
{
    protected $connection = 'central';

    protected $table = 'tenant_release_acknowledgments';

    protected $fillable = [
        'version_history_id',
        'tenant_id',
        'acknowledged_at',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public function release(): BelongsTo
    {
        return $this->belongsTo(VersionHistory::class, 'version_history_id');
    }
}
