<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'barcode', 'brand', 'frame_shape',
        'frame_material', 'gender', 'price', 'discount_price',
        'stock', 'color_primary', 'color_hex', 'size_frame',
        'description', 'image_url', 'bundle_lens_type_id', 'is_featured', 'is_bestseller'
    ];

    /** Lensa yang dipaketkan untuk penjualan bundling (null = tidak ada). */
    public function bundleLens(): BelongsTo
    {
        return $this->belongsTo(LensType::class, 'bundle_lens_type_id');
    }

    /** Label bundling utk POS: 'Nama Lensa — hemat Rp X'. Null bila tidak dipaketkan. */
    public function bundleLabel(?float $percent = null): ?string
    {
        if (!$this->bundle_lens_type_id || !$this->bundleLens) {
            return null;
        }
        if ($percent === null) {
            $percent = max(0, min(90, (float) StoreSetting::get('bundle_discount_percent', '10')));
        }
        if ($percent <= 0) {
            return null;
        }
        $hargaSatuan = $this->effective_price + (float) $this->bundleLens->additional_price;
        $hemat = round($hargaSatuan * $percent / 100);
        return $this->bundleLens->name . ' — hemat Rp ' . number_format($hemat, 0, ',', '.');
    }

    protected static function booted(): void
    {
        // Kode barcode otomatis (AG-#####) bila tidak diisi manual
        static::created(function (Product $product) {
            if (empty($product->barcode)) {
                $product->forceFill(['barcode' => sprintf('AG-%05d', $product->id)])->save();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }
}
