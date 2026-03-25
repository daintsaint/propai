<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\VerticalBundle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Get user's subscription details.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $subscription = $user->subscriptions()
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No subscription found',
            ], 404);
        }

        return response()->json([
            'subscription' => $subscription->load('verticalBundle'),
        ]);
    }

    /**
     * Cancel user's subscription.
     */
    public function cancel(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $subscription = $user->subscriptions()
            ->where('stripe_status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription to cancel',
            ], 404);
        }

        // Cancel at period end (soft cancel)
        $subscription->update([
            'stripe_status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Update user status if this was the only active subscription
        $hasActiveSubscription = $user->subscriptions()
            ->whereIn('stripe_status', ['active', 'trialing'])
            ->exists();

        if (!$hasActiveSubscription) {
            $user->update([
                'subscription_status' => 'cancelled',
            ]);
        }

        return response()->json([
            'message' => 'Subscription cancelled successfully. Access will continue until the end of the billing period.',
            'subscription' => $subscription->load('verticalBundle'),
        ]);
    }

    /**
     * Reactivate a cancelled subscription.
     */
    public function reactivate(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $subscription = $user->subscriptions()
            ->where('stripe_status', 'cancelled')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No cancelled subscription found',
            ], 404);
        }

        $subscription->update([
            'stripe_status' => 'active',
            'cancelled_at' => null,
        ]);

        $user->update([
            'subscription_status' => 'active',
        ]);

        return response()->json([
            'message' => 'Subscription reactivated successfully',
            'subscription' => $subscription->load('verticalBundle'),
        ]);
    }

    /**
     * Handle Stripe webhooks.
     * Note: This route should be protected by Stripe webhook signature verification middleware.
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? null;
        $data = $payload['data']['object'] ?? [];

        Log::info('Stripe webhook received', [
            'type' => $eventType,
            'subscription_id' => $data['id'] ?? null,
        ]);

        try {
            switch ($eventType) {
                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($data);
                    break;

                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($data);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($data);
                    break;

                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($data);
                    break;

                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($data);
                    break;

                default:
                    Log::info('Unhandled webhook event type: ' . $eventType);
            }

            return response()->json(['message' => 'Webhook processed successfully']);
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
                'event_type' => $eventType,
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle subscription.created event.
     */
    private function handleSubscriptionCreated(array $data): void
    {
        $stripeSubscriptionId = $data['id'];
        $status = $data['status'];
        $metadata = $data['metadata'] ?? [];
        
        // Find existing subscription or create new one
        $subscription = Subscription::firstOrCreate(
            ['stripe_subscription_id' => $stripeSubscriptionId],
            [
                'user_id' => $metadata['user_id'] ?? null,
                'vertical_bundle_id' => $metadata['bundle_id'] ?? null,
                'stripe_status' => $status,
                'monthly_price' => ($data['plan']['amount'] ?? 0) / 100,
            ]
        );

        if ($status === 'active') {
            $subscription->update([
                'starts_at' => now(),
            ]);

            if ($subscription->user) {
                $subscription->user->update([
                    'subscription_status' => 'active',
                ]);
            }
        }

        Log::info('Subscription created', ['subscription_id' => $subscription->id]);
    }

    /**
     * Handle subscription.updated event.
     */
    private function handleSubscriptionUpdated(array $data): void
    {
        $stripeSubscriptionId = $data['id'];
        $status = $data['status'];

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for update', ['stripe_id' => $stripeSubscriptionId]);
            return;
        }

        $updateData = ['stripe_status' => $status];

        if (isset($data['cancel_at'])) {
            $updateData['ends_at'] = \Carbon\Carbon::createFromTimestamp($data['cancel_at']);
        }

        $subscription->update($updateData);

        Log::info('Subscription updated', [
            'subscription_id' => $subscription->id,
            'new_status' => $status,
        ]);
    }

    /**
     * Handle subscription.deleted event.
     */
    private function handleSubscriptionDeleted(array $data): void
    {
        $stripeSubscriptionId = $data['id'];

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for deletion', ['stripe_id' => $stripeSubscriptionId]);
            return;
        }

        $subscription->update([
            'stripe_status' => 'cancelled',
            'cancelled_at' => now(),
            'ends_at' => now(),
        ]);

        if ($subscription->user) {
            // Check if user has any other active subscriptions
            $hasActiveSubscription = $subscription->user->subscriptions()
                ->whereIn('stripe_status', ['active', 'trialing'])
                ->exists();

            if (!$hasActiveSubscription) {
                $subscription->user->update([
                    'subscription_status' => 'expired',
                ]);
            }
        }

        Log::info('Subscription deleted', ['subscription_id' => $subscription->id]);
    }

    /**
     * Handle payment_succeeded event.
     */
    private function handlePaymentSucceeded(array $data): void
    {
        $subscriptionId = $data['subscription'] ?? null;

        if (!$subscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

        if ($subscription && $subscription->stripe_status === 'past_due') {
            $subscription->update([
                'stripe_status' => 'active',
            ]);

            if ($subscription->user) {
                $subscription->user->update([
                    'subscription_status' => 'active',
                ]);
            }

            Log::info('Payment succeeded, subscription reactivated', ['subscription_id' => $subscription->id]);
        }
    }

    /**
     * Handle payment_failed event.
     */
    private function handlePaymentFailed(array $data): void
    {
        $subscriptionId = $data['subscription'] ?? null;

        if (!$subscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->update([
                'stripe_status' => 'past_due',
            ]);

            Log::info('Payment failed, subscription marked as past_due', ['subscription_id' => $subscription->id]);
        }
    }
}
