<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'gender',
        'birth_date',
        'sph_r',
        'cyl_r',
        'axis_r',
        'sph_l',
        'cyl_l',
        'axis_l',
        'pd',
        'add_r',
        'add_l',
        'total_orders',
        'total_spent',
        'last_visit_at',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_visit_at' => 'datetime',
        'total_spent' => 'decimal:2',
    ];

    /**
     * Cari riwayat semua order berdasarkan nomor HP pelanggan
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_phone', 'phone');
    }
}
