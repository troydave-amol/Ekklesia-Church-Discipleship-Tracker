<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Scopes\ChurchTenantScope;

class Lesson extends Model
{
    protected $table = 'lessons';

    protected $fillable = [
        'church_id', 
        'program_id', 
        'week_number', 
        'title', 
        'content'
    ];

     protected static function booted(): void
    {
        static::addGlobalScope(new ChurchTenantScope());
    }

    /**
     * Scope: Filter lessons by user role/permissions
     * - church_admin: all lessons in their church
     * - program_leader: lessons in their programs
     * - cell_leader: lessons in their assigned cells
     * - disciple: lessons in their programs
     */
    public function scopeForUser(Builder $query, User $user = null): Builder
    {
        $user = $user ?? auth()->user();
        if (!$user) return $query;

        return $query->when($user->role === 'program_leader', function (Builder $q) use ($user) {
            $q->whereHas('program.leaders', fn($l) => $l->where('user_id', $user->id));
        })->when($user->role === 'cell_leader', function (Builder $q) use ($user) {
            $q->whereHas('attendance', fn($a) => $a->where('user_id', $user->id));
        })->when($user->role === 'disciple', function (Builder $q) use ($user) {
            $q->whereHas('attendance', fn($a) => $a->where('user_id', $user->id));
        });
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
