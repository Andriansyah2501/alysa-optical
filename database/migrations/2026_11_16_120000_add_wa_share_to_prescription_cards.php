<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescription_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_cards', 'share_token')) {
                $table->string('share_token', 40)->nullable()->after('user_id');
                $table->timestamp('wa_sent_at')->nullable()->after('share_token');
                $table->string('wa_status', 40)->nullable()->after('wa_sent_at');
            }
        });
        \App\Models\StoreSetting::updateOrCreate(
            ['key' => 'rx_wa_auto'],
            ['value' => '1', 'label' => 'Auto-kirim Kartu Resep via WA (n8n)', 'type' => 'text']
        );
    }

    public function down(): void
    {
        Schema::table('prescription_cards', function (Blueprint $table) {
            if (Schema::hasColumn('prescription_cards', 'share_token')) {
                $table->dropColumn(['share_token', 'wa_sent_at', 'wa_status']);
            }
        });
    }
};
