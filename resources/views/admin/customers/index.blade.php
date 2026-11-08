@extends('layouts.app')

@section('title', 'Database Pelanggan & Rekam Medis Kacamata - Alysa Optical')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-100 text-brand-800 uppercase tracking-wider">
                    CRM & Rekam Refraksi
                </span>
                <span class="text-xs text-slate-400">• Total {{ $stats['total_customers'] }} Pelanggan</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-slate-900 mt-1">
                Database Pelanggan & Resep Kacamata
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data pasien, riwayat ukuran kacamata (SPH, CYL, AXIS, PD), dan akumulasi belanja.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('modalAddCustomer').classList.remove('hidden')" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-600/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Pelanggan Baru</span>
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

    <!-- 3 Kartu Metrik Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 block">Total Pasien / Pelanggan</span>
                <span class="text-lg font-bold text-slate-900">{{ number_format($stats['total_customers']) }} Orang</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 block">Total Kacamata Dipesan</span>
                <span class="text-lg font-bold text-slate-900">{{ number_format($stats['total_orders']) }} Transaksi</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 block">Akumulasi Nilai Belanja</span>
                <span class="text-lg font-bold text-slate-900">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="w-full sm:w-80 relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode pasien, atau no. HP..." class="w-full pl-9 pr-4 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
        </form>

        <div class="text-xs text-slate-500">
            Menampilkan {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} dari {{ $customers->total() }} data
        </div>
    </div>

    <!-- Tabel Data Pelanggan & Rekam Refraksi -->
    <div class="bg-white rounded-2xl border border-sand-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 divide-y divide-sand-200">
                <thead class="bg-sand-50 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Kode & Pelanggan</th>
                        <th class="px-4 py-3">Kontak & Kota</th>
                        <th class="px-4 py-3">Resep Refraksi Terakhir (R / L)</th>
                        <th class="px-4 py-3 text-center">PD</th>
                        <th class="px-4 py-3 text-center">Total Order</th>
                        <th class="px-4 py-3 text-right">Total Belanja</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100">
                    @forelse($customers as $c)
                    <tr class="hover:bg-sand-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900 text-sm">{{ $c->name }}</div>
                            <div class="text-[10px] font-mono font-semibold text-brand-600">{{ $c->customer_code ?? 'CUST-' . str_pad($c->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $c->phone }}</div>
                            <div class="text-[10px] text-slate-400">{{ $c->city ?? 'Batam' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($c->sph_r || $c->sph_l)
                                <div class="font-mono text-[11px] text-slate-800">
                                    <span class="font-semibold text-blue-600">R:</span> SPH {{ $c->sph_r ?? '0.00' }} | CYL {{ $c->cyl_r ?? '0.00' }} | AXIS {{ $c->axis_r ?? '0' }}
                                </div>
                                <div class="font-mono text-[11px] text-slate-800">
                                    <span class="font-semibold text-emerald-600">L:</span> SPH {{ $c->sph_l ?? '0.00' }} | CYL {{ $c->cyl_l ?? '0.00' }} | AXIS {{ $c->axis_l ?? '0' }}
                                </div>
                            @else
                                <span class="text-slate-400 italic text-[10px]">Belum ada data resep</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-mono font-semibold text-slate-700">
                            {{ $c->pd ? $c->pd . ' mm' : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 bg-sand-100 text-slate-700 rounded-full font-bold text-[11px]">
                                {{ $c->total_orders }}x
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-slate-900">
                            Rp {{ number_format($c->total_spent, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.customers.show', $c->id) }}" class="px-2 py-1 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg text-[10px] font-bold border border-brand-200 transition-colors flex items-center gap-1" title="Lihat Rekam Medis & Riwayat Order">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                                <form action="{{ route('admin.customers.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus pelanggan {{ $c->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded-lg transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-users text-4xl text-slate-300 mb-2 block"></i>
                            Belum ada data pelanggan yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="px-4 py-3 border-t border-sand-200 bg-sand-50/50">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Pelanggan Baru -->
<div id="modalAddCustomer" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-sand-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-sand-200 mb-4">
            <h3 class="font-serif font-bold text-lg text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-brand-600"></i> Pendaftaran Pasien / Pelanggan Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalAddCustomer').classList.add('hidden')" class="w-8 h-8 rounded-full bg-sand-100 hover:bg-sand-200 text-slate-500 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Lengkap Pasien *</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP *</label>
                    <input type="text" name="phone" required placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-mono text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Email (Opsional)</label>
                    <input type="email" name="email" placeholder="email@domain.com" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kota Asal</label>
                    <input type="text" name="city" value="Batam" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-slate-800">
                </div>
            </div>

            <!-- Rekam Refraksi Optik Awal -->
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
                    <input type="text" name="sph_r" placeholder="-0.00" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                    <input type="text" name="cyl_r" placeholder="0.00" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                    <input type="text" name="axis_r" placeholder="0" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">

                    <!-- Mata Kiri (OS/L) -->
                    <div class="font-bold text-emerald-600 flex items-center">L (Kiri)</div>
                    <input type="text" name="sph_l" placeholder="-0.00" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                    <input type="text" name="cyl_l" placeholder="0.00" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                    <input type="text" name="axis_l" placeholder="0" class="px-2 py-1 bg-white border border-sand-300 rounded-lg text-center font-mono">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Pupil Distance (PD) mm</label>
                        <input type="text" name="pd" placeholder="62" class="w-full px-3 py-1.5 bg-white border border-sand-300 rounded-xl text-center font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Catatan Tambahan / Gejala</label>
                        <input type="text" name="notes" placeholder="Sering silau malam, sensitif layar..." class="w-full px-3 py-1.5 bg-white border border-sand-300 rounded-xl">
                    </div>
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAddCustomer').classList.add('hidden')" class="px-4 py-2 bg-sand-200 text-slate-700 rounded-xl font-bold hover:bg-sand-300 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md shadow-brand-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pasien
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
