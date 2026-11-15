<?php
    $fmt = fn($x) => App\Models\PrescriptionCard::fmtVal($x);
    $logo = public_path('icons/icon-512.png');
    $store = $settings['store_name'] ?? 'ALYSA GLASSES';
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
    * { margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 9px; }
    .outer { border: 2px solid #1e3a5f; padding: 10px; }
    table { border-collapse: collapse; width: 100%; }
    .hdr td { vertical-align: middle; border-bottom: 2px solid #1e3a5f; padding-bottom: 6px; }
    .logo { width: 42px; }
    .store { font-size: 15px; font-weight: bold; color: #1e3a5f; }
    .tagline { font-size: 6.5px; color: #64748b; letter-spacing: 1px; font-weight: bold; }
    .addr { font-size: 6.5px; color: #475569; padding-top: 2px; }
    .rtype { font-size: 8px; font-weight: bold; color: #7c3aed; text-align: right; letter-spacing: 1px; }
    .rcode { font-size: 8.5px; color: #334155; text-align: right; font-weight: bold; }
    .rdate { font-size: 7.5px; color: #64748b; text-align: right; }
    .sec { font-size: 7.5px; font-weight: bold; color: #1e3a5f; letter-spacing: 1px; padding: 8px 0 3px 0; }
    .f td { padding: 1.5px 0; vertical-align: top; }
    .fl { width: 22%; color: #64748b; font-weight: bold; font-size: 7.5px; text-transform: uppercase; }
    .fv { font-weight: bold; color: #0f172a; }
    .name { font-size: 12px; font-weight: bold; }
    table.rx { border: 1px solid #1e3a5f; margin-top: 3px; }
    table.rx th, table.rx td { border: 1px solid #1e3a5f; text-align: center; padding: 4.5px 2px; font-size: 9.5px; }
    table.rx th { background: #1e3a5f; color: #fff; font-size: 7.5px; letter-spacing: 1px; }
    td.eye { text-align: left; font-weight: bold; font-size: 8px; padding-left: 5px; width: 34%; }
    td.od { color: #1d4ed8; } td.os { color: #b45309; }
    td.val { font-weight: bold; font-size: 11px; }
    .meta td { padding-top: 5px; font-size: 7.5px; color: #475569; }
    .meta b { color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .meta .v { color: #0f172a; font-weight: bold; font-size: 8.5px; }
    .notes { border: 1px dashed #94a3b8; padding: 5px; margin-top: 6px; font-size: 7.5px; color: #334155; }
    .notes .nt { font-size: 6.5px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: .8px; }
    .sig td { padding-top: 22px; text-align: center; font-size: 8px; color: #475569; }
    .sigline { border-top: 1.5px solid #1e3a5f; font-weight: bold; color: #1e3a5f; font-size: 8.5px; padding-top: 2px; }
    .foot { text-align: center; font-size: 6.5px; color: #94a3b8; border-top: 1px solid #e2e8f0; margin-top: 10px; padding-top: 4px; }
    .foot b { color: #1e3a5f; }
</style></head>
<body>
<div class="outer">
    <table class="hdr">
        <tr>
            @if(file_exists($logo))<td class="logo"><img src="{{ $logo }}" style="width:40px;height:40px;"></td>@endif
            <td>
                <div class="store">{{ $store }}</div>
                <div class="tagline">{{ $settings['store_tagline'] ?? 'OPTICAL & EYEWEAR' }}</div>
                <div class="addr">{{ $settings['store_address'] ?? '' }} &nbsp;•&nbsp; Telp/WA: {{ $settings['store_phone'] ?? '' }}</div>
            </td>
            <td width="32%">
                <table style="width:100%;border-collapse:collapse;"><tr>
                <td style="vertical-align:top;">
                    <div class="rtype">KARTU RESEP KACAMATA</div>
                    <div class="rcode">{{ $card->card_code }}</div>
                    <div class="rdate">{{ \Carbon\Carbon::parse($card->exam_date)->translatedFormat('d F Y') }}</div>
                </td>
                @if($card->qrPngDataUri())
                <td style="width:62px;text-align:right;vertical-align:top;">
                    <img src="{{ $card->qrPngDataUri() }}" style="width:58px;height:58px;">
                    <div style="font-size:5.5pt;color:#64748b;text-align:center;margin-top:2px;">scan: kartu digital</div>
                </td>
                @endif
                </tr></table>
            </td>
        </tr>
    </table>

    <div class="sec">DATA PEMBELI / PASIEN</div>
    <table class="f">
        <tr><td class="fl">Nama</td><td class="fv name">{{ $card->customer_name }}</td></tr>
        <tr><td class="fl">No. WA</td><td class="fv">{{ $card->customer_phone ?: '-' }}</td></tr>
        <tr><td class="fl">JK / TTL</td><td class="fv">{{ trim(($card->gender ?: '-').($card->birth_place ? ' / '.$card->birth_place : '').($card->birth_date ? ', '.$card->birth_date : ''), ' /') ?: '-' }}</td></tr>
        <tr><td class="fl">Alamat</td><td class="fv">{{ $card->address ?: '-' }}</td></tr>
        @if($card->order_code)<tr><td class="fl">Kaitan Struk</td><td class="fv">{{ $card->order_code }}</td></tr>@endif
    </table>

    <div class="sec">UKURAN RESEP LENSA (DIOPTER)</div>
    <table class="rx">
        <tr><th style="text-align:left;padding-left:5px">MATA</th><th>SPH</th><th>CYL</th><th>AXIS</th><th>ADD</th></tr>
        <tr><td class="eye od">KA — KANAN (OD / R)</td><td class="val">{{ $fmt($card->sph_r) }}</td><td class="val">{{ $fmt($card->cyl_r) }}</td><td class="val">{{ $card->axis_r ?: '—' }}</td><td class="val">{{ $card->add_r ?: '—' }}</td></tr>
        <tr><td class="eye os">KI — KIRI (OS / L)</td><td class="val">{{ $fmt($card->sph_l) }}</td><td class="val">{{ $fmt($card->cyl_l) }}</td><td class="val">{{ $card->axis_l ?: '—' }}</td><td class="val">{{ $card->add_l ?: '—' }}</td></tr>
    </table>
    <table class="meta">
        <tr>
            <td><b>PD: </b><span class="v">{{ $card->pd ? $card->pd.' mm' : '—' }}</span></td>
            <td><b>Kegunaan: </b><span class="v">{{ $card->vision_goal ?: 'Jauh' }}</span></td>
            <td><b>Cara Ukur: </b><span class="v">{{ $card->refraction_method ?: 'Auto Refraktometer' }}</span></td>
            <td><b>Sumber: </b><span class="v">{{ $card->origin ?: 'Pemeriksaan Toko' }}</span></td>
        </tr>
    </table>

    <div class="notes">
        <span class="nt">Catatan / Keluhan:</span><br>
        {{ $card->notes ?: 'Tidak ada catatan khusus. Resep berlaku selama tidak terjadi perubahan tajam penglihatan.' }}
        @if($card->origin === 'Rujukan Dokter' && $card->referrer_name)<br><span class="nt">Rujukan:</span> {{ $card->referrer_name }}@endif
    </div>

    <table class="sig">
        <tr>
            <td width="45%">
                <div class="sigline">Pemeriksa / Refraksionis</div>
                <div>({{ $card->examiner_name ?: ($card->user->name ?? '............................') }})</div>
            </td>
            <td width="10%"></td>
            <td width="45%">
                <div class="sigline">Pasien</div>
                <div>({{ $card->customer_name }})</div>
            </td>
        </tr>
    </table>

    <div class="foot">Bawa kartu ini saat pembelian / penggantian lensa berikutnya. &nbsp;<b>{{ $store }}</b> © {{ date('Y') }} — {{ $settings['store_instagram'] ?? '' }}</div>
</div>
</body></html>
