<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Scopes\ChurchTenantScope;

class Devotional extends Model
{
    protected $table = 'devotionals';
    protected $fillable = [
        'church_id', 
        'user_id', 
        'date', 
        'completed',    
        'verified_by', 
        'verified_at'
    ];

    protected $casts = [
        'date'      => 'date',
        'completed' => 'boolean',
    ];

     protected static function booted(): void
    {
        static::addGlobalScope(new ChurchTenantScope());
    }

    /**
     * Scope: Filter devotionals by user role/permissions
     * - church_admin: all devotionals in their church (via GlobalScope)
     * - program_leader: devotionals for disciples they lead
     * - cell_leader: devotionals for their assigned disciples
     * - disciple: only their own devotionals
     */
    public function scopeForUser(Builder $query, User $user = null): Builder
    {
        $user = $user ?? auth()->user();
        if (!$user) return $query;

        if ($user->role === 'church_admin') {
            return $query; // GlobalScope handles church/tenant isolation
        }

        return $query->when($user->role === 'program_leader', function (Builder $q) use ($user) {
            // See devotionals for disciples they lead
            $q->whereHas('user.membership', fn($m) => $m->where('leader_id', $user->id));
        })->when($user->role === 'cell_leader', function (Builder $q) use ($user) {
            // Cell leaders see devotionals for disciples they're assigned to lead
            $q->whereHas('user.membership', fn($m) => $m->where('leader_id', $user->id));
        })->when($user->role === 'disciple', function (Builder $q) use ($user) {
            // Disciples see only their own devotionals
            $q->where('user_id', $user->id);
        });
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
