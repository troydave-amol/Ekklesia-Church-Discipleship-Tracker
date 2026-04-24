<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Product release model (central DB, version_history table).
 *
 * NOTE: The old audit-log model (central_version_history) has been superseded.
 * Any audit logging should use a dedicated AuditLog model or Laravel Auditing package.
 */
class VersionHistory extends Model
{
    protected $connection = 'central';

    protected $table = 'version_history';

    protected $fillable = [
        'version',
        'title',
        'release_notes',
        'features',
        'bug_fixes',
        'is_major',
        'status',
        'published_at',
        'pushed_at',
        'pushed_by',
    ];

    protected $casts = [
        'features'     => 'array',
        'bug_fixes'    => 'array',
        'is_major'     => 'boolean',
        'published_at' => 'datetime',
        'pushed_at'    => 'datetime',
    ];

    // ── Scopes ────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('published_at');
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function isPushed(): bool
    {
        return $this->pushed_at !== null;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    // ── Relationships ─────────────────────────────────────────────

    public function acknowledgments(): HasMany
    {
        return $this->hasMany(TenantReleaseAcknowledgment::class, 'version_history_id');
    }
}
