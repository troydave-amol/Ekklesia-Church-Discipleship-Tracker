<?php

namespace App\Helpers;

class DomainHelper
{
    /**
     * Returns the central (root) domain without scheme or trailing slash.
     * Driven entirely by APP_URL in .env.
     * Example: church-discipleship-tracker.io
     */
    public static function centralDomain(): string
    {
        return parse_url(config('app.url'), PHP_URL_HOST);
    }

    /**
     * Alias for centralDomain() — keeps backward-compat with existing calls to central().
     */
    public static function central(): string
    {
        return static::centralDomain();
    }

    /**
     * Returns the full tenant subdomain FQDN (no scheme).
     * Example: lpz.church-discipleship-tracker.io
     */
    public static function forTenant(string $slug): string
    {
        return $slug . '.' . static::centralDomain();
    }

    /**
     * Returns the full tenant URL with scheme from APP_URL and an optional path.
     * Example: tenantUrl('lpz', 'login') → http://lpz.church-discipleship-tracker.io/login
     * Example: tenantUrl('lpz')          → http://lpz.church-discipleship-tracker.io
     */
    public static function tenantUrl(string $slug, string $path = ''): string
    {
        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?? 'http';
        $base   = $scheme . '://' . static::forTenant($slug);

        return $path !== '' ? $base . '/' . ltrim($path, '/') : $base;
    }

    /**
     * Returns the full URL for the central app with an optional path.
     * Example: centralUrl('admin') → http://church-discipleship-tracker.io/admin
     */
    public static function centralUrl(string $path = ''): string
    {
        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME) ?? 'http';
        $base   = $scheme . '://' . static::centralDomain();

        return $path !== '' ? $base . '/' . ltrim($path, '/') : $base;
    }
}
