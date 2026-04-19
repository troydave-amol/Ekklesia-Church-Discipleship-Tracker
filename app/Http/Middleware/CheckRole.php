<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();
        if (! $user) {
            abort(403, 'Unauthorized');
        }

        // Primary: database-driven role slug (preferred)
        $dbSlug = $user->roleRecord?->slug;

        // Fallback: legacy string column + normalized alias
        $legacyRole = (string) $user->role;
        $normalized = $user->normalizedRole();

        foreach ($roles as $allowed) {
            // Direct match on DB slug, legacy column, or normalized alias
            if ($dbSlug === $allowed || $legacyRole === $allowed || $normalized === $allowed) {
                return $next($request);
            }

            // Synonym: "pastor" ↔ "church_admin"
            if ($allowed === 'pastor' && ($dbSlug === 'church_admin' || $legacyRole === 'church_admin' || $normalized === 'church_admin')) {
                return $next($request);
            }
            if ($allowed === 'church_admin' && ($dbSlug === 'pastor' || $legacyRole === 'pastor')) {
                return $next($request);
            }

            // Synonym: "leader" matches any leader variant
            if ($allowed === 'leader' && in_array($dbSlug ?? $legacyRole, ['program_leader', 'cell_leader', 'leader'], true)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}
