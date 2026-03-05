<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;

class CheckSubscriptionLimit
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function handle(Request $request, Closure $next, string $feature): mixed
    {
        $church = $request->user()->church;

        if (!$this->subscriptionService->isAllowed($church, $feature)) {
            abort(403, 'Subscription limit reached for: ' . $feature);
        }

        return $next($request);
    }
}