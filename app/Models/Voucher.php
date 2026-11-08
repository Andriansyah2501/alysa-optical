<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'amount',
        'min_spend',
        'max_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function isValidFor($amount): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Voucher promo ini sudah tidak aktif.'];
        }

        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return ['valid' => false, 'message' => 'Voucher ini baru bisa digunakan mulai ' . $this->start_date->format('d/m/Y') . '.'];
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return ['valid' => false, 'message' => 'Masa berlaku voucher promo ini sudah berakhir.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Batas kuota penggunaan voucher promo ini telah habis.'];
        }

        if ($amount < $this->min_spend) {
            return [
                'valid' => false,
                'message' => 'Minimal belanja untuk voucher ini adalah Rp ' . number_format($this->min_spend, 0, ',', '.') . '.'
            ];
        }

        // Hitung nilai diskon
        $discount = 0;
        if ($this->type === 'percentage') {
            $discount = ($amount * $this->amount) / 100;
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = min($this->amount, $amount);
        }

        return [
            'valid' => true,
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'rate' => $this->amount,
            'discount' => round($discount),
            'message' => 'Voucher promo "' . $this->name . '" berhasil dipasang!'
        ];
    }
}
