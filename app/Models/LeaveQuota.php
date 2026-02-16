<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveQuota extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'leave_quotas';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'employee_name',
        'department',
        'quota_year',
        'previous_year_quota',
        'current_year_quota',
        'quota_used',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quota_year' => 'integer',
        'previous_year_quota' => 'decimal:2',
        'current_year_quota' => 'decimal:2',
        'quota_used' => 'decimal:2',
    ];

    /**
     * Get the user that owns this quota.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get total quota (previous + current year).
     */
    public function getTotalQuotaAttribute(): float
    {
        return (float) ($this->previous_year_quota + $this->current_year_quota);
    }

    /**
     * Get remaining quota.
     */
    public function getRemainingQuotaAttribute(): float
    {
        return max(0, $this->total_quota - $this->quota_used);
    }

    /**
     * Check if quota is sufficient for requested days.
     */
    public function hasSufficientQuota(float $requestedDays): bool
    {
        return $this->remaining_quota >= $requestedDays;
    }

    /**
     * Use quota (increment quota_used).
     */
    public function useQuota(float $days): void
    {
        $this->quota_used += $days;
        $this->save();
    }

    /**
     * Restore quota (decrement quota_used).
     */
    public function restoreQuota(float $days): void
    {
        $this->quota_used = max(0, $this->quota_used - $days);
        $this->save();
    }
}
