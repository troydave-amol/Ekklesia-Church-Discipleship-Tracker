<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
