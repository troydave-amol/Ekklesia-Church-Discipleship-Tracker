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
        'logo_path',
        'logo_dark_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'theme_config',
    ];

    protected $casts = [
        'theme_config' => 'array',
        'is_active' => 'boolean',
    ];

    // ── Customization Defaults ──────────────────────────────────

    /**
     * Return the tenant's brand color, falling back to Ekklesia Gold.
     */
    public function getBrandColorAttribute(): string
    {
        return $this->primary_color ?? '#D4A853';
    }

    /**
     * Get the logo URL with fallback.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return null;
    }

    /**
     * Get the dark mode logo URL with fallback to regular logo.
     */
    public function getLogoDarkUrlAttribute(): ?string
    {
        if ($this->logo_dark_path) {
            return asset('storage/' . $this->logo_dark_path);
        }
        return $this->logo_url;
    }

    /**
     * Get the favicon URL.
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if ($this->favicon_path) {
            return asset('storage/' . $this->favicon_path);
        }
        return null;
    }

    /**
     * Check if church has a custom logo.
     */
    public function hasLogo(): bool
    {
        return !empty($this->logo_path);
    }

    // ── Relationships ──────────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function members()
    {
        return $this->hasMany(User::class)->where('role', 'disciple');
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    // ── Multi-tenancy Note ─────────────────────────────────────
    // Each tenant database is fully isolated (one church per DB).
    // No global scope is needed here — stancl/tenancy handles
    // database-level isolation automatically.
}