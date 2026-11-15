<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescription_cards', function (Blueprint $table) {
            $table->string('wa_status', 191)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('prescription_cards', function (Blueprint $table) {
            $table->string('wa_status', 40)->nullable()->change();
        });
    }
};
