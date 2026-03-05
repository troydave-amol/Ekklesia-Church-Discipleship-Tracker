<?php

namespace App\Models;

use App\Scopes\ChurchTenantScope;
use Illuminate\Database\Eloquent\Model;

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