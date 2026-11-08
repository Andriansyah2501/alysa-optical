@extends('layouts.app')

@section('title', 'Edit Transaksi ' . $order->order_code . ' | Alysa Glasses')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <div class="flex items-center justify-between pb-6 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-brand-600 font-bold uppercase tracking-wider">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Backoffice</a>
                <span>/</span>
                <span>Edit Transaksi</span>
            </div>
            <h1 class="font-serif text-3xl font-bold text-slate-900 mt-1">Edit & Kustomisasi Transaksi (CRUD Update)</h1>
            <p class="text-xs text-slate-500 mt-1">Super Admin & Admin dapat mengedit seluruh nilai transaksi: nama item, harga, lensa, ukuran resep, sisa DP, dan nomor resi.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.order.detail', $order->id) }}" class="px-3.5 py-2 bg-white border border-sand-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-sand-50 transition-colors">
                <i class="fa-solid fa-eye mr-1 text-brand-600"></i> Lihat Detail
            </a>
            <form action="{{ route('admin.order.destroy', $order->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Hapus transaksi {{ $order->order_code }} secara permanen beserta seluruh itemnya?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-xl text-xs font-bold transition-colors">
                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus Transaksi
                </button>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Terjadi kesalahan validasi:
            </div>
            <ul class="list-disc pl-5 text-[11px]">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.order.update', $order->id) }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- KODE & WAKTU -->
        <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Nomor Order Transaksi</span>
                <span class="font-mono text-base font-black text-brand-700">{{ $order->order_code }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Waktu Dibuat</span>
                <span class="font-bold text-slate-700">{{ $order->created_at->format('d/m/Y H:i') }} WIB</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tipe Transaksi</span>
                <select name="transaction_type" class="px-2.5 py-1 bg-white border border-sand-300 rounded-lg font-bold text-slate-800 mt-1">
                    <option value="catalog" {{ $order->transaction_type == 'catalog' ? 'selected' : '' }}>Katalog</option>
                    <option value="custom_frame" {{ $order->transaction_type == 'custom_frame' ? 'selected' : '' }}>Custom Frame</option>
                    <option value="lens_only" {{ $order->transaction_type == 'lens_only' ? 'selected' : '' }}>Lensa Saja</option>
                    <option value="service_only" {{ $order->transaction_type == 'service_only' ? 'selected' : '' }}>Upah Jasa</option>
                </select>
            </div>
        </div>

        <!-- 1. DATA PELANGGAN -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user text-brand-600"></i> 1. Identitas Pelanggan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Pelanggan *</label>
                    <input type="text" name="customer_name" required value="{{ old('customer_name', $order->customer_name) }}" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
                <div class="sm:col-span-3">
                    <label class="font-bold text-slate-700 block mb-1">Alamat Lengkap Konsumen</label>
                    <input type="text" name="customer_address" value="{{ old('customer_address', $order->shipping_address) }}" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
            </div>
        </div>

        <!-- 2. DATA FRAME & ITEM -->
        @php
            $firstItem = $order->items->first();
            $itemName = $firstItem ? $firstItem->product_name : 'Frame Kacamata';
        @endphp
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-glasses text-brand-600"></i> 2. Rincian Item Frame & Harga
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Deskripsi Nama Frame / Item *</label>
                    <input type="text" name="item_product_name" required value="{{ old('item_product_name', $itemName) }}" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Subtotal Frame (Rp) *</label>
                    <input type="number" name="subtotal" id="editSubtotal" required value="{{ old('subtotal', (int)$order->subtotal) }}" min="0" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-extrabold text-slate-900">
                </div>
            </div>
        </div>

        <!-- 3. LENSA & UPAH JASA -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-wrench text-brand-600"></i> 3. Paket Lensa & Biaya Servis Tambahan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Paket Lensa</label>
                    <input type="text" name="lens_type_name" value="{{ old('lens_type_name', $order->lens_type_name) }}" placeholder="Contoh: Bluecromic 11.11" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Biaya Lensa (Rp)</label>
                    <input type="number" name="lens_price" id="editLensPrice" value="{{ old('lens_price', (int)$order->lens_price) }}" min="0" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Jasa / Servis</label>
                    <input type="text" name="service_name" value="{{ old('service_name', $order->service_name) }}" placeholder="Biaya Pasang / Stel Frame" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Upah Servis (Rp)</label>
                    <input type="number" name="service_fee" id="editServiceFee" value="{{ old('service_fee', (int)$order->service_fee) }}" min="0" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
                </div>
            </div>
        </div>

        <!-- 4. RESEP MATA (RX) -->
        <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 space-y-3">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="has_prescription" id="checkEditRx" value="1" {{ $order->has_prescription ? 'checked' : '' }} class="rounded text-brand-600">
                <label for="checkEditRx" class="font-bold text-slate-900">Sertakan Resep Lensa Mata (Rx)</label>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 pt-2">
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">R (OD) Sph</label>
                    <input type="text" name="sph_r" value="{{ $order->sph_r }}" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">R (OD) Cyl</label>
                    <input type="text" name="cyl_r" value="{{ $order->cyl_r }}" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">R (OD) Axis</label>
                    <input type="text" name="axis_r" value="{{ $order->axis_r }}" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">L (OS) Sph</label>
                    <input type="text" name="sph_l" value="{{ $order->sph_l }}" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">L (OS) Cyl</label>
                    <input type="text" name="cyl_l" value="{{ $order->cyl_l }}" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">L (OS) Axis</label>
                    <input type="text" name="axis_l" value="{{ $order->axis_l }}" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">PD (Pupil mm)</label>
                    <input type="text" name="pd" value="{{ $order->pd ?: '62' }}" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
            </div>
            <div>
                <label class="font-bold text-[10px] text-slate-500 block mb-0.5">Catatan Dokter / Refraksionis</label>
                <input type="text" name="prescription_notes" value="{{ $order->prescription_notes }}" placeholder="Lensa silinder tinggi / pupil offset" class="w-full px-3 py-1.5 bg-white border border-sand-300 rounded-lg font-medium">
            </div>
        </div>

        <!-- 5. KEUANGAN, DOWN PAYMENT & GRAND TOTAL -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-brand-600"></i> 4. Keuangan, Down Payment & Pelunasan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Diskon Potongan (Rp)</label>
                    <input type="number" name="discount_amount" id="editDiscount" value="{{ old('discount_amount', (int)$order->discount_amount) }}" min="0" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Grand Total (Rp) *</label>
                    <input type="number" name="grand_total" id="editGrandTotal" required value="{{ old('grand_total', (int)$order->grand_total) }}" min="0" class="w-full px-3.5 py-2.5 bg-sand-100 border border-sand-300 rounded-xl font-black text-brand-700">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tipe Pembayaran *</label>
                    <select name="payment_type" id="editPayType" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="full" {{ $order->payment_type == 'full' ? 'selected' : '' }}>Lunas Penuh (Full)</option>
                        <option value="dp" {{ $order->payment_type == 'dp' ? 'selected' : '' }}>Down Payment (DP)</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Metode Bayar *</label>
                    <select name="payment_method" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="Cash / Tunai" {{ $order->payment_method == 'Cash / Tunai' ? 'selected' : '' }}>Cash / Tunai</option>
                        <option value="QRIS Statis Toko (BCA/GoPay)" {{ str_contains($order->payment_method, 'QRIS') ? 'selected' : '' }}>QRIS Statis Toko</option>
                        <option value="EDC Debit / Kartu Kredit BCA" {{ str_contains($order->payment_method, 'EDC') ? 'selected' : '' }}>Mesin EDC Kartu BCA</option>
                        <option value="Transfer Bank Mandiri" {{ str_contains($order->payment_method, 'Transfer') ? 'selected' : '' }}>Transfer Bank Mandiri</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-amber-900 block mb-1">Nominal DP Diterima (Rp)</label>
                    <input type="number" name="dp_amount" id="editDpAmount" value="{{ old('dp_amount', (int)$order->dp_amount) }}" min="0" class="w-full px-3.5 py-2.5 bg-amber-50 border border-amber-300 rounded-xl font-extrabold text-amber-900">
                </div>
                <div>
                    <label class="font-bold text-rose-700 block mb-1">Sisa Tagihan Belum Lunas (Rp)</label>
                    <input type="number" name="remaining_balance" id="editRemainingBalance" value="{{ old('remaining_balance', (int)$order->remaining_balance) }}" min="0" class="w-full px-3.5 py-2.5 bg-rose-50 border border-rose-300 rounded-xl font-extrabold text-rose-700">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Status Pembayaran *</label>
                    <select name="payment_status" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="Lunas" {{ $order->payment_status == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="DP (Uang Muka Terbayar)" {{ $order->payment_status == 'DP (Uang Muka Terbayar)' ? 'selected' : '' }}>DP (Uang Muka Terbayar)</option>
                        <option value="Menunggu Pembayaran" {{ $order->payment_status == 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Status Pengerjaan Kacamata *</label>
                    <select name="order_status" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="Order Diterima / Menunggu Antrean" {{ $order->order_status == 'Order Diterima / Menunggu Antrean' ? 'selected' : '' }}>Order Diterima / Menunggu Antrean</option>
                        <option value="Proses Potong Lensa (Lab Optik)" {{ $order->order_status == 'Proses Potong Lensa (Lab Optik)' ? 'selected' : '' }}>Proses Potong Lensa (Lab Optik)</option>
                        <option value="QC & Pemasangan Frame" {{ $order->order_status == 'QC & Pemasangan Frame' ? 'selected' : '' }}>QC & Pemasangan Frame</option>
                        <option value="Siap Diambil / Dalam Pengiriman" {{ $order->order_status == 'Siap Diambil / Dalam Pengiriman' ? 'selected' : '' }}>Siap Diambil / Dalam Pengiriman</option>
                        <option value="Selesai / Diterima Pelanggan" {{ $order->order_status == 'Selesai / Diterima Pelanggan' ? 'selected' : '' }}>Selesai / Diterima Pelanggan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 6. RESI & CATATAN LAB -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-truck text-brand-600"></i> 5. Nomor Resi Ekspedisi & Catatan Backoffice
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nomor Resi / Kurir (Tracking Number)</label>
                    <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="JNT-BTM-998877" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-mono font-bold text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Catatan Staf / Refraksionis</label>
                    <input type="text" name="admin_notes" value="{{ old('admin_notes', $order->admin_notes) }}" placeholder="Lensa selesai dipotong, siap diambil" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-sand-200 flex items-center justify-between">
            <a href="{{ route('admin.order.detail', $order->id) }}" class="px-5 py-2.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold transition-all">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-check-double"></i> Simpan Seluruh Perubahan Transaksi
            </button>
        </div>
    </form>

</div>

<script>
    const editSubtotal = document.getElementById('editSubtotal');
    const editLensPrice = document.getElementById('editLensPrice');
    const editServiceFee = document.getElementById('editServiceFee');
    const editDiscount = document.getElementById('editDiscount');
    const editGrandTotal = document.getElementById('editGrandTotal');
    const editPayType = document.getElementById('editPayType');
    const editDpAmount = document.getElementById('editDpAmount');
    const editRemainingBalance = document.getElementById('editRemainingBalance');

    function recalcTotal() {
        const frame = parseFloat(editSubtotal.value) || 0;
        const lens = parseFloat(editLensPrice.value) || 0;
        const service = parseFloat(editServiceFee.value) || 0;
        const disc = parseFloat(editDiscount.value) || 0;

        const grand = Math.max(0, (frame + lens + service) - disc);
        editGrandTotal.value = grand;

        if (editPayType.value === 'dp') {
            const dp = parseFloat(editDpAmount.value) || 0;
            editRemainingBalance.value = Math.max(0, grand - dp);
        } else {
            editDpAmount.value = grand;
            editRemainingBalance.value = 0;
        }
    }

    [editSubtotal, editLensPrice, editServiceFee, editDiscount, editDpAmount].forEach(el => {
        el.addEventListener('input', recalcTotal);
    });

    editPayType.addEventListener('change', recalcTotal);
</script>
@endsection
