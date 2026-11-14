<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Frame: lensa yang direkomendasikan untuk paket bundling
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'bundle_lens_type_id')) {
                $table->unsignedBigInteger('bundle_lens_type_id')->nullable()->after('image_url');
            }
        });

        // Belanja modal: tautan opsional ke kode struk POS (HPP per transaksi)
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'linked_order_code')) {
                $table->string('linked_order_code', 40)->nullable()->after('payment_source');
                $table->index('linked_order_code');
            }
        });

        // Pengaturan default potongan paket bundling (%)
        \App\Models\StoreSetting::updateOrCreate(
            ['key' => 'bundle_discount_percent'],
            ['value' => '10', 'label' => 'Potongan Harga Paket Bundling (%)', 'type' => 'number']
        );
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'bundle_lens_type_id')) {
                $table->dropColumn('bundle_lens_type_id');
            }
        });
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'linked_order_code')) {
                $table->dropIndex(['linked_order_code']);
                $table->dropColumn('linked_order_code');
            }
        });
    }
};
