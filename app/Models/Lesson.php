<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ChurchTenantScope;

class Lesson extends Model
{
    protected $table = 'lessons';

    protected $fillable = [
        'church_id', 
        'program_id', 
        'week_number', 
        'title', 
        'content'
    ];

     protected static function booted(): void
    {
        static::addGlobalScope(new ChurchTenantScope());
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
