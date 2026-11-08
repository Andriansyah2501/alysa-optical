<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('badge')->default('PROMO');
            $table->text('description')->nullable();
            $table->string('discount_info')->nullable();
            $table->string('voucher_code')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('color_theme')->default('rose'); // rose, amber, emerald, sky, purple
            $table->string('target_audience')->default('Semua Pelanggan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_events');
    }
};
