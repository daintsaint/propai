<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentInstance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'agent_type',
        'config_json',
        'active',
        'last_run_at',
        'status',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'config_json' => 'array',
        'active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    /**
     * Get the user that owns the agent instance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active agents.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to get agents by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('agent_type', $type);
    }

    /**
     * Mark the agent as running.
     */
    public function markAsRunning(): void
    {
        $this->update([
            'status' => 'running',
            'last_run_at' => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Mark the agent as idle.
     */
    public function markAsIdle(): void
    {
        $this->update(['status' => 'idle']);
    }

    /**
     * Mark the agent as having an error.
     */
    public function markAsError(string $message): void
    {
        $this->update([
            'status' => 'error',
            'error_message' => $message,
        ]);
    }
}
