<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    /**
     * Reject all requests if the resolved tenant is not active.
     * Runs AFTER InitializeTenancyBySubdomain so tenant() is available.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant && $tenant->status !== 'active') {
            return response()->view('errors.tenant-suspended', [
                'tenant' => $tenant,
            ], 403);
        }

        return $next($request);
    }
}
