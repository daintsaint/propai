<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\AgentInstance;
use App\Services\AgentService;
use App\Jobs\LeadIngestJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    protected AgentService $agentService;

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Handle incoming webhook events from Nebula automations.
     * Processes agent execution results, status updates, and notifications.
     */
    public function handleNebulaWebhook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string',
            'agent_id' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'payload' => 'required|array',
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid webhook payload',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify webhook signature (implement your signature verification logic)
        $signature = $request->header('X-Nebula-Signature');
        if (!$this->verifySignature($request->getContent(), $signature)) {
            Log::warning('Invalid Nebula webhook signature', ['ip' => $request->ip()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature'
            ], 401);
        }

        $eventType = $request->input('event_type');
        $payload = $request->input('payload');
        $agentId = $request->input('agent_id');
        $userId = $request->input('user_id');

        Log::info('Nebula webhook received', [
            'event_type' => $eventType,
            'agent_id' => $agentId,
            'user_id' => $userId
        ]);

        // Route to appropriate handler based on event type
        return match($eventType) {
            'agent.completed' => $this->handleAgentCompleted($agentId, $payload),
            'agent.failed' => $this->handleAgentFailed($agentId, $payload),
            'lead.created' => $this->handleLeadCreated($payload),
            'task.completed' => $this->handleTaskCompleted($agentId, $payload),
            default => response()->json([
                'success' => true,
                'message' => 'Event received but not processed',
                'event_type' => $eventType
            ])
        };
    }

    /**
     * Handle Telegram bot webhook messages.
     * Processes incoming messages, commands, and callback queries.
     */
    public function handleTelegramWebhook(Request $request)
    {
        $update = $request->all();

        Log::info('Telegram webhook received', [
            'update_id' => $update['update_id'] ?? null,
            'chat_id' => $update['message']['chat']['id'] ?? null
        ]);

        // Handle different update types
        if (isset($update['message'])) {
            return $this->handleTelegramMessage($update['message']);
        }

        if (isset($update['callback_query'])) {
            return $this->handleCallbackQuery($update['callback_query']);
        }

        return response()->json(['success' => true, 'message' => 'Update received']);
    }

    /**
     * Store incoming leads from webhook sources.
     * Accepts single lead or batch of leads.
     */
    public function storeLead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leads' => 'nullable|array',
            'leads.*.name' => 'required_with:leads|string|max:255',
            'leads.*.email' => 'required_with:leads|email|max:255',
            'leads.*.phone' => 'nullable|string|max:50',
            'leads.*.company' => 'nullable|string|max:255',
            'leads.*.source' => 'nullable|string|max:100',
            'leads.*.notes' => 'nullable|string',
            // Single lead format
            'name' => 'required_without:leads|string|max:255',
            'email' => 'required_without:leads|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle batch lead import
        if ($request->has('leads')) {
            LeadIngestJob::dispatch($request->input('leads'), $request->input('user_id'));

            return response()->json([
                'success' => true,
                'message' => 'Leads queued for processing',
                'count' => count($request->input('leads'))
            ]);
        }

        // Handle single lead
        $leadData = $request->only(['name', 'email', 'phone', 'company', 'source', 'notes', 'user_id']);
        $leadData['status'] = 'new';

        $lead = Lead::create($leadData);

        Log::info('Lead created via webhook', ['lead_id' => $lead->id]);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'lead' => $lead
        ], 201);
    }

    /**
     * Trigger an agent workflow manually.
     * Used for on-demand agent execution.
     */
    public function triggerAgentAction(Request $request, string $agentId)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|string',
            'parameters' => 'nullable|array',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify user owns this agent
        $agent = AgentInstance::where('id', $agentId)
            ->where('user_id', $request->input('user_id'))
            ->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found or access denied'
            ], 404);
        }

        if (!$agent->active) {
            return response()->json([
                'success' => false,
                'message' => 'Agent is not active'
            ], 400);
        }

        try {
            $result = $this->agentService->triggerAgent(
                $agent,
                $request->input('action'),
                $request->input('parameters', [])
            );

            return response()->json([
                'success' => true,
                'message' => 'Agent triggered successfully',
                'result' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Agent trigger failed', [
                'agent_id' => $agentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to trigger agent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods

    private function verifySignature(string $payload, ?string $signature): bool
    {
        // Implement signature verification using shared secret
        $secret = config('services.nebula.webhook_secret');
        if (!$secret || !$signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    private function handleAgentCompleted(?string $agentId, array $payload)
    {
        if ($agentId) {
            $agent = AgentInstance::find($agentId);
            if ($agent) {
                $agent->update([
                    'last_run_at' => now(),
                    'status' => 'completed'
                ]);
            }
        }

        // Log completion and trigger any post-completion actions
        Log::info('Agent completed', ['agent_id' => $agentId, 'payload' => $payload]);

        return response()->json([
            'success' => true,
            'message' => 'Agent completion recorded'
        ]);
    }

    private function handleAgentFailed(?string $agentId, array $payload)
    {
        if ($agentId) {
            $agent = AgentInstance::find($agentId);
            if ($agent) {
                $agent->update([
                    'last_run_at' => now(),
                    'status' => 'failed'
                ]);
            }
        }

        Log::error('Agent failed', ['agent_id' => $agentId, 'payload' => $payload]);

        // TODO: Send notification to user about failure

        return response()->json([
            'success' => true,
            'message' => 'Agent failure recorded'
        ]);
    }

    private function handleLeadCreated(array $payload)
    {
        // Process lead created by Nebula agent
        $leadData = array_merge($payload, [
            'status' => 'new',
            'source' => $payload['source'] ?? 'nebula-agent'
        ]);

        Lead::create($leadData);

        Log::info('Lead created from Nebula event', $payload);

        return response()->json([
            'success' => true,
            'message' => 'Lead processed'
        ]);
    }

    private function handleTaskCompleted(?string $agentId, array $payload)
    {
        Log::info('Task completed by agent', [
            'agent_id' => $agentId,
            'task' => $payload['task'] ?? null
        ]);

        // TODO: Update task status, notify user, etc.

        return response()->json([
            'success' => true,
            'message' => 'Task completion recorded'
        ]);
    }

    private function handleTelegramMessage(array $message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        Log::info('Telegram message received', [
            'chat_id' => $chatId,
            'text' => substr($text, 0, 100)
        ]);

        // Process Telegram commands and messages
        // TODO: Implement command handling logic

        return response()->json(['success' => true]);
    }

    private function handleCallbackQuery(array $callbackQuery)
    {
        $data = $callbackQuery['data'] ?? '';
        $messageId = $callbackQuery['message']['message_id'] ?? null;

        Log::info('Telegram callback query', [
            'data' => $data,
            'message_id' => $messageId
        ]);

        // Process inline button clicks
        // TODO: Implement callback handling logic

        return response()->json(['success' => true]);
    }
}
