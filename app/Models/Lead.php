<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'status',
        'source',
        'notes',
        'metadata',
        'last_contacted_at',
        'converted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'last_contacted_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the lead.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get leads by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get uncontacted leads.
     */
    public function scopeUncontacted($query)
    {
        return $query->where('last_contacted_at', null);
    }

    /**
     * Scope to get new leads.
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Mark the lead as contacted.
     */
    public function markAsContacted(): void
    {
        $this->update([
            'last_contacted_at' => now(),
            'status' => 'contacted',
        ]);
    }

    /**
     * Mark the lead as converted.
     */
    public function markAsConverted(): void
    {
        $this->update([
            'converted_at' => now(),
            'status' => 'converted',
        ]);
    }

    /**
     * Check if lead is new.
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    /**
     * Check if lead is converted.
     */
    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }
}
