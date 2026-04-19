<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
