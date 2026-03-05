<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'mobile_number',
        'gender',
        'civil_status',
        'date_of_birth',
        'address',
        'city',
        'state',
        'postal_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
