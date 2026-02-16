<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveApproval extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'leave_approvals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'leave_id',
        'role',
        'approval_order',
        'signature_path',
        'approved_at',
        'hak_prev',
        'hak_curr',
        'total_hak',
        'taken_until',
        'sisa_curr',
        'request_days',
        'sisa_after',
        'approver_email',
        'approver_name',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'approval_order' => 'integer',
        'approved_at' => 'datetime',
        'hak_prev' => 'decimal:1',
        'hak_curr' => 'decimal:1',
        'total_hak' => 'decimal:1',
        'taken_until' => 'decimal:1',
        'sisa_curr' => 'decimal:1',
        'request_days' => 'decimal:1',
        'sisa_after' => 'decimal:1',
    ];

    /**
     * Disable timestamps since table has custom columns
     */
    public $timestamps = false;

    /**
     * Get the leave request this approval belongs to.
     */
    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_id');
    }

    /**
     * Check if this approval is signed
     */
    public function isSigned(): bool
    {
        return !empty($this->signature_path) && !empty($this->approved_at);
    }
}
