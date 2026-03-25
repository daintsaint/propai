<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Active subscription required',
                'subscription_status' => $user->subscription_status,
                'error_code' => 'SUBSCRIPTION_INACTIVE',
            ], 403);
        }

        return $next($request);
    }
}
