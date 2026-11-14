<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionCard extends Model
{
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'card_code', 'order_code',
        'customer_name', 'customer_phone', 'gender', 'birth_place', 'birth_date', 'address', 'exam_date',
        'sph_r', 'cyl_r', 'axis_r', 'sph_l', 'cyl_l', 'axis_l', 'add_r', 'add_l', 'pd',
        'vision_goal', 'refraction_method', 'origin', 'referrer_name', 'notes',
        'examiner_name', 'user_id',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public static function generateCode(): string
    {
        return 'RX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    }

    /** Nilai resep terbaca: tampilkan 0.00 bila kosong, + untuk plus. */
    public static function fmtVal(?string $v): string
    {
        if ($v === null || trim($v) === '') return '0.00';
        $n = (float) str_replace(',', '.', $v);
        $s = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        return $s === '' || $s === '-0' ? '0.00' : ($n > 0 ? '+' . $s : $s);
    }
}
