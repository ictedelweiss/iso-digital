<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'location_id',
        'serial_number',
        'model',
        'manufacturer',
        'purchase_date',
        'purchase_price',
        'status',
        'condition',
        'description',
        'notes',
        'qr_code',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    /**
     * Boot method untuk observers
     */
    protected static function booted(): void
    {
        // Saat asset akan dibuat
        static::creating(function (Asset $asset) {
            // Generate asset code jika belum ada
            if (empty($asset->asset_code)) {
                $asset->asset_code = $asset->generateAssetCode();
            }

            // Set created_by
            if (Auth::check()) {
                $asset->created_by = Auth::id();
            }
        });

        // Setelah asset dibuat
        static::created(function (Asset $asset) {
            // Generate QR code
            $asset->generateQrCode();
            $asset->saveQuietly(); // Save tanpa trigger event lagi

            // Log ke history
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'CREATE',
                'field_name' => null,
                'old_value' => null,
                'new_value' => 'Asset created',
                'changed_by' => Auth::id() ?? 1,
            ]);
        });

        // Saat asset akan diupdate
        static::updating(function (Asset $asset) {
            // Set updated_by
            if (Auth::check()) {
                $asset->updated_by = Auth::id();
            }
        });

        // Setelah asset diupdate
        static::updated(function (Asset $asset) {
            $changes = $asset->getChanges();
            $original = $asset->getOriginal();

            // Log semua changes kecuali updated_at dan qr_code
            foreach ($changes as $field => $newValue) {
                if (in_array($field, ['updated_at', 'qr_code', 'updated_by'])) {
                    continue;
                }

                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'action' => 'UPDATE',
                    'field_name' => $field,
                    'old_value' => $original[$field] ?? null,
                    'new_value' => $newValue,
                    'changed_by' => Auth::id() ?? 1,
                ]);
            }
        });

        // Saat asset dihapus
        static::deleted(function (Asset $asset) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'DELETE',
                'field_name' => null,
                'old_value' => null,
                'new_value' => 'Asset deleted',
                'changed_by' => Auth::id() ?? 1,
            ]);
        });

        // Saat asset direstore
        static::restored(function (Asset $asset) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => 'RESTORE',
                'field_name' => null,
                'old_value' => null,
                'new_value' => 'Asset restored',
                'changed_by' => Auth::id() ?? 1,
            ]);
        });
    }

    /**
     * Generate asset code dengan format: PREFIX-LOC-YYYY-NNNNNN
     */
    public function generateAssetCode(): string
    {
        $category = Category::find($this->category_id);
        $location = Location::find($this->location_id);

        $prefix = $category?->prefix ?? 'XXX';
        $locationCode = $location?->code ?? 'XXX';
        $year = date('Y');

        // Get next sequence untuk kategori dan tahun ini
        $sequence = $this->getNextSequence($this->category_id, $year);

        return sprintf('%s-%s-%s-%06d', $prefix, $locationCode, $year, $sequence);
    }

    /**
     * Get nomor urut berikutnya untuk kategori dan tahun tertentu
     */
    private function getNextSequence(int $categoryId, string $year): int
    {
        $category = Category::find($categoryId);
        if (!$category) {
            return 1;
        }

        $prefix = $category->prefix;

        // Cari asset terakhir dengan prefix dan tahun yang sama
        $lastAsset = static::where('asset_code', 'LIKE', "{$prefix}%-{$year}-%")
            ->orderBy('asset_code', 'desc')
            ->first();

        if (!$lastAsset) {
            return 1;
        }

        // Extract nomor urut dari asset code
        // Format: PREFIX-LOC-YYYY-NNNNNN
        $parts = explode('-', $lastAsset->asset_code);
        $lastSequence = (int) end($parts);

        return $lastSequence + 1;
    }

    /**
     * Generate QR code untuk asset
     */
    public function generateQrCode(): void
    {
        $qrData = json_encode([
            'asset_code' => $this->asset_code,
            'name' => $this->name,
            'category' => $this->category?->name,
            'location' => $this->location?->name,
        ]);

        // Generate QR code sebagai SVG (tidak memerlukan imagick)
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrData);

        // Store sebagai base64 SVG
        $this->qr_code = base64_encode($qrCode);
    }

    /**
     * Relationships
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AssetHistory::class, 'asset_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'Maintenance');
    }

    public function scopeRetired($query)
    {
        return $query->where('status', 'Retired');
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByLocation($query, int $locationId)
    {
        return $query->where('location_id', $locationId);
    }
}
