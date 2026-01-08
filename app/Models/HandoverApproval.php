<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandoverApproval extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'handover_approvals';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'handover_id',
        'role',
        'approval_order',
        'signature_path',
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
     * Get the handover form this approval belongs to.
     */
    public function handoverForm(): BelongsTo
    {
        return $this->belongsTo(HandoverForm::class, 'handover_id');
    }

    /**
     * Check if this approval is signed
     */
    public function isSigned(): bool
    {
        return !empty($this->signature_path) && !empty($this->approved_at);
    }
}
