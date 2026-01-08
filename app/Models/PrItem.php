<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrItem extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'pr_items';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pr_id',
        'item_name',
        'qty',
        'unit',
        'price',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Disable updated_at since table doesn't have it
     */
    const UPDATED_AT = null;

    /**
     * Get the purchase requisition this item belongs to.
     */
    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'pr_id');
    }

    /**
     * Get subtotal for this item
     */
    public function getSubtotalAttribute(): float
    {
        return $this->qty * $this->price;
    }
}
