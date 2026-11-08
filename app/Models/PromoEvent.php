<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PromoEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'badge',
        'description',
        'discount_info',
        'voucher_code',
        'start_date',
        'end_date',
        'color_theme',
        'target_audience',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function getStatusAttribute()
    {
        $today = Carbon::today();
        if (!$this->is_active) {
            return 'nonaktif';
        }
        if ($today->lt($this->start_date)) {
            return 'upcoming';
        }
        if ($today->gt($this->end_date)) {
            return 'expired';
        }
        return 'running';
    }
}
