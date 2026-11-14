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
        'description', 'image_url', 'is_featured', 'is_bestseller'
    ];

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
