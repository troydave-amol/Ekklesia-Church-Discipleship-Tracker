<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Resolves user permissions from role_id and checks against required permissions.
     * Accepts comma-separated permission slugs: permission:programs.manage,lessons.manage
     * Caches resolved permissions in request attributes for the lifecycle.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): mixed
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized($request);
        }

        // Get cached permissions or resolve from database
        $userPermissions = $this->resolveUserPermissions($request, $user);

        if (!$userPermissions) {
            return $this->unauthorized($request);
        }

        // Check if user has any of the required permissions
        foreach ($permissions as $requiredPermission) {
            if (in_array($requiredPermission, $userPermissions)) {
                return $next($request);
            }
        }

        return $this->unauthorized($request);
    }

    /**
     * Resolve user permissions from role_id.
     * Caches in $request->attributes to avoid repeated DB queries.
     */
    private function resolveUserPermissions(Request $request, $user): array
    {
        // Check if already cached in this request
        $cacheKey = 'user_permissions_' . $user->id;
        if ($request->attributes->has($cacheKey)) {
            return $request->attributes->get($cacheKey);
        }

        if (!$user->role_id) {
            return [];
        }

        try {
            // Query: roles -> permission_role -> permissions (all via tenant connection)
            $permissions = DB::connection('tenant')
                ->table('permission_role')
                ->join('roles', 'permission_role.role_id', '=', 'roles.id')
                ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                ->where('roles.id', $user->role_id)
                ->pluck('permissions.slug')
                ->toArray();

            // Cache in request attributes for this request lifecycle
            $request->attributes->set($cacheKey, $permissions);

            return $permissions;
        } catch (\Exception $e) {
            return [];
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

        if ($request->route()?->getName() === 'tenant.dashboard') {
            abort(403, 'You do not have permission to access the dashboard.');
        }

        return redirect()
            ->route('tenant.dashboard')
            ->with('error', 'You do not have permission to perform this action.');
    }
}
