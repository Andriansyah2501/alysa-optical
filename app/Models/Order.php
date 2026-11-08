<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'city', 'shipping_courier', 'shipping_cost', 'has_prescription',
        'transaction_type', 'lens_type_name', 'lens_price', 'service_fee', 'service_name', 'voucher_code', 'discount_amount',
        'sph_r', 'cyl_r', 'axis_r', 'sph_l', 'cyl_l', 'axis_l', 'pd',
        'prescription_notes', 'subtotal', 'grand_total', 'payment_type', 'dp_amount', 'remaining_balance', 'settled_at', 'settlement_payment_method', 'payment_method',
        'payment_status', 'order_status', 'tracking_number', 'admin_notes', 'validated_at'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}