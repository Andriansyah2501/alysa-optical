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
        'examiner_name', 'user_id', 'share_token', 'wa_sent_at', 'wa_status',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'wa_sent_at' => 'datetime',
    ];

    /** Token link share publik (untuk QR & kirim WA). */
    public function ensureShareToken(): string
    {
        if (!$this->share_token) {
            $this->forceFill(['share_token' => bin2hex(random_bytes(12))])->save();
        }
        return $this->share_token;
    }

    public function shareUrlHash(): string
    {
        return $this->ensureShareToken();
    }

    public function shareUrl(): string
    {
        return url('/r/' . $this->ensureShareToken());
    }

    public function sharePdfUrl(): string
    {
        return url('/r/' . $this->ensureShareToken() . '/pdf');
    }

    /** Nama file PDF seragam: Kartu_Resep_<kode>_<tgl periksa Y-m-d>.pdf */
    public function pdfFileName(): string
    {
        $tgl = $this->exam_date ? \Carbon\Carbon::parse($this->exam_date)->format('Y-m-d') : date('Y-m-d');
        return 'Kartu_Resep_' . $this->card_code . '_' . $tgl . '.pdf';
    }

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
