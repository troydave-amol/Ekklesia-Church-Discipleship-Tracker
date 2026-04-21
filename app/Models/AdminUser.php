<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'central';
    protected $table = 'admin_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canManageTenants(): bool
    {
        return $this->isManager();
    }

    public function canManageAdmins(): bool
    {
        return $this->isSuperAdmin();
    }

    public function canViewTickets(): bool
    {
        return $this->isManager();
    }

    public function canBroadcastAnnouncements(): bool
    {
        return $this->isManager();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
