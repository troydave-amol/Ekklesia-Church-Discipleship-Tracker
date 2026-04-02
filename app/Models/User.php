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

    protected $connection = 'tenant';

    protected $fillable = [
        'church_id',
        'first_name',
        'last_name',
        'nickname',
        'email',
        'password',
        'role',
        'role_id',
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

    public function roleRecord(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function membership(): HasOne
    {
        return $this->hasOne(ChurchMembership::class);
    }

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

    // ── Role helpers (string `role` column is source for middleware checks) ──

    /** @var list<string> */
    protected static function churchAdminSlugs(): array
    {
        return ['church_admin', 'pastor'];
    }

    /** @var list<string> */
    protected static function staffLeaderSlugs(): array
    {
        return ['program_leader', 'cell_leader', 'leader'];
    }

    public function normalizedRole(): string
    {
        $r = (string) $this->role;

        return match ($r) {
            'pastor' => 'church_admin',
            'leader' => 'program_leader',
            default  => $r,
        };
    }

    public function isChurchAdmin(): bool
    {
        return in_array((string) $this->role, self::churchAdminSlugs(), true);
    }

    /** Program or cell leader (scoped to assigned disciples). */
    public function isRestrictedLeader(): bool
    {
        return in_array((string) $this->role, self::staffLeaderSlugs(), true);
    }

    /** Leader sidebar / leader stats (not disciple). */
    public function isStaffLeader(): bool
    {
        return $this->isChurchAdmin() || $this->isRestrictedLeader();
    }

    public function isPastor(): bool
    {
        return $this->isChurchAdmin();
    }

    public function isLeader(): bool
    {
        return $this->isRestrictedLeader();
    }

    public function isDisciple(): bool
    {
        return (string) $this->role === 'disciple';
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isChurchAdmin()) {
            return true;
        }

        $this->loadMissing('roleRecord.permissions');

        return $this->roleRecord
            ? $this->roleRecord->permissions->contains('slug', $permissionSlug)
            : false;
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function initials(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }
}
