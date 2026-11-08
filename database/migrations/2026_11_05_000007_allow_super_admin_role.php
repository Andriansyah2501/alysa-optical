<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan kolom role pada tabel users mendukung string panjang (varchar 50)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('cashier')->change();
        });

        // Upgrade user admin@alysaglasses.com menjadi 'super_admin'
        DB::table('users')->where('email', 'admin@alysaglasses.com')->update([
            'role' => 'super_admin',
            'name' => 'Owner Alysa (Super Admin)'
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'super_admin')->update([
            'role' => 'admin'
        ]);
    }
};
