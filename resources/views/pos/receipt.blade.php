<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi Optik - {{ $order->order_code }}</title>
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
        }
        body {
            background-color: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 25px 15px;
            min-height: 100vh;
        }
        .main-wrapper {
            max-width: 440px;
            width: 100%;
        }
        .action-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .action-card h3 {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-wa {
            background: #25D366;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.25);
        }
        .btn-wa:hover {
            background: #1eb956;
            transform: translateY(-1px);
        }
        .btn-print {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }
        .btn-print:hover {
            background: #0369a1;
            transform: translateY(-1px);
        }
        .btn-settle {
            background: #f59e0b;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
        }
        .btn-settle:hover {
            background: #d97706;
        }
        .btn-back {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .receipt-container {
            width: {{ ($settings['receipt_printer_width'] ?? '58mm') == '80mm' ? '300px' : '260px' }};
            background: #fff;
            padding: 16px 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            font-size: 11px;
            line-height: 1.35;
            color: #000;
            margin: 0 auto;
            border-radius: 6px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .rx-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin: 5px 0;
        }
        .rx-table th, .rx-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
        }
        .dp-badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            margin-top: 4px;
        }
        .settle-box {
            margin-top: 14px;
            padding: 12px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 12px;
        }
        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .action-card, .settle-box {
                display: none !important;
            }
            .main-wrapper {
                max-width: 100%;
                width: 100%;
            }
            .receipt-container {
                box-shadow: none;
                padding: 4px;
                margin: 0 auto;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    @php
        $rawPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($rawPhone, '0')) {
            $waNumber = '62' . substr($rawPhone, 1);
        } elseif (str_starts_with($rawPhone, '62')) {
            $waNumber = $rawPhone;
        } else {
            $waNumber = '62' . $rawPhone;
        }

        $storeTitle = $settings['store_name'] ?? 'ALYSA GLASSES';
        $itemsText = "";
        foreach($order->items as $it) {
            $itemsText .= "• " . $it->product_name . " (Rp " . number_format($it->price, 0, ',', '.') . ")\n";
        }

        $lensText = "";
        if ($order->lens_price > 0 || !empty($order->lens_type_name)) {
            $lensText = "• Paket Lensa: " . ($order->lens_type_name ?: 'CRMC Minus') . " (+ Rp " . number_format($order->lens_price, 0, ',', '.') . ")\n";
        }

        $serviceText = "";
        if ($order->service_fee > 0) {
            $serviceText = "• " . ($order->service_name ?: 'Upah Jasa / Servis') . ": + Rp " . number_format($order->service_fee, 0, ',', '.') . "\n";
        }

        $rxText = "";
        if ($order->has_prescription) {
            $rxText = "📋 *UKURAN RESEP LENSA (RX):*\n"
                    . "• R (Kanan) : Sph " . ($order->sph_r ?: '0.00') . " | Cyl " . ($order->cyl_r ?: '0.00') . " | Ax " . ($order->axis_r ?: '0') . "\n"
                    . "• L (Kiri)  : Sph " . ($order->sph_l ?: '0.00') . " | Cyl " . ($order->cyl_l ?: '0.00') . " | Ax " . ($order->axis_l ?: '0') . "\n"
                    . "• PD (Pupil): " . $order->pd . " mm\n\n";
        }

        $voucherText = "";
        if ($order->discount_amount > 0) {
            $voucherText = "• Diskon Voucher (" . ($order->voucher_code ?: 'Promo') . ") : - Rp " . number_format($order->discount_amount, 0, ',', '.') . "\n";
        }

        // Teks Pembayaran DP vs Full
        $paymentInfoText = "";
        if ($order->payment_type === 'dp' && $order->remaining_balance > 0) {
            $paymentInfoText = "💰 *PEMBAYARAN DOWN PAYMENT (UANG MUKA):*\n"
                             . "• Total Tagihan : *Rp " . number_format($order->grand_total, 0, ',', '.') . "*\n"
                             . "• Uang Muka (DP): *Rp " . number_format($order->dp_amount, 0, ',', '.') . "* (DIBAYAR)\n"
                             . "• SISA TAGIHAN  : *Rp " . number_format($order->remaining_balance, 0, ',', '.') . "* (BELUM LUNAS)\n"
                             . "• Metode Bayar  : " . $order->payment_method . "\n"
                             . "• Bayar Tunai   : Rp " . number_format($paid, 0, ',', '.') . "\n"
                             . "• Kembalian     : Rp " . number_format($change, 0, ',', '.') . "\n"
                             . "• Status        : *DP TERBAYAR - AMBIL KACAMATA SAAT LUNAS*\n";
        } else {
            $paymentInfoText = "💰 *PEMBAYARAN:*\n"
                             . "• Total Tagihan : *Rp " . number_format($order->grand_total, 0, ',', '.') . "*\n"
                             . "• Metode Bayar  : " . $order->payment_method . "\n"
                             . "• Bayar Tunai   : Rp " . number_format($paid, 0, ',', '.') . "\n"
                             . "• Kembalian     : Rp " . number_format($change, 0, ',', '.') . "\n"
                             . "• Status        : *LUNAS*\n";
        }

        $waMessage = "Halo Kak *" . $order->customer_name . "*,\n"
                   . "Terima kasih telah bertransaksi di *" . $storeTitle . "*! ✨\n\n"
                   . "🧾 *STRUK TRANSAKSI OPTIK (" . ($order->payment_type === 'dp' && $order->remaining_balance > 0 ? 'DOWN PAYMENT / DP' : 'LUNAS') . ")*\n"
                   . "━━━━━━━━━━━━━━━━━━━━━━\n"
                   . "• No. Struk : *" . $order->order_code . "*\n"
                   . "• Tanggal   : " . $order->created_at->format('d/m/Y H:i') . " WIB\n"
                   . "• Pelanggan : " . $order->customer_name . "\n\n"
                   . "👓 *RINCIAN TRANSAKSI:*\n"
                   . $itemsText
                   . $lensText
                   . $serviceText
                   . $voucherText . "\n"
                   . $rxText
                   . $paymentInfoText
                   . "━━━━━━━━━━━━━━━━━━━━━━\n"
                   . "🔍 *Lacak Status Produksi Kacamata:*\n"
                   . url('/lacak-pesanan?code=' . $order->order_code) . "\n\n"
                   . "🎁 *CATATAN OPTIK & GARANSI:*\n"
                   . ($settings['receipt_footer_notes'] ?? "Garansi kenyamanan lensa 30 hari. Free pembersihan & penyetelan frame.") . "\n\n"
                   . "Salam hangat,\n"
                   . "*" . $storeTitle . "*\n"
                   . "Follow IG: @alysa_glasses";

        $encodedWa = urlencode($waMessage);
        $waUrl = "https://wa.me/" . $waNumber . "?text=" . $encodedWa;
    @endphp

    <div class="main-wrapper">
        
        <div class="action-card">
            <h3><i class="fa-solid fa-bolt text-brand-600"></i> Aksi Transaksi Kasir</h3>
            <div class="btn-group">
                @if(!empty($rawPhone) && strlen($rawPhone) >= 9)
                    <a href="{{ $waUrl }}" target="_blank" class="btn btn-wa">
                        <i class="fa-brands fa-whatsapp text-lg"></i> Kirim Struk Digital ke WhatsApp Pelanggan
                    </a>
                @else
                    <button type="button" disabled class="btn btn-wa" style="opacity: 0.6; cursor: not-allowed;">
                        <i class="fa-brands fa-whatsapp text-lg"></i> Nomor WhatsApp Belum Terdaftar
                    </button>
                @endif

                @php
                    $thermerEndpoint = route('pos.thermer.json', $order->order_code);
                    $thermerScheme = "my.bluetoothprint.scheme://" . $thermerEndpoint;
                @endphp
                <a href="{{ $thermerScheme }}" class="btn" style="background: #be123c; color: #ffffff; box-shadow: 0 4px 12px rgba(190, 18, 60, 0.25); text-decoration: none;">
                    ⚡ Cetak Langsung via Thermer (Bluetooth)
                </a>

                <a href="{{ route('pos.receipt.pdf', $order->order_code) }}" target="_blank" class="btn" style="background: #0f766e; color: #ffffff; box-shadow: 0 4px 12px rgba(15, 118, 110, 0.25); text-decoration: none;">
                    📄 Download Struk PDF (Format Thermal)
                </a>

                <button onclick="window.print()" class="btn btn-print">
                    🖨️ Cetak Standar Browser (System Print)
                </button>

                <a href="{{ route('pos.index') }}" class="btn btn-back">
                    ← Transaksi Baru (POS Kasir)
                </a>
            </div>

            <!-- Form Pelunasan Cepat Jika Masih Memiliki Sisa DP -->
            @if($order->payment_type === 'dp' && $order->remaining_balance > 0)
                <div class="settle-box">
                    <div style="font-weight: bold; color: #92400e; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-hand-holding-dollar"></i> Form Pelunasan Sisa DP
                    </div>
                    <p style="color: #78350f; font-size: 11px; margin-bottom: 10px;">
                        Sisa tagihan yang harus dilunasi pembeli saat ambil kacamata: 
                        <strong style="font-size: 13px; color: #b45309;">Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</strong>
                    </p>
                    <form action="{{ route('pos.settle', $order->id) }}" method="POST">
                        @csrf
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <div>
                                <label style="font-weight: 600; font-size: 11px;">Metode Pelunasan:</label>
                                <select name="settlement_payment_method" style="width: 100%; padding: 6px 10px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 12px; margin-top: 2px;">
                                    <option value="Cash / Tunai">Cash / Tunai</option>
                                    <option value="QRIS Statis Toko (BCA/GoPay)">QRIS Statis Toko (BCA/GoPay)</option>
                                    <option value="EDC Debit / Kartu Kredit BCA">Mesin EDC Kartu BCA</option>
                                    <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-weight: 600; font-size: 11px;">Nominal Uang Diterima (Rp):</label>
                                <input type="number" name="settlement_amount_paid" value="{{ $order->remaining_balance }}" min="{{ $order->remaining_balance }}" required style="width: 100%; padding: 6px 10px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 13px; font-weight: bold; margin-top: 2px;">
                            </div>
                            <button type="submit" class="btn btn-settle" style="padding: 9px; font-size: 12px;">
                                <i class="fa-solid fa-check-double"></i> Proses Pelunasan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <div class="receipt-container" id="printableReceipt">
            <div class="text-center">
                <div class="bold" style="font-size: 13px;">{{ $settings['store_name'] ?? 'ALYSA GLASSES' }}</div>
                <div style="font-size: 9px; margin-top: 2px;">{{ $settings['store_tagline'] ?? 'Eyewear That Completes Your Look 🫶🏻' }}</div>
                <div style="font-size: 9px; margin-top: 4px;">{{ $settings['store_address'] ?? 'Batam • Tanjung Pinang • Bintan' }}</div>
                <div style="font-size: 9px;">IG: @alysa_glasses | Telp: {{ $settings['store_phone'] ?? '0812-3456-7890' }}</div>
                
                @if($order->payment_type === 'dp' && $order->remaining_balance > 0)
                    <div class="dp-badge">STRUK UANG MUKA (DP)</div>
                @else
                    <div style="font-size: 9px; font-weight: bold; margin-top: 3px;">BUKTI PEMBAYARAN LUNAS</div>
                @endif
            </div>

            <div class="divider"></div>

            <div class="row">
                <span>No. Struk</span>
                <span class="bold">{{ $order->order_code }}</span>
            </div>
            <div class="row">
                <span>Tanggal</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="row">
                <span>Kasir</span>
                <span>{{ Auth::check() ? Auth::user()->name : 'Kasir Toko' }}</span>
            </div>
            <div class="row">
                <span>Pelanggan</span>
                <span class="bold">{{ $order->customer_name }}</span>
            </div>
            @if(!empty($order->customer_phone) && $order->customer_phone !== 'Walk-in Customer')
                <div class="row">
                    <span>No. WA</span>
                    <span>{{ $order->customer_phone }}</span>
                </div>
            @endif
            @if(!empty($order->shipping_address) && !str_contains($order->shipping_address, 'Transaksi Langsung di Store'))
                <div class="row" style="font-size: 8.5px;">
                    <span>Alamat</span>
                    <span style="text-align: right; max-width: 140px;">{{ $order->shipping_address }}</span>
                </div>
            @endif

            <div class="divider"></div>

            @foreach($order->items as $item)
                <div>
                    <div class="bold">{{ $item->product_name }}</div>
                    <div class="row">
                        <span>1 pcs x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        <span class="bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach

            @if($order->lens_price > 0 || !empty($order->lens_type_name))
                <div style="margin-top: 4px;">
                    <div>+ {{ $order->lens_type_name }}</div>
                    <div class="row">
                        <span>Lensa Kacamata</span>
                        <span>Rp {{ number_format($order->lens_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif

            @if($order->service_fee > 0)
                <div style="margin-top: 4px;">
                    <div>+ {{ $order->service_name ?: 'Upah Jasa / Servis' }}</div>
                    <div class="row">
                        <span>Jasa Reparasi / Pasang</span>
                        <span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif

            @if($order->has_prescription)
                <div class="divider"></div>
                <div class="bold text-center" style="font-size: 9px; margin-bottom: 2px;">UKURAN RESEP KACAMATA (RX)</div>
                <table class="rx-table">
                    <thead>
                        <tr>
                            <th>MATA</th>
                            <th>SPH</th>
                            <th>CYL</th>
                            <th>AXIS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="bold">R (OD)</td>
                            <td>{{ $order->sph_r ?: '0.00' }}</td>
                            <td>{{ $order->cyl_r ?: '0.00' }}</td>
                            <td>{{ $order->axis_r ?: '0' }}</td>
                        </tr>
                        <tr>
                            <td class="bold">L (OS)</td>
                            <td>{{ $order->sph_l ?: '0.00' }}</td>
                            <td>{{ $order->cyl_l ?: '0.00' }}</td>
                            <td>{{ $order->axis_l ?: '0' }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="row" style="font-size: 9px;">
                    <span>Jarak Pupil (PD):</span>
                    <span class="bold">{{ $order->pd }} mm</span>
                </div>
            @endif

            <div class="divider"></div>

            <div class="row">
                <span>Subtotal Frame:</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->lens_price > 0)
                <div class="row">
                    <span>Lensa Kacamata:</span>
                    <span>Rp {{ number_format($order->lens_price, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($order->service_fee > 0)
                <div class="row">
                    <span>Upah Jasa / Servis:</span>
                    <span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
                </div>
            @endif
            @if(($order->shipping_cost ?? 0) > 0)
                <div class="row">
                    <span>Ongkos Kirim ({{ $order->shipping_courier ?: 'Kurir' }}):</span>
                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($order->discount_amount > 0)
                <div class="row bold" style="color: #047857;">
                    <span>Diskon Voucher ({{ $order->voucher_code }}):</span>
                    <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="row bold" style="font-size: 11px; margin-top: 3px;">
                <span>TOTAL TRANSAKSI:</span>
                <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>

            <!-- Bagian Rincian Down Payment -->
            @if($order->payment_type === 'dp' && $order->remaining_balance > 0)
                <div class="divider"></div>
                <div class="row bold" style="color: #0284c7;">
                    <span>UANG MUKA (DP):</span>
                    <span>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
                </div>
                <div class="row bold" style="color: #b45309; font-size: 11px;">
                    <span>SISA TAGIHAN:</span>
                    <span>Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</span>
                </div>
                <div class="row">
                    <span>Bayar Uang Muka:</span>
                    <span>Rp {{ number_format($paid, 0, ',', '.') }}</span>
                </div>
                <div class="row bold">
                    <span>Kembalian DP:</span>
                    <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
                </div>
                <div class="row" style="font-size: 9px; margin-top: 2px;">
                    <span>Status:</span>
                    <span class="bold" style="color: #d97706;">BELUM LUNAS (SISA DP)</span>
                </div>
            @else
                <div class="divider"></div>
                <div class="row">
                    <span>Metode:</span>
                    <span>{{ $order->payment_method }}</span>
                </div>
                <div class="row">
                    <span>Bayar:</span>
                    <span>Rp {{ number_format($paid, 0, ',', '.') }}</span>
                </div>
                <div class="row bold">
                    <span>Kembali:</span>
                    <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
                </div>
                <div class="row" style="font-size: 9px; margin-top: 2px;">
                    <span>Status:</span>
                    <span class="bold" style="color: #047857;">LUNAS</span>
                </div>
            @endif

            <div class="divider"></div>

            <div class="text-center" style="font-size: 9px; margin-top: 6px; white-space: pre-line;">
@if($order->payment_type === 'dp' && $order->remaining_balance > 0)
*Harap bawa struk ini saat mengambil kacamata dan melunasi sisa tagihan.*
Garansi Lensa & Penyetelan Frame 30 Hari.
@else
{{ $settings['receipt_footer_notes'] ?? "Garansi Lensa & Penyetelan Frame 30 Hari.\nEyewear That Completes Your Look 🫶🏻" }}
@endif
            </div>
            <div class="text-center" style="font-size: 8px; margin-top: 6px; color: #555;">
                Simpan struk ini sebagai bukti klaim & pengambilan kacamata.
            </div>
        </div>

    </div>


    

</body>
</html>
