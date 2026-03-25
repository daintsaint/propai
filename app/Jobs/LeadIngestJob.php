<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeadIngestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $leads;
    protected int $userId;
    protected int $deduplicationWindow = 30; // days

    /**
     * Create a new job instance.
     */
    public function __construct(array $leads, int $userId)
    {
        $this->leads = $leads;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Lead ingestion job started', [
            'user_id' => $this->userId,
            'lead_count' => count($this->leads)
        ]);

        $results = [
            'imported' => 0,
            'duplicates' => 0,
            'failed' => 0,
        ];

        // Process leads in batches to avoid memory issues
        $batches = array_chunk($this->leads, 50);

        foreach ($batches as $batch) {
            $batchResults = $this->processBatch($batch);
            $results['imported'] += $batchResults['imported'];
            $results['duplicates'] += $batchResults['duplicates'];
            $results['failed'] += $batchResults['failed'];
        }

        Log::info('Lead ingestion job completed', [
            'user_id' => $this->userId,
            'results' => $results
        ]);

        // Send summary notification if there were imported leads
        if ($results['imported'] > 0) {
            $this->sendImportNotification($results);
        }
    }

    /**
     * Process a batch of leads.
     */
    protected function processBatch(array $batch): array
    {
        $results = [
            'imported' => 0,
            'duplicates' => 0,
            'failed' => 0,
        ];

        foreach ($batch as $leadData) {
            try {
                // Validate lead data
                if (!$this->validateLead($leadData)) {
                    $results['failed']++;
                    continue;
                }

                // Normalize email for deduplication
                $email = strtolower(trim($leadData['email']));

                // Check for duplicates
                if ($this->isDuplicate($email, $this->userId)) {
                    $results['duplicates']++;
                    Log::debug('Duplicate lead skipped', [
                        'email' => $email,
                        'user_id' => $this->userId
                    ]);
                    continue;
                }

                // Create lead
                $lead = Lead::create([
                    'user_id' => $this->userId,
                    'name' => $leadData['name'],
                    'email' => $email,
                    'phone' => $leadData['phone'] ?? null,
                    'company' => $leadData['company'] ?? null,
                    'status' => 'new',
                    'source' => $leadData['source'] ?? 'webhook',
                    'notes' => $leadData['notes'] ?? null,
                    'metadata' => $this->extractMetadata($leadData),
                ]);

                $results['imported']++;

                Log::info('Lead imported via batch job', [
                    'lead_id' => $lead->id,
                    'email' => $email,
                    'user_id' => $this->userId
                ]);

                // Trigger follow-up actions for new leads
                $this->triggerLeadActions($lead);

            } catch (\Exception $e) {
                $results['failed']++;
                Log::error('Failed to import lead', [
                    'error' => $e->getMessage(),
                    'data' => $leadData
                ]);
            }
        }

        return $results;
    }

    /**
     * Validate lead data structure.
     */
    protected function validateLead(array $leadData): bool
    {
        // Required fields
        if (empty($leadData['email'])) {
            return false;
        }

        // Validate email format
        if (!filter_var($leadData['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Name is optional but if provided, should be a string
        if (isset($leadData['name']) && !is_string($leadData['name'])) {
            return false;
        }

        return true;
    }

    /**
     * Check if lead is a duplicate within the deduplication window.
     */
    protected function isDuplicate(string $email, int $userId): bool
    {
        $exists = Lead::where('user_id', $userId)
            ->where('email', $email)
            ->where('created_at', '>=', now()->subDays($this->deduplicationWindow))
            ->exists();

        return $exists;
    }

    /**
     * Extract additional metadata from lead data.
     */
    protected function extractMetadata(array $leadData): array
    {
        $allowedFields = [
            'budget',
            'timeline',
            'interest_level',
            'referrer',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'ip_address',
            'user_agent',
        ];

        $metadata = [];
        foreach ($allowedFields as $field) {
            if (isset($leadData[$field])) {
                $metadata[$field] = $leadData[$field];
            }
        }

        return $metadata;
    }

    /**
     * Trigger follow-up actions for a new lead.
     */
    protected function triggerLeadActions(Lead $lead): void
    {
        // Dispatch notification job
        // LeadNotificationJob::dispatch($lead);

        // If user has active agents, trigger lead processing
        $user = User::find($this->userId);
        if ($user && $user->hasActiveSubscription()) {
            // Trigger agent to process the new lead
            // This would typically call AgentService to execute a lead processing workflow
            Log::info('Lead ready for agent processing', [
                'lead_id' => $lead->id,
                'user_id' => $this->userId
            ]);
        }
    }

    /**
     * Send import summary notification to user.
     */
    protected function sendImportNotification(array $results): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        $subject = "Lead Import Complete: {$results['imported']} imported";
        $message = "Hi {$user->name},\n\n";
        $message .= "Your lead import has completed:\n";
        $message .= "- Imported: {$results['imported']}\n";
        $message .= "- Duplicates skipped: {$results['duplicates']}\n";
        $message .= "- Failed: {$results['failed']}\n\n";
        $message .= "You can view your leads in the dashboard.\n\n";
        $message .= "Best regards,\nPropAI Team";

        // Send email notification
        // Mail::to($user->email)->send(new LeadImportNotification($subject, $message));

        Log::info('Lead import notification sent', [
            'user_id' => $this->userId,
            'email' => $user->email
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Lead ingestion job failed', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Send failure notification to user
        $user = User::find($this->userId);
        if ($user) {
            $subject = 'Lead Import Failed';
            $message = "Hi {$user->name},\n\n";
            $message .= "Your lead import encountered an error: {$exception->getMessage()}\n\n";
            $message .= "Please try again or contact support if the issue persists.\n\n";
            $message .= "Best regards,\nPropAI Team";

            // Mail::to($user->email)->send(new LeadImportNotification($subject, $message));
        }
    }
}
