<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('order_status');
            $table->text('admin_notes')->nullable()->after('tracking_number');
            $table->timestamp('validated_at')->nullable()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'admin_notes', 'validated_at']);
        });
    }
};
