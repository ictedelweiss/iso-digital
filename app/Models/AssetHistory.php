<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetHistory extends Model
{
    use HasFactory;

    protected $table = 'asset_histories';

    // Disable updated_at karena history table hanya perlu created_at
    public $timestamps = false;

    protected $fillable = [
        'asset_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'changed_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Boot method untuk set created_at
     */
    protected static function booted(): void
    {
        static::creating(function (AssetHistory $history) {
            $history->created_at = now();
        });
    }

    /**
     * Relationship: History belongs to Asset
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Relationship: History belongs to User (who made the change)
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get formatted action badge
     */
    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'CREATE' => 'success',
            'UPDATE' => 'warning',
            'DELETE' => 'danger',
            'RESTORE' => 'info',
            default => 'secondary',
        };
    }
}
