@extends('layouts.app')

@section('title', 'Manajemen Antrean Lab Optik & Kasir | Alysa Glasses')

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-sand-200">
            <div>
                <span class="text-xs font-bold text-brand-600 uppercase tracking-widest">Backoffice & Laboratorium Optik</span>
                <h1 class="font-serif text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">Antrean Resep & Penjualan Kacamata</h1>
                <p class="text-xs text-slate-500 mt-1">Pantau pesanan masuk, instruksi potong lensa refraksionis, cetak struk, dan kirim pesan WhatsApp ke pelanggan.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" class="px-3 py-1.5 bg-sky-600 text-[11px] hover:bg-sky-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-line"></i> Laporan & Grafik Omset
                </a>
                <a href="{{ route('admin.order.create') }}" class="px-3 py-1.5 bg-purple-600 text-[11px] hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Transaksi (Create)
                </a>
                <a href="{{ route('pos.index') }}" class="px-3 py-1.5 bg-emerald-600 text-[11px] hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-cash-register"></i> Buka POS Kasir
                </a>
                <a href="{{ route('shop.index') }}" class="px-3 py-1.5 bg-white text-[11px] border border-sand-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-sand-50 transition-colors">
                    <i class="fa-solid fa-store mr-1 text-brand-600"></i> Lihat Toko Publik
                </a>
            </div>
        </div>

        <!-- 4 Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-5 bg-white rounded-3xl border border-sand-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Transaksi</span>
                    <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $totalOrders }} Order</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-700 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>

            <div class="p-5 bg-white rounded-3xl border border-sand-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Omset Penjualan</span>
                    <span class="text-xl font-extrabold text-emerald-700 mt-1 block">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>

            <div class="p-5 bg-white rounded-3xl border border-sand-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Koleksi Frame Aktif</span>
                    <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $totalProducts }} Produk</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-glasses"></i>
                </div>
            </div>

            <div class="p-5 bg-white rounded-3xl border border-sand-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pesanan Terkini</span>
                    <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $recentOrders->count() }} Order</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
        </div>

        <!-- Tabel Antrean Pesanan dengan Fitur Batch Checkbox Delete -->
        <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-sand-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-serif text-lg font-bold text-slate-900">Daftar Antrean Produksi & Penjualan</h3>
                        <span class="text-xs text-slate-500">Ceklis baris data untuk menghapus beberapa pesanan sekaligus</span>
                    </div>

                    <!-- Batch Action Panel (Aktif saat ada ceklis) -->
                    <div id="batch-action-bar" class="flex items-center gap-3">
                        <span id="selected-count" class="text-xs font-bold text-slate-500 hidden">0 dipilih</span>
                        <button type="button" id="batch-delete-btn" onclick="executeBatchDelete();" class="hidden px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 transition-all items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i> Hapus yang Dipilih (<span id="btn-count">0</span>)
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-sand-50/80 text-slate-500 uppercase tracking-wider font-bold text-[10px] border-b border-sand-200">
                            <tr>
                                <th class="px-4 py-4 w-12 text-center">
                                    <input type="checkbox" id="check-all" class="w-4 h-4 text-brand-600 rounded border-sand-300 focus:ring-brand-500 cursor-pointer" title="Pilih Semua">
                                </th>
                                <th class="px-5 py-4">Kode & Tanggal</th>
                            <th class="px-5 py-4">Pelanggan</th>
                            <th class="px-5 py-4">Frame & Lensa</th>
                            <th class="px-5 py-4">Resep Optik (R / L / PD)</th>
                            <th class="px-5 py-4">Total & Bayar</th>
                            <th class="px-5 py-4 text-center">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sand-200">
                        @forelse($recentOrders as $ord)
                            @php
                                $rawPh = preg_replace('/[^0-9]/', '', $ord->customer_phone);
                                if (str_starts_with($rawPh, '0')) {
                                    $cPhone = '62' . substr($rawPh, 1);
                                } elseif (str_starts_with($rawPh, '62')) {
                                    $cPhone = $rawPh;
                                } else {
                                    $cPhone = '62' . $rawPh;
                                }
                                $msgWa = "Halo Kak *" . $ord->customer_name . "*,\nPesanan kacamata Anda (" . $ord->order_code . ") sedang diproses di lab optik Alysa Glasses. Total: Rp " . number_format($ord->grand_total, 0, ',', '.') . " (" . $ord->payment_status . "). Lacak status: " . url('/lacak-pesanan?code=' . $ord->order_code);
                                $waLink = "https://wa.me/" . $cPhone . "?text=" . urlencode($msgWa);
                            @endphp
                            <tr class="hover:bg-sand-50/50 transition-colors">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" name="order_ids[]" value="{{ $ord->id }}" class="order-checkbox w-4 h-4 text-brand-600 rounded border-sand-300 focus:ring-brand-500 cursor-pointer">
                                </td>
                                <td class="px-5 py-4 font-mono">
                                    <a href="{{ route('admin.order.detail', $ord->id) }}" class="font-bold text-brand-600 hover:underline block" title="Buka Detail & Validasi Pemesan">{{ $ord->order_code }} &rarr;</a>
                                    <span class="text-[10px] text-slate-400">{{ $ord->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 block">{{ $ord->customer_name }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $ord->customer_phone }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $ord->city }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    @foreach($ord->items as $it)
                                        <div class="font-bold text-slate-800">{{ $it->product_name }}</div>
                                    @endforeach
                                    <span class="text-[11px] text-brand-700 font-semibold block mt-0.5">{{ $ord->lens_type_name }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($ord->has_prescription)
                                        <div class="space-y-1 text-[10px]">
                                            <div class="px-2 py-0.5 bg-blue-50 text-blue-800 rounded font-mono">
                                                <strong>R:</strong> Sph {{ $ord->sph_r }} | Cyl {{ $ord->cyl_r }} | Ax {{ $ord->axis_r ?: '0' }}
                                            </div>
                                            <div class="px-2 py-0.5 bg-amber-50 text-amber-800 rounded font-mono">
                                                <strong>L:</strong> Sph {{ $ord->sph_l }} | Cyl {{ $ord->cyl_l }} | Ax {{ $ord->axis_l ?: '0' }}
                                            </div>
                                            <div class="text-slate-500">PD: <strong>{{ $ord->pd }} mm</strong></div>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Lensa Normal</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 block">Rp {{ number_format($ord->grand_total, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-emerald-600 font-bold block">{{ $ord->payment_status }}</span>
                                    <span class="text-[9px] text-slate-400 block">{{ $ord->payment_method }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Validasi & Detail Pemesan -->
                                        <a href="{{ route('admin.order.detail', $ord->id) }}" class="p-2 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-xl text-xs font-bold transition-all shadow-sm" title="Validasi & Update Pesanan">
                                            <i class="fa-solid fa-clipboard-check"></i>
                                        </a>

                                        <!-- Cetak Struk -->
                                        <a href="{{ route('pos.receipt', $ord->order_code) }}" target="_blank" class="p-2 bg-sky-50 hover:bg-sky-100 text-sky-700 rounded-xl text-xs font-bold transition-all shadow-sm" title="Cetak Struk Pembelian">
                                            <i class="fa-solid fa-print"></i>
                                        </a>

                                        <!-- Kirim WhatsApp Pelanggan -->
                                        @if(strlen($rawPh) >= 9)
                                            <a href="{{ $waLink }}" target="_blank" class="p-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-xl text-xs font-bold transition-all shadow-sm" title="Kirim Struk Digital via WhatsApp">
                                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                            </a>
                                        @endif

                                        <!-- Lacak Pesanan -->
                                        <a href="{{ route('shop.track', ['code' => $ord->order_code]) }}" target="_blank" class="p-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl text-xs font-bold transition-all shadow-sm" title="Buka Status Pelacakan">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <!-- Edit Transaksi (CRUD) -->
                                        <a href="{{ route('admin.order.edit', $ord->id) }}" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl text-xs font-bold transition-all shadow-sm" title="Edit Transaksi / Data Pasien">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <!-- Hapus Transaksi (CRUD Delete Permanen) -->
                                        <form action="{{ route('admin.order.destroy', $ord->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin MENGHAPUS PERMANEN pesanan {{ $ord->order_code }} atas nama {{ $ord->customer_name }}?\n\nData pesanan dan item terkait akan dihapus secara total.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-bold transition-all shadow-sm" title="Hapus Transaksi Permanen">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                    Belum ada antrean pesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Standalone Hidden Form untuk Batch Delete (Menghindari Nested Form HTML Invalid) -->
        <form id="batch-delete-form" action="{{ route('admin.order.batchDestroy') }}" method="POST" class="hidden">
            @csrf
            <div id="batch-delete-inputs"></div>
        </form>

    </div>

    <!-- Script Interaktif Ceklis & Bulk Delete -->
    <script>
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.order-checkbox');
        const batchBtn = document.getElementById('batch-delete-btn');
        const countSpan = document.getElementById('selected-count');
        const btnCount = document.getElementById('btn-count');

        function updateBatchUI() {
            const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
            if (checkedCount > 0) {
                batchBtn.classList.remove('hidden');
                batchBtn.classList.add('inline-flex');
                countSpan.classList.remove('hidden');
                countSpan.textContent = checkedCount + ' pesanan dipilih';
                btnCount.textContent = checkedCount;
            } else {
                batchBtn.classList.add('hidden');
                batchBtn.classList.remove('inline-flex');
                countSpan.classList.add('hidden');
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = checkAll.checked;
                });
                updateBatchUI();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const total = checkboxes.length;
                const checked = document.querySelectorAll('.order-checkbox:checked').length;
                if (checkAll) {
                    checkAll.checked = (total === checked && total > 0);
                }
                updateBatchUI();
            });
        });

        function executeBatchDelete() {
            const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Silakan pilih minimal 1 data pesanan untuk dihapus.');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin MENGHAPUS ' + checkedBoxes.length + ' TRANSAKSI sekaligus secara permanen?\n\nData dan item rincian pesanan akan dihapus dari sistem dan tidak dapat dikembalikan.')) {
                return;
            }

            const container = document.getElementById('batch-delete-inputs');
            container.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'order_ids[]';
                hiddenInput.value = cb.value;
                container.appendChild(hiddenInput);
            });

            // Simpan posisi scroll sebelum submit
            if (window.saveScrollPos) {
                window.saveScrollPos();
            }

            document.getElementById('batch-delete-form').submit();
        }
    </script>

@endsection
