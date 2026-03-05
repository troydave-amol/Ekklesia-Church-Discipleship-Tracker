<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'church_id',
        'first_name',
        'last_name',
        'nickname',
        'email',
        'password',
        'role',
        'church_id',
        'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function membership(): HasOne
    {
        return $this->hasOne(ChurchMembership::class);
    }

    // Disciples assigned to this leader
    public function assignedDisciples(): HasMany
    {
        return $this->hasMany(ChurchMembership::class, 'leader_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function devotionals(): HasMany
    {
        return $this->hasMany(Devotional::class);
    }

    // ── Role Helpers ───────────────────────────────────────────────

    public function isPastor(): bool  { return $this->role === 'pastor'; }
    public function isLeader(): bool  { return $this->role === 'leader'; }
    public function isDisciple(): bool{ return $this->role === 'disciple'; }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function initials(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }
}
