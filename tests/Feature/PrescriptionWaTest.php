<?php

namespace Tests\Feature;

use App\Models\PrescriptionCard;
use App\Models\StoreSetting;
use App\Models\User;
use Tests\TestCase;

class PrescriptionWaTest extends TestCase
{
    public function test_wa_status_endpoint_json(): void
    {
        $u = User::where('role', 'optician')->first() ?? User::where('role', 'super_admin')->first();
        $resp = $this->actingAs($u)->getJson('/admin/kartu-resep/wa/status');
        $resp->assertOk()->assertJsonStructure(['status']);
        $this->assertContains($resp->json('status'), ['ACTIVE', 'WORKING', 'SCAN_QR_CODE', 'STARTING', 'STOPPED', 'FAILED', 'OFFLINE', 'UNKNOWN']);
    }

    public function test_auto_toggle_setting(): void
    {
        $u = User::where('role', 'super_admin')->first();
        $this->actingAs($u)->post('/admin/kartu-resep/wa/toggle', ['enabled' => '1'])->assertRedirect();
        $this->assertSame('1', StoreSetting::get('rx_wa_auto'));
        $this->actingAs($u)->post('/admin/kartu-resep/wa/toggle', ['enabled' => '0'])->assertRedirect();
        $this->assertSame('0', StoreSetting::get('rx_wa_auto'));
    }

    public function test_send_wa_manual_records_status_even_if_gateway_down(): void
    {
        $u = User::where('role', 'optician')->first() ?? User::where('role', 'super_admin')->first();
        $card = PrescriptionCard::create([
            'card_code' => PrescriptionCard::generateCode(),
            'customer_name' => 'Uji WA', 'customer_phone' => '081234567890',
            'exam_date' => '2026-11-16', 'sph_r' => '-1.00', 'origin' => 'Pemeriksaan Toko',
        ]);
        $resp = $this->actingAs($u)->post('/admin/kartu-resep/' . $card->id . '/kirim-wa');
        $resp->assertRedirect();
        $card->refresh();
        // WA sedang pairing (FAILED) -> harus tercatat gagal, tidak crash
        $this->assertNotNull($card->wa_status);
        $this->assertTrue(
            str_starts_with($card->wa_status, 'Terkirim') || str_starts_with($card->wa_status, 'Gagal'),
            'status harus Terkirim/Gagal, dapat: ' . $card->wa_status
        );
        $card->delete();
    }

    public function test_share_public_page_and_pdf(): void
    {
        $u = User::where('role', 'optician')->first() ?? User::where('role', 'super_admin')->first();
        $card = PrescriptionCard::create([
            'card_code' => PrescriptionCard::generateCode(),
            'customer_name' => 'Uji Share', 'exam_date' => '2026-11-16', 'origin' => 'Pemeriksaan Toko',
        ]);
        $token = $card->ensureShareToken();
        // publik tanpa login
        $this->get('/r/' . $token)->assertOk()->assertSee('Uji Share');
        $pdf = $this->get('/r/' . $token . '/pdf');
        $pdf->assertOk();
        $this->assertStringStartsWith('%PDF', $pdf->getContent());
        $card->delete();
    }
}
