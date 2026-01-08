<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HandoverForm extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'handover_forms';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'item_name',
        'handover_date',
        'recipient_name',
        'recipient_email',
        'recipient_department',
        'quantity',
        'serial_number',
        'item_condition',
        'notes',
        'ict_signature_path',
        'current_approval_step',
        'status',
        'created_by',
        'specification',
        'loan_period',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'handover_date' => 'date',
        'quantity' => 'integer',
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
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';

    /**
     * Get the creator of this handover form.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the approvals for this handover form.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(HandoverApproval::class, 'handover_id');
    }

    /**
     * Check if handover is fully approved
     */
    public function isFullyApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
