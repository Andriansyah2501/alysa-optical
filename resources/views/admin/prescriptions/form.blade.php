@extends('layouts.app')

@section('title', ($title ?? 'Kartu Resep') . ' | Alysa Glasses')

@section('content')
@php
    $v = function ($key, $default = '') use ($card, $prefill) {
        if ($card !== null && $card->getAttribute($key) !== null) return $card->getAttribute($key);
        if ($prefill !== null && $prefill->getAttribute($key) !== null) return $prefill->getAttribute($key);
        return old($key, $default);
    };
@endphp
<div class="max-w-4xl mx-auto px-3 sm:px-4 py-5 space-y-4">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="/icons/icon-192.png" alt="Logo Alysa Glasses" class="w-10 h-10 rounded-xl shadow-sm object-cover">
            <div>
                <span class="text-xs font-bold text-brand-600 uppercase tracking-widest">{{ $card ? 'Edit Arsip' : 'Arsip Refraksi' }}</span>
                <h1 class="font-serif text-xl font-bold text-slate-900 leading-tight">{{ $title }}</h1>
            </div>
        </div>
        <a href="{{ route('admin.prescriptions.index') }}" class="px-3 py-2 bg-white border border-sand-300 rounded-xl text-[11px] font-bold text-slate-700 hover:bg-sand-50"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <form method="POST" action="{{ $card ? route('admin.prescriptions.update', $card->id) : route('admin.prescriptions.store') }}" class="space-y-4" id="rxForm">
        @csrf
        @if($card) @method('PUT') @endif

        <!-- KAITAN TRANSAKSI -->
        <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-4 sm:p-5 space-y-3">
            <h3 class="font-bold text-sm text-slate-900"><i class="fa-solid fa-link text-brand-600 mr-1"></i> Kaitkan Transaksi (opsional)</h3>
            @unless($card)
            <select id="orderPick" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                <option value="">— Tidak dikaitkan / isian manual —</option>
                @foreach($orders as $o)
                    <option value="{{ $o->order_code }}" data-name="{{ $o->customer_name }}" data-phone="{{ $o->customer_phone }}">{{ $o->order_code }} — {{ $o->customer_name }}</option>
                @endforeach
            </select>
            <p class="text-[10px] text-slate-400">Pilih struk POS yang punya input resep → nama, WA & angka resep terisi otomatis.</p>
            @endunless
            <input type="hidden" name="order_code" value="{{ $v('order_code') }}" id="orderCodeInput">
        </div>

        <!-- DATA PEMBELI -->
        <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-4 sm:p-5 space-y-3">
            <h3 class="font-bold text-sm text-slate-900"><i class="fa-solid fa-user-pen text-emerald-600 mr-1"></i> Data Pembeli / Pasien</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Nama Lengkap *</label>
                    <input type="text" name="customer_name" required value="{{ $v('customer_name') }}" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">No. WhatsApp</label>
                    <input type="text" name="customer_phone" value="{{ $v('customer_phone') }}" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                        <option value="">—</option>
                        <option {{ $v('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option {{ $v('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Tanggal Pemeriksaan *</label>
                    <input type="date" name="exam_date" required value="{{ $card ? \Carbon\Carbon::parse($card->exam_date)->format('Y-m-d') : old('exam_date', date('Y-m-d')) }}" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Tempat / Tanggal Lahir</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="birth_place" value="{{ $v('birth_place') }}" placeholder="Batam" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                        <input type="text" name="birth_date" value="{{ $v('birth_date') }}" placeholder="12-05-1990" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Alamat</label>
                    <input type="text" name="address" value="{{ $v('address') }}" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                </div>
            </div>
        </div>

        <!-- RESEP -->
        <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-4 sm:p-5 space-y-3">
            <h3 class="font-bold text-sm text-slate-900"><i class="fa-solid fa-prescription text-blue-600 mr-1"></i> Ukuran Resep (Diopter)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3 bg-blue-50/60 rounded-2xl border border-blue-100">
                    <span class="font-bold text-blue-900 block text-[11px] mb-2"><i class="fa-solid fa-eye mr-1"></i> KANAN — OD (R)</span>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['sph'=>'SPH','cyl'=>'CYL','axis'=>'AXIS'] as $k=>$lbl)
                        <div>
                            <span class="text-[9px] text-slate-500 font-bold block">{{ $lbl }}</span>
                            <input type="text" name="{{ $k }}_r" value="{{ $v($k.'_r') }}" placeholder="{{ $k=='axis' ? '180' : '-1.25' }}" class="w-full px-2 py-1.5 bg-white border border-blue-200 rounded-lg text-center font-bold text-xs">
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-2"><span class="text-[9px] text-slate-500 font-bold block">ADD (dekat)</span>
                    <input type="text" name="add_r" value="{{ $v('add_r') }}" placeholder="+1.50" class="w-full px-2 py-1.5 bg-white border border-blue-200 rounded-lg text-center font-bold text-xs"></div>
                </div>
                <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100">
                    <span class="font-bold text-amber-900 block text-[11px] mb-2"><i class="fa-solid fa-eye mr-1"></i> KIRI — OS (L)</span>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['sph'=>'SPH','cyl'=>'CYL','axis'=>'AXIS'] as $k=>$lbl)
                        <div>
                            <span class="text-[9px] text-slate-500 font-bold block">{{ $lbl }}</span>
                            <input type="text" name="{{ $k }}_l" value="{{ $v($k.'_l') }}" placeholder="{{ $k=='axis' ? '180' : '-1.50' }}" class="w-full px-2 py-1.5 bg-white border border-amber-200 rounded-lg text-center font-bold text-xs">
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-2"><span class="text-[9px] text-slate-500 font-bold block">ADD (dekat)</span>
                    <input type="text" name="add_l" value="{{ $v('add_l') }}" placeholder="+1.50" class="w-full px-2 py-1.5 bg-white border border-amber-200 rounded-lg text-center font-bold text-xs"></div>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div><label class="text-[11px] font-bold text-slate-600 block mb-1">PD (mm)</label>
                    <input type="text" name="pd" value="{{ $v('pd') }}" placeholder="62" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-bold text-center"></div>
                <div><label class="text-[11px] font-bold text-slate-600 block mb-1">Kegunaan</label>
                    <select name="vision_goal" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                        @foreach(['Jauh','Dekat','Progressif / Multifokal','Komputer'] as $g)
                            <option {{ $v('vision_goal')===$g ? 'selected':'' }}>{{ $g }}</option>
                        @endforeach
                    </select></div>
                <div><label class="text-[11px] font-bold text-slate-600 block mb-1">Cara Ukur</label>
                    <select name="refraction_method" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                        @foreach(['Auto Refraktometer','Refraksi Subjektif','Kombinasi'] as $m)
                            <option {{ $v('refraction_method')===$m ? 'selected':'' }}>{{ $m }}</option>
                        @endforeach
                    </select></div>
                <div><label class="text-[11px] font-bold text-slate-600 block mb-1">Sumber Resep</label>
                    <select name="origin" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                        @foreach(['Pemeriksaan Toko','Rujukan Dokter'] as $o)
                            <option {{ $v('origin')===$o ? 'selected':'' }}>{{ $o }}</option>
                        @endforeach
                    </select></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div id="referrerWrap" class="{{ $v('origin') === 'Rujukan Dokter' ? '' : 'hidden' }}">
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Nama Dokter Perujuk (RS / Klinik / dr.)</label>
                    <input type="text" name="referrer_name" value="{{ $v('referrer_name') }}" placeholder="dr. Ayu Sp.M — RS Bhke Bhakti Batam" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Pemeriksa / Refraksionis</label>
                    <input type="text" name="examiner_name" value="{{ $v('examiner_name', $card ? ($card->user->name ?? '') : (Auth::user()->name ?? '')) }}" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium">
                </div>
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-600 block mb-1">Catatan / Keluhan</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs">{{ $v('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 sticky bottom-3">
            <a href="{{ route('admin.prescriptions.index') }}" class="px-4 py-2.5 bg-white border border-sand-300 rounded-xl text-xs font-bold text-slate-700">Batal</a>
            <button class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-extrabold shadow-lg"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Kartu Resep</button>
        </div>
    </form>
</div>

@if(!$card)
<script>
(function(){
    const pick = document.getElementById('orderPick');
    if (!pick) return;
    const RX = @json($orders->mapWithKeys(fn($o) => [$o->order_code => $o])->values());
    // Ambil resep lengkap dari server saat order dipilih
    pick.addEventListener('change', async function(){
        const code = this.value;
        document.getElementById('orderCodeInput').value = code || '';
        if (!code) return;
        const name = this.options[this.selectedIndex].dataset.name;
        const phone = this.options[this.selectedIndex].dataset.phone;
        document.querySelector('[name=customer_name]').value = name || '';
        document.querySelector('[name=customer_phone]').value = phone || '';
        try {
            const res = await fetch("{{ url('admin/pelanggan/cari-resep') }}/" + encodeURIComponent(code), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const d = await res.json();
            ['sph_r','cyl_r','axis_r','sph_l','cyl_l','axis_l','pd'].forEach(k => {
                const el = document.querySelector(`[name="${k}"]`);
                if (el && d[k]) el.value = d[k];
            });
        } catch (e) { /* diamkan: isian manual tetap bisa */ }
    });
    // toggle dokter perujuk
    const origin = document.querySelector('[name=origin]');
    origin.addEventListener('change', () => {
        document.getElementById('referrerWrap').classList.toggle('hidden', origin.value !== 'Rujukan Dokter');
    });
})();
</script>
@endif
@endsection
