<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_code', 40)->unique();
            $table->string('order_code', 40)->nullable()->index();
            // Data pembeli / pasien
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('birth_place', 80)->nullable();
            $table->string('birth_date', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->date('exam_date');
            // Resep
            $table->string('sph_r', 15)->nullable();
            $table->string('cyl_r', 15)->nullable();
            $table->string('axis_r', 10)->nullable();
            $table->string('sph_l', 15)->nullable();
            $table->string('cyl_l', 15)->nullable();
            $table->string('axis_l', 10)->nullable();
            $table->string('add_r', 15)->nullable();
            $table->string('add_l', 15)->nullable();
            $table->string('pd', 10)->nullable();
            $table->string('vision_goal', 60)->nullable();   // Jauh / Dekat / Progressif
            $table->string('refraction_method', 60)->nullable(); // Auto Refraksi / Subjectif
            $table->string('origin', 60)->default('Pemeriksaan Toko'); // atau rujukan dokter
            $table->string('referrer_name', 120)->nullable();
            $table->text('notes')->nullable();
            $table->string('examiner_name', 120)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_cards');
    }
};
