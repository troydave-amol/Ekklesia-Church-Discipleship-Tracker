<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSignup extends Model
{
    protected $fillable = [
        'church_name',
        'church_address',
        'pastor_name',
        'pastor_email',
        'password',
        'subdomain',
        'subscription_plan',
        'status',
        'rejection_note',
        'tenant_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
