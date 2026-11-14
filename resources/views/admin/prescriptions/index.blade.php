@extends('layouts.app')

@section('title', 'Kartu Resep Kacamata | Alysa Glasses')

@section('content')
<div class="max-w-6xl mx-auto px-3 sm:px-4 py-5 space-y-4">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <img src="/icons/icon-192.png" alt="Logo Alysa Glasses" class="w-11 h-11 rounded-2xl shadow-sm object-cover">
            <div>
                <span class="text-xs font-bold text-brand-600 uppercase tracking-widest">Refraksi & Pelayanan Pelanggan</span>
                <h1 class="font-serif text-xl sm:text-2xl font-bold text-slate-900 leading-tight">Kartu Resep Kacamata</h1>
                <p class="text-[11px] text-slate-500 mt-0.5">Arsip resep lengkap dengan data pasien — siap cetak & kirim ke pelanggan.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.prescriptions.create') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Buat Kartu Resep
            </a>
        </div>
    </div>

    <!-- PANEL GATEWAY WA + AUTO KIRIM n8n -->
    <div class="bg-white rounded-3xl border border-emerald-200 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-center gap-3 flex-1">
            <div id="waDot" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-lg"><i class="fa-brands fa-whatsapp"></i></div>
            <div>
                <span class="text-xs font-extrabold text-slate-900 block">Gateway WhatsApp — Otomasi n8n</span>
                <span id="waStatusText" class="text-[10px] text-slate-500">Cek status koneksi...</span>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.prescriptions.wa.toggle') }}" class="flex items-center gap-2">
            @csrf
            <input type="hidden" name="enabled" value="{{ (string) ($settings['rx_wa_auto'] ?? '1') === '1' ? '0' : '1' }}">
            <button class="px-3 py-2 rounded-xl text-[11px] font-bold border transition-all {{ (string) ($settings['rx_wa_auto'] ?? '1') === '1' ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' : 'bg-white text-slate-600 border-sand-300 hover:bg-sand-50' }}">
                <i class="fa-solid fa-bolt mr-1"></i> Auto-Kirim saat kartu dibuat: <b>{{ (string) ($settings['rx_wa_auto'] ?? '1') === '1' ? 'AKTIF ✅' : 'NONAKTIF' }}</b>
            </button>
        </form>
    </div>

    <!-- MODAL QR PAIRING WAHA -->
    <div id="qrModal" class="hidden fixed inset-0 z-[70] bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full text-center shadow-2xl">
            <img src="/icons/icon-192.png" class="w-12 h-12 mx-auto rounded-2xl mb-3 shadow-sm object-cover" alt="Logo">
            <h3 class="font-serif font-extrabold text-slate-900">Scan QR untuk Sambungkan WA</h3>
            <p class="text-[11px] text-slate-500 mt-1 mb-3">Buka WhatsApp di HP owner → <b>Perangkat Tertaut</b> → <b>Tautkan Perangkat</b> → arahkan ke QR ini. QR berganti otomatis.</p>
            <div class="border-4 border-slate-900 rounded-2xl p-2 bg-white inline-block"><img id="qrImg" src="" alt="QR Pairing WA" class="w-56 h-56"></div>
            <p id="qrHint" class="text-[10px] text-slate-400 mt-3">Status: menunggu scan... (refresh otomatis)</p>
            <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="mt-4 px-4 py-2 bg-sand-100 hover:bg-sand-200 rounded-xl text-xs font-bold text-slate-700">Tutup</button>
        </div>
    </div>

    <!-- STAT + SEARCH -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="p-4 bg-white rounded-2xl border border-sand-200 flex items-center justify-between">
            <div><span class="text-[10px] font-bold text-slate-400 uppercase block">Total Kartu Resep</span>
            <span class="text-xl font-extrabold text-slate-900">{{ $totalCards }}</span></div>
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"><i class="fa-solid fa-prescription"></i></div>
        </div>
        <div class="p-4 bg-white rounded-2xl border border-sand-200 flex items-center justify-between">
            <div><span class="text-[10px] font-bold text-slate-400 uppercase block">Dibuat Bulan Ini</span>
            <span class="text-xl font-extrabold text-emerald-700">{{ $thisMonth }}</span></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-calendar-check"></i></div>
        </div>
        <form method="GET" class="relative">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / kode kartu / struk / WA..." class="w-full h-full px-4 py-2 bg-white border border-sand-300 rounded-2xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-brand-400">
            <button class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-slate-900 hover:bg-slate-700 text-white rounded-xl text-[10px] font-bold"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <!-- TABEL -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs divide-y divide-sand-200">
                <thead class="bg-sand-50 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Kode & Tgl Periksa</th>
                        <th class="px-4 py-3">Pasien</th>
                        <th class="px-4 py-3 text-center">Resep Kanan (OD)</th>
                        <th class="px-4 py-3 text-center">Resep Kiri (OS)</th>
                        <th class="px-4 py-3 text-center">PD</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $c)
                    <tr class="hover:bg-sand-50/60 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono font-bold text-slate-800 block">{{ $c->card_code }}</span>
                            <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($c->exam_date)->translatedFormat('d M Y') }}</span>
                            @if($c->wa_sent_at)
                                <span class="text-[9px] {{ str_starts_with((string)$c->wa_status,'Terkirim') ? 'text-emerald-600' : 'text-rose-600' }} font-bold block"><i class="fa-brands fa-whatsapp mr-0.5"></i>{{ $c->wa_status }}</span>
                            @endif
                            @if($c->order_code)<span class="text-[9px] font-mono text-brand-600 block">{{ $c->order_code }}</span>@endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-slate-900 block">{{ $c->customer_name }}</span>
                            <span class="text-[10px] text-slate-500">{{ $c->customer_phone ?: '-' }}{{ $c->gender ? ' • '.$c->gender : '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center font-mono text-[11px]">
                            <span class="text-blue-700 font-bold">{{ App\Models\PrescriptionCard::fmtVal($c->sph_r) }}</span> /
                            <span class="text-slate-600">{{ App\Models\PrescriptionCard::fmtVal($c->cyl_r) }}</span> ×
                            <span class="text-slate-600">{{ $c->axis_r ?: '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center font-mono text-[11px]">
                            <span class="text-amber-700 font-bold">{{ App\Models\PrescriptionCard::fmtVal($c->sph_l) }}</span> /
                            <span class="text-slate-600">{{ App\Models\PrescriptionCard::fmtVal($c->cyl_l) }}</span> ×
                            <span class="text-slate-600">{{ $c->axis_l ?: '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center font-mono font-bold text-slate-700">{{ $c->pd ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.prescriptions.show', $c->id) }}" title="Detail" class="w-7 h-7 rounded-lg bg-sand-100 hover:bg-sand-200 text-slate-700 flex items-center justify-center text-[11px]"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('admin.prescriptions.print', $c->id) }}" title="Print" class="w-7 h-7 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 flex items-center justify-center text-[11px]"><i class="fa-solid fa-print"></i></a>
                                <a href="{{ route('admin.prescriptions.pdf', $c->id) }}" title="Download PDF" class="w-7 h-7 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 flex items-center justify-center text-[11px]"><i class="fa-solid fa-file-pdf"></i></a>
                                <form method="POST" action="{{ route('admin.prescriptions.send-wa', $c->id) }}" title="Kirim ulang ke WA pasien via n8n">
                                    @csrf
                                    <button class="w-7 h-7 rounded-lg {{ $c->wa_sent_at ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }} flex items-center justify-center text-[11px]"><i class="fa-brands fa-whatsapp"></i></button>
                                </form>
                                <a href="{{ route('admin.prescriptions.edit', $c->id) }}" title="Edit" class="w-7 h-7 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 flex items-center justify-center text-[11px]"><i class="fa-solid fa-pen"></i></a>
                                <form method="POST" action="{{ route('admin.prescriptions.destroy', $c->id) }}" onsubmit="return confirm('Hapus kartu resep {{ $c->card_code }}?')">
                                    @csrf @method('DELETE')
                                    <button title="Hapus" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-[11px]"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-prescription text-3xl text-sand-300 block mb-2"></i>
                        Belum ada kartu resep. Klik <b>Buat Kartu Resep</b> untuk memulai arsip.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cards->hasPages())
            <div class="p-4 border-t border-sand-200">{{ $cards->links() }}</div>
        @endif
    </div>
</div>

<script>
(function(){
    const dot = document.getElementById('waDot'), txt = document.getElementById('waStatusText');
    const modal = document.getElementById('qrModal'), img = document.getElementById('qrImg'), hint = document.getElementById('qrHint');
    const URL = "{{ route('admin.prescriptions.wa.status') }}";
    function paint(d){
        const st = d.status;
        const map = {
            'ACTIVE':      ['bg-emerald-100 text-emerald-600', '<b class="text-emerald-700">Terhubung</b> — WA ' + (d.pushName || 'aktif') + '. Kartu terkirim otomatis via n8n.'],
            'WORKING':     ['bg-emerald-100 text-emerald-600', '<b class="text-emerald-700">Terhubung</b> — WA ' + (d.pushName || 'aktif') + '. Kartu terkirim otomatis via n8n.'],
            'SCAN_QR_CODE':['bg-amber-100 text-amber-600', '<b class="text-amber-700">Menunggu scan QR</b> — hubungkan WhatsApp owner.'],
            'STARTING':    ['bg-sky-100 text-sky-600', 'Menyalakan sesi WhatsApp...'],
            'STOPPED':     ['bg-slate-100 text-slate-500', 'Sesi mati — klik untuk aktifkan & scan QR.'],
            'FAILED':      ['bg-rose-100 text-rose-600', '<b class="text-rose-700">Sesi terputus</b> — perlu scan QR ulang.'],
            'OFFLINE':     ['bg-rose-100 text-rose-600', '<b class="text-rose-700">Gateway mati</b> — server WA tidak merespon.'],
        };
        const [cls, msg] = map[st] || ['bg-slate-100 text-slate-500', 'Status: ' + st];
        dot.className = 'w-10 h-10 rounded-xl flex items-center justify-center text-lg ' + cls;
        txt.innerHTML = msg + ' <button id="waFix" class="underline font-bold ml-1">' + (st==='SCAN_QR_CODE' ? 'Lihat QR' : (st==='ACTIVE' ? '' : 'Scan QR ulang')) + '</button>';
        const fix = document.getElementById('waFix');
        if (fix) fix.onclick = () => { if (d.qr_base64) { img.src = d.qr_base64; modal.classList.remove('hidden'); } else location.reload(); };
        if (st === 'SCAN_QR_CODE' && d.qr_base64 && !modal.classList.contains('hidden')) img.src = d.qr_base64;
        if (st === 'SCAN_QR_CODE' && d.qr_base64 && !modal.dataset.open) { /* jangan auto-buka modal berulang */ }
        if (st === 'ACTIVE' && !modal.classList.contains('hidden') && !document.activeElement.closest('#qrModal')) { hint.textContent = '✅ Terhubung! WA siap — kirim kartu otomatis jalan.'; }
    }
    async function poll(){
        try { const r = await fetch(URL, {headers:{'Accept':'application/json'}}); paint(await r.json()); }
        catch(e){ dot.className='w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg'; txt.textContent='Gateway WA tidak terjangkau.'; }
    }
    poll(); setInterval(poll, 6000);
})();
</script>
@endsection
