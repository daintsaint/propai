<?php

namespace App\Services;

use App\Models\AgentInstance;
use App\Models\User;
use App\Models\VerticalBundle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AgentService
{
    /**
     * Create a new agent instance for a user.
     * Provisions the agent with default configuration based on vertical bundle.
     */
    public function createAgentInstance(
        User $user,
        string $agentType,
        array $config = [],
        bool $active = true
    ): AgentInstance {
        // Verify user has an active subscription
        if (!$user->hasActiveSubscription()) {
            throw new \Exception('User must have an active subscription to create agents');
        }

        // Get vertical bundle to determine allowed agent types
        $subscription = $user->activeSubscription();
        $bundle = VerticalBundle::where('slug', $subscription->vertical_bundle)->first();

        if (!$bundle) {
            throw new \Exception('Invalid subscription bundle');
        }

        // Validate agent type is allowed for this bundle
        $allowedAgents = $bundle->features['agents'] ?? [];
        if (!empty($allowedAgents) && !in_array($agentType, $allowedAgents)) {
            throw new \Exception("Agent type '{$agentType}' is not included in your subscription");
        }

        // Merge default config with custom config
        $defaultConfig = $this->getDefaultAgentConfig($agentType, $bundle);
        $finalConfig = array_merge($defaultConfig, $config);

        // Create agent instance
        $agent = AgentInstance::create([
            'user_id' => $user->id,
            'agent_type' => $agentType,
            'name' => $this->generateAgentName($agentType),
            'config_json' => $finalConfig,
            'status' => 'active',
            'active' => $active,
            'vertical_bundle' => $bundle->slug,
        ]);

        Log::info('Agent instance created', [
            'agent_id' => $agent->id,
            'user_id' => $user->id,
            'agent_type' => $agentType
        ]);

        // Optionally trigger initial setup via Nebula webhook
        $this->triggerAgentSetup($agent);

        return $agent;
    }

    /**
     * Trigger an agent workflow with specific action and parameters.
     * Sends request to Nebula to execute the agent.
     */
    public function triggerAgent(
        AgentInstance $agent,
        string $action,
        array $parameters = []
    ): array {
        if (!$agent->active) {
            throw new \Exception('Agent is not active');
        }

        // Prepare payload for Nebula
        $payload = [
            'agent_id' => $agent->id,
            'agent_type' => $agent->agent_type,
            'user_id' => $agent->user_id,
            'action' => $action,
            'parameters' => $parameters,
            'config' => $agent->config_json,
            'callback_url' => route('api.webhooks.nebula'),
        ];

        // Send to Nebula webhook
        $response = $this->sendToNebula($payload);

        // Update agent status
        $agent->update([
            'last_run_at' => now(),
            'status' => 'running',
            'run_count' => $agent->run_count + 1,
        ]);

        Log::info('Agent triggered', [
            'agent_id' => $agent->id,
            'action' => $action,
            'nebula_response' => $response['status'] ?? 'pending'
        ]);

        return [
            'success' => true,
            'agent_id' => $agent->id,
            'action' => $action,
            'status' => 'queued',
            'nebula_task_id' => $response['task_id'] ?? null,
        ];
    }

    /**
     * Check the health and status of an agent.
     * Returns current status, last run info, and any errors.
     */
    public function getAgentStatus(AgentInstance $agent): array
    {
        $status = [
            'id' => $agent->id,
            'name' => $agent->name,
            'type' => $agent->agent_type,
            'active' => $agent->active,
            'status' => $agent->status,
            'last_run_at' => $agent->last_run_at,
            'last_run_status' => $agent->last_run_status,
            'last_error' => $agent->last_error,
            'run_count' => $agent->run_count,
            'created_at' => $agent->created_at,
            'health' => 'healthy',
        ];

        // Determine health status
        if (!$agent->active) {
            $status['health'] = 'inactive';
        } elseif ($agent->status === 'failed') {
            // Check if last failure was recent (within 1 hour)
            $lastRun = $agent->last_run_at;
            if ($lastRun && $lastRun->diffInMinutes(now()) < 60) {
                $status['health'] = 'unhealthy';
            } else {
                $status['health'] = 'degraded';
            }
        } elseif ($agent->status === 'running') {
            // Check if running for too long (potential hang)
            $lastRun = $agent->last_run_at;
            if ($lastRun && $lastRun->diffInMinutes(now()) > 30) {
                $status['health'] = 'potentially_stuck';
            }
        }

        // Fetch latest metrics from Nebula if available
        $metrics = $this->fetchAgentMetrics($agent);
        if ($metrics) {
            $status['metrics'] = $metrics;
        }

        return $status;
    }

    /**
     * Get all agents for a user with their status.
     */
    public function getUserAgents(User $user): array
    {
        $agents = AgentInstance::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $agents->map(function ($agent) {
            return $this->getAgentStatus($agent);
        })->toArray();
    }

    /**
     * Update agent configuration.
     */
    public function updateAgentConfig(
        AgentInstance $agent,
        array $config,
        bool $merge = true
    ): AgentInstance {
        if ($merge) {
            $finalConfig = array_merge($agent->config_json, $config);
        } else {
            $finalConfig = $config;
        }

        $agent->update([
            'config_json' => $finalConfig,
        ]);

        Log::info('Agent config updated', [
            'agent_id' => $agent->id,
            'config_keys' => array_keys($finalConfig)
        ]);

        return $agent->fresh();
    }

    /**
     * Pause an agent (set inactive without deleting).
     */
    public function pauseAgent(AgentInstance $agent): bool
    {
        return $agent->update([
            'active' => false,
            'status' => 'paused',
        ]);
    }

    /**
     * Resume a paused agent.
     */
    public function resumeAgent(AgentInstance $agent): bool
    {
        return $agent->update([
            'active' => true,
            'status' => 'active',
        ]);
    }

    /**
     * Delete an agent instance.
     */
    public function deleteAgent(AgentInstance $agent): bool
    {
        // Notify Nebula to clean up any running instances
        $this->notifyAgentDeletion($agent);

        return $agent->delete();
    }

    // Private helper methods

    /**
     * Get default configuration for an agent type.
     */
    private function getDefaultAgentConfig(string $agentType, VerticalBundle $bundle): array
    {
        $defaults = [
            'real_estate_agent' => [
                'lead_response_time' => '5 minutes',
                'follow_up_sequence' => [1, 3, 7], // days
                'crm_integration' => null,
                'email_signature' => '',
            ],
            'ecommerce_agent' => [
                'inventory_check_interval' => '1 hour',
                'low_stock_threshold' => 10,
                'order_notification' => true,
                'customer_service_mode' => 'auto',
            ],
            'local_business_agent' => [
                'appointment_reminder' => true,
                'review_monitoring' => true,
                'social_media_posting' => false,
            ],
            'professional_services_agent' => [
                'time_tracking' => true,
                'invoice_generation' => 'weekly',
                'client_communication' => 'email',
            ],
            'saas_agent' => [
                'user_onboarding' => true,
                'churn_monitoring' => true,
                'feature_usage_tracking' => true,
            ],
        ];

        return $defaults[$agentType] ?? [
            'mode' => 'standard',
            'notifications' => true,
        ];
    }

    /**
     * Generate a friendly name for the agent.
     */
    private function generateAgentName(string $agentType): string
    {
        $names = [
            'real_estate_agent' => 'Property Assistant',
            'ecommerce_agent' => 'Store Manager',
            'local_business_agent' => 'Business Helper',
            'professional_services_agent' => 'Practice Assistant',
            'saas_agent' => 'Growth Agent',
        ];

        $baseName = $names[$agentType] ?? 'AI Agent';
        $suffix = Str::random(4);

        return "{$baseName} {$suffix}";
    }

    /**
     * Send payload to Nebula for agent execution.
     */
    private function sendToNebula(array $payload): array
    {
        $nebulaUrl = config('services.nebula.base_url') . '/tasks';
        $apiKey = config('services.nebula.api_key');

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($nebulaUrl, $payload);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to send to Nebula', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    /**
     * Trigger initial setup for a new agent.
     */
    private function triggerAgentSetup(AgentInstance $agent): void
    {
        // Send setup request to Nebula
        $payload = [
            'agent_id' => $agent->id,
            'action' => 'setup',
            'parameters' => [
                'vertical' => $agent->vertical_bundle,
                'config' => $agent->config_json,
            ],
            'callback_url' => route('api.webhooks.nebula'),
        ];

        $this->sendToNebula($payload);
    }

    /**
     * Fetch latest metrics from Nebula for an agent.
     */
    private function fetchAgentMetrics(AgentInstance $agent): ?array
    {
        $nebulaUrl = config('services.nebula.base_url') . "/agents/{$agent->id}/metrics";
        $apiKey = config('services.nebula.api_key');

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])->get($nebulaUrl);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::debug('Failed to fetch agent metrics', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Notify Nebula when an agent is deleted.
     */
    private function notifyAgentDeletion(AgentInstance $agent): void
    {
        $nebulaUrl = config('services.nebula.base_url') . "/agents/{$agent->id}";
        $apiKey = config('services.nebula.api_key');

        try {
            Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
            ])->delete($nebulaUrl);
        } catch (\Exception $e) {
            Log::error('Failed to notify Nebula of agent deletion', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
