<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'hpp_modal')) {
                $table->unsignedBigInteger('hpp_modal')->nullable()->after('remaining_balance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'hpp_modal')) {
                $table->dropColumn('hpp_modal');
            }
        });
    }
};
