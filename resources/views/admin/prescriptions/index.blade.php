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
@endsection
