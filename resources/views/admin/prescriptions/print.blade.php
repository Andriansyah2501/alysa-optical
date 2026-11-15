<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Kartu Resep {{ $card->card_code }} — {{ $settings['store_name'] ?? 'Alysa Glasses' }}</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', system-ui, sans-serif; background: #e2e8f0; }

    .toolbar { position: sticky; top: 0; z-index: 50; display: flex; gap: 8px; justify-content: center; padding: 12px; background: rgba(15,23,42,.92); }
    .toolbar a { text-decoration: none; padding: 8px 18px; border-radius: 10px; font-size: 12px; font-weight: 700; }
    .btn-print { background: #059669; color: #fff; }
    .btn-back  { background: #334155; color: #cbd5e1; }

    .page { width: 148mm; min-height: 200mm; margin: 16px auto; background: #fff; padding: 10mm 11mm; box-shadow: 0 10px 30px rgba(0,0,0,.15); position: relative; }
    .page::before { content: ''; position: absolute; inset: 4mm; border: 1.5px solid #1e3a5f; border-radius: 3mm; pointer-events: none; }

    .hdr { display: flex; align-items: center; gap: 4mm; padding-bottom: 3.5mm; border-bottom: 2px solid #1e3a5f; }
    .hdr img { width: 17mm; height: 17mm; border-radius: 3mm; object-fit: cover; border: 1px solid #e2e8f0; }
    .hdr .store { font-family: Georgia, serif; font-size: 17px; font-weight: 800; color: #1e3a5f; letter-spacing: .3px; }
    .hdr .tagline { font-size: 8px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; }
    .hdr .addr { font-size: 8px; color: #475569; margin-top: .8mm; }
    .hdr .right { margin-left: auto; text-align: right; }
    .hdr .rtype { font-size: 10px; font-weight: 800; color: #7c3aed; text-transform: uppercase; letter-spacing: 1px; }
    .hdr .rcode { font-family: monospace; font-size: 10px; color: #334155; font-weight: 700; }
    .hdr .qr { margin-top: 2mm; text-align: right; }
    .hdr .qr svg { width: 16mm; height: 16mm; }
    .hdr .qr .qrcap { font-size: 6.5px; color: #64748b; }
    .hdr .rdate { font-size: 9px; color: #475569; margin-top: .8mm; }

    .sec-title { font-size: 8.5px; font-weight: 800; color: #1e3a5f; text-transform: uppercase; letter-spacing: 1px; margin: 4mm 0 1.8mm; }
    .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2mm 6mm; }
    .field { display: flex; font-size: 10px; color: #334155; }
    .field b { width: 26mm; flex-shrink: 0; color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; }
    .field span { font-weight: 700; }
    .name-lg { font-size: 14px; font-weight: 800; color: #0f172a; }

    table.rx { width: 100%; border-collapse: collapse; margin-top: 1mm; }
    table.rx th, table.rx td { border: 1px solid #1e3a5f; padding: 2.2mm 1.5mm; text-align: center; font-size: 11.5px; }
    table.rx thead th { background: #1e3a5f; color: #fff; font-size: 9px; text-transform: uppercase; letter-spacing: .8px; }
    table.rx td.eye { font-weight: 800; font-size: 9.5px; text-align: left; padding-left: 2.5mm; }
    table.rx td.eye.od { color: #1d4ed8; } table.rx td.eye.os { color: #b45309; }
    table.rx td.val { font-family: 'Consolas', monospace; font-weight: 800; font-size: 13px; color: #0f172a; }
    table.rx td.pdv { font-family: 'Consolas', monospace; font-weight: 800; font-size: 13px; }

    .pd-row { display: flex; gap: 6mm; margin-top: 2.5mm; font-size: 10px; color: #334155; }
    .pd-row b { color: #64748b; font-size: 9px; text-transform: uppercase; }
    .pd-row span { font-weight: 800; font-family: monospace; font-size: 12px; }

    .notes { margin-top: 3mm; border: 1px dashed #94a3b8; border-radius: 2mm; padding: 2.5mm 3mm; font-size: 9.5px; color: #334155; min-height: 10mm; }
    .notes .nt { font-size: 8px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: .8px; }

    .sig-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 8mm; margin-top: 7mm; text-align: center; }
    .sig-box { font-size: 9px; color: #475569; }
    .sig-space { height: 16mm; }
    .sig-line { border-top: 1.5px solid #1e3a5f; padding-top: 1.2mm; font-weight: 800; color: #1e3a5f; font-size: 10px; }
    .sig-stamp { font-size: 7.5px; color: #94a3b8; margin-top: .6mm; }

    .foot { position: absolute; left: 11mm; right: 11mm; bottom: 7mm; text-align: center; font-size: 7.5px; color: #94a3b8; border-top: .5px solid #e2e8f0; padding-top: 2mm; }
    .foot b { color: #1e3a5f; }

    @media print {
        body { background: #fff; }
        .toolbar { display: none; }
        .page { margin: 0; box-shadow: none; width: auto; min-height: auto; }
        @page { size: A5 portrait; margin: 0; }
    }
</style>
</head>
<body>
    <div class="toolbar no-print">
        <a href="javascript:window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Cetak Sekarang (A5)</a>
        <a href="{{ route('admin.prescriptions.pdf', $card->id) }}" class="btn-back"><i class="fa-solid fa-file-pdf"></i> Versi PDF</a>
        <a href="{{ route('admin.prescriptions.show', $card->id) }}" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="page">
        <!-- KOP + LOGO -->
        <div class="hdr">
            <img src="{{ asset('icons/icon-512.png') }}" alt="Logo {{ $settings['store_name'] ?? 'Alysa Glasses' }}">
            <div>
                <div class="store">{{ $settings['store_name'] ?? 'ALYSA GLASSES' }}</div>
                <div class="tagline">{{ $settings['store_tagline'] ?? 'Optical & Eyewear' }}</div>
                <div class="addr"><i class="fa-solid fa-location-dot"></i> {{ $settings['store_address'] ?? '' }} &nbsp;•&nbsp; <i class="fa-solid fa-phone"></i> {{ $settings['store_phone'] ?? '' }}</div>
            </div>
            <div class="right">
                <div class="rtype">Kartu Resep Kacamata</div>
                <div class="rcode">{{ $card->card_code }}</div>
                <div class="rdate">{{ \Carbon\Carbon::parse($card->exam_date)->translatedFormat('d F Y') }}</div>
                @if($card->qrSvgMarkup())
                <div class="qr">{!! $card->qrSvgMarkup() !!}<div class="qrcap">scan: kartu digital</div></div>
                @endif
            </div>
        </div>

        <!-- DATA PEMBELI -->
        <div class="sec-title"><i class="fa-solid fa-user"></i> Data Pembeli / Pasien</div>
        <div class="grid2">
            <div class="field" style="grid-column:1/-1"><b>Nama</b> <span class="name-lg">{{ $card->customer_name }}</span></div>
            <div class="field"><b>No. WA</b> <span>{{ $card->customer_phone ?: '-' }}</span></div>
            <div class="field"><b>JK / TTL</b> <span>{{ trim(($card->gender ?: '-').($card->birth_place ? ' / '.$card->birth_place : '').($card->birth_date ? ', '.$card->birth_date : ''), ' /') ?: '-' }}</span></div>
            <div class="field" style="grid-column:1/-1"><b>Alamat</b> <span>{{ $card->address ?: '-' }}</span></div>
        </div>

        <!-- RESEP -->
        <div class="sec-title"><i class="fa-solid fa-prescription"></i> Ukuran Resep Lensa (Diopter)</div>
        @php $fmt = fn($x) => App\Models\PrescriptionCard::fmtVal($x); @endphp
        <table class="rx">
            <thead>
                <tr><th style="text-align:left;width:34mm">Mata</th><th>SPH</th><th>CYL</th><th>AXIS</th><th>ADD</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td class="eye od">KA — Kanan (OD / R)</td>
                    <td class="val">{{ $fmt($card->sph_r) }}</td><td class="val">{{ $fmt($card->cyl_r) }}</td><td class="val">{{ $card->axis_r ?: '—' }}</td><td class="val">{{ $card->add_r ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="eye os">KI — Kiri (OS / L)</td>
                    <td class="val">{{ $fmt($card->sph_l) }}</td><td class="val">{{ $fmt($card->cyl_l) }}</td><td class="val">{{ $card->axis_l ?: '—' }}</td><td class="val">{{ $card->add_l ?: '—' }}</td>
                </tr>
            </tbody>
        </table>
        <div class="pd-row">
            <div><b>PD (Jarak Pupil):</b> <span>{{ $card->pd ? $card->pd.' mm' : '—' }}</span></div>
            <div><b>Kegunaan:</b> <span style="font-family:inherit">{{ $card->vision_goal ?: 'Jauh' }}</span></div>
            <div><b>Cara Ukur:</b> <span style="font-family:inherit">{{ $card->refraction_method ?: 'Auto Refraktometer' }}</span></div>
        </div>

        <div class="notes">
            <span class="nt">Catatan / Keluhan:</span><br>
            {{ $card->notes ?: 'Tidak ada catatan khusus. Resep berlaku selama tidak terjadi perubahan tajam penglihatan.' }}
            @if($card->origin === 'Rujukan Dokter' && $card->referrer_name)
                <br><span class="nt">Rujukan:</span> {{ $card->referrer_name }}
            @endif
        </div>

        <!-- TANDA TANGAN -->
        <div class="sig-wrap">
            <div class="sig-box">
                <div class="sig-space"></div>
                <div class="sig-line">Pemeriksa / Refraksionis</div>
                <div>{{ $card->examiner_name ?: ($card->user->name ?? '( ............................ )') }}</div>
            </div>
            <div class="sig-box">
                <div class="sig-space"></div>
                <div class="sig-line">Pasien</div>
                <div>{{ $card->customer_name }}</div>
            </div>
        </div>

        <div class="foot">
            Dokumen ini adalah salinan arsip resep — bawa saat pembelian / penggantian lensa berikutnya. &nbsp;
            <b>{{ $settings['store_name'] ?? 'ALYSA GLASSES' }}</b> © {{ date('Y') }} — {{ $settings['store_instagram'] ?? '' }}
        </div>
    </div>

    <script>window.addEventListener('load', () => { /* tidak auto-print: biar staf pilih kertas dulu */ });</script>
</body>
</html>
