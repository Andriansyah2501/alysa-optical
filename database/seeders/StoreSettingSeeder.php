<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Default Berbagai Role
        User::updateOrCreate(
            ['email' => 'admin@alysaglasses.com'],
            [
                'name' => 'Pak Lurah Optik (Super Admin)',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@alysaglasses.com'],
            [
                'name' => 'Siti Kasir Alysa',
                'password' => Hash::make('kasir123'),
                'role' => 'cashier',
                'phone' => '081298765432',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'refraksi@alysaglasses.com'],
            [
                'name' => 'Bambang Refraksionis A.Md.RO',
                'password' => Hash::make('refraksi123'),
                'role' => 'optician',
                'phone' => '081377889900',
                'is_active' => true,
            ]
        );

        // 2. Default Store Settings yang Bisa Dicustom
        $settings = [
            ['key' => 'store_name', 'value' => 'ALYSA GLASSES & OPTOMETRY', 'label' => 'Nama Toko / Brand', 'type' => 'text'],
            ['key' => 'store_tagline', 'value' => 'Solusi Kacamata Modern & Presisi Lensa Klinis', 'label' => 'Slogan Toko', 'type' => 'text'],
            ['key' => 'store_address', 'value' => 'Jl. Jenderal Sudirman No. 88, Megamall Lantai 1, Batam Centre', 'label' => 'Alamat Toko & Cabang', 'type' => 'textarea'],
            ['key' => 'store_phone', 'value' => '0812-3456-7890 / 0778-456789', 'label' => 'Nomor WhatsApp / Telp Kasir', 'type' => 'text'],
            ['key' => 'store_email', 'value' => 'halo@alysaglasses.com', 'label' => 'Email Resmi Toko', 'type' => 'text'],
            ['key' => 'receipt_footer_notes', 'value' => "Garansi Lensa & Penyetelan Frame 30 Hari.\nKacamata yang sudah pas bikin hari-hari makin jelas.\nTerima kasih atas kunjungan Anda!", 'label' => 'Pesan Catatan Kaki Struk', 'type' => 'textarea'],
            ['key' => 'receipt_printer_width', 'value' => '58mm', 'label' => 'Lebar Struk Thermal (58mm / 80mm)', 'type' => 'text'],
        ];

        foreach ($settings as $s) {
            StoreSetting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
