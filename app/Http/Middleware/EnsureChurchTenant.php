<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureChurchTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if (!auth()->check() || !auth()->user()->church_id) {
            abort(403, 'No church associated with this account.');
        }

        // Bind church_id into request for controllers to use freely
        $request->merge(['_church_id' => auth()->user()->church_id]);

        return $next($request);
    }
}
