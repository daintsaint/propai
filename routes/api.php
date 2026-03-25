<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VerticalController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;

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

// Webhook routes (public, with signature verification)
Route::post('/webhooks/stripe', [SubscriptionController::class, 'webhook']);
Route::post('/webhooks/nebula', [WebhookController::class, 'handleNebulaWebhook'])->name('api.webhooks.nebula');
Route::post('/webhooks/telegram', [WebhookController::class, 'handleTelegramWebhook']);
Route::post('/leads/ingest', [WebhookController::class, 'storeLead']);

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

    // Agent management routes (require active subscription)
    Route::middleware('subscription.active')->group(function () {
        
        // Agent CRUD operations
        Route::prefix('agents')->group(function () {
            Route::get('/', [DashboardController::class, 'agents']);
            Route::post('/', [DashboardController::class, 'createAgent']);
            Route::get('/{id}', [DashboardController::class, 'getAgent']);
            Route::put('/{id}', [DashboardController::class, 'updateAgent']);
            Route::delete('/{id}', [DashboardController::class, 'deleteAgent']);
            
            // Agent actions
            Route::post('/{id}/trigger', [WebhookController::class, 'triggerAgentAction']);
            Route::post('/{id}/pause', [DashboardController::class, 'pauseAgent']);
            Route::post('/{id}/resume', [DashboardController::class, 'resumeAgent']);
            Route::get('/{id}/status', [DashboardController::class, 'agentStatus']);
            Route::get('/{id}/metrics', [DashboardController::class, 'agentMetrics']);
        });

        // Lead management routes
        Route::prefix('leads')->group(function () {
            Route::get('/', [DashboardController::class, 'leads']);
            Route::post('/', [DashboardController::class, 'createLead']);
            Route::get('/{id}', [DashboardController::class, 'getLead']);
            Route::put('/{id}', [DashboardController::class, 'updateLead']);
            Route::delete('/{id}', [DashboardController::class, 'deleteLead']);
            Route::post('/{id}/note', [DashboardController::class, 'addLeadNote']);
        });
    });
});
