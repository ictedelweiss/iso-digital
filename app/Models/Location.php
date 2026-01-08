<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $table = 'asset_locations';

    protected $fillable = [
        'name',
        'code',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot method untuk auto-uppercase code
     */
    protected static function booted(): void
    {
        static::saving(function (Location $location) {
            $location->code = strtoupper($location->code);
        });
    }

    /**
     * Relationship: Location has many Assets
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id');
    }

    /**
     * Scope: Filter lokasi aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get count of assets in this location
     */
    public function getAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }
}
