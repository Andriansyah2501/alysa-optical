<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Default: hanya cashier yang boleh pakai POS (sama seperti perilaku lama via role:cashier,admin)
        DB::table('store_settings')->insertOrIgnore([
            'key' => 'pos_enabled_roles',
            'value' => 'cashier',
            'label' => 'Akses: Role yang boleh melakukan transaksi kasir (POS)',
            'type' => 'text',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('store_settings')->where('key', 'pos_enabled_roles')->delete();
    }
};
