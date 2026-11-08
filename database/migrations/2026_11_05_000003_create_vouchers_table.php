<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['fixed', 'percentage'])->default('fixed'); // fixed = nominal Rp, percentage = %
            $table->decimal('amount', 12, 2); // nominal potongan atau nilai %
            $table->decimal('min_spend', 12, 2)->default(0); // minimal total belanja frame
            $table->decimal('max_discount', 12, 2)->nullable(); // limit maksimal diskon jika tipe persentase
            $table->integer('usage_limit')->nullable(); // batas maksimal pemakaian
            $table->integer('used_count')->default(0); // jumlah kali telah digunakan
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'voucher_code')) {
                $table->string('voucher_code')->nullable()->after('lens_price');
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('voucher_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'discount_amount']);
        });
        Schema::dropIfExists('vouchers');
    }
};
