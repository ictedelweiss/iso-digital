<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrSupportingDocument extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'pr_supporting_documents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pr_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    /**
     * Disable timestamps - use uploaded_at instead
     */
    public $timestamps = false;

    /**
     * Get the purchase requisition this document belongs to.
     */
    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'pr_id');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
