<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'purchase_requisitions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pr_number',
        'title',
        'requester',
        'department',
        'needed_date',
        'notes',
        'status',
        'budget_status',
        'created_by',
        'requester_signature_path',
        'current_approval_step',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'needed_date' => 'date',
        'created_at' => 'datetime',
        'current_approval_step' => 'integer',
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
     * Get the creator of this PR.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the items for this PR.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PrItem::class, 'pr_id');
    }

    /**
     * Get the approvals for this PR.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(PrApproval::class, 'pr_id');
    }

    /**
     * Get the supporting documents for this PR.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PrSupportingDocument::class, 'pr_id');
    }

    /**
     * Calculate total amount
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->qty * $item->price;
        });
    }

    /**
     * Check if PR is fully approved
     */
    public function isFullyApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Get current approver role based on step
     */
    public function getCurrentApproverRole(): ?string
    {
        $sequence = [
            1 => 'koordinator',
            2 => 'accounting',
            3 => 'ketua_yayasan',
        ];

        return $sequence[$this->current_approval_step] ?? null;
    }
}
