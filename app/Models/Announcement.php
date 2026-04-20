<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'title',
        'body',
        'published_at',
        'audience',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
