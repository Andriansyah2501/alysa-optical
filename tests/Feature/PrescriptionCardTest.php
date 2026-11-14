<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PrescriptionCard;
use App\Models\User;
use Illuminate\Foundation\Testing\WithTinker;
use Tests\TestCase;

class PrescriptionCardTest extends TestCase
{
    public function test_full_card_lifecycle_print_pdf(): void
    {
        $u = User::where('role', 'optician')->first() ?? User::where('role', 'super_admin')->first();
        $this->actingAs($u)->get('/admin/kartu-resep')->assertOk()->assertSee('Kartu Resep Kacamata');

        // create
        $resp = $this->actingAs($u)->post('/admin/kartu-resep', [
            'customer_name' => 'Sari Dewi', 'customer_phone' => '081234000011',
            'gender' => 'Perempuan', 'birth_place' => 'Batam', 'birth_date' => '12-05-1990',
            'address' => 'Batu Aji, Batam Kota', 'exam_date' => '2026-11-16',
            'sph_r' => '-2.25', 'cyl_r' => '-0.75', 'axis_r' => '180',
            'sph_l' => '-2.50', 'cyl_l' => '-0.50', 'axis_l' => '175',
            'pd' => '63', 'vision_goal' => 'Jauh', 'refraction_method' => 'Refraksi Subjektif',
            'origin' => 'Pemeriksaan Toko', 'examiner_name' => 'Rian Refraksi',
            'notes' => 'Keluhan sering pusing saat baca jauh.',
        ]);
        $card = PrescriptionCard::latest('id')->first();
        $this->assertNotNull($card);
        $this->assertSame('Sari Dewi', $card->customer_name);
        $resp->assertRedirect(route('admin.prescriptions.show', $card->id));

        // show + edit + update
        $this->actingAs($u)->get('/admin/kartu-resep/' . $card->id)->assertOk()->assertSee('Sari Dewi')->assertSee($card->card_code);
        $this->actingAs($u)->get('/admin/kartu-resep/' . $card->id . '/edit')->assertOk();
        $this->actingAs($u)->put('/admin/kartu-resep/' . $card->id, [
            'customer_name' => 'Sari Dewi', 'exam_date' => '2026-11-16', 'pd' => '64', 'origin' => 'Pemeriksaan Toko',
        ])->assertRedirect();
        $this->assertSame('64', $card->fresh()->pd);

        // print page
        $print = $this->actingAs($u)->get('/admin/kartu-resep/' . $card->id . '/cetak');
        $print->assertOk()->assertSee('Kartu Resep Kacamata')->assertSee('icon-512.png')->assertSee('-2.25', false);

        // PDF
        $pdf = $this->actingAs($u)->get('/admin/kartu-resep/' . $card->id . '/pdf');
        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));
        $body = $pdf->getContent() !== false ? $pdf->getContent() : $pdf->streamedContent();
        $this->assertGreaterThan(3000, strlen($body));
        $this->assertStringStartsWith('%PDF', $body);

        // lookup AJAX dari struk POS
        $order = Order::where('has_prescription', true)->first();
        if ($order) {
            $this->actingAs($u)->getJson('/admin/pelanggan/cari-resep/' . $order->order_code)
                ->assertOk()->assertJsonStructure(['sph_r', 'cyl_r', 'axis_r', 'pd']);
        }

        // search
        $this->actingAs($u)->get('/admin/kartu-resep?q=Sari')->assertOk()->assertSee('Sari Dewi');

        // cleanup test record
        $card->delete();
        print("RX CARD LIFECYCLE OK\n");
    }

    public function test_cashier_blocked_from_prescriptions(): void
    {
        $c = User::where('role', 'cashier')->first();
        if (!$c) $this->markTestSkipped('no cashier');
        $this->actingAs($c)->get('/admin/kartu-resep')->assertForbidden();
    }
}
