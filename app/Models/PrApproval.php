<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrApproval extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'pr_approvals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pr_id',
        'role',
        'approval_order',
        'signature_path',
        'pr_number',
        'approved_at',
        'approver_email',
        'approver_name',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'approval_order' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Disable timestamps since table has custom columns
     */
    public $timestamps = false;

    /**
     * Get the purchase requisition this approval belongs to.
     */
    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'pr_id');
    }

    /**
     * Check if this approval is signed
     */
    public function isSigned(): bool
    {
        return !empty($this->signature_path) && !empty($this->approved_at);
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        $roles = [
            'koordinator' => 'Koordinator',
            'accounting' => 'Accounting',
            'ketua_yayasan' => 'Ketua Yayasan',
        ];

        return $roles[$this->role] ?? $this->role;
    }
}
