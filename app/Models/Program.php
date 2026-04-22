<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Scopes\ChurchTenantScope;

class Program extends Model
{
    protected $table = 'programs';

    protected $fillable = [
        'church_id',
        'name',
        'description',
        'program_key',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Maps is_active → active so both work
    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->active;
    }

    public function setIsActiveAttribute(bool $value): void
    {
        $this->attributes['active'] = $value;
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ChurchTenantScope());
    }

    /**
     * Scope: Filter programs by user role/permissions
     * - church_admin: all programs in their church
     * - program_leader: only programs they lead
     * - cell_leader: programs their cells belong to
     * - disciple: programs they're members of
     */
    public function scopeForUser(Builder $query, User $user = null): Builder
    {
        $user = $user ?? auth()->user();
        if (!$user) return $query;

        return $query->when($user->role === 'program_leader', function (Builder $q) use ($user) {
            $q->whereHas('leaders', fn($l) => $l->where('user_id', $user->id));
        })->when($user->role === 'cell_leader', function (Builder $q) use ($user) {
            $q->whereHas('lessons.attendance', fn($a) => $a->where('user_id', $user->id));
        })->when($user->role === 'disciple', function (Builder $q) use ($user) {
            $q->whereHas('members', fn($m) => $m->where('user_id', $user->id));
        });
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function members()
    {
        return $this->hasMany(ChurchMembership::class, 'destiny_training_level', 'program_key');
    }
}