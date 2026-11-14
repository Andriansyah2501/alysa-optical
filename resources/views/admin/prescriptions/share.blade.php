<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kartu Resep Digital {{ $card->card_code }} — {{ $settings['store_name'] ?? 'Alysa Glasses' }}</title>
<link rel="icon" href="/icons/icon-192.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .print-card { box-shadow: none !important; border: none !important; margin: 0 !important; max-width: 100% !important; }
        @page { size: A5 portrait; margin: 8mm; }
    }
</style>
</head>
<body class="bg-slate-100">
@php $fmt = fn($x) => App\Models\PrescriptionCard::fmtVal($x); @endphp
<div class="max-w-md mx-auto px-3 py-5 print-card">

    <!-- KOP + LOGO -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-blue-50 to-emerald-50 border-b border-slate-200 flex items-center gap-3">
            <img src="/icons/icon-512.png" class="w-14 h-14 rounded-2xl shadow bg-white object-cover" alt="Logo">
            <div class="flex-1">
                <h1 class="font-serif font-extrabold text-slate-900 leading-tight">{{ $settings['store_name'] ?? 'ALYSA GLASSES' }}</h1>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">{{ $settings['store_tagline'] ?? 'Optical & Eyewear' }}</p>
                <p class="text-[10px] text-slate-400"><i class="fa-solid fa-phone mr-0.5"></i>{{ $settings['store_phone'] ?? '' }}</p>
            </div>
            <div class="text-right">
                <span class="text-[9px] font-extrabold text-violet-600 uppercase block">Kartu Resep</span>
                <span class="font-mono text-[10px] font-bold text-slate-700">{{ $card->card_code }}</span>
            </div>
        </div>

        <!-- PASIEN -->
        <div class="px-4 pt-3 text-xs text-slate-600 space-y-0.5">
            <div class="text-sm font-extrabold text-slate-900">{{ $card->customer_name }}</div>
            <div>{{ $card->customer_phone }}{{ $card->gender ? ' • '.$card->gender : '' }}{{ ($card->birth_place||$card->birth_date) ? ' • '.trim(($card->birth_place??'').($card->birth_date?', '.$card->birth_date:'')) : '' }}</div>
            @if($card->address)<div><i class="fa-solid fa-location-dot text-slate-400 mr-0.5"></i>{{ $card->address }}</div>@endif
            <div class="text-[10px] text-slate-400">Periksa: {{ \Carbon\Carbon::parse($card->exam_date)->translatedFormat('d F Y') }} • {{ $card->examiner_name ?: 'Refraksionis Toko' }}</div>
        </div>

        <!-- TABEL RESEP -->
        <div class="p-4">
            <table class="w-full border-collapse text-center text-sm">
                <thead>
                    <tr class="bg-slate-900 text-white text-[9px] uppercase tracking-wider">
                        <th class="border border-slate-700 px-2 py-2 text-left">Mata</th>
                        <th class="border border-slate-700 px-2 py-2">SPH</th>
                        <th class="border border-slate-700 px-2 py-2">CYL</th>
                        <th class="border border-slate-700 px-2 py-2">AXIS</th>
                        <th class="border border-slate-700 px-2 py-2">ADD</th>
                    </tr>
                </thead>
                <tbody class="font-mono font-bold">
                    <tr class="bg-blue-50/50">
                        <td class="border border-slate-200 px-2 py-2.5 text-left text-blue-800 font-sans font-extrabold text-[11px]">KANAN (OD)</td>
                        <td class="border border-slate-200 px-2 py-2.5 text-slate-900">{{ $fmt($card->sph_r) }}</td>
                        <td class="border border-slate-200 px-2 py-2.5">{{ $fmt($card->cyl_r) }}</td>
                        <td class="border border-slate-200 px-2 py-2.5">{{ $card->axis_r ?: '—' }}</td>
                        <td class="border border-slate-200 px-2 py-2.5">{{ $card->add_r ?: '—' }}</td>
                    </tr>
                    <tr class="bg-amber-50/50">
                        <td class="border border-slate-200 px-2 py-2.5 text-left text-amber-800 font-sans font-extrabold text-[11px]">KIRI (OS)</td>
                        <td class="border border-slate-200 px-2 py-2.5 text-slate-900">{{ $fmt($card->sph_l) }}</td>
                        <td class="border border-slate-200 px-2 py-2.5">{{ $fmt($card->cyl_l) }}</td>
                        <td class="border border-slate-200 px-2 py-2.5">{{ $card->axis_l ?: '—' }}</td>
                        <td class="border border-slate-200 px-2 py-2.5">{{ $card->add_l ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="border border-slate-200 px-2 py-2 text-[11px] font-sans">
                            <b class="text-slate-500">PD:</b> <span class="font-mono font-extrabold">{{ $card->pd ? $card->pd.' mm' : '—' }}</span>
                            <span class="mx-2 text-slate-300">|</span><b class="text-slate-500">Kegunaan:</b> {{ $card->vision_goal ?: 'Jauh' }}
                            <span class="mx-2 text-slate-300">|</span><b class="text-slate-500">Ukur:</b> {{ $card->refraction_method ?: 'Auto' }}
                        </td>
                    </tr>
                </tbody>
            </table>

            @if($card->notes)
            <div class="mt-3 p-3 bg-sand-50 rounded-xl text-[11px] text-slate-600 border border-dashed border-slate-300"><b>Catatan:</b> {{ $card->notes }}</div>
            @endif

            <!-- QR -->
            <div class="mt-4 flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 no-print">
                <div class="qr-box bg-white p-1.5 rounded-xl border border-slate-200 w-24 h-24 flex items-center justify-center overflow-hidden">{!! $qr !!}</div>
                <div class="text-[10px] text-slate-500 leading-relaxed">
                    <b class="text-slate-700 block mb-0.5"><i class="fa-solid fa-qrcode text-violet-600 mr-1"></i>Scan untuk buka kartu ini</b>
                    Tautan pribadi Anda — tunjukkan QR ini di kasir saat beli / ganti lensa berikutnya.
                </div>
            </div>
        </div>

        <div class="px-4 pb-4 text-center text-[9px] text-slate-400 border-t border-slate-100 pt-3">
            {{ $settings['store_address'] ?? '' }} — simpan kartu ini untuk pembelian lensa berikutnya.
        </div>
    </div>

    <!-- AKSI -->
    <div class="grid grid-cols-2 gap-2 mt-4 no-print">
        <button onclick="window.print()" class="px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-extrabold shadow-sm"><i class="fa-solid fa-print mr-1"></i> Cetak Kartu</button>
        <a href="{{ route('rx.share.pdf', $card->share_token) }}" class="px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-xs font-extrabold shadow-sm text-center"><i class="fa-solid fa-download mr-1"></i> Download PDF</a>
    </div>
    <p class="text-center text-[10px] text-slate-400 mt-3 no-print">Dokumen digital resmi — data terenkripsi tautan pribadi, hanya bisa dibuka lewat tautan/QR ini.</p>
</div>
<script>
/* QR svg tailwind — paksa ukuran rapi */
document.querySelectorAll('.qr-box svg').forEach(s => { s.setAttribute('width','84'); s.setAttribute('height','84'); s.style.width='84px'; s.style.height='84px'; });
</script>
</body>
</html>
