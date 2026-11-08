<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi order_items agar product_id bisa nullable (untuk transaksi custom / ganti lensa / jasa)
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->string('item_type')->default('product')->after('product_id'); // product, custom_frame, lens_only, service_fee
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        // 2. Modifikasi orders untuk menyimpan info biaya jasa/service dan tipe transaksi
        Schema::table('orders', function (Blueprint $table) {
            $table->string('transaction_type')->default('kacamata_lengkap')->after('order_code'); // kacamata_lengkap, custom_frame, ganti_lensa, jasa_servis
            $table->decimal('service_fee', 12, 2)->default(0)->after('lens_price');
            $table->string('service_name')->nullable()->after('service_fee');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['transaction_type', 'service_fee', 'service_name']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }
};
