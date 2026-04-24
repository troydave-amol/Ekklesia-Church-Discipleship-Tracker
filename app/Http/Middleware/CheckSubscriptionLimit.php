<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;

class CheckSubscriptionLimit
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    /**
     * Handle an incoming request.
     *
     * Accepts a feature slug parameter via route middleware definition:
     *   'subscription:manage-programs', 'subscription:devotional_tracking', etc.
     *
     * Delegates entirely to SubscriptionService::isAllowed() for both
     * boolean feature flags and count-based limits.
     */
    public function handle(Request $request, Closure $next, string $feature): mixed
    {
        $church = $request->user()?->church;

        if (! $this->subscriptionService->isAllowed($church, $feature)) {
            abort(403, 'Upgrade your plan to access this feature.');
        }

        return $next($request);
    }
}