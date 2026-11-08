<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'brand', 'frame_shape',
        'frame_material', 'gender', 'price', 'discount_price',
        'stock', 'color_primary', 'color_hex', 'size_frame',
        'description', 'image_url', 'is_featured', 'is_bestseller'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }
}
