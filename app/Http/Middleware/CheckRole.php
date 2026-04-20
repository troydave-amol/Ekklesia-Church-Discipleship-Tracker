<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Resolves user role_id to role slug and checks against allowed roles.
     * Accepts comma-separated role slugs: role:church_admin,program_leader
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized($request);
        }

        // Resolve role_id to role slug from database
        $userRoleSlug = $this->getUserRoleSlug($user);

        if (!$userRoleSlug) {
            return $this->unauthorized($request);
        }

        // Check if user's role matches any allowed role
        foreach ($roles as $allowedRole) {
            if ($userRoleSlug === $allowedRole) {
                return $next($request);
            }
        }

        return $this->unauthorized($request);
    }

    /**
     * Resolve user role_id to role slug from tenant database.
     */
    private function getUserRoleSlug($user): ?string
    {
        if (!$user->role_id) {
            return null;
        }

        try {
            $role = DB::connection('tenant')
                ->table('roles')
                ->where('id', $user->role_id)
                ->value('slug');

            return $role;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Return unauthorized response (JSON or redirect).
     */
    private function unauthorized(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return redirect()
            ->route('tenant.dashboard')
            ->with('error', 'You do not have permission to access this resource.');
    }
}
