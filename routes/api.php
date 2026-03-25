<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VerticalController;
use App\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Stripe webhook (must be before auth middleware)
Route::post('/webhooks/stripe', [SubscriptionController::class, 'webhook']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/agents', [DashboardController::class, 'agents']);
        Route::get('/agents/active', [DashboardController::class, 'activeAgents']);
        Route::get('/leads', [DashboardController::class, 'leads']);
        Route::get('/leads/{status}', [DashboardController::class, 'leadsByStatus']);
    });

    // Vertical bundle routes
    Route::prefix('verticals')->group(function () {
        Route::get('/', [VerticalController::class, 'index']); // List all bundles
        Route::get('/{slug}', [VerticalController::class, 'show']); // Get specific bundle
        Route::get('/current', [VerticalController::class, 'current']); // Get user's current bundle
        Route::post('/{slug}/activate', [VerticalController::class, 'activate']);
        Route::post('/{slug}/upgrade', [VerticalController::class, 'upgrade']);
    });

    // Subscription routes
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'show']);
        Route::post('/cancel', [SubscriptionController::class, 'cancel']);
        Route::post('/reactivate', [SubscriptionController::class, 'reactivate']);
    });

    // Bundle-specific routes (require active subscription)
    Route::middleware('subscription.active')->group(function () {
        Route::prefix('agents')->group(function () {
            // Agent management routes (to be expanded)
            Route::get('/', [DashboardController::class, 'agents']);
        });

        Route::prefix('leads')->group(function () {
            // Lead management routes (to be expanded)
            Route::get('/', [DashboardController::class, 'leads']);
        });
    });
});
