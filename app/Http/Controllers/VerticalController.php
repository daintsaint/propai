<?php

namespace App\Http\Controllers;

use App\Models\VerticalBundle;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VerticalController extends Controller
{
    /**
     * List all available vertical bundles.
     */
    public function index(): JsonResponse
    {
        $bundles = VerticalBundle::active()
            ->orderBy('monthly_price')
            ->get();

        return response()->json([
            'bundles' => $bundles,
        ]);
    }

    /**
     * Get a specific vertical bundle by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $bundle = VerticalBundle::active()->bySlug($slug)->firstOrFail();

        return response()->json([
            'bundle' => $bundle,
        ]);
    }

    /**
     * Activate a vertical bundle for the authenticated user.
     * This is typically called after successful Stripe payment.
     */
    public function activate(Request $request, string $slug): JsonResponse
    {
        $user = $request->user();
        
        $bundle = VerticalBundle::active()->bySlug($slug)->firstOrFail();

        // Check if user already has an active subscription to this bundle
        $existingSubscription = $user->subscriptions()
            ->whereHas('verticalBundle', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->where('stripe_status', 'active')
            ->first();

        if ($existingSubscription) {
            return response()->json([
                'message' => 'You already have an active subscription to this bundle',
                'subscription' => $existingSubscription,
            ], 400);
        }

        // Create subscription (this should be called after Stripe webhook confirms payment)
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'vertical_bundle_id' => $bundle->id,
            'stripe_status' => 'active',
            'monthly_price' => $bundle->monthly_price,
            'starts_at' => now(),
        ]);

        // Update user's subscription status
        $user->update([
            'subscription_status' => 'active',
            'vertical_type' => $bundle->slug,
        ]);

        return response()->json([
            'message' => 'Bundle activated successfully',
            'subscription' => $subscription->load('verticalBundle'),
        ], 201);
    }

    /**
     * Upgrade user's subscription to a different bundle.
     */
    public function upgrade(Request $request, string $newSlug): JsonResponse
    {
        $user = $request->user();
        
        $newBundle = VerticalBundle::active()->bySlug($newSlug)->firstOrFail();

        // Get current active subscription
        $currentSubscription = $user->subscriptions()
            ->where('stripe_status', 'active')
            ->latest()
            ->first();

        if (!$currentSubscription) {
            return response()->json([
                'message' => 'No active subscription found',
            ], 400);
        }

        // Cancel current subscription (soft cancel - ends at current period end)
        $currentSubscription->update([
            'stripe_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Create new subscription
        $newSubscription = Subscription::create([
            'user_id' => $user->id,
            'vertical_bundle_id' => $newBundle->id,
            'stripe_status' => 'active',
            'monthly_price' => $newBundle->monthly_price,
            'starts_at' => now(),
        ]);

        // Update user
        $user->update([
            'vertical_type' => $newBundle->slug,
        ]);

        return response()->json([
            'message' => 'Subscription upgraded successfully',
            'new_subscription' => $newSubscription->load('verticalBundle'),
        ]);
    }

    /**
     * Get user's current active bundle.
     */
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $subscription = $user->subscriptions()
            ->where('stripe_status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription',
                'bundle' => null,
            ], 404);
        }

        return response()->json([
            'bundle' => $subscription->verticalBundle,
            'subscription' => $subscription,
        ]);
    }
}
