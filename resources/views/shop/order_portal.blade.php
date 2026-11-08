@extends('layouts.app')

@section('title', 'Pemesanan Kacamata Online & Resep Optik | Alysa Glasses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">

    <!-- HERO / LANDING BANNER PEMESANAN ONLINE -->
    <div class="relative bg-gradient-to-br from-brand-600 via-brand-700 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-15 text-white text-9xl font-serif pointer-events-none">👓</div>
        <div class="relative z-10 max-w-2xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-black uppercase tracking-wider text-amber-300">
                <i class="fa-solid fa-bolt"></i> Online Order & Custom Glasses Portal
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold leading-tight">
                Pesan Kacamata Online & Ganti Lensa Mudah dari Rumah
            </h1>
            <p class="text-xs sm:text-sm text-sand-100 leading-relaxed">
                Pilih frame favorit Anda, sesuaikan jenis paket lensa optik (Blueray, Photocromic, Bluecromic, Progresif), masukkan ukuran minus/resep dokter, dan pilih sistem pembayaran (Lunas atau DP 50%).
            </p>
            <div class="flex flex-wrap items-center gap-4 text-xs font-bold pt-2">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-shield-check text-emerald-400"></i> Garansi Resep 30 Hari</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-sky-400"></i> Gratis Ongkir Batam Area</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-receipt text-amber-400"></i> Faktur Resmi & Live Tracking</span>
            </div>
        </div>
    </div>

    <!-- PANEL UTAMA PEMESANAN ONLINE (INTERAKTIF & REAL-TIME BILLING) -->
    <form action="{{ route('shop.customer.portal.store') }}" method="POST" id="customerOrderForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        @csrf

        <!-- KOLOM KIRI: KONFIGURATOR KACAMATA & RESEP (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- 1. PILIH KATEGORI / MODE PEMBELIAN -->
            <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                <h3 class="font-serif text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center text-xs font-black">1</span>
                    Tentukan Jenis Kebutuhan Kacamata
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-bold">
                    <label class="cursor-pointer border-2 border-brand-500 bg-brand-50/50 p-3.5 rounded-2xl flex flex-col justify-between mode-card transition-all text-center" onclick="setOrderMode('complete', this)">
                        <input type="radio" name="order_mode" value="complete" checked class="hidden">
                        <i class="fa-solid fa-glasses text-brand-600 text-xl mb-1.5"></i>
                        <span class="text-slate-900 font-extrabold text-xs">Paket Lengkap</span>
                        <span class="text-[10px] text-slate-500 font-normal">Frame + Lensa Baru</span>
                    </label>

                    <label class="cursor-pointer border-2 border-sand-200 hover:border-sand-300 p-3.5 rounded-2xl flex flex-col justify-between mode-card transition-all text-center" onclick="setOrderMode('lens_only', this)">
                        <input type="radio" name="order_mode" value="lens_only" class="hidden">
                        <i class="fa-solid fa-circle-dot text-amber-600 text-xl mb-1.5"></i>
                        <span class="text-slate-900 font-extrabold text-xs">Ganti Lensa</span>
                        <span class="text-[10px] text-slate-500 font-normal">Pakai Frame Lama</span>
                    </label>

                    <label class="cursor-pointer border-2 border-sand-200 hover:border-sand-300 p-3.5 rounded-2xl flex flex-col justify-between mode-card transition-all text-center" onclick="setOrderMode('frame_only', this)">
                        <input type="radio" name="order_mode" value="frame_only" class="hidden">
                        <i class="fa-solid fa-shapes text-sky-600 text-xl mb-1.5"></i>
                        <span class="text-slate-900 font-extrabold text-xs">Frame Saja</span>
                        <span class="text-[10px] text-slate-500 font-normal">Lensa Bawaan Standar</span>
                    </label>

                    <label class="cursor-pointer border-2 border-sand-200 hover:border-sand-300 p-3.5 rounded-2xl flex flex-col justify-between mode-card transition-all text-center" onclick="setOrderMode('service', this)">
                        <input type="radio" name="order_mode" value="service" class="hidden">
                        <i class="fa-solid fa-screwdriver-wrench text-emerald-600 text-xl mb-1.5"></i>
                        <span class="text-slate-900 font-extrabold text-xs">Servis / Reparasi</span>
                        <span class="text-[10px] text-slate-500 font-normal">Ganti Nosepad / Baut</span>
                    </label>
                </div>
            </div>

            <!-- 2. PEMILIHAN FRAME (TAMPIL JIKA LENGKAP ATAU FRAME SAJA) -->
            <div id="sectionFrame" class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-serif text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center text-xs font-black">2</span>
                        Pilih Frame Favorit Anda
                    </h3>
                    <span class="text-xs text-brand-600 font-bold">Koleksi Alysa 11.11</span>
                </div>

                <!-- Pilihan Frame dari Katalog -->
                <div class="space-y-2">
                    <label class="font-bold text-xs text-slate-700">Katalog Frame Alysa Glasses:</label>
                    <select name="product_id" id="productSelect" onchange="calculateBill()" class="w-full px-4 py-3 bg-sand-50 border border-sand-300 rounded-2xl font-bold text-xs text-slate-800">
                        <option value="" data-price="0">-- Pilih Frame dari Katalog Alysa --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->effective_price }}" {{ $selectedProductId == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->model_type }}) — Rp {{ number_format($p->effective_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Opsi Custom Frame / Request Khusus -->
                <div class="pt-3 border-t border-sand-100">
                    <button type="button" onclick="document.getElementById('customFrameBox').classList.toggle('hidden')" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-sliders"></i> Atau Mau Request Model / Nama Frame Bebas? (Klik di sini)
                    </button>

                    <div id="customFrameBox" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3">
                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Nama / Model Frame Custom:</label>
                            <input type="text" name="custom_frame_name" id="customFrameName" placeholder="Misal: Titanium Korean Cat Eye Gold" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-semibold">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Estimasi Harga Frame (Rp):</label>
                            <input type="number" name="frame_price" id="customFramePrice" oninput="calculateBill()" placeholder="Contoh: 280000" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-bold">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. PEMILIHAN PAKET LENSA (TAMPIL JIKA LENGKAP ATAU GANTI LENSA) -->
            <div id="sectionLens" class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-serif text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center text-xs font-black">3</span>
                        Pilih Paket Lensa Resep & Optik
                    </h3>
                    <span class="text-xs text-amber-600 font-black"><i class="fa-solid fa-fire mr-1"></i> Special Price 11.11</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($lensTypes as $idx => $lens)
                        @php
                            $isSelected = ($selectedLensId == $lens->id) || (!$selectedLensId && $idx === 0);
                        @endphp
                        <label class="cursor-pointer border-2 {{ $isSelected ? 'border-brand-500 bg-brand-50/40' : 'border-sand-200 hover:border-sand-300' }} p-3.5 rounded-2xl flex flex-col justify-between lens-card transition-all" onclick="selectLensRadio(this)">
                            <input type="radio" name="lens_type_id" value="{{ $lens->id }}" data-price="{{ $lens->additional_price }}" {{ $isSelected ? 'checked' : '' }} class="hidden">
                            <div class="space-y-1">
                                <span class="text-xs font-extrabold text-slate-900 block leading-snug">{{ $lens->name }}</span>
                                <span class="text-[10px] text-slate-500 block">{{ $lens->description ?: 'Perlindungan mata optimal' }}</span>
                            </div>
                            <div class="pt-3 mt-3 border-t border-sand-100 flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-bold">Biaya Paket:</span>
                                <span class="text-xs font-black text-brand-700">+ Rp {{ number_format($lens->additional_price, 0, ',', '.') }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 4. FORM RESEP KLINIS MATA (SPH, CYL, AXIS, PD) -->
            <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-serif text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center text-xs font-black">4</span>
                        Ukuran Resep Mata (Opsional / Jika Ada)
                    </h3>
                    <label class="flex items-center gap-2 text-xs font-bold text-brand-700 cursor-pointer">
                        <input type="checkbox" name="has_prescription" id="hasPrescription" value="1" checked onchange="togglePrescriptionForm()" class="rounded text-brand-600">
                        <span>Sertakan Ukuran Resep</span>
                    </label>
                </div>

                <div id="prescriptionFields" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- MATA KANAN (OD) -->
                        <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 space-y-2.5">
                            <span class="text-xs font-black uppercase text-brand-700 block"><i class="fa-solid fa-eye mr-1"></i> Mata Kanan (OD)</span>
                            <div class="grid grid-cols-3 gap-2 text-xs font-bold">
                                <div>
                                    <label class="text-[10px] text-slate-500 block mb-0.5">SPH (Minus)</label>
                                    <input type="text" name="sph_r" placeholder="-1.50" class="w-full px-2.5 py-1.5 bg-white border border-sand-300 rounded-xl text-xs font-bold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-slate-500 block mb-0.5">CYL (Silinder)</label>
                                    <input type="text" name="cyl_r" placeholder="-0.50" class="w-full px-2.5 py-1.5 bg-white border border-sand-300 rounded-xl text-xs font-bold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-slate-500 block mb-0.5">AXIS</label>
                                    <input type="text" name="axis_r" placeholder="180" class="w-full px-2.5 py-1.5 bg-white border border-sand-300 rounded-xl text-xs font-bold text-center">
                                </div>
                            </div>
                        </div>

                        <!-- MATA KIRI (OS) -->
                        <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 space-y-2.5">
                            <span class="text-xs font-black uppercase text-brand-700 block"><i class="fa-solid fa-eye mr-1"></i> Mata Kiri (OS)</span>
                            <div class="grid grid-cols-3 gap-2 text-xs font-bold">
                                <div>
                                    <label class="text-[10px] text-slate-500 block mb-0.5">SPH (Minus)</label>
                                    <input type="text" name="sph_l" placeholder="-1.75" class="w-full px-2.5 py-1.5 bg-white border border-sand-300 rounded-xl text-xs font-bold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-slate-500 block mb-0.5">CYL (Silinder)</label>
                                    <input type="text" name="cyl_l" placeholder="-0.25" class="w-full px-2.5 py-1.5 bg-white border border-sand-300 rounded-xl text-xs font-bold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-slate-500 block mb-0.5">AXIS</label>
                                    <input type="text" name="axis_l" placeholder="90" class="w-full px-2.5 py-1.5 bg-white border border-sand-300 rounded-xl text-xs font-bold text-center">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Pupillary Distance (PD mm):</label>
                            <input type="text" name="pd" placeholder="Contoh: 62" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-bold">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Catatan Tambahan Optik:</label>
                            <input type="text" name="notes" placeholder="Misal: untuk mata lelah / banyak kerja di depan laptop" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. DATA PENERIMA & ALAMAT KONSUMEN -->
            <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                <h3 class="font-serif text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center text-xs font-black">5</span>
                    Data Pemesan & Alamat Pengiriman
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Lengkap *</label>
                        <input type="text" name="customer_name" required placeholder="Nama Anda" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">No. WhatsApp Aktif *</label>
                        <input type="text" name="customer_phone" required placeholder="0812-xxxx-xxxx" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Email (Faktur Digital)</label>
                        <input type="email" name="customer_email" placeholder="nama@email.com" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="sm:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Alamat Lengkap Pengiriman *</label>
                        <textarea name="shipping_address" required rows="2" placeholder="Nama jalan, perumahan, nomor rumah, RT/RW, kecamatan..." class="w-full px-3.5 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs"></textarea>
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Kota / Wilayah *</label>
                        <select name="city" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold">
                            <option value="Batam">Kota Batam</option>
                            <option value="Tanjung Pinang">Kota Tanjung Pinang</option>
                            <option value="Bintan">Kabupaten Bintan</option>
                            <option value="Karimun">Kabupaten Karimun</option>
                            <option value="Luar Kepri">Luar Kepulauan Riau</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: RINGKASAN TAGIHAN & PEMBAYARAN (4 Cols) -->
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

            <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-xl space-y-5">
                <div class="border-b border-sand-200 pb-3">
                    <span class="text-[10px] font-black uppercase text-brand-600 tracking-wider">Ringkasan Pesanan Online</span>
                    <h3 class="font-serif text-xl font-bold text-slate-900 mt-0.5">Rincian Pembayaran</h3>
                </div>

                <!-- Rincian Biaya -->
                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Biaya Frame:</span>
                        <span id="billFrame" class="font-bold text-slate-900">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Paket Lensa Optik:</span>
                        <span id="billLens" class="font-bold text-slate-900">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600 border-t border-sand-100 pt-2">
                        <span>Subtotal:</span>
                        <span id="billSubtotal" class="font-bold text-slate-900">Rp 0</span>
                    </div>
                    <div id="voucherRow" class="flex items-center justify-between text-emerald-700 hidden">
                        <span>Diskon Kupon Promo:</span>
                        <span id="billDiscount" class="font-black">- Rp 0</span>
                    </div>
                </div>

                <!-- Input Kupon Voucher Promo -->
                <div class="pt-3 border-t border-sand-100 space-y-2">
                    <label class="text-[11px] font-bold text-slate-700 flex items-center justify-between">
                        <span>Punya Kode Voucher Promo?</span>
                        <a href="{{ route('shop.calendar') }}" target="_blank" class="text-brand-600 hover:underline text-[10px]">Cek Kalender Promo &rarr;</a>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" name="voucher_code" id="voucherCodeInput" placeholder="Contoh: ALYSA50K" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-mono text-xs uppercase font-bold text-slate-900">
                        <button type="button" onclick="applyVoucher()" class="px-3.5 py-2 bg-slate-900 hover:bg-brand-600 text-white rounded-xl text-xs font-bold transition-colors">
                            Pakai
                        </button>
                    </div>
                    <p id="voucherMsg" class="text-[10px] hidden font-semibold"></p>
                </div>

                <!-- PILIH SKEMA PEMBAYARAN: LUNAS VS DP -->
                <div class="pt-3 border-t border-sand-100 space-y-2.5">
                    <label class="text-[11px] font-bold text-slate-800 block">Skema Pembayaran:</label>
                    <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                        <label class="cursor-pointer border-2 border-brand-500 bg-brand-50 p-2.5 rounded-xl text-center pay-type-card" onclick="setPaymentType('full', this)">
                            <input type="radio" name="payment_type" value="full" checked class="hidden">
                            <span class="block text-slate-900">Bayar Lunas</span>
                            <span class="text-[9px] text-slate-500 font-normal">Diskon maksimal</span>
                        </label>
                        <label class="cursor-pointer border-2 border-sand-200 p-2.5 rounded-xl text-center pay-type-card" onclick="setPaymentType('dp', this)">
                            <input type="radio" name="payment_type" value="dp" class="hidden">
                            <span class="block text-slate-900">Uang Muka (DP)</span>
                            <span class="text-[9px] text-slate-500 font-normal">DP 50% dulu</span>
                        </label>
                    </div>

                    <div id="dpBox" class="hidden pt-2 space-y-1">
                        <label class="text-[10px] font-bold text-slate-600 block">Nominal Uang Muka (DP Rp):</label>
                        <input type="number" name="dp_amount" id="dpAmountInput" oninput="calculateBill()" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-bold text-amber-700">
                        <p class="text-[9px] text-slate-400">Sisa pembayaran dilunasi saat kacamata selesai / dikirim.</p>
                    </div>
                </div>

                <!-- METODE TRANSFER / QRIS -->
                <div class="pt-3 border-t border-sand-100 space-y-2">
                    <label class="text-[11px] font-bold text-slate-800 block">Metode Pembayaran Online:</label>
                    <select name="payment_method" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-bold text-slate-800">
                        <option value="qris">QRIS Standar (BCA / Mandiri / GoPay / OVO)</option>
                        <option value="transfer_bca">Transfer Bank BCA (Virtual Account)</option>
                        <option value="transfer_mandiri">Transfer Bank Mandiri</option>
                        <option value="cod">COD / Bayar di Toko Saat Ambil</option>
                    </select>
                </div>

                <!-- GRAND TOTAL DISPLAY -->
                <div class="p-4 bg-brand-50/70 border border-brand-200 rounded-2xl space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-brand-900" id="totalLabel">Total Tagihan:</span>
                        <span id="billGrandTotal" class="text-xl font-black text-brand-700">Rp 0</span>
                    </div>
                    <div id="remainingRow" class="flex items-center justify-between text-[11px] text-amber-800 font-bold hidden border-t border-brand-200/60 pt-1 mt-1">
                        <span>Sisa Piutang (Pelunasan):</span>
                        <span id="billRemaining">Rp 0</span>
                    </div>
                </div>

                <!-- TOMBOL SUBMIT PESANAN -->
                <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl font-extrabold text-sm shadow-lg shadow-brand-500/25 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-cart-shopping"></i> Kirim Pesanan Kacamata Sekarang
                </button>
                <p class="text-[10px] text-center text-slate-400">Pesanan langsung masuk ke antrean optik & kasir Alysa Glasses.</p>
            </div>

        </div>

    </form>

</div>

<script>
    let currentMode = 'complete';
    let currentPaymentType = 'full';
    let voucherDiscount = 0;

    const availableVouchersData = @json($vouchers);

    function setOrderMode(mode, el) {
        currentMode = mode;
        document.querySelectorAll('.mode-card').forEach(c => {
            c.classList.remove('border-brand-500', 'bg-brand-50/50');
            c.classList.add('border-sand-200');
        });
        el.classList.add('border-brand-500', 'bg-brand-50/50');
        el.querySelector('input').checked = true;

        const secFrame = document.getElementById('sectionFrame');
        const secLens = document.getElementById('sectionLens');

        if (mode === 'complete') {
            secFrame.classList.remove('hidden');
            secLens.classList.remove('hidden');
        } else if (mode === 'lens_only') {
            secFrame.classList.add('hidden');
            secLens.classList.remove('hidden');
        } else if (mode === 'frame_only') {
            secFrame.classList.remove('hidden');
            secLens.classList.add('hidden');
        } else if (mode === 'service') {
            secFrame.classList.add('hidden');
            secLens.classList.add('hidden');
        }
        calculateBill();
    }

    function selectLensRadio(el) {
        document.querySelectorAll('.lens-card').forEach(c => {
            c.classList.remove('border-brand-500', 'bg-brand-50/40');
            c.classList.add('border-sand-200');
        });
        el.classList.add('border-brand-500', 'bg-brand-50/40');
        el.querySelector('input').checked = true;
        calculateBill();
    }

    function togglePrescriptionForm() {
        const checked = document.getElementById('hasPrescription').checked;
        document.getElementById('prescriptionFields').style.display = checked ? 'block' : 'none';
    }

    function setPaymentType(type, el) {
        currentPaymentType = type;
        document.querySelectorAll('.pay-type-card').forEach(c => {
            c.classList.remove('border-brand-500', 'bg-brand-50');
            c.classList.add('border-sand-200');
        });
        el.classList.add('border-brand-500', 'bg-brand-50');
        el.querySelector('input').checked = true;

        const dpBox = document.getElementById('dpBox');
        if (type === 'dp') {
            dpBox.classList.remove('hidden');
        } else {
            dpBox.classList.add('hidden');
        }
        calculateBill();
    }

    function applyVoucher() {
        const code = document.getElementById('voucherCodeInput').value.trim().toUpperCase();
        const msg = document.getElementById('voucherMsg');
        msg.classList.remove('hidden', 'text-emerald-600', 'text-rose-600');

        if (!code) {
            voucherDiscount = 0;
            msg.textContent = 'Silakan masukkan kode kupon.';
            msg.classList.add('text-rose-600');
            calculateBill();
            return;
        }

        const found = availableVouchersData.find(v => v.code.toUpperCase() === code);
        if (found) {
            voucherDiscount = parseFloat(found.discount_amount);
            msg.textContent = 'Kupon ' + found.code + ' berhasil! Hemat Rp ' + Number(voucherDiscount).toLocaleString('id-ID');
            msg.classList.add('text-emerald-600');
        } else {
            voucherDiscount = 0;
            msg.textContent = 'Kode kupon promo tidak valid atau telah berakhir.';
            msg.classList.add('text-rose-600');
        }
        calculateBill();
    }

    function calculateBill() {
        let framePrice = 0;
        let lensPrice = 0;

        // Frame
        if (currentMode === 'complete' || currentMode === 'frame_only') {
            const prodSelect = document.getElementById('productSelect');
            const selectedOpt = prodSelect.options[prodSelect.selectedIndex];
            const catPrice = selectedOpt ? parseFloat(selectedOpt.getAttribute('data-price') || 0) : 0;
            const customPrice = parseFloat(document.getElementById('customFramePrice').value || 0);
            framePrice = customPrice > 0 ? customPrice : catPrice;
        } else if (currentMode === 'service') {
            framePrice = 35000; // Standar upah reparasi
        }

        // Lens
        if (currentMode === 'complete' || currentMode === 'lens_only') {
            const checkedLens = document.querySelector('input[name="lens_type_id"]:checked');
            lensPrice = checkedLens ? parseFloat(checkedLens.getAttribute('data-price') || 0) : 0;
        }

        const subtotal = framePrice + lensPrice;
        const discount = Math.min(voucherDiscount, subtotal);
        const grandTotal = Math.max(0, subtotal - discount);

        document.getElementById('billFrame').textContent = 'Rp ' + Number(framePrice).toLocaleString('id-ID');
        document.getElementById('billLens').textContent = 'Rp ' + Number(lensPrice).toLocaleString('id-ID');
        document.getElementById('billSubtotal').textContent = 'Rp ' + Number(subtotal).toLocaleString('id-ID');

        const voucherRow = document.getElementById('voucherRow');
        if (discount > 0) {
            voucherRow.classList.remove('hidden');
            document.getElementById('billDiscount').textContent = '- Rp ' + Number(discount).toLocaleString('id-ID');
        } else {
            voucherRow.classList.add('hidden');
        }

        const dpInput = document.getElementById('dpAmountInput');
        const remainingRow = document.getElementById('remainingRow');
        const totalLabel = document.getElementById('totalLabel');

        if (currentPaymentType === 'dp') {
            if (!dpInput.value || parseFloat(dpInput.value) <= 0 || parseFloat(dpInput.value) > grandTotal) {
                dpInput.value = Math.round(grandTotal * 0.5);
            }
            const dpVal = parseFloat(dpInput.value || 0);
            const remaining = Math.max(0, grandTotal - dpVal);

            totalLabel.textContent = 'Tagihan Uang Muka (DP):';
            document.getElementById('billGrandTotal').textContent = 'Rp ' + Number(dpVal).toLocaleString('id-ID');
            remainingRow.classList.remove('hidden');
            document.getElementById('billRemaining').textContent = 'Rp ' + Number(remaining).toLocaleString('id-ID');
        } else {
            totalLabel.textContent = 'Total Tagihan (Lunas):';
            document.getElementById('billGrandTotal').textContent = 'Rp ' + Number(grandTotal).toLocaleString('id-ID');
            remainingRow.classList.add('hidden');
        }
    }

    // Jalankan kalkulasi perdana saat dokumen dimuat
    document.addEventListener('DOMContentLoaded', function() {
        calculateBill();
    });
</script>
@endsection
