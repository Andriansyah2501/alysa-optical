@extends('layouts.app')

@section('title', 'Kartu Resep ' . $card->card_code . ' | Alysa Glasses')

@section('content')
@php
    $fmt = fn($x) => App\Models\PrescriptionCard::fmtVal($x);
@endphp
<div class="max-w-3xl mx-auto px-3 sm:px-4 py-5 space-y-4">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="/icons/icon-192.png" alt="Logo Alysa Glasses" class="w-10 h-10 rounded-xl shadow-sm object-cover">
            <div>
                <span class="text-xs font-bold text-brand-600 uppercase tracking-widest">Detail Kartu Resep</span>
                <h1 class="font-mono text-lg font-extrabold text-slate-900">{{ $card->card_code }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-1.5">
            <a href="{{ route('admin.prescriptions.index') }}" class="px-3 py-2 bg-white border border-sand-300 rounded-xl text-[11px] font-bold text-slate-700 hover:bg-sand-50"><i class="fa-solid fa-arrow-left mr-1"></i>Kembali</a>
            <a href="{{ route('admin.prescriptions.edit', $card->id) }}" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[11px] font-bold"><i class="fa-solid fa-pen mr-1"></i>Edit</a>
            <a href="{{ route('admin.prescriptions.print', $card->id) }}" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-bold"><i class="fa-solid fa-print mr-1"></i>Cetak</a>
            <a href="{{ route('admin.prescriptions.pdf', $card->id) }}" class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[11px] font-bold"><i class="fa-solid fa-file-pdf mr-1"></i>PDF</a>
        </div>
    </div>

    <!-- PRATINJAU KARTU -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-brand-50 to-emerald-50 border-b border-sand-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/icons/icon-192.png" class="w-12 h-12 rounded-2xl shadow-sm bg-white object-cover" alt="Logo">
                <div>
                    <h2 class="font-serif font-extrabold text-slate-900 leading-tight">{{ $settings['store_name'] ?? 'Alysa Glasses' }}</h2>
                    <p class="text-[10px] text-slate-500 font-bold">{{ $settings['store_tagline'] ?? 'Eyewear That Completes Your Look' }}</p>
                    <p class="text-[10px] text-slate-400">{{ $settings['store_phone'] ?? '' }} • {{ $settings['store_address'] ?? '' }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[9px] font-bold text-slate-400 uppercase block">Tgl Periksa</span>
                <span class="font-mono font-extrabold text-slate-800 text-xs">{{ \Carbon\Carbon::parse($card->exam_date)->translatedFormat('d M Y') }}</span>
            </div>
        </div>

        <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="space-y-1.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">Data Pembeli</span>
                <div><b class="text-slate-900 text-sm">{{ $card->customer_name }}</b></div>
                <div class="text-slate-600">{{ $card->customer_phone ?: '-' }}{{ $card->gender ? ' • '.$card->gender : '' }}</div>
                @if($card->birth_place || $card->birth_date)<div class="text-slate-500">Lahir: {{ trim(($card->birth_place ?? '').($card->birth_date ? ', '.$card->birth_date : '')) }}</div>@endif
                @if($card->address)<div class="text-slate-500">{{ $card->address }}</div>@endif
                @if($card->order_code)<div class="text-slate-500">Kaitan struk: <span class="font-mono text-brand-700 font-bold">{{ $card->order_code }}</span></div>@endif
            </div>
            <div class="space-y-1.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">Keterangan Pemeriksaan</span>
                <div class="text-slate-600">Kegunaan: {{ $card->vision_goal ?: 'Jauh' }}</div>
                <div class="text-slate-600">Cara ukur: {{ $card->refraction_method ?: 'Auto Refraktometer' }}</div>
                <div class="text-slate-600">Sumber: {{ $card->origin ?: 'Pemeriksaan Toko' }}</div>
                @if($card->referrer_name)<div class="text-slate-600">Perujuk: {{ $card->referrer_name }}</div>@endif
                <div class="text-slate-600">Pemeriksa: {{ $card->examiner_name ?: ($card->user->name ?? '-') }}</div>
            </div>
        </div>

        <div class="px-4 sm:px-5 pb-5">
            <table class="w-full border-collapse text-center text-sm font-mono">
                <thead>
                    <tr class="bg-sand-50 text-[10px] uppercase text-slate-500">
                        <th class="border border-sand-200 px-3 py-2 text-left font-bold">Mata</th>
                        <th class="border border-sand-200 px-3 py-2 font-bold">SPH</th>
                        <th class="border border-sand-200 px-3 py-2 font-bold">CYL</th>
                        <th class="border border-sand-200 px-3 py-2 font-bold">AXIS</th>
                        <th class="border border-sand-200 px-3 py-2 font-bold">ADD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-sand-200 px-3 py-2 text-left font-bold text-blue-800 font-sans">Kanan (OD / R)</td>
                        <td class="border border-sand-200 px-3 py-2 font-extrabold">{{ $fmt($card->sph_r) }}</td>
                        <td class="border border-sand-200 px-3 py-2">{{ $fmt($card->cyl_r) }}</td>
                        <td class="border border-sand-200 px-3 py-2">{{ $card->axis_r ?: '-' }}</td>
                        <td class="border border-sand-200 px-3 py-2">{{ $card->add_r ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="border border-sand-200 px-3 py-2 text-left font-bold text-amber-800 font-sans">Kiri (OS / L)</td>
                        <td class="border border-sand-200 px-3 py-2 font-extrabold">{{ $fmt($card->sph_l) }}</td>
                        <td class="border border-sand-200 px-3 py-2">{{ $fmt($card->cyl_l) }}</td>
                        <td class="border border-sand-200 px-3 py-2">{{ $card->axis_l ?: '-' }}</td>
                        <td class="border border-sand-200 px-3 py-2">{{ $card->add_l ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="border border-sand-200 px-3 py-2 text-left font-bold font-sans">PD (Jarak Pupil)</td>
                        <td class="border border-sand-200 px-3 py-2 font-extrabold" colspan="4">{{ $card->pd ? $card->pd . ' mm' : '-' }}</td>
                    </tr>
                </tbody>
            </table>
            @if($card->notes)
            <div class="mt-3 p-3 bg-sand-50 rounded-xl text-[11px] text-slate-600 border border-sand-200">
                <b class="text-slate-700">Catatan:</b> {{ $card->notes }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
