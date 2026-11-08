<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Struk - {{ $order->order_code }}</title>
<style>
    @page {
        margin: 0px;
    }
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: "DejaVu Sans Mono", monospace;
    }
    body {
        width: 220px;
        margin: 0 auto;
        padding: 6px 10px;
        background: #fff;
        color: #000;
        font-size: 8px;
        line-height: 1.25;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: bold; }
    .divider {
        border-top: 1px dashed #000;
        margin: 4px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    td {
        padding: 1px 0;
        font-size: 8px;
        vertical-align: top;
    }
    .rx-table {
        width: 100%;
        border-collapse: collapse;
        margin: 3px 0;
    }
    .rx-table th, .rx-table td {
        border: 1px solid #000;
        padding: 2px 1px;
        text-align: center;
        font-size: 7.5px;
    }
    .dp-badge {
        display: inline-block;
        background: #fef3c7;
        color: #92400e;
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: bold;
        font-size: 7.5px;
        margin-top: 3px;
    }
</style>
</head>
<body>

    <!-- 1. Header Toko -->
    <div class="text-center">
        <div class="bold" style="font-size: 11px;">{{ $settings['store_name'] ?? 'ALYSA GLASSES' }}</div>
        <div style="font-size: 7px; margin-top: 1px;">{{ $settings['store_tagline'] ?? 'Eyewear That Completes Your Look' }}</div>
        <div style="font-size: 7px;">{{ $settings['store_address'] ?? 'Batam • Tanjung Pinang • Bintan' }}</div>
        <div style="font-size: 7px;">IG: @alysa_glasses | Telp: {{ $settings['store_phone'] ?? '0812-3456-7890' }}</div>
        
        @if($order->payment_type === 'dp' && $order->remaining_balance > 0)
            <div class="dp-badge">STRUK UANG MUKA (DP)</div>
        @else
            <div style="font-size: 8px; font-weight: bold; margin-top: 3px;">BUKTI PEMBAYARAN LUNAS</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- 2. Metadata Transaksi -->
    <table>
        <tr>
            <td style="width: 28%;">No. Struk</td>
            <td class="bold text-right" style="width: 72%;">{{ $order->order_code }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="text-right">{{ Auth::check() ? Auth::user()->name : 'Kasir Toko' }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td class="bold text-right">{{ $order->customer_name }}</td>
        </tr>
        @if(!empty($order->customer_phone) && $order->customer_phone !== 'Walk-in Customer')
        <tr>
            <td>No. WA</td>
            <td class="text-right">{{ $order->customer_phone }}</td>
        </tr>
        @endif
        @if(!empty($order->shipping_address) && !str_contains($order->shipping_address, 'Transaksi Langsung di Store'))
        <tr>
            <td>Alamat</td>
            <td class="text-right" style="font-size: 7px;">{{ $order->shipping_address }}</td>
        </tr>
        @endif
    </table>

    <div class="divider"></div>

    <!-- 3. Rincian Item Produk -->
    @foreach($order->items as $item)
        <div style="margin-bottom: 2px;">
            <div class="bold">{{ $item->product_name }}</div>
            <table>
                <tr>
                    <td>1 pcs x Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="bold text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endforeach

    @if($order->lens_price > 0 || !empty($order->lens_type_name))
        <div style="margin-top: 2px;">
            <div>+ {{ $order->lens_type_name }}</div>
            <table>
                <tr>
                    <td>Lensa Kacamata</td>
                    <td class="text-right">Rp {{ number_format($order->lens_price, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endif

    @if($order->service_fee > 0)
        <div style="margin-top: 2px;">
            <div>+ {{ $order->service_name ?: 'Upah Jasa / Servis' }}</div>
            <table>
                <tr>
                    <td>Jasa Reparasi / Pasang</td>
                    <td class="text-right">Rp {{ number_format($order->service_fee, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- 4. Resep Refraksi Lensa (RX) Grid 4x3 Kompak -->
    @if($order->has_prescription)
        <div class="divider"></div>
        <div class="bold text-center" style="font-size: 7.5px;">UKURAN RESEP KACAMATA (RX)</div>
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
        <table>
            <tr>
                <td style="font-size: 7.5px;">Jarak Pupil (PD):</td>
                <td class="bold text-right" style="font-size: 7.5px;">{{ $order->pd }} mm</td>
            </tr>
        </table>
    @endif

    <div class="divider"></div>

    <!-- 5. Perhitungan Total & Pembayaran -->
    <table>
        <tr>
            <td>Subtotal Frame:</td>
            <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->lens_price > 0)
        <tr>
            <td>Lensa Kacamata:</td>
            <td class="text-right">Rp {{ number_format($order->lens_price, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($order->service_fee > 0)
        <tr>
            <td>Upah Jasa / Servis:</td>
            <td class="text-right">Rp {{ number_format($order->service_fee, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if(($order->shipping_cost ?? 0) > 0)
        <tr>
            <td>Ongkir ({{ $order->shipping_courier ?: 'Kurir' }}):</td>
            <td class="text-right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($order->discount_amount > 0)
        <tr class="bold" style="color: #047857;">
            <td>Diskon ({{ $order->voucher_code }}):</td>
            <td class="text-right">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="bold" style="font-size: 9px;">
            <td style="padding-top: 2px;">TOTAL TRANSAKSI:</td>
            <td class="text-right" style="padding-top: 2px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- 6. Status Pelunasan DP vs Lunas -->
    @if($order->payment_type === 'dp' && $order->remaining_balance > 0)
        <div class="divider"></div>
        <table>
            <tr class="bold" style="color: #0284c7;">
                <td>UANG MUKA (DP):</td>
                <td class="text-right">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="bold" style="color: #b45309; font-size: 8.5px;">
                <td>SISA TAGIHAN:</td>
                <td class="text-right">Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar Uang Muka:</td>
                <td class="text-right">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="bold">
                <td>Kembalian DP:</td>
                <td class="text-right">Rp 0</td>
            </tr>
            <tr style="font-size: 7.5px;">
                <td>Status:</td>
                <td class="bold text-right" style="color: #d97706;">BELUM LUNAS (SISA DP)</td>
            </tr>
        </table>
    @else
        <div class="divider"></div>
        <table>
            <tr>
                <td>Metode:</td>
                <td class="text-right">{{ $order->payment_method }}</td>
            </tr>
            <tr>
                <td>Bayar:</td>
                <td class="text-right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr class="bold">
                <td>Kembali:</td>
                <td class="text-right">Rp 0</td>
            </tr>
            <tr style="font-size: 7.5px;">
                <td>Status:</td>
                <td class="bold text-right" style="color: #047857;">LUNAS</td>
            </tr>
        </table>
    @endif

    <div class="divider"></div>

    <!-- 7. Kebijakan & Footer -->
    <div class="text-center" style="font-size: 7px; margin-top: 2px; line-height: 1.15;">
        @if($order->payment_type === 'dp' && $order->remaining_balance > 0)
            *Harap bawa struk ini saat ambil kacamata*<br>
            Garansi Lensa & Penyetelan Frame 30 Hari.<br>
            <span style="font-size: 6.5px; color: #555;">Simpan struk ini sebagai bukti klaim kacamata.</span>
        @else
            {{ $settings['receipt_footer_notes'] ?? "Garansi Lensa & Penyetelan Frame 30 Hari.\nEyewear That Completes Your Look" }}
        @endif
    </div>

</body>
</html>
