<?php

namespace App\Models;

use App\Scopes\ChurchTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'church_id',
        'user_id',
        'lesson_id',
        'date',
        'status',
        'recorded_by',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ChurchTenantScope());
    }

    /**
     * Scope: Filter attendance by user role/permissions
     * - church_admin: all attendance in their church (via GlobalScope)
     * - program_leader: attendance for disciples they lead
     * - cell_leader: attendance for their assigned disciples  
     * - disciple: only their own attendance
     */
    public function scopeForUser(Builder $query, User $user = null): Builder
    {
        $user = $user ?? auth()->user();
        if (!$user) return $query;

        if ($user->role === 'church_admin') {
            return $query; // GlobalScope handles church/tenant isolation
        }

        return $query->when($user->role === 'program_leader', function (Builder $q) use ($user) {
            // See attendance for disciples they lead
            $q->whereHas('user.membership', fn($m) => $m->where('leader_id', $user->id));
        })->when($user->role === 'cell_leader', function (Builder $q) use ($user) {
            // Cell leaders see attendance for disciples they're assigned to lead
            $q->whereHas('user.membership', fn($m) => $m->where('leader_id', $user->id));
        })->when($user->role === 'disciple', function (Builder $q) use ($user) {
            // Disciples see only their own attendance
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

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}