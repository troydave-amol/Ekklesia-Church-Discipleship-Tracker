<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\DestinyLevel;

class ChurchMembership extends Model
{

    protected $table = 'church_membership';
    
    protected $fillable = [
        'user_id',
        'church_id',
        'status',
        'joined_at',
        'baptized_at',
        'leader_id',
        'satellite',
        'destiny_training_level',
    ];

    protected $casts = [
        'destiny_training_level' => DestinyLevel::class,
    ];

    public function destinyLevel(): ?DestinyLevel
    {
        return $this->destiny_training_level;
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    // ── Multi-tenancy Scope ────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        // Enforce church_id isolation for this tenant
        static::addGlobalScope(function (Builder $query) {
            if (tenant()) {
                $query->where('church_id', tenant()->id);
            }
        });
    }
}
