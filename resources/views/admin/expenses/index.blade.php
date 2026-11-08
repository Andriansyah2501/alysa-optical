@extends('layouts.app')

@section('title', 'Belanja Modal & Arus Kas (Cashflow) - Alysa Optical')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                    Buku Kas & Keuangan Toko
                </span>
                <span class="text-xs text-slate-400">• Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-slate-900 mt-1">
                Belanja Modal & Arus Kas (Cashflow)
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau uang masuk (penjualan kasir) vs uang keluar (kulakan frame, lensa, operasional) dan ketahui laba bersih toko.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('modalAddExpense').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Catat Pengeluaran Baru</span>
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

    <!-- 3 KARTU CASHFLOW UTAMA (UANG MASUK VS KELUAR = LABA BERSIH) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- 1. Total Uang Masuk (Omset Kasir) -->
        <div class="bg-white p-5 rounded-3xl border border-sand-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-arrow-down-left"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Total Uang Masuk</span>
                <span class="text-xl font-serif font-bold text-slate-900">Rp {{ number_format($totalOmsetMasuk, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">Dari transaksi kasir POS & DP</span>
            </div>
        </div>

        <!-- 2. Total Belanja Modal / Pengeluaran -->
        <div class="bg-white p-5 rounded-3xl border border-sand-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-arrow-up-right"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Total Pengeluaran</span>
                <span class="text-xl font-serif font-bold text-slate-900">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">Belanja frame, lensa, listrik, gaji</span>
            </div>
        </div>

        <!-- 3. Arus Kas Bersih (Sisa Uang di Tangan / Laba Kas) -->
        <div class="bg-white p-5 rounded-3xl border {{ $netCashflow >= 0 ? 'border-brand-200 bg-brand-50/20' : 'border-rose-200 bg-rose-50/20' }} shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl {{ $netCashflow >= 0 ? 'bg-brand-50 text-brand-600' : 'bg-rose-100 text-rose-600' }} flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold {{ $netCashflow >= 0 ? 'text-brand-800' : 'text-rose-700' }} uppercase tracking-wider block">Sisa Arus Kas (Net Cashflow)</span>
                <span class="text-xl font-serif font-bold {{ $netCashflow >= 0 ? 'text-brand-700' : 'text-rose-600' }}">
                    {{ $netCashflow < 0 ? '-' : '+' }} Rp {{ number_format(abs($netCashflow), 0, ',', '.') }}
                </span>
                <span class="text-[10px] {{ $netCashflow >= 0 ? 'text-brand-600' : 'text-rose-500' }} block mt-0.5 font-medium">
                    {{ $netCashflow >= 0 ? 'Surplus (Uang Kas Aman)' : 'Defisit (Pengeluaran Lebih Besar)' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Rentang Tanggal & Kategori -->
    <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
        <form method="GET" action="{{ route('admin.expenses.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <div class="flex items-center gap-1.5">
                <span class="text-slate-500 font-medium">Dari:</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-2.5 py-1.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-slate-500 font-medium">Sampai:</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-2.5 py-1.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div class="flex items-center gap-1.5">
                <select name="category" class="px-2.5 py-1.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    <option value="">Semua Kategori</option>
                    <option value="Belanja Frame (Kulakan)" {{ $category === 'Belanja Frame (Kulakan)' ? 'selected' : '' }}>Belanja Frame</option>
                    <option value="Belanja Lensa Optik" {{ $category === 'Belanja Lensa Optik' ? 'selected' : '' }}>Belanja Lensa</option>
                    <option value="Peralatan & Sparepart Lab" {{ $category === 'Peralatan & Sparepart Lab' ? 'selected' : '' }}>Sparepart Lab</option>
                    <option value="Operasional & Listrik Toko" {{ $category === 'Operasional & Listrik Toko' ? 'selected' : '' }}>Operasional & Listrik</option>
                    <option value="Gaji & Uang Makan Staf" {{ $category === 'Gaji & Uang Makan Staf' ? 'selected' : '' }}>Gaji & Staf</option>
                    <option value="Lainnya" {{ $category === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <button type="submit" class="px-3.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-xs transition-colors flex items-center gap-1">
                <i class="fa-solid fa-filter text-[10px]"></i> Terapkan Filter
            </button>
        </form>

        <div class="text-slate-500 text-[11px]">
            Menampilkan {{ $expenses->count() }} transaksi pengeluaran
        </div>
    </div>

    <!-- TABEL CATATAN PENGELUARAN KAS -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-sand-200 flex items-center justify-between">
            <h3 class="font-serif font-bold text-base text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-rose-600"></i> Rincian Pengeluaran Belanja Modal
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 divide-y divide-sand-200">
                <thead class="bg-sand-50 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Kode & Tanggal</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Keterangan Pengeluaran</th>
                        <th class="px-4 py-3">Sumber Dana</th>
                        <th class="px-4 py-3 text-right">Nominal</th>
                        <th class="px-4 py-3 text-center">Nota / Bukti</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100">
                    @forelse($expenses as $e)
                    <tr class="hover:bg-sand-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-mono font-bold text-slate-900">{{ $e->expense_code }}</div>
                            <div class="text-[10px] text-slate-400">{{ $e->expense_date->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sand-100 text-slate-700">
                                {{ $e->category }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900 text-sm">{{ $e->title }}</div>
                            @if($e->notes)
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $e->notes }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-700">
                            {{ $e->payment_source }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-rose-600 text-sm">
                            Rp {{ number_format($e->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($e->receipt_photo)
                                <a href="{{ asset($e->receipt_photo) }}" target="_blank" class="px-2 py-1 bg-sand-100 hover:bg-sand-200 text-brand-700 rounded-lg text-[10px] font-bold border border-sand-300 transition-colors inline-flex items-center gap-1">
                                    <i class="fa-solid fa-image"></i> Lihat Nota
                                </a>
                            @else
                                <span class="text-slate-400 italic text-[10px]">Tanpa Foto</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.expenses.destroy', $e->id) }}" method="POST" onsubmit="return confirm('Hapus catatan pengeluaran {{ $e->title }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded-lg transition-colors" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400">
                            <i class="fa-solid fa-money-bill-transfer text-3xl text-slate-300 mb-2 block"></i>
                            Belum ada catatan pengeluaran modal pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
        <div class="px-4 py-3 border-t border-sand-200 bg-sand-50/50">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Catatan Pengeluaran Modal / Kas -->
<div id="modalAddExpense" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-sand-200">
        <div class="flex items-center justify-between pb-4 border-b border-sand-200 mb-4">
            <h3 class="font-serif font-bold text-lg text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-rose-600"></i> Catat Belanja Modal / Kas Keluar
            </h3>
            <button type="button" onclick="document.getElementById('modalAddExpense').classList.add('hidden')" class="w-8 h-8 rounded-full bg-sand-100 hover:bg-sand-200 text-slate-500 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="font-bold text-slate-700 block mb-1">Tanggal Pengeluaran *</label>
                <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Kategori Pengeluaran *</label>
                <select name="category" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    <option value="Belanja Frame (Kulakan)">Belanja Frame Kacamata (Kulakan)</option>
                    <option value="Belanja Lensa Optik">Belanja Stok Lensa Optik</option>
                    <option value="Peralatan & Sparepart Lab">Peralatan / Sparepart Lab Optik & Edger</option>
                    <option value="Operasional & Listrik Toko">Operasional, Listrik & Internet Toko</option>
                    <option value="Gaji & Uang Makan Staf">Gaji / Insentif / Konsumsi Staf</option>
                    <option value="Lainnya">Lain-lain / Biaya Tak Terduga</option>
                </select>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Judul / Keterangan Belanja *</label>
                <input type="text" name="title" required placeholder="Contoh: Kulakan Frame Titanium 20 pcs dari Supplier" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nominal (Rp) *</label>
                    <input type="number" name="amount" required min="1" step="any" placeholder="Contoh: 1500000" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-mono text-slate-800 font-bold">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Sumber Dana Kas *</label>
                    <select name="payment_source" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                        <option value="Kas Laci Kasir">Kas Laci Kasir (Cash Toko)</option>
                        <option value="Rekening Bank Toko (BCA/Mandiri)">Transfer Rekening Bank</option>
                        <option value="Uang Pribadi Owner">Talangan Pribadi Owner</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Foto Nota / Kuitansi (Opsional)</label>
                <input type="file" name="receipt_photo" accept="image/*" class="w-full px-3 py-1.5 bg-sand-50 border border-sand-300 rounded-xl text-slate-700 text-xs file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-sand-200 file:text-slate-700 hover:file:bg-sand-300">
                <span class="text-[10px] text-slate-400 mt-0.5 block">Format: JPG, PNG, WEBP (Maksimal 3MB)</span>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Nama supplier, nomor resi pengiriman koli..." class="w-full px-3 py-1.5 bg-sand-50 border border-sand-300 rounded-xl"></textarea>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAddExpense').classList.add('hidden')" class="px-4 py-2 bg-sand-200 text-slate-700 rounded-xl font-bold hover:bg-sand-300 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-md shadow-rose-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Catatan Kas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
