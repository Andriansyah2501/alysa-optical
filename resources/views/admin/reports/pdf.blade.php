<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - Alysa Glasses</title>
    <style>
        @page {
            margin: 18mm 14mm 18mm 14mm;
            size: A4 portrait;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        body {
            font-size: 9pt;
            line-height: 1.35;
            background: #ffffff;
        }
        
        /* HEADER SURAT RESMI LAPORAN OPTIK */
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #c25b3a;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .brand-logo-text {
            font-size: 20pt;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #c25b3a;
            text-transform: uppercase;
        }
        .brand-tagline {
            font-size: 8.5pt;
            font-style: italic;
            color: #64748b;
            margin-top: 2px;
        }
        .brand-meta {
            font-size: 8pt;
            color: #475569;
            margin-top: 3px;
        }
        .report-badge-box {
            text-align: right;
        }
        .report-title-badge {
            display: inline-block;
            background: #fdf2e9;
            border: 1px solid #c25b3a;
            color: #9a3412;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-gen-date {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 4px;
        }

        /* METRIC SUMMARY BOXES (4 KPI) */
        .kpi-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 10px;
            width: 25%;
            text-align: left;
        }
        .kpi-card.highlight {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }
        .kpi-title {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3px;
        }
        .kpi-value {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
        }
        .kpi-card.highlight .kpi-value {
            color: #15803d;
        }
        .kpi-sub {
            font-size: 7pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* SECTION HEADER */
        .section-header {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            border-left: 3.5px solid #c25b3a;
            padding-left: 8px;
            margin-bottom: 8px;
            margin-top: 12px;
            text-transform: uppercase;
        }

        /* TABEL TRANSAKSI DETAIL */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.8pt;
            margin-bottom: 15px;
        }
        .detail-table th {
            background: #1e293b;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 6px;
            border: 1px solid #1e293b;
            font-size: 7.2pt;
            text-transform: uppercase;
        }
        .detail-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .detail-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .badge-type {
            display: inline-block;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1.5px 5px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .type-catalog { background: #dbeafe; color: #1e40af; }
        .type-custom { background: #e0f2fe; color: #0369a1; }
        .type-lens { background: #dcfce7; color: #15803d; }
        .type-service { background: #fef3c7; color: #92400e; }

        .badge-status {
            display: inline-block;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1.5px 5px;
            border-radius: 4px;
        }
        .status-lunas { background: #dcfce7; color: #15803d; }
        .status-dp { background: #fef3c7; color: #b45309; }

        .rx-box {
            font-size: 7pt;
            color: #9a3412;
            background: #fff7ed;
            padding: 2px 4px;
            border-radius: 3px;
            margin-top: 2px;
            display: inline-block;
        }

        /* TOTAL SUMMARY ROW */
        .total-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .total-summary-table td {
            padding: 4px 8px;
            font-size: 8.5pt;
        }

        /* FOOTER & TANDA TANGAN */
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 33.3%;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
        }
        .signature-space {
            height: 55px;
        }

        .footer-note {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT OPTIK RESMI -->
    <table class="header-table">
        <tr>
            <td style="width: 58%;">
                <div class="brand-logo-text">{{ $settings['store_name'] ?? 'ALYSA GLASSES' }}</div>
                <div class="brand-tagline">{{ str_replace(['🫶🏻', '🫶', '✨'], '', $settings['store_tagline'] ?? 'Eyewear That Completes Your Look') }}</div>
                <div class="brand-meta">
                    {{ $settings['store_address'] ?? 'Batam • Tanjung Pinang • Bintan' }} | WhatsApp: {{ $settings['store_phone'] ?? '0812-3456-7890' }} | Instagram: @alysa_glasses
                </div>
            </td>
            <td class="report-badge-box" style="width: 42%;">
                <div class="report-title-badge">{{ $reportHeader }}</div>
                <div class="report-gen-date">Periode: <strong>{{ $dateRangeText }}</strong></div>
                <div class="report-gen-date">Dicetak pada: {{ $generatedAt }} WIB oleh {{ $printedBy }}</div>
            </td>
        </tr>
    </table>

    <!-- 4 KPI RINGKASAN PERIODE -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card highlight">
                <div class="kpi-title">Total Omset Penjualan</div>
                <div class="kpi-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ $totalOrders }} transaksi tercatat</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Uang Tunai / DP Terkumpul</div>
                <div class="kpi-value">Rp {{ number_format($totalCollected, 0, ',', '.') }}</div>
                <div class="kpi-sub">Kas riil kasir toko</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Sisa Tagihan DP (Piutang)</div>
                <div class="kpi-value" style="color: #c2410c;">Rp {{ number_format($unpaidBalance, 0, ',', '.') }}</div>
                <div class="kpi-sub">Dilunasi saat ambil kacamata</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Rata-Rata Keranjang</div>
                <div class="kpi-value">Rp {{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 0, ',', '.') : 0 }}</div>
                <div class="kpi-sub">AOV per pelanggan</div>
            </td>
        </tr>
    </table>

    <!-- RINGKASAN DISTRIBUSI LAYANAN & LENSA -->
    <table style="width: 100%; margin-bottom: 12px; font-size: 7.5pt;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 8px;">
                    <strong style="color: #0f172a; text-transform: uppercase; font-size: 7pt; display: block; margin-bottom: 3px;">
                        Komposisi Penjualan:
                    </strong>
                    • Katalog Frame Toko: <strong>{{ $distribution['catalog'] }} order</strong><br>
                    • Custom Frame & Model Bebas: <strong>{{ $distribution['custom_frame'] }} order</strong><br>
                    • Ganti Kaca / Pasang Lensa Saja: <strong>{{ $distribution['lens_only'] }} order</strong><br>
                    • Upah Jasa Servis / Reparasi: <strong>{{ $distribution['service_only'] }} order</strong>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 8px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 8px;">
                    <strong style="color: #0f172a; text-transform: uppercase; font-size: 7pt; display: block; margin-bottom: 3px;">
                        Top Paket Lensa Terpasang:
                    </strong>
                    @forelse($topLensesSummary as $idx => $tl)
                        {{ $idx + 1 }}. {{ $tl->lens_type_name }} ({{ $tl->count }}x - Rp {{ number_format($tl->total_lens_rev, 0, ',', '.') }})<br>
                    @empty
                        - Belum ada data lensa pada rentang ini -
                    @endforelse
                </div>
            </td>
        </tr>
    </table>

    <!-- TABEL DETAIL TRANSAKSI RESMI -->
    <div class="section-header">Rincian Lengkap Riwayat Transaksi ({{ $totalOrders }} Item)</div>

    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 15%;">Kode & Waktu</th>
                <th style="width: 16%;">Pelanggan & No. WA</th>
                <th style="width: 25%;">Item Frame & Tipe</th>
                <th style="width: 18%;">Lensa & Ukuran Resep (Rx)</th>
                <th style="width: 12%;" class="text-right">Grand Total</th>
                <th style="width: 10%;" class="text-center">Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $ord)
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: #c25b3a; font-size: 8pt;">{{ $ord->order_code }}</strong>
                        <div style="font-size: 6.8pt; color: #64748b; margin-top: 1px;">
                            {{ $ord->created_at->format('d/m/Y H:i') }} WIB
                        </div>
                    </td>
                    <td>
                        <strong style="color: #0f172a;">{{ $ord->customer_name }}</strong>
                        <div style="font-size: 6.8pt; color: #64748b;">
                            {{ $ord->customer_phone ?: 'Walk-in' }}
                        </div>
                        @if(!empty($ord->shipping_address) && !str_contains($ord->shipping_address, 'Transaksi Langsung'))
                            <div style="font-size: 6.2pt; color: #475569; margin-top: 1px;">
                                📍 {{ Str::limit($ord->shipping_address, 35) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @php
                            $tType = $ord->transaction_type ?: 'catalog';
                            $typeClass = 'type-' . ($tType == 'custom_frame' ? 'custom' : ($tType == 'lens_only' ? 'lens' : ($tType == 'service_only' ? 'service' : 'catalog')));
                            $typeLabel = $tType == 'custom_frame' ? 'Custom' : ($tType == 'lens_only' ? 'Lensa Saja' : ($tType == 'service_only' ? 'Jasa Servis' : 'Katalog'));
                        @endphp
                        <span class="badge-type {{ $typeClass }}">{{ $typeLabel }}</span>
                        
                        <div style="margin-top: 2px; font-weight: 600;">
                            @foreach($ord->items as $it)
                                {{ $it->product_name }}@if(!$loop->last), @endif
                            @endforeach
                        </div>
                        @if($ord->service_fee > 0)
                            <div style="font-size: 6.8pt; color: #b45309; margin-top: 1px;">
                                + {{ $ord->service_name ?: 'Biaya Servis' }} (Rp {{ number_format($ord->service_fee, 0, ',', '.') }})
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($ord->lens_type_name)
                            <strong style="color: #0f172a;">{{ $ord->lens_type_name }}</strong>
                            @if($ord->has_prescription)
                                <div class="rx-box">
                                    R: {{ $ord->sph_r ?: '0' }}/{{ $ord->cyl_r ?: '0' }} | L: {{ $ord->sph_l ?: '0' }}/{{ $ord->cyl_l ?: '0' }} (PD:{{ $ord->pd ?: '62' }})
                                </div>
                            @endif
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Tanpa Lensa</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <strong style="font-size: 8.5pt; color: #0f172a;">Rp {{ number_format($ord->grand_total, 0, ',', '.') }}</strong>
                        <div style="font-size: 6.8pt; color: #64748b;">
                            {{ $ord->payment_method }}
                        </div>
                        @if($ord->discount_amount > 0)
                            <div style="font-size: 6.5pt; color: #15803d;">
                                Diskon: -Rp {{ number_format($ord->discount_amount, 0, ',', '.') }}
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($ord->payment_type === 'dp' && $ord->remaining_balance > 0)
                            <span class="badge-status status-dp">DP TERBAYAR</span>
                            <div style="font-size: 6.5pt; font-weight: bold; color: #c2410c; margin-top: 1px;">
                                Sisa: Rp {{ number_format($ord->remaining_balance, 0, ',', '.') }}
                            </div>
                        @else
                            <span class="badge-status status-lunas">LUNAS</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 18px; color: #94a3b8;">
                        Tidak ada transaksi optik tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TOTAL FOOTER SUMMARY -->
    <table class="total-summary-table" style="page-break-inside: avoid;">
        <tr style="background: #f1f5f9; border-top: 1.5px solid #cbd5e1; border-bottom: 1.5px solid #cbd5e1;">
            <td style="font-weight: bold; text-transform: uppercase;">Akumulasi Subtotal Frame & Produk</td>
            <td class="text-right font-bold">Rp {{ number_format($orders->sum('subtotal'), 0, ',', '.') }}</td>
        </tr>
        <tr style="background: #f1f5f9; border-bottom: 1.5px solid #cbd5e1;">
            <td style="font-weight: bold; text-transform: uppercase;">Akumulasi Paket Lensa Kacamata</td>
            <td class="text-right font-bold">Rp {{ number_format($orders->sum('lens_price'), 0, ',', '.') }}</td>
        </tr>
        <tr style="background: #f1f5f9; border-bottom: 1.5px solid #cbd5e1;">
            <td style="font-weight: bold; text-transform: uppercase;">Akumulasi Upah Jasa Servis & Reparasi</td>
            <td class="text-right font-bold">Rp {{ number_format($orders->sum('service_fee'), 0, ',', '.') }}</td>
        </tr>
        @if($orders->sum('discount_amount') > 0)
            <tr style="background: #f0fdf4; border-bottom: 1.5px solid #bbf7d0;">
                <td style="font-weight: bold; color: #15803d; text-transform: uppercase;">Total Potongan Diskon Voucher</td>
                <td class="text-right font-bold" style="color: #15803d;">- Rp {{ number_format($orders->sum('discount_amount'), 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr style="background: #fdf2e9; border-top: 2px solid #c25b3a; border-bottom: 2px solid #c25b3a;">
            <td style="font-size: 10pt; font-weight: 900; color: #9a3412; text-transform: uppercase;">GRAND TOTAL PENJUALAN PERIODE INI</td>
            <td class="text-right" style="font-size: 11pt; font-weight: 900; color: #9a3412;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- LEMBAR PENGESAHAN LAPORAN -->
    <table class="signature-table">
        <tr>
            <td>
                Dibuat Oleh,<br>
                <strong>Kasir Store / Frontliner</strong>
                <div class="signature-space"></div>
                <u>( ........................................... )</u><br>
                <span style="font-size: 7pt; color: #64748b;">NIP. STF-KSR-01</span>
            </td>
            <td>
                Diverifikasi Oleh,<br>
                <strong>Refraksionis Optik / Lab</strong>
                <div class="signature-space"></div>
                <u>( ........................................... )</u><br>
                <span style="font-size: 7pt; color: #64748b;">RO / Refractionist Lab</span>
            </td>
            <td>
                Mengetahui & Menyetujui,<br>
                <strong>Store Manager / Owner</strong>
                <div class="signature-space"></div>
                <u>( ........................................... )</u><br>
                <span style="font-size: 7pt; color: #64748b;">Alysa Glasses Management</span>
            </td>
        </tr>
    </table>

    <!-- FOOTER CATATAN -->
    <div class="footer-note">
        Dokumen ini diterbitkan secara otomatis oleh Sistem Enterprise Point of Sales & Backoffice <strong>Alysa Glasses Batam</strong>.<br>
        Segala bentuk selisih kas, piutang DP, atau retur klaim garansi lensa mengacu pada nomor referensi transaksi yang tertera pada dokumen ini.
    </div>

</body>
</html>
