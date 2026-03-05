<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Church extends Model
{
    protected $fillable = [
        'name',
        'address',
        'subscription_plan',
        'is_active',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function members()
    {
        return (bool) $this->is_active;
    }

    public function users() {
        return $this->hasMany(User::class);
    }
}
