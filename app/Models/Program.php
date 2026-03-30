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

    protected $casts = [
        'active' => 'boolean',
    ];

    // Maps is_active → active so both work
    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->active;
    }

    public function setIsActiveAttribute(bool $value): void
    {
        $this->attributes['active'] = $value;
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ChurchTenantScope());
    }

    public function church()
    {
        return $this->belongsTo(Church::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function members()
    {
        return $this->hasMany(ChurchMembership::class, 'destiny_training_level', 'program_key');
    }
}