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

        $userRole = (string) $user->role;
        $normalized = $user->normalizedRole();

        foreach ($roles as $allowed) {
            if ($userRole === $allowed || $normalized === $allowed) {
                return $next($request);
            }
            // Synonyms accepted in route definitions
            if ($allowed === 'pastor' && ($userRole === 'church_admin' || $normalized === 'church_admin')) {
                return $next($request);
            }
            if ($allowed === 'church_admin' && $userRole === 'pastor') {
                return $next($request);
            }
            if ($allowed === 'leader' && in_array($userRole, ['program_leader', 'cell_leader', 'leader'], true)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}
