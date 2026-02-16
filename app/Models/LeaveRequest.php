<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveRequest extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'leave_requests';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'position',
        'department',
        'work_days',
        'start_date',
        'end_date',
        'purpose',
        'note',
        'hak_prev',
        'hak_curr',
        'total_hak',
        'taken_until',
        'sisa_curr',
        'request_days',
        'sisa_after',
        'created_by',
        'signature_pemohon',
        'current_approval_step',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'hak_prev' => 'decimal:2',
        'hak_curr' => 'decimal:2',
        'total_hak' => 'decimal:2',
        'taken_until' => 'decimal:2',
        'sisa_curr' => 'decimal:2',
        'request_days' => 'decimal:2',
        'sisa_after' => 'decimal:2',
        'current_approval_step' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Disable updated_at since table doesn't have it
     */
    const UPDATED_AT = null;

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'Draft';
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';

    /**
     * Get the creator of this leave request.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user's leave quota for current year.
     */
    public function userQuota(): BelongsTo
    {
        return $this->belongsTo(\App\Models\LeaveQuota::class, 'created_by', 'user_id')
            ->where('quota_year', date('Y'));
    }

    /**
     * Get the approvals for this leave request.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(LeaveApproval::class, 'leave_id');
    }

    /**
     * Get leave duration in days
     */
    public function getDurationDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Check if leave request is fully approved
     */
    public function isFullyApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
