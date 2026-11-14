<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionCard;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\PrescriptionWaService;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QROutputInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionCardController extends Controller
{
    /** AR-1. Daftar Kartu Resep */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $cards = PrescriptionCard::with('user:id,name')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('customer_name', 'like', "%{$q}%")
                      ->orWhere('card_code', 'like', "%{$q}%")
                      ->orWhere('order_code', 'like', "%{$q}%")
                      ->orWhere('customer_phone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('exam_date')->orderByDesc('id')
            ->paginate(15)->withQueryString();

        $totalCards = PrescriptionCard::count();
        $thisMonth = PrescriptionCard::whereMonth('exam_date', now()->month)
            ->whereYear('exam_date', now()->year)->count();

        $settings = StoreSetting::all()->pluck('value', 'key');
        return view('admin.prescriptions.index', compact('cards', 'q', 'totalCards', 'thisMonth', 'settings'));
    }

    /** AR-2. Form Buat Kartu (prefill dari struk POS: ?order=POS-XXXX) */
    public function create(Request $request)
    {
        $prefill = null;
        if ($code = $request->input('order')) {
            $prefill = Order::where('order_code', $code)->first();
        }
        $orders = Order::where('has_prescription', true)
            ->orderByDesc('id')->take(100)
            ->get(['id', 'order_code', 'customer_name', 'customer_phone']);

        return view('admin.prescriptions.form', [
            'card' => null, 'prefill' => $prefill, 'orders' => $orders,
            'title' => 'Buat Kartu Resep Baru',
        ]);
    }

    /** AR-3. Simpan Kartu */
    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['card_code'] = PrescriptionCard::generateCode();
        $data['user_id'] = Auth::id();
        $card = PrescriptionCard::create($data);

        // AUTO-KIRIM WA via n8n bila diaktifkan di Pengaturan
        $waNote = '';
        if ((string) (StoreSetting::get('rx_wa_auto', '1') ?? '1') === '1' && $card->customer_phone) {
            $res = PrescriptionWaService::send($card);
            $card->update(['wa_sent_at' => now(), 'wa_status' => $res['ok'] ? 'Terkirim (' . $res['via'] . ')' : 'Gagal: ' . $res['error']]);
            $waNote = $res['ok'] ? ' Kartu otomatis dikirim ke WA pasien via ' . strtoupper($res['via']) . '.' : ' Catatan: kirim WA gagal (' . $res['error'] . ').';
        }

        return redirect()->route('admin.prescriptions.show', $card->id)
            ->with('success', 'Kartu resep ' . $card->card_code . ' untuk ' . $card->customer_name . ' berhasil dibuat.' . $waNote);
    }

    /** AR-4. Detail Kartu */
    public function show($id)
    {
        $card = PrescriptionCard::with('user:id,name')->findOrFail($id);
        $settings = StoreSetting::all()->pluck('value', 'key');
        return view('admin.prescriptions.show', compact('card', 'settings'));
    }

    /** AR-5. Form Edit */
    public function edit($id)
    {
        $card = PrescriptionCard::findOrFail($id);
        $orders = Order::where('has_prescription', true)
            ->orderByDesc('id')->take(100)
            ->get(['id', 'order_code', 'customer_name', 'customer_phone']);
        return view('admin.prescriptions.form', [
            'card' => $card, 'prefill' => null, 'orders' => $orders,
            'title' => 'Edit Kartu Resep ' . $card->card_code,
        ]);
    }

    /** AR-6. Update */
    public function update(Request $request, $id)
    {
        $card = PrescriptionCard::findOrFail($id);
        $card->update($this->validated($request));
        return redirect()->route('admin.prescriptions.show', $card->id)
            ->with('success', 'Kartu resep ' . $card->card_code . ' diperbarui.');
    }

    /** AR-7. Hapus */
    public function destroy($id)
    {
        $card = PrescriptionCard::findOrFail($id);
        $card->delete();
        return redirect()->route('admin.prescriptions.index')->with('success', 'Kartu resep dihapus.');
    }

    /** AR-8. Halaman Cetak (A5, tombol print browser) */
    public function print($id)
    {
        $card = PrescriptionCard::findOrFail($id);
        $settings = StoreSetting::all()->pluck('value', 'key');
        return view('admin.prescriptions.print', compact('card', 'settings'));
    }

    /** AR-9. Download PDF (logo asli, A5) */
    public function pdf($id)
    {
        $card = PrescriptionCard::findOrFail($id);
        $settings = StoreSetting::all()->pluck('value', 'key');
        $pdf = Pdf::loadView('admin.prescriptions.pdf', compact('card', 'settings'))
            ->setPaper('a5', 'portrait');
        return $pdf->stream('Kartu_Resep_' . $card->card_code . '_' . preg_replace('/\W+/', '_', $card->customer_name) . '.pdf');
    }

    /** AR-10. Kirim / ulang kartu ke WA pasien (tombol manual). */
    public function sendWa($id)
    {
        $card = PrescriptionCard::findOrFail($id);
        if (!$card->customer_phone) {
            return back()->with('error', 'Nomor WhatsApp pasien belum diisi di kartu ini.');
        }
        $res = PrescriptionWaService::send($card);
        $card->update(['wa_sent_at' => now(), 'wa_status' => $res['ok'] ? 'Terkirim (' . $res['via'] . ')' : 'Gagal: ' . $res['error']]);

        return $res['ok']
            ? back()->with('success', 'Kartu resep dikirim ke WA ' . $card->customer_name . ' via ' . strtoupper($res['via']) . ' ✅')
            : back()->with('error', 'Gagal kirim WA: ' . $res['error']);
    }

    /** AR-11. Halaman share PUBLIK utk pasien (token acak, tanpa login). */
    public function share($token)
    {
        $card = PrescriptionCard::where('share_token', $token)->firstOrFail();
        $settings = StoreSetting::all()->pluck('value', 'key');
        // QR -> link PDF (praktis discan & langsung bisa dicetak)
        $options = new QROptions(['outputType' => QROutputInterface::MARKUP_HTML, 'eccLevel' => QRCode::ECC_L]);
        $qr = (new QRCode($options))->render($card->shareUrl());

        return view('admin.prescriptions.share', compact('card', 'settings', 'qr'));
    }

    /** AR-12. PDF utk pasien (via token publik). */
    public function sharePdf($token)
    {
        $card = PrescriptionCard::where('share_token', $token)->firstOrFail();
        $settings = StoreSetting::all()->pluck('value', 'key');
        $pdf = Pdf::loadView('admin.prescriptions.pdf', compact('card', 'settings'))
            ->setPaper('a5', 'portrait');
        return $pdf->stream('Kartu_Resep_' . $card->card_code . '.pdf');
    }

    /** AR-13. Status gateway WA + QR pairing (live proxy WAHA) utk halaman Kartu Resep. */
    public function waStatus()
    {
        $st = 'UNKNOWN';
        $payload = ['status' => 'UNKNOWN'];
        try {
            $r = \Illuminate\Support\Facades\Http::withHeaders(['X-Api-Key' => 'alysa2026'])
                ->timeout(5)->get('http://127.0.0.1:3008/api/sessions/default');
            if ($r->successful()) {
                $st = strtoupper($r->json('status') ?? 'UNKNOWN');
                $payload['status'] = $st;
                if ($st === 'ACTIVE') $payload['pushName'] = $r->json('me.pushName');
            } else {
                $payload['status'] = $st = 'OFFLINE';
            }
        } catch (\Throwable $e) { $payload['status'] = $st = 'OFFLINE'; }
        if ($st === 'SCAN_QR_CODE') {
            try {
                $qr = \Illuminate\Support\Facades\Http::withHeaders(['X-Api-Key' => 'alysa2026'])
                    ->timeout(5)->get('http://127.0.0.1:3008/api/default/auth/qr');
                if ($qr->successful()) {
                    $payload['qr_base64'] = 'data:image/png;base64,' . base64_encode($qr->body());
                }
            } catch (\Throwable $e) { /* qr kosong */ }
        }
        return response()->json($payload);
    }

    /** AR-14. Toggle kirim otomatis WA saat kartu dibuat. */
    public function waToggle(Request $request)
    {
        $on = $request->boolean('enabled') ? '1' : '0';
        \App\Models\StoreSetting::updateOrCreate(
            ['key' => 'rx_wa_auto'],
            ['value' => $on, 'label' => 'Auto-Kirim Kartu Resep via WA (n8n)', 'type' => 'boolean']
        );
        return back()->with('success', $on === '1'
            ? 'Kirim otomatis WA via n8n AKTIF — setiap kartu baru langsung terkirim ke pasien.'
            : 'Kirim otomatis WA DINONAKTIFKAN. Kirim tetap bisa manual lewat tombol Kirim WA.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'order_code' => 'nullable|string|max:40',
            'customer_name' => 'required|string|max:120',
            'customer_phone' => 'nullable|string|max:30',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:80',
            'birth_date' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'exam_date' => 'required|date',
            'sph_r' => 'nullable|string|max:15', 'cyl_r' => 'nullable|string|max:15', 'axis_r' => 'nullable|string|max:10',
            'sph_l' => 'nullable|string|max:15', 'cyl_l' => 'nullable|string|max:15', 'axis_l' => 'nullable|string|max:10',
            'add_r' => 'nullable|string|max:15', 'add_l' => 'nullable|string|max:15',
            'pd' => 'nullable|string|max:10',
            'vision_goal' => 'nullable|string|max:60',
            'refraction_method' => 'nullable|string|max:60',
            'origin' => 'nullable|string|max:60',
            'referrer_name' => 'nullable|string|max:120',
            'notes' => 'nullable|string|max:1000',
            'examiner_name' => 'nullable|string|max:120',
        ]);
        // Normalisasi: strip spasi, kosong -> null
        foreach ($data as $k => $v) {
            if (is_string($v)) $data[$k] = trim($v) === '' ? null : trim($v);
        }
        return $data;
    }
}
