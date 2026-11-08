<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'item_type', 'product_name', 'price', 'quantity', 'total'];

    // Accessor to safely support either $item->total or $item->subtotal in Blade views
    public function getSubtotalAttribute()
    {
        return $this->total ?? ($this->price * $this->quantity);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}