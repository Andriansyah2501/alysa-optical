<?php

namespace App\Services;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Kirim notifikasi WhatsApp otomatis saat ada pesanan baru atau perubahan status.
     * Menggunakan Webhook n8n lokal atau Gateway pihak ketiga (Fonnte/WppConnect).
     */
    public static function sendOrderNotification(Order $order, string $event = 'order_created'): bool
    {
        try {
            $settings = StoreSetting::all()->pluck('value', 'key');
            $isEnabled = ($settings['wa_gateway_enabled'] ?? '0') === '1';

            if (!$isEnabled) {
                return false;
            }

            $webhookUrl = $settings['wa_gateway_webhook_url'] ?? 'http://127.0.0.1:5678/webhook/alysa-wa-notify';
            $apiToken = $settings['wa_gateway_api_token'] ?? '';
            $provider = strtolower($settings['wa_gateway_provider'] ?? 'n8n');

            $rawPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
            if (str_starts_with($rawPhone, '0')) {
                $targetPhone = '62' . substr($rawPhone, 1);
            } elseif (str_starts_with($rawPhone, '62')) {
                $targetPhone = $rawPhone;
            } else {
                $targetPhone = '62' . $rawPhone;
            }

            if (strlen($targetPhone) < 10) {
                return false;
            }

            $storeName = $settings['store_name'] ?? 'Alysa Optical';
            $trackingUrl = url('/lacak-pesanan?code=' . $order->order_code);
            $receiptUrl = route('pos.receipt', $order->order_code);

            // Susun template pesan profesional
            if ($event === 'customer_registered') {
                $message = "Halo Kak *{$order->customer_name}*,

"
                    . "Selamat datang dan terima kasih telah terdaftar sebagai pelanggan di *{$storeName}*! 🎉

"
                    . "• *ID Pelanggan:* `{$order->customer_code}`
"
                    . "• *Nama:* {$order->customer_name}
"
                    . "• *No. WhatsApp:* {$order->customer_phone}

"
                    . "Data resep refraksi optik Anda telah tersimpan rapi di sistem kami. Anda akan menerima pembaruan otomatis setiap kali melakukan pemeriksaan mata atau pemesanan kacamata.

"
                    . "_Sistem Otomasi Pelanggan {$storeName}._";
            } elseif ($event === 'prescription_reminder') {
                $message = "Halo Kak *{$order->customer_name}*,

"
                    . "Pengingat kesehatan mata dari *{$storeName}*! 👁️👓

"
                    . "Pemeriksaan mata dan resep kacamata Anda terakhir kali tercatat pada sistem kami. Untuk kenyamanan penglihatan dan kesehatan mata optimal, para ahli menyarankan pemeriksaan berkala setiap 6 - 12 bulan.

"
                    . "Silakan kunjungi *{$storeName}* untuk cek refraksi gratis dan penyesuaian kacamata Anda.

"
                    . "_Layanan Pelanggan {$storeName}._";
            } elseif ($event === 'order_created') {
                $message = "Halo Kak *{$order->customer_name}*,\n\n"
                    . "Terima kasih telah mempercayakan kacamata Anda di *{$storeName}*.\n"
                    . "Pesanan Anda telah berhasil dibuat dengan rincian:\n\n"
                    . "• *No. Pesanan:* `{$order->order_code}`\n"
                    . "• *Tanggal:* " . $order->created_at->format('d/m/Y H:i') . " WIB\n"
                    . "• *Pilihan Lensa:* {$order->lens_type_name}\n"
                    . "• *Total Tagihan:* Rp " . number_format($order->grand_total, 0, ',', '.') . "\n"
                    . "• *Metode Bayar:* {$order->payment_method}\n"
                    . "• *Status Bayar:* " . strtoupper($order->payment_status) . "\n\n"
                    . "🔍 *Lacak Progres Pengerjaan Lab Optik:*\n{$trackingUrl}\n\n"
                    . "📄 *Lihat Struk Digital:*\n{$receiptUrl}\n\n"
                    . "_Pesan otomatis oleh Sistem Otomasi {$storeName}._";
            } elseif ($event === 'status_updated') {
                $message = "Halo Kak *{$order->customer_name}*,\n\n"
                    . "Update progres pesanan kacamata Anda di *{$storeName}*:\n\n"
                    . "• *No. Pesanan:* `{$order->order_code}`\n"
                    . "• *Status Terkini:* *{$order->order_status}*\n\n"
                    . "🔍 *Cek detail pelacakan real-time:*\n{$trackingUrl}\n\n"
                    . "Terima kasih atas kepercayaannya. 🙏";
            } else {
                $message = "Halo Kak *{$order->customer_name}*, pesanan Anda dengan kode {$order->order_code} sedang diproses di {$storeName}.";
            }

            // Kirim payload ke endpoint yang ditentukan
            if ($provider === 'waha' || $provider === 'local') {
                // Menggunakan Self-Hosted WAHA Gateway Lokal
                $chatId = $targetPhone . '@c.us';
                $response = Http::withHeaders([
                    'X-Api-Key' => 'alysa2026',
                    'Content-Type' => 'application/json'
                ])->timeout(5)->post('http://127.0.0.1:3008/api/sendText', [
                    'session' => 'default',
                    'chatId' => $chatId,
                    'text' => $message,
                ]);
            } elseif ($provider === 'fonnte') {
                $response = Http::withHeaders([
                    'Authorization' => $apiToken,
                ])->timeout(5)->post('https://api.fonnte.com/send', [
                    'target' => $targetPhone,
                    'message' => $message,
                ]);
            } else {
                // Default: Kirim JSON payload ke n8n Workflow Webhook lokal
                $payload = [
                    'event' => $event,
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $targetPhone,
                    'grand_total' => $order->grand_total,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'message_text' => $message,
                    'tracking_url' => $trackingUrl,
                    'receipt_url' => $receiptUrl,
                    'timestamp' => now()->toIso8601String(),
                ];

                $response = Http::timeout(3)->post($webhookUrl, $payload);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim WhatsApp otomatis untuk pesanan {$order->order_code}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pesan notifikasi langsung ke profil Pelanggan
     */
    public static function sendCustomerNotification(\App\Models\Customer $customer, string $event = 'customer_registered', ?string $customMessage = null): bool
    {
        try {
            $settings = StoreSetting::all()->pluck('value', 'key');
            $isEnabled = ($settings['wa_gateway_enabled'] ?? '0') === '1';
            if (!$isEnabled) return false;

            $provider = strtolower($settings['wa_gateway_provider'] ?? 'local');
            $storeName = $settings['store_name'] ?? 'Alysa Optical';

            $rawPhone = preg_replace('/[^0-9]/', '', $customer->phone);
            if (str_starts_with($rawPhone, '0')) {
                $targetPhone = '62' . substr($rawPhone, 1);
            } elseif (str_starts_with($rawPhone, '62')) {
                $targetPhone = $rawPhone;
            } else {
                $targetPhone = '62' . $rawPhone;
            }

            if (strlen($targetPhone) < 10) return false;

            if ($customMessage) {
                $message = $customMessage;
            } elseif ($event === 'customer_registered') {
                $message = "Halo Kak *{$customer->name}*,

"
                    . "Selamat datang dan terima kasih telah terdaftar sebagai pelanggan setia di *{$storeName}*! 🎉

"
                    . "• *ID Pelanggan:* `{$customer->customer_code}`
"
                    . "• *Tanggal Daftar:* " . now()->format('d/m/Y H:i') . " WIB

"
                    . "Data resep kacamata Anda telah tercatat aman di sistem kami. Kami siap memberikan layanan penglihatan terbaik untuk Anda.

"
                    . "_Pesan otomatis oleh {$storeName}._";
            } elseif ($event === 'prescription_updated') {
                $resepR = "SPH: {$customer->sph_r} | CYL: {$customer->cyl_r} | AXIS: {$customer->axis_r}";
                $resepL = "SPH: {$customer->sph_l} | CYL: {$customer->cyl_l} | AXIS: {$customer->axis_l}";
                $message = "Halo Kak *{$customer->name}*,

"
                    . "Pembaruan data resep refraksi mata Anda di *{$storeName}*:

"
                    . "👁️ *Mata Kanan (R):* {$resepR}
"
                    . "👁️ *Mata Kiri (L):* {$resepL}
"
                    . "📏 *PD (Pupil Distance):* {$customer->pd} mm

"
                    . "Resep ini sudah siap digunakan untuk proses pembuatan kacamata atau lensa Anda.

"
                    . "_Sistem Optik {$storeName}._";
            } else {
                $message = "Halo Kak *{$customer->name}*, terima kasih telah mengunjungi {$storeName}.";
            }

            if ($provider === 'waha' || $provider === 'local') {
                $chatId = $targetPhone . '@c.us';
                $response = Http::withHeaders([
                    'X-Api-Key' => 'alysa2026',
                    'Content-Type' => 'application/json'
                ])->timeout(5)->post('http://127.0.0.1:3008/api/sendText', [
                    'session' => 'default',
                    'chatId' => $chatId,
                    'text' => $message,
                ]);
                return $response->successful();
            }

            return false;
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim WA ke customer {$customer->name}: " . $e->getMessage());
            return false;
        }
    }

}
