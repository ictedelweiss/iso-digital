<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'asset_categories';

    protected $fillable = [
        'name',
        'prefix',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot method untuk auto-uppercase prefix
     */
    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            $category->prefix = strtoupper($category->prefix);
        });
    }

    /**
     * Relationship: Category has many Assets
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    /**
     * Scope: Filter kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get count of assets in this category
     */
    public function getAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }
}
