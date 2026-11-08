<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_type')->default('full')->after('payment_method'); // full, dp
            $table->decimal('dp_amount', 12, 2)->default(0)->after('grand_total');
            $table->decimal('remaining_balance', 12, 2)->default(0)->after('dp_amount');
            $table->timestamp('settled_at')->nullable()->after('validated_at'); // saat pelunasan
            $table->string('settlement_payment_method')->nullable()->after('settled_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'dp_amount',
                'remaining_balance',
                'settled_at',
                'settlement_payment_method'
            ]);
        });
    }
};
