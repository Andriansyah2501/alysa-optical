<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode', 32)->nullable()->unique()->after('slug');
            }
        });

        // Backfill kode barcode untuk produk lama: AG-{id 5 digit} (contoh AG-00014)
        $ids = DB::table('products')->whereNull('barcode')->pluck('id');
        foreach ($ids as $id) {
            DB::table('products')->where('id', $id)->update([
                'barcode' => sprintf('AG-%05d', $id),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'barcode')) {
                $table->dropUnique(['barcode']);
                $table->dropColumn('barcode');
            }
        });
    }
};
