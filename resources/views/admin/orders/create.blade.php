@extends('layouts.app')

@section('title', 'Tambah Transaksi Baru (Manual Backoffice) | Alysa Glasses')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <div class="flex items-center justify-between pb-6 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-brand-600 font-bold uppercase tracking-wider">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Backoffice</a>
                <span>/</span>
                <span>Tambah Transaksi Baru</span>
            </div>
            <h1 class="font-serif text-3xl font-bold text-slate-900 mt-1">Input Transaksi Optik Baru (CRUD Create)</h1>
            <p class="text-xs text-slate-500 mt-1">Super Admin & Admin dapat membuat entri transaksi baru secara manual lengkap dengan resep dan status.</p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-white border border-sand-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-sand-50 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Antrean
        </a>
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

    <form action="{{ route('admin.order.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-6 text-xs" id="formCreateOrder">
        @csrf

        <!-- 1. IDENTITAS PELANGGAN -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user text-brand-600"></i> 1. Data Pelanggan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Lengkap *</label>
                    <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="081234567890" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="pelanggan@gmail.com" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
                <div class="sm:col-span-3">
                    <label class="font-bold text-slate-700 block mb-1">Alamat Lengkap Konsumen</label>
                    <input type="text" name="customer_address" value="{{ old('customer_address') }}" placeholder="Alamat rumah, kelurahan/kecamatan, kota domisili atau catatan pengambilan..." class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
            </div>
        </div>

        <!-- 2. PILIHAN FRAME & TIPE TRANSAKSI -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-glasses text-brand-600"></i> 2. Spesifikasi Frame & Layanan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tipe Transaksi *</label>
                    <select name="transaction_type" id="selTrxType" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="catalog">Katalog Frame Toko</option>
                        <option value="custom_frame">Custom Frame Bebas</option>
                        <option value="lens_only">Ganti Lensa Saja (Frame Bawa Sendiri)</option>
                        <option value="service_only">Jasa Servis / Reparasi Saja</option>
                    </select>
                </div>
                <div id="boxCatalogProduct">
                    <label class="font-bold text-slate-700 block mb-1">Pilih Produk Katalog</label>
                    <select name="product_id" id="selProduct" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                        <option value="">-- Pilih Frame Etalase --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->effective_price }}">{{ $p->name }} (Rp {{ number_format($p->effective_price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div id="boxCustomFrame" class="hidden">
                    <label class="font-bold text-slate-700 block mb-1">Nama Frame Custom</label>
                    <input type="text" name="custom_frame_name" placeholder="Contoh: Frame Titanium Retro" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Harga Frame (Rp) *</label>
                    <input type="number" name="frame_price" id="inFramePrice" required value="0" min="0" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-extrabold text-slate-900">
                </div>
            </div>
        </div>

        <!-- 3. LENSA & UPAH JASA -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-wrench text-brand-600"></i> 3. Paket Lensa & Biaya Servis
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Paket Lensa</label>
                    <select name="lens_type_id" id="selLens" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                        <option value="">-- Tanpa Lensa --</option>
                        @foreach($lensTypes as $lt)
                            <option value="{{ $lt->id }}" data-price="{{ $lt->additional_price }}">{{ $lt->name }} (+Rp {{ number_format($lt->additional_price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Biaya Lensa (Rp)</label>
                    <input type="number" name="lens_price" id="inLensPrice" value="0" min="0" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Jasa / Servis</label>
                    <input type="text" name="service_name" placeholder="Biaya Pasang / Stel Frame" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Upah Servis (Rp)</label>
                    <input type="number" name="service_fee" id="inServiceFee" value="0" min="0" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
                </div>
            </div>
        </div>

        <!-- 4. RESEP LENSA (RX) -->
        <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 space-y-3">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="has_prescription" id="checkRx" value="1" class="rounded text-brand-600">
                <label for="checkRx" class="font-bold text-slate-900">Catat Ukuran Resep Lensa Mata (Rx)</label>
            </div>

            <div id="boxRxInputs" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 pt-2">
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">R (OD) Sph</label>
                    <input type="text" name="sph_r" placeholder="-1.50" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">R (OD) Cyl</label>
                    <input type="text" name="cyl_r" placeholder="0.00" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">R (OD) Axis</label>
                    <input type="text" name="axis_r" placeholder="180" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">L (OS) Sph</label>
                    <input type="text" name="sph_l" placeholder="-2.00" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">L (OS) Cyl</label>
                    <input type="text" name="cyl_l" placeholder="0.00" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">L (OS) Axis</label>
                    <input type="text" name="axis_l" placeholder="0" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
                <div>
                    <label class="font-bold text-[10px] text-slate-500 block mb-0.5">PD (Pupil mm)</label>
                    <input type="text" name="pd" value="62" class="w-full px-2 py-1.5 bg-white border border-sand-300 rounded-lg text-center font-bold">
                </div>
            </div>
        </div>

        <!-- 5. PEMBAYARAN & STATUS -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm border-b border-sand-200 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-brand-600"></i> 4. Pembayaran, Down Payment & Status
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tipe Pembayaran *</label>
                    <select name="payment_type" id="selPayType" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="full">Bayar Lunas Penuh</option>
                        <option value="dp">Down Payment (Uang Muka / DP)</option>
                    </select>
                </div>
                <div id="boxDpInput" class="hidden">
                    <label class="font-bold text-amber-900 block mb-1">Nominal Uang Muka (DP) Rp</label>
                    <input type="number" name="dp_amount" id="inDpAmount" value="0" min="0" class="w-full px-3.5 py-2.5 bg-amber-50 border border-amber-300 rounded-xl font-extrabold text-amber-900">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Metode Bayar *</label>
                    <select name="payment_method" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="Cash / Tunai">Cash / Tunai</option>
                        <option value="QRIS Statis Toko (BCA/GoPay)">QRIS Statis Toko</option>
                        <option value="EDC Debit / Kartu Kredit BCA">Mesin EDC Kartu BCA</option>
                        <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Status Pembayaran *</label>
                    <select name="payment_status" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="Lunas">Lunas</option>
                        <option value="DP (Uang Muka Terbayar)">DP (Uang Muka Terbayar)</option>
                        <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Status Pengerjaan Kacamata *</label>
                    <select name="order_status" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="Order Diterima / Menunggu Antrean">Order Diterima / Menunggu Antrean</option>
                        <option value="Proses Potong Lensa (Lab Optik)">Proses Potong Lensa (Lab Optik)</option>
                        <option value="QC & Pemasangan Frame">QC & Pemasangan Frame</option>
                        <option value="Siap Diambil / Dalam Pengiriman">Siap Diambil / Dalam Pengiriman</option>
                        <option value="Selesai / Diterima Pelanggan">Selesai / Diterima Pelanggan</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-sand-200 flex items-center justify-between">
            <span class="text-xs text-slate-500 italic">*Setelah disimpan, kode order otomatis terbit dan struk thermal serta link WhatsApp dapat langsung dicetak.</span>
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Transaksi Baru
            </button>
        </div>
    </form>

</div>

<script>
    const selTrxType = document.getElementById('selTrxType');
    const boxCatalogProduct = document.getElementById('boxCatalogProduct');
    const boxCustomFrame = document.getElementById('boxCustomFrame');
    const selProduct = document.getElementById('selProduct');
    const inFramePrice = document.getElementById('inFramePrice');
    const selLens = document.getElementById('selLens');
    const inLensPrice = document.getElementById('inLensPrice');
    const selPayType = document.getElementById('selPayType');
    const boxDpInput = document.getElementById('boxDpInput');

    selTrxType.addEventListener('change', function() {
        if (this.value === 'catalog') {
            boxCatalogProduct.classList.remove('hidden');
            boxCustomFrame.classList.add('hidden');
        } else if (this.value === 'custom_frame') {
            boxCatalogProduct.classList.add('hidden');
            boxCustomFrame.classList.remove('hidden');
        } else {
            boxCatalogProduct.classList.add('hidden');
            boxCustomFrame.classList.add('hidden');
            inFramePrice.value = 0;
        }
    });

    selProduct.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.price) {
            inFramePrice.value = opt.dataset.price;
        }
    });

    selLens.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.price) {
            inLensPrice.value = opt.dataset.price;
        }
    });

    selPayType.addEventListener('change', function() {
        if (this.value === 'dp') {
            boxDpInput.classList.remove('hidden');
        } else {
            boxDpInput.classList.add('hidden');
        }
    });
</script>
@endsection
