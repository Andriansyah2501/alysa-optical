<?php

namespace App\Services;

use App\Models\PrescriptionCard;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Kirim Kartu Resep ke WhatsApp pasien via otomasi n8n (webhook alysa-rx-card),
 * fallback langsung ke gateway WAHA bila n8n tidak menjawab.
 */
class PrescriptionWaService
{
    public static function send(PrescriptionCard $card): array
    {
        $settings = StoreSetting::all()->pluck('value', 'key');
        $storeName = $settings['store_name'] ?? 'Alysa Glasses';

        $raw = preg_replace('/[^0-9]/', '', (string) $card->customer_phone);
        if (!$raw || strlen($raw) < 9) {
            return ['ok' => false, 'via' => null, 'error' => 'Nomor WA pasien tidak valid'];
        }
        $target = str_starts_with($raw, '62') ? $raw : '62' . ltrim($raw, '0');

        $shareUrl = $card->shareUrl();
        $pdfUrl = $card->sharePdfUrl();
        // URL unduhan lokal utk gateway (jauh lebih cepat & andal daripada tunnel publik)
        $downloadUrl = 'http://192.168.0.30:8084/r/' . $card->ensureShareToken() . '/pdf';

        $fmt = fn($x) => PrescriptionCard::fmtVal($x);
        $message = "Halo Kak *{$card->customer_name}* 👓\n\n"
            . "Resep kacamata Anda di *{$storeName}* sudah selesai diperiksa:\n\n"
            . "📄 *Kartu Resep:* `{$card->card_code}`\n"
            . "🗓️ *Tgl Periksa:* " . \Carbon\Carbon::parse($card->exam_date)->format('d/m/Y') . "\n\n"
            . "👁️ *Mata Kanan (OD):* SPH {$fmt($card->sph_r)} | CYL {$fmt($card->cyl_r)} | AXIS " . ($card->axis_r ?: '-') . "\n"
            . "👁️ *Mata Kiri (OS):* SPH {$fmt($card->sph_l)} | CYL {$fmt($card->cyl_l)} | AXIS " . ($card->axis_l ?: '-') . "\n"
            . "📏 *PD:* " . ($card->pd ?: '-') . " mm" . (($card->add_r || $card->add_l) ? " | *ADD:* R " . ($card->add_r ?: '-') . " / L " . ($card->add_l ?: '-') : '') . "\n\n"
            . "🔗 *Kartu Resep Digital (bisa dibuka & dicetak):*\n{$shareUrl}\n"
            . "📥 *Download PDF:* {$pdfUrl}\n\n"
            . "Simpan pesan ini untuk pembelian lensa berikutnya. Terima kasih 🙏\n"
            . "_Otomasi {$storeName}_";

        $payload = [
            'event' => 'prescription_card_created',
            'card_code' => $card->card_code,
            'customer_name' => $card->customer_name,
            'customer_phone' => $target,
            'message_text' => $message,
            'share_url' => $shareUrl,
            'pdf_url' => $pdfUrl,
            'download_url' => $downloadUrl,
            'pdf_file_name' => $card->pdfFileName(),
            'exam_date' => (string) $card->exam_date,
            'timestamp' => now()->toIso8601String(),
        ];

        // 1) VIA n8n (otomasi) — workflow alysa-rx-card meneruskan ke WAHA (teks + PDF)
        $n8nUrl = $settings['rx_wa_n8n_webhook_url'] ?? 'http://127.0.0.1:5678/webhook/alysa-rx-card';
        try {
            $res = Http::timeout(20)->post($n8nUrl, $payload);
            if ($res->successful()) {
                return ['ok' => true, 'via' => 'n8n', 'error' => null];
            }
            Log::info('Rx WA via n8n gagal (' . $res->status() . '), fallback WAHA langsung');
        } catch (\Throwable $e) {
            Log::info('Rx WA n8n tidak terjangkau, fallback: ' . $e->getMessage());
        }

        // 2) FALLBACK: WAHA langsung
        try {
            $r1 = Http::withHeaders(['X-Api-Key' => 'alysa2026'])->timeout(8)->post('http://127.0.0.1:3008/api/sendText', [
                'session' => 'default', 'chatId' => $target . '@c.us', 'text' => $message,
            ]);
            if (!$r1->successful()) {
                return ['ok' => false, 'via' => 'waha', 'error' => 'WAHA menolak: ' . substr($r1->body(), 0, 120)];
            }
            // lampirkan PDF (best-effort)
            try {
                Http::withHeaders(['X-Api-Key' => 'alysa2026'])->timeout(30)->post('http://127.0.0.1:3008/api/sendFile', [
                    'session' => 'default', 'chatId' => $target . '@c.us',
                    'file' => [
                        'url' => $downloadUrl,
                        'fileName' => $card->pdfFileName(),
                        'caption' => 'Kartu Resep PDF — ' . $card->card_code,
                    ],
                ]);
            } catch (\Throwable $e) { /* PDF opsional */ }
            return ['ok' => true, 'via' => 'waha', 'error' => null];
        } catch (\Throwable $e) {
            return ['ok' => false, 'via' => null, 'error' => $e->getMessage()];
        }
    }
}
