<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'ALYSA50K',
                'name' => 'Potongan Langsung Rp 50.000',
                'type' => 'fixed',
                'amount' => 50000,
                'min_spend' => 300000,
                'max_discount' => null,
                'usage_limit' => 100,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SPRING20',
                'name' => 'Diskon Spesial 20%',
                'type' => 'percentage',
                'amount' => 20,
                'min_spend' => 250000,
                'max_discount' => 100000,
                'usage_limit' => 50,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SUPEROPTIC',
                'name' => 'Diskon Paket Lensa & Frame Rp 75.000',
                'type' => 'fixed',
                'amount' => 75000,
                'min_spend' => 450000,
                'max_discount' => null,
                'usage_limit' => 200,
                'used_count' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $v) {
            Voucher::updateOrCreate(['code' => $v['code']], $v);
        }
    }
}
