<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ChurchTenantScope;

class Program extends Model
{
    protected $table = 'programs';

    protected $fillable = [
        'church_id',
        'name',
        'description',
        'program_key',
        'active',
    ];

     protected static function booted(): void
    {
        static::addGlobalScope(new ChurchTenantScope());
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function lesson()
    {
        return $this->hasMany(Lesson::class);
    }
}
