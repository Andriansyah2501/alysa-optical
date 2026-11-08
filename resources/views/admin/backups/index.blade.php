@extends('layouts.app')

@section('title', 'Pusat Backup Data & Database - Alysa Optical')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 uppercase tracking-wider">
                    Sistem Pemulihan & Backup
                </span>
                <span class="text-xs text-slate-400">• Total {{ count($backups) }} Berkas Tersimpan</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-slate-900 mt-1">
                Pusat Cadangan Data & Database
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Amankan seluruh data pesanan, inventaris kacamata, resep pasien, dan source code sistem secara berkala.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('modalCreateBackup').classList.remove('hidden')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-md shadow-purple-600/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                <span>Buat Backup Baru Sekarang</span>
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
    @if(session('error'))
        <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-xs text-rose-800">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- 3 Kartu Metrik Backup -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-database"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 block">Ukuran Database Aktif</span>
                <span class="text-base font-bold text-slate-900">{{ $dbSize }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-box-archive"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 block">Total Arsip Tersedia</span>
                <span class="text-base font-bold text-slate-900">{{ count($backups) }} File</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 block">Status Keamanan</span>
                <span class="text-base font-bold text-emerald-600">Aman & Terenkripsi</span>
            </div>
        </div>
    </div>

    <!-- OPSI CEPAT PEMBUATAN CADANGAN (PILIHAN LENGKAP, HARIAN, BULANAN) -->
    <div class="bg-gradient-to-r from-sand-100/70 to-brand-50/50 p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
        <div>
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-bolt text-amber-500"></i> Pilihan Cepat Backup Sistem
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Pilih salah satu template di bawah untuk mengamankan data sesuai periode operasional toko:</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- 1. Backup Lengkap Semua Data -->
            <form action="{{ route('admin.backups.create') }}" method="POST" class="bg-white p-4 rounded-2xl border border-sand-200 hover:border-purple-300 transition-all flex flex-col justify-between space-y-3">
                @csrf
                <input type="hidden" name="type" value="full">
                <input type="hidden" name="scope" value="manual">
                <div>
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-md text-[10px] font-bold uppercase">Rekomendasi</span>
                    <h4 class="font-bold text-slate-900 text-sm mt-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-purple-600"></i> Backup Lengkap (Full)
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-1">Mengamankan seluruh database SQL beserta kode sumber aplikasi dan foto nota kuitansi.</p>
                </div>
                <button type="submit" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-download text-[10px]"></i> Cadangkan Sekarang
                </button>
            </form>

            <!-- 2. Backup Harian (Database Transaksi Saja) -->
            <form action="{{ route('admin.backups.create') }}" method="POST" class="bg-white p-4 rounded-2xl border border-sand-200 hover:border-blue-300 transition-all flex flex-col justify-between space-y-3">
                @csrf
                <input type="hidden" name="type" value="db_only">
                <input type="hidden" name="scope" value="daily">
                <div>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md text-[10px] font-bold uppercase">Per Hari</span>
                    <h4 class="font-bold text-slate-900 text-sm mt-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-day text-blue-600"></i> Backup Harian (Database)
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-1">Format SQL ringan untuk rekapitulasi data harian kasir, lab optik, dan pasien baru.</p>
                </div>
                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-calendar-check text-[10px]"></i> Cadangkan Harian
                </button>
            </form>

            <!-- 3. Backup Bulanan (Arsip Tutup Buku) -->
            <form action="{{ route('admin.backups.create') }}" method="POST" class="bg-white p-4 rounded-2xl border border-sand-200 hover:border-amber-300 transition-all flex flex-col justify-between space-y-3">
                @csrf
                <input type="hidden" name="type" value="full">
                <input type="hidden" name="scope" value="monthly">
                <div>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-md text-[10px] font-bold uppercase">Per Bulan</span>
                    <h4 class="font-bold text-slate-900 text-sm mt-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-week text-amber-600"></i> Backup Bulanan (Tutup Buku)
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-1">Arsip kumulatif komplit untuk tutup buku bulanan toko dan laporan keuangan.</p>
                </div>
                <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-archive text-[10px]"></i> Cadangkan Bulanan
                </button>
            </form>
        </div>
    </div>

    <!-- TABEL RIWAYAT FILE CADANGAN TERSIMPAN -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-sand-200 flex items-center justify-between">
            <h3 class="font-serif font-bold text-base text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-brand-600"></i> Riwayat Berkas Cadangan Tersimpan
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 divide-y divide-sand-200">
                <thead class="bg-sand-50 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Nama Berkas Cadangan</th>
                        <th class="px-4 py-3">Jenis Konten</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Ukuran File</th>
                        <th class="px-4 py-3">Waktu Pembuatan</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100">
                    @forelse($backups as $b)
                    <tr class="hover:bg-sand-50/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-slate-900">
                            {{ $b['filename'] }}
                        </td>
                        <td class="px-4 py-3">
                            @if($b['type'] === 'Full System')
                                <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full font-bold text-[10px]">
                                    Semua Data (Full)
                                </span>
                            @elseif($b['type'] === 'Database Saja')
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full font-bold text-[10px]">
                                    Database SQL
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-sand-200 text-slate-700 rounded-full font-bold text-[10px]">
                                    Source Code
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-slate-700 font-medium">{{ $b['scope'] }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-slate-800">
                            {{ $b['size'] }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ $b['created_at']->format('d/m/Y H:i:s') }} WIB
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.backups.download', $b['filename']) }}" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-bold border border-emerald-200 transition-colors flex items-center gap-1">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                                <form action="{{ route('admin.backups.destroy', $b['filename']) }}" method="POST" onsubmit="return confirm('Hapus file cadangan {{ $b['filename'] }} dari server?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-box-open text-4xl text-slate-300 mb-2 block"></i>
                            Belum ada berkas cadangan di folder storage. Klik tombol di atas untuk membuat cadangan baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Kustomisasi Pembuatan Backup -->
<div id="modalCreateBackup" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-sand-200">
        <div class="flex items-center justify-between pb-4 border-b border-sand-200 mb-4">
            <h3 class="font-serif font-bold text-lg text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-purple-600"></i> Buat Cadangan Sistem
            </h3>
            <button type="button" onclick="document.getElementById('modalCreateBackup').classList.add('hidden')" class="w-8 h-8 rounded-full bg-sand-100 hover:bg-sand-200 text-slate-500 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.backups.create') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="font-bold text-slate-700 block mb-1">Cakupan Data yang Ingin Dicadangkan</label>
                <select name="type" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                    <option value="full">Semua Lengkap (Database SQL + Source Code + Uploads)</option>
                    <option value="db_only">Database Saja (Format SQL MariaDB - Ringan & Cepat)</option>
                    <option value="app_only">Source Code Aplikasi Saja (Tanpa Database)</option>
                </select>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Periode Cadangan</label>
                <select name="scope" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    <option value="manual">Manual / Instan</option>
                    <option value="daily">Cadangan Harian (Daily Backup)</option>
                    <option value="monthly">Cadangan Bulanan (Monthly Closing Archive)</option>
                </select>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalCreateBackup').classList.add('hidden')" class="px-4 py-2 bg-sand-200 text-slate-700 rounded-xl font-bold hover:bg-sand-300 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold shadow-md shadow-purple-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Eksekusi Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
