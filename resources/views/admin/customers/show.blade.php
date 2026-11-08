@extends('layouts.app')

@section('title', 'Detail Pasien: ' . $customer->name . ' - Alysa Optical')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Breadcrumb & Navigasi -->
    <div class="flex items-center justify-between pb-4 border-b border-sand-200">
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('admin.customers.index') }}" class="text-brand-600 hover:text-brand-700 font-bold flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Database Pelanggan
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-500 font-mono">{{ $customer->customer_code }}</span>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('modalEditCustomer').classList.remove('hidden')" class="px-3.5 py-1.5 bg-sand-100 hover:bg-sand-200 text-slate-800 rounded-xl text-xs font-bold border border-sand-300 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square text-brand-600"></i> Edit Profil / Resep
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-xs text-emerald-800">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Fitur Otomasi WhatsApp Pelanggan -->
    <div class="bg-white rounded-3xl p-6 border border-sand-300 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-emerald-600 text-lg"></i> Otomasi Pengiriman Pesan WhatsApp Pelanggan
            </h3>
            <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                Otomatis
            </span>
        </div>
        <p class="text-xs text-slate-500">Kirimkan notifikasi langsung ke WhatsApp pelanggan ({{ $customer->phone }}) tanpa perlu mengetik manual.</p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <form action="{{ route('admin.customers.send_wa', $customer->id) }}" method="POST">
                @csrf
                <input type="hidden" name="event" value="prescription_reminder">
                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-bell"></i> Kirim Pengingat Cek Mata Berkala
                </button>
            </form>

            <form action="{{ route('admin.customers.send_wa', $customer->id) }}" method="POST">
                @csrf
                <input type="hidden" name="event" value="prescription_updated">
                <button type="submit" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-glasses"></i> Kirim Salinan Resep Refraksi via WA
                </button>
            </form>
        </div>
    </div>

    <!-- Profile Header & Statistik -->
    <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-2 md:border-r md:border-sand-200 md:pr-6">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-100 text-brand-800 uppercase tracking-wider font-mono">
                    {{ $customer->customer_code }}
                </span>
                <span class="text-xs text-slate-400">Kunjungan Terakhir: {{ $customer->last_visit_at ? $customer->last_visit_at->format('d/m/Y H:i') : '-' }}</span>
            </div>
            <h2 class="text-2xl font-serif font-bold text-slate-900">{{ $customer->name }}</h2>
            <div class="text-xs text-slate-600 space-y-1">
                <p class="flex items-center gap-2"><i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i> {{ $customer->phone }}</p>
                <p class="flex items-center gap-2"><i class="fa-solid fa-envelope text-slate-400"></i> {{ $customer->email ?: 'Tidak ada email' }}</p>
                <p class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-slate-400"></i> {{ $customer->city ?? 'Batam' }}</p>
            </div>
        </div>

        <!-- Rekam Refraksi Terkini -->
        <div class="space-y-2 md:border-r md:border-sand-200 md:pr-6">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-glasses text-brand-600"></i> Rekam Refraksi Terkini
            </h3>
            <div class="bg-sand-50 p-3 rounded-2xl border border-sand-200 text-xs space-y-2">
                <div class="flex justify-between items-center font-mono">
                    <span class="font-bold text-blue-600">R (Kanan):</span>
                    <span>SPH {{ $customer->sph_r ?? '0.00' }} | CYL {{ $customer->cyl_r ?? '0.00' }} | AXIS {{ $customer->axis_r ?? '0' }}°</span>
                </div>
                <div class="flex justify-between items-center font-mono">
                    <span class="font-bold text-emerald-600">L (Kiri):</span>
                    <span>SPH {{ $customer->sph_l ?? '0.00' }} | CYL {{ $customer->cyl_l ?? '0.00' }} | AXIS {{ $customer->axis_l ?? '0' }}°</span>
                </div>
                <div class="flex justify-between items-center pt-1 border-t border-sand-200">
                    <span class="text-slate-500 font-semibold">Pupil Distance (PD):</span>
                    <span class="font-bold text-slate-800">{{ $customer->pd ? $customer->pd . ' mm' : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Statistik Transaksi -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Metrik Nilai Pasien</h3>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200">
                    <span class="text-[10px] text-slate-500 block">Total Pesanan</span>
                    <span class="text-base font-bold text-slate-900">{{ $customer->total_orders }}x</span>
                </div>
                <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200">
                    <span class="text-[10px] text-slate-500 block">Total Belanja</span>
                    <span class="text-base font-bold text-brand-700">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</span>
                </div>
            </div>
            @if($customer->notes)
            <div class="text-[11px] text-slate-500 bg-sand-50/60 p-2.5 rounded-xl border border-sand-200">
                <span class="font-bold text-slate-700 block mb-0.5">Catatan Khusus:</span>
                {{ $customer->notes }}
            </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Seluruh Transaksi & Pengerjaan Lab -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-sand-200 flex items-center justify-between">
            <h3 class="font-serif font-bold text-base text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-brand-600"></i> Riwayat Transaksi & Pengerjaan Lab ({{ $orders->count() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 divide-y divide-sand-200">
                <thead class="bg-sand-50 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3">No. Order</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Paket Kacamata / Lensa</th>
                        <th class="px-4 py-3 text-right">Total Tagihan</th>
                        <th class="px-4 py-3 text-center">Status Bayar</th>
                        <th class="px-4 py-3 text-center">Status Lab</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100">
                    @forelse($orders as $o)
                    <tr class="hover:bg-sand-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900">
                            {{ $o->order_code }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ $o->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800">{{ $o->lens_type_name ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400 capitalize">{{ str_replace('_', ' ', $o->transaction_type) }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-slate-900">
                            Rp {{ number_format($o->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(strtoupper($o->payment_status) === 'PAID')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">LUNAS</span>
                            @elseif(strtoupper($o->payment_status) === 'DP')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">DP (Sisa Rp {{ number_format($o->remaining_balance, 0, ',', '.') }})</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">{{ $o->payment_status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-medium">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sand-100 text-slate-700">
                                {{ $o->order_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('pos.receipt', $o->order_code) }}" target="_blank" class="px-2 py-1 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-lg text-[10px] font-bold border border-sand-300 transition-colors inline-flex items-center gap-1">
                                <i class="fa-solid fa-receipt"></i> Struk
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                            Belum ada riwayat pesanan tercatat untuk pelanggan ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Edit Profil & Resep Pasien -->
<div id="modalEditCustomer" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-sand-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-sand-200 mb-4">
            <h3 class="font-serif font-bold text-lg text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-brand-600"></i> Edit Pasien: {{ $customer->name }}
            </h3>
            <button type="button" onclick="document.getElementById('modalEditCustomer').classList.add('hidden')" class="w-8 h-8 rounded-full bg-sand-100 hover:bg-sand-200 text-slate-500 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Pasien *</label>
                    <input type="text" name="name" value="{{ $customer->name }}" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP *</label>
                    <input type="text" name="phone" value="{{ $customer->phone }}" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-mono text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Email</label>
                    <input type="email" name="email" value="{{ $customer->email }}" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kota Asal</label>
                    <input type="text" name="city" value="{{ $customer->city ?? 'Batam' }}" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-slate-800">
                </div>
            </div>

            <!-- Rekam Refraksi Optik -->
            <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 space-y-3">
                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-glasses text-brand-600"></i> Rekam Refraksi Optik (Ukuran Kacamata)
                </div>
                <div class="grid grid-cols-4 gap-2 text-[11px]">
                    <div class="font-bold text-slate-500">Mata</div>
                    <div class="font-bold text-slate-500">SPH (Minus/Plus)</div>
                    <div class="font-bold text-slate-500">CYL (Silinder)</div>
                    <div class="font-bold text-slate-500">AXIS (Derajat)</div>

                    <!-- Mata Kanan (OD/R) -->
                    <div class="font-bold text-blue-600 flex items-center">R (Kanan)</div>
                    <input type="text" name="sph_r" value="{{ $customer->sph_r }}" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                    <input type="text" name="cyl_r" value="{{ $customer->cyl_r }}" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                    <input type="text" name="axis_r" value="{{ $customer->axis_r }}" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">

                    <!-- Mata Kiri (OS/L) -->
                    <div class="font-bold text-emerald-600 flex items-center">L (Kiri)</div>
                    <input type="text" name="sph_l" value="{{ $customer->sph_l }}" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                    <input type="text" name="cyl_l" value="{{ $customer->cyl_l }}" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                    <input type="text" name="axis_l" value="{{ $customer->axis_l }}" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Pupil Distance (PD) mm</label>
                        <input type="text" name="pd" value="{{ $customer->pd }}" class="w-full px-3 py-1.5 bg-white border border-sand-300 rounded-xl text-center font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Catatan Tambahan / Gejala</label>
                        <input type="text" name="notes" value="{{ $customer->notes }}" class="w-full px-3 py-1.5 bg-white border border-sand-300 rounded-xl">
                    </div>
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalEditCustomer').classList.add('hidden')" class="px-4 py-2 bg-sand-200 text-slate-700 rounded-xl font-bold hover:bg-sand-300 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md shadow-brand-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
