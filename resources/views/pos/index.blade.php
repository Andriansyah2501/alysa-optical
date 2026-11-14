@extends('layouts.app')

@section('title', 'Point of Sales (POS) Kasir Optik | Alysa Glasses')

@section('content')
<style>
    /* HP: input min 16px agar tidak auto-zoom di iOS */
    @media (max-width: 640px) {
        #posForm input, #posForm select, #posForm textarea { font-size: 16px !important; }
        #posForm .pos-tiny-keep { font-size: 11px !important; }
    }
</style>
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8 pb-28 wide:pb-8 lg:pb-8">
    
    <!-- Header Kasir -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 sm:pb-6 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase tracking-wider">
                    <i class="fa-solid fa-cash-register mr-1"></i> POS Kasir Online
                </span>
                <span class="text-xs text-slate-400">• {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</span>
            </div>
            <h1 class="font-serif text-xl sm:text-3xl font-bold text-slate-900 mt-1">Sistem Kasir Toko Kacamata</h1>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 line-clamp-2 sm:line-clamp-none">Penjualan kacamata etalase, custom frame & harga, ganti kaca saja, dan upah jasa servis.</p>
        </div>

        <div class="flex items-center gap-3">
            <span id="digitalClock" class="px-3.5 py-1.5 bg-sand-100 text-slate-700 rounded-xl text-xs font-mono font-bold">
                00:00:00 WIB
            </span>
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-white border border-sand-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-sand-50 transition-colors">
                    <i class="fa-solid fa-chart-line mr-1 text-brand-600"></i> Backoffice
                </a>
            @endif
        </div>
    </div>

    <!-- Pintu Cepat Kasir (Step Bar) -->
    <div class="mt-5 grid grid-cols-4 sm:grid-cols-4 gap-1.5 sm:gap-2 text-[10px] sm:text-[11px] font-bold">
        <button type="button" onclick="posGoStep('posStepPelanggan')" class="pos-step-btn flex items-center justify-center gap-1.5 px-1.5 sm:px-3 py-3 rounded-2xl bg-brand-600 text-white shadow-sm transition-all" data-step="posStepPelanggan">
            <span class="w-5 h-5 shrink-0 rounded-full bg-white/20 flex items-center justify-center text-[10px]">1</span> <span class="hidden sm:inline">Pelanggan</span><span class="sm:hidden">Data</span>
        </button>
        <button type="button" onclick="posGoStep('posStepTransaksi')" class="pos-step-btn flex items-center justify-center gap-1.5 px-1.5 sm:px-3 py-3 rounded-2xl bg-white text-slate-700 border border-sand-300 hover:bg-sand-50 transition-all" data-step="posStepTransaksi">
            <span class="w-5 h-5 shrink-0 rounded-full bg-sand-100 flex items-center justify-center text-[10px]">2</span> <span class="hidden sm:inline">Tipe & Barang</span><span class="sm:hidden">Barang</span>
        </button>
        <button type="button" onclick="posGoStep('sectionLens')" class="pos-step-btn flex items-center justify-center gap-1.5 px-1.5 sm:px-3 py-3 rounded-2xl bg-white text-slate-700 border border-sand-300 hover:bg-sand-50 transition-all" data-step="sectionLens">
            <span class="w-5 h-5 shrink-0 rounded-full bg-sand-100 flex items-center justify-center text-[10px]">3</span> <span class="hidden sm:inline">Lensa & Resep</span><span class="sm:hidden">Lensa</span>
        </button>
        <button type="button" onclick="posGoStep('posStepBilling')" class="pos-step-btn flex items-center justify-center gap-1.5 px-1.5 sm:px-3 py-3 rounded-2xl bg-white text-slate-700 border border-sand-300 hover:bg-sand-50 transition-all" data-step="posStepBilling">
            <span class="w-5 h-5 shrink-0 rounded-full bg-sand-100 flex items-center justify-center text-[10px]">4</span> <span class="hidden sm:inline">Bayar & Struk</span><span class="sm:hidden">Bayar</span>
        </button>
    </div>

    <!-- Layout Form POS -->
    <form action="{{ route('pos.store') }}" method="POST" id="posForm" class="mt-8">
        @csrf
        <input type="hidden" name="voucher_code" id="posInputVoucher" value="">

        <div class="grid grid-cols-1 wide:grid-cols-12 lg:grid-cols-12 gap-4 sm:gap-6 wide:gap-5 lg:gap-8 items-start">
            
            <!-- Left Side: Pelanggan, Pilihan Transaksi & Resep (7 Kolom) -->
            <div class="wide:col-span-7 lg:col-span-7 space-y-4 wide:space-y-5 lg:space-y-6">
                
                <!-- 1. Identitas Pelanggan Toko -->
                <div id="posStepPelanggan" class="bg-white p-4 sm:p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4 scroll-mt-36">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-sand-200">
                        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-user text-brand-600"></i> Identitas Pelanggan & Rekam Medis
                        </h3>
                        <div class="flex items-center gap-1.5">
                            <select id="selectSavedCustomer" onchange="loadCustomerData(this)" class="px-3 py-1.5 bg-brand-50 border border-brand-300 rounded-xl text-xs font-bold text-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">🔍 Pilih Pelanggan Terdaftar (Auto-Fill)...</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-phone="{{ $c->phone }}"
                                        data-address="{{ $c->address ?: '' }}"
                                        data-city="{{ $c->city ?: 'Batam' }}"
                                        data-sph-r="{{ $c->sph_r ?: '' }}"
                                        data-cyl-r="{{ $c->cyl_r ?: '' }}"
                                        data-axis-r="{{ $c->axis_r ?: '' }}"
                                        data-sph-l="{{ $c->sph_l ?: '' }}"
                                        data-cyl-l="{{ $c->cyl_l ?: '' }}"
                                        data-axis-l="{{ $c->axis_l ?: '' }}"
                                        data-pd="{{ $c->pd ?: '' }}"
                                        data-notes="{{ $c->notes ?: '' }}">
                                        {{ $c->customer_code ? '[' . $c->customer_code . '] ' : '' }}{{ $c->name }} ({{ $c->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Nama Pembeli <span class="text-rose-500">*</span></label>
                            <input type="text" name="customer_name" id="posCustomerName" required placeholder="Contoh: Budi Santoso" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">WhatsApp / No. HP (Untuk Kirim Struk) <span class="text-rose-500">*</span></label>
                            <input type="tel" name="customer_phone" id="posCustomerPhone" required placeholder="08123456789" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="font-bold text-slate-700 block mb-1">Alamat Konsumen (Kota / Pengiriman / Ambil di Toko)</label>
                        <input type="text" name="customer_address" id="posCustomerAddress" placeholder="Contoh: Jl. Sudirman No. 12, Batam Centre / Pickup Store" class="w-full px-4 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3 pt-3 border-t border-sand-200">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kurir / Metode Kirim (Opsional)</label>
                            <select name="shipping_courier" id="posShippingCourier" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white">
                                <option value="Pickup / Ambil di Toko">Pickup / Ambil di Toko (Rp 0)</option>
                                <option value="Kurir Toko (Batam Express)">Kurir Toko (Batam Express)</option>
                                <option value="JNT / JNE Express">JNT / JNE Express</option>
                                <option value="GrabExpress / GoSend">GrabExpress / GoSend Instant</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Biaya Ongkir (Rp)</label>
                            <input type="number" inputmode="numeric" name="shipping_cost" id="posShippingCost" min="0" step="1000" value="0" placeholder="0" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- 2. PILIHAN TIPE TRANSAKSI (Tab Navigasi Mode) -->
                <div id="posStepTransaksi" class="bg-white p-4 sm:p-6 rounded-3xl border border-sand-200 shadow-sm space-y-5 scroll-mt-36">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-brand-600"></i> Pilih Tipe Transaksi Kasir
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1">Pilih jenis layanan sesuai kebutuhan pembeli:</p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                        <!-- Mode 1: Etalase Katalog -->
                        <label class="cursor-pointer border-2 rounded-2xl p-3 text-center transition-all has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/70 has-[:checked]:shadow-sm border-sand-200 hover:bg-sand-50">
                            <input type="radio" name="order_mode" value="catalog" checked class="hidden mode-radio">
                            <i class="fa-solid fa-glasses text-lg text-brand-600 block mb-1"></i>
                            <span class="font-bold text-slate-800 block text-[11px]">Katalog Toko</span>
                            <span class="text-[9px] text-slate-400">Frame Etalase</span>
                        </label>

                        <!-- Mode 2: Custom Frame & Harga -->
                        <label class="cursor-pointer border-2 rounded-2xl p-3 text-center transition-all has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/70 has-[:checked]:shadow-sm border-sand-200 hover:bg-sand-50">
                            <input type="radio" name="order_mode" value="custom_frame" class="hidden mode-radio">
                            <i class="fa-solid fa-pen-ruler text-lg text-purple-600 block mb-1"></i>
                            <span class="font-bold text-slate-800 block text-[11px]">Custom Frame</span>
                            <span class="text-[9px] text-slate-400">Nama & Harga Bebas</span>
                        </label>

                        <!-- Mode 3: Hanya Lensa / Ganti Kaca -->
                        <label class="cursor-pointer border-2 rounded-2xl p-3 text-center transition-all has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/70 has-[:checked]:shadow-sm border-sand-200 hover:bg-sand-50">
                            <input type="radio" name="order_mode" value="lens_only" class="hidden mode-radio">
                            <i class="fa-solid fa-circle-notch text-lg text-emerald-600 block mb-1"></i>
                            <span class="font-bold text-slate-800 block text-[11px]">Ganti Kaca Saja</span>
                            <span class="text-[9px] text-slate-400">Frame Bawa Sendiri</span>
                        </label>

                        <!-- Mode 4: Servis / Reparasi / Jasa -->
                        <label class="cursor-pointer border-2 rounded-2xl p-3 text-center transition-all has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/70 has-[:checked]:shadow-sm border-sand-200 hover:bg-sand-50">
                            <input type="radio" name="order_mode" value="service_only" class="hidden mode-radio">
                            <i class="fa-solid fa-wrench text-lg text-amber-600 block mb-1"></i>
                            <span class="font-bold text-slate-800 block text-[11px]">Upah Jasa / Servis</span>
                            <span class="text-[9px] text-slate-400">Reparasi & Stel</span>
                        </label>
                    </div>

                    <!-- Panel Sub 1: Etalase Katalog -->
                    <div id="panelCatalog" class="space-y-3 pt-2">
                        <div>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" id="frameSearchInput" placeholder="Ketik nama / scan barcode tag frame..." class="w-full pl-9 pr-4 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium text-slate-800">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                </div>
                                <button type="button" onclick="document.getElementById('frameSearchInput').focus();" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shrink-0" title="Scan dengan Barcode Scanner USB (arahkan & tembakkan)">
                                    <i class="fa-solid fa-barcode text-sm"></i> <span class="hidden sm:inline">Scan Tag</span>
                                </button>
                                <button type="button" id="frameCamScanBtn" onclick="openFrameCamScanner()" class="hidden px-3 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold items-center gap-1.5 shrink-0" title="Scan barcode tag pakai kamera HP">
                                    <i class="fa-solid fa-camera text-sm"></i> <span class="hidden sm:inline">Kamera</span>
                                </button>
                            </div>
                            <!-- Pemindai kamera frame (menumpang mesin ZXing scanner lensa) -->
                            <div id="frameCamWrap" class="hidden mt-2 rounded-xl border-2 border-brand-300 bg-slate-950 p-2">
                                <div id="frameCamReader" class="mx-auto"></div>
                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-[10px] text-slate-300 font-bold">Arahkan kamera ke barcode tag frame</span>
                                    <button type="button" onclick="closeFrameCamScanner()" class="px-2.5 py-1 bg-white/10 hover:bg-white/20 text-white rounded-lg text-[10px] font-bold">✕ Tutup</button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 wide:grid-cols-1 xl:grid-cols-2 gap-3 max-h-[45vh] wide:max-h-[42vh] lg:max-h-64 overflow-y-auto pr-1 overscroll-contain" id="frameContainer">
                            @foreach($products as $index => $prod)
                                <label class="frame-item p-3 rounded-2xl border border-sand-200 hover:border-brand-400 flex items-center gap-3 cursor-pointer transition-all hover:bg-sand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/70 has-[:checked]:ring-1 has-[:checked]:ring-brand-500"
                                    data-search="{{ strtolower(trim(($prod->barcode ?? '') . ' ' . $prod->name . ' ' . $prod->brand . ' ' . $prod->frame_shape)) }}">
                                    <input type="radio" name="product_id" value="{{ $prod->id }}" data-name="{{ $prod->name }}" data-barcode="{{ $prod->barcode }}" data-price="{{ $prod->effective_price }}" data-bundle="{{ $prod->bundle_lens_type_id }}" data-packagelabel="{{ $prod->bundleLabel($bundleDiscountPercent) }}" {{ $index === 0 ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500 shrink-0">
                                    <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-12 h-12 rounded-xl object-cover bg-white border border-sand-200 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-xs text-slate-900 truncate">{{ $prod->name }}</h4>
                                        <span class="text-[10px] text-slate-500 block">{{ $prod->frame_shape }} • {{ $prod->color_primary }}</span>
                                        @if($prod->barcode)
                                            <span class="text-[9px] font-mono font-bold text-slate-400 block"><i class="fa-solid fa-barcode mr-0.5"></i>{{ $prod->barcode }}</span>
                                        @endif
                                        <span class="text-xs font-extrabold text-brand-700 block mt-0.5">Rp {{ number_format($prod->effective_price, 0, ',', '.') }}</span>
                                        @if($prod->bundleLabel($bundleDiscountPercent))
                                            <span class="frame-bundle-badge mt-1 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-violet-100 border border-violet-300 text-violet-800 text-[8.5px] font-extrabold">
                                                <i class="fa-solid fa-box-open"></i> PAKET: {{ $prod->bundleLabel($bundleDiscountPercent) }}
                                            </span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Panel Sub 2: Custom Frame & Model & Harga -->
                    <div id="panelCustomFrame" class="hidden space-y-4 pt-2 bg-purple-50/50 p-4 rounded-2xl border border-purple-200 text-xs">
                        <div class="flex items-center gap-2 text-purple-900 font-bold">
                            <i class="fa-solid fa-pen-to-square"></i> Input Kacamata / Frame Custom
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="font-bold text-slate-700 block mb-1">Nama Frame Kacamata <span class="text-rose-500">*</span></label>
                                <input type="text" name="custom_frame_name" id="customFrameName" placeholder="Contoh: Frame Titanium Vintage Hitam" class="w-full px-3 py-2 bg-white border border-purple-300 rounded-xl font-medium text-slate-800">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Model / Bentuk</label>
                                <input type="text" name="custom_frame_model" id="customFrameModel" placeholder="Contoh: Hexagonal / Bulat" class="w-full px-3 py-2 bg-white border border-purple-300 rounded-xl font-medium text-slate-800">
                            </div>
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Harga Frame Custom (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" inputmode="numeric" name="custom_frame_price" id="customFramePrice" placeholder="0" min="0" class="w-full px-3 py-2 bg-white border border-purple-300 rounded-xl font-extrabold text-sm text-purple-900">
                            <span class="text-[10px] text-slate-500 mt-1 block">Kasir bebas menentukan harga frame di tempat transaksi.</span>
                        </div>
                    </div>

                    <!-- Panel Sub 3: Keterangan Ganti Lensa Saja -->
                    <div id="panelLensOnly" class="hidden p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900 space-y-1">
                        <div class="font-bold flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i> Mode Ganti Lensa Kacamata (Tanpa Frame)
                        </div>
                        <p class="text-[11px] text-emerald-800">
                            Pelanggan membawa frame sendiri. Tagihan hanya menghitung biaya paket lensa yang dipilih di bawah.
                        </p>
                    </div>

                    <!-- Panel Sub 4: Keterangan Servis Jasa Saja -->
                    <div id="panelServiceOnly" class="hidden p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900 space-y-1">
                        <div class="font-bold flex items-center gap-2">
                            <i class="fa-solid fa-wrench"></i> Mode Jasa / Servis Saja
                        </div>
                        <p class="text-[11px] text-amber-800">
                            Tagihan dihitung murni dari biaya upah jasa servis atau penggantian sparepart baut / nosepad di bawah.
                        </p>
                    </div>

                </div>

                <!-- 3. PEMILIHAN LENSA & CUSTOM HARGA LENSA -->
                <div id="sectionLens" class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-wand-magic-sparkles text-brand-600"></i> Pilihan Paket Lensa Kacamata
                        </h3>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">Tersedia Harga Promo</span>
                    </div>

                    <div class="space-y-2">
                        <!-- Opsi Tanpa Lensa -->
                        <label class="p-2.5 rounded-2xl border border-sand-200 hover:border-brand-400 flex items-center justify-between cursor-pointer transition-all hover:bg-sand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/70 has-[:checked]:ring-1 has-[:checked]:ring-brand-500 text-xs">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="lens_type_id" value="none" data-name="Tanpa Lensa (Frame Saja)" data-price="0" class="text-brand-600 focus:ring-brand-500 lens-radio">
                                <div>
                                    <span class="font-bold text-slate-800 block">Tanpa Lensa / Frame Saja</span>
                                    <span class="text-[10px] text-slate-400">Hanya beli frame kacamata tanpa pasang lensa optik</span>
                                </div>
                            </div>
                            <span class="font-extrabold text-slate-500 shrink-0">Rp 0</span>
                        </label>

                        @foreach($lensTypes as $idx => $lens)
                            <label data-lens-id="{{ $lens->id }}" class="lens-card p-3 rounded-2xl border border-sand-200 hover:border-brand-400 flex items-center justify-between cursor-pointer transition-all hover:bg-sand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/70 has-[:checked]:ring-1 has-[:checked]:ring-brand-500 text-xs">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="lens_type_id" value="{{ $lens->id }}" data-name="{{ $lens->name }}" data-price="{{ $lens->additional_price }}" {{ $idx === 0 ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500 lens-radio">
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $lens->name }}
                                            <span data-lensprice="{{ $lens->additional_price }}" class="lens-bundle-hint hidden ml-1 px-1.5 py-0.5 rounded-full bg-violet-100 border border-violet-300 text-violet-800 text-[8px] font-extrabold align-middle"><i class="fa-solid fa-box-open mr-0.5"></i>PAKET -{{ $bundleDiscountPercent }}%<span data-pkgprice class="font-bold"></span></span>
                                        </span>
                                        <span class="text-[10px] text-slate-500">{{ $lens->description }}</span>
                                    </div>
                                </div>
                                <span class="font-extrabold text-brand-700 shrink-0">
                                    + Rp {{ number_format($lens->additional_price, 0, ',', '.') }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Override Harga Lensa Manual Kasir -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-slate-700">Custom / Nego Harga Lensa (Opsional):</label>
                            <span class="text-[10px] text-slate-400">Isi jika kasir memberi diskon atau harga khusus</span>
                        </div>
                        <input type="number" inputmode="numeric" name="custom_lens_price" id="customLensPrice" placeholder="Kosongkan jika memakai tarif standar di atas" min="0" class="w-full mt-1 px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-bold text-slate-800">
                    </div>

                    <!-- Toggle Resep Mata -->
                    <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200 flex items-center gap-2">
                        <input type="checkbox" id="posPrescriptionToggle" name="has_prescription" value="1" checked class="w-4 h-4 text-brand-600 rounded">
                        <label for="posPrescriptionToggle" class="text-xs font-bold text-slate-800 cursor-pointer flex-1">
                            Input Ukuran Resep Pasien (Minus / Silinder / Axis)
                        </label>
                        @if(Auth::user()->canAccessAdminArea())
                        <a href="{{ route('admin.prescriptions.create') }}" target="_blank" title="Buat Kartu Resep Arsip" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[9px] font-extrabold shrink-0"><i class="fa-solid fa-file-medical"></i> Kartu</a>
                        @endif
                    </div>

                    <!-- Input Resep Matriks -->
                    <div id="posPrescriptionBox" class="space-y-3 pt-1 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 bg-blue-50/60 rounded-2xl border border-blue-100 space-y-2">
                                <span class="font-bold text-blue-900 block text-[11px]"><i class="fa-solid fa-eye mr-1"></i> Kanan (OD / R)</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <span class="text-[9px] text-slate-500 font-bold block">SPH</span>
                                        <input type="text" name="sph_r" value="-1.25" placeholder="-1.25" class="w-full px-2 py-1 bg-white border border-blue-200 rounded-lg text-center font-bold">
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-500 font-bold block">CYL</span>
                                        <input type="text" name="cyl_r" value="0.00" placeholder="0.00" class="w-full px-2 py-1 bg-white border border-blue-200 rounded-lg text-center font-bold">
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-500 font-bold block">AXIS</span>
                                        <input type="text" name="axis_r" value="" placeholder="180" class="w-full px-2 py-1 bg-white border border-blue-200 rounded-lg text-center font-bold">
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100 space-y-2">
                                <span class="font-bold text-amber-900 block text-[11px]"><i class="fa-solid fa-eye mr-1"></i> Kiri (OS / L)</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <span class="text-[9px] text-slate-500 font-bold block">SPH</span>
                                        <input type="text" name="sph_l" value="-1.50" placeholder="-1.50" class="w-full px-2 py-1 bg-white border border-amber-200 rounded-lg text-center font-bold">
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-500 font-bold block">CYL</span>
                                        <input type="text" name="cyl_l" value="0.00" placeholder="0.00" class="w-full px-2 py-1 bg-white border border-amber-200 rounded-lg text-center font-bold">
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-500 font-bold block">AXIS</span>
                                        <input type="text" name="axis_l" value="" placeholder="175" class="w-full px-2 py-1 bg-white border border-amber-200 rounded-lg text-center font-bold">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-slate-600 block mb-1">Pupil Distance (PD mm)</label>
                                <input type="text" name="pd" value="63" class="w-full px-3 py-1.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-center">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-600 block mb-1">Catatan Khusus</label>
                                <input type="text" name="prescription_notes" placeholder="Contoh: potong presisi" class="w-full px-3 py-1.5 bg-sand-50 border border-sand-300 rounded-xl">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. UPAH JASA / BIAYA SERVIS TAMBAHAN -->
                <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="serviceFeeToggle" name="has_service_fee" value="1" class="w-4 h-4 text-brand-600 rounded">
                            <label for="serviceFeeToggle" class="font-bold text-slate-900 cursor-pointer">
                                Tambahkan Upah Jasa Servis / Reparasi / Pasang Lensa
                            </label>
                        </div>
                        <span class="text-[10px] text-slate-400">Opsional</span>
                    </div>

                    <div id="serviceFeeBox" class="hidden space-y-3 p-4 bg-sand-50 rounded-2xl border border-sand-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Nama Layanan Jasa</label>
                                <input type="text" name="service_name" id="serviceNameInput" placeholder="Contoh: Jasa Pasang Lensa / Ganti Baut" class="w-full px-3 py-2 bg-white border border-sand-300 rounded-xl font-medium">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Biaya Jasa (Rp)</label>
                                <input type="number" inputmode="numeric" name="service_fee" id="serviceFeeInput" placeholder="0" min="0" class="w-full px-3 py-2 bg-white border border-sand-300 rounded-xl font-bold text-slate-900">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Side: Billing Kasir & Voucher & Cetak Struk (5 Kolom) -->
            <div class="wide:col-span-5 lg:col-span-5 space-y-4 wide:space-y-5 lg:space-y-6 wide:sticky lg:sticky wide:top-24 lg:top-28 max-h-[calc(100dvh-7rem)] wide:overflow-y-auto overscroll-contain wide:pr-1">
                
                <div id="posStepBilling" class="bg-white p-4 sm:p-6 rounded-3xl border border-sand-200 shadow-xl space-y-6 scroll-mt-36">
                    <div class="flex items-center justify-between pb-4 border-b border-sand-200">
                        <div>
                            <span class="text-[10px] font-bold text-brand-600 uppercase tracking-widest">{{ $storeName }}</span>
                            <h3 class="font-serif text-xl font-bold text-slate-900">Billing & Kasir</h3>
                        </div>
                        <i class="fa-solid fa-receipt text-2xl text-slate-300"></i>
                    </div>

                    <input type="hidden" name="bundle_applied" id="bundleAppliedInput" value="">

                    <!-- Voucher Kasir -->
                    <div class="p-3.5 bg-sand-50 rounded-2xl border border-sand-300 space-y-2">
                        <label class="text-[11px] font-bold text-slate-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-ticket text-brand-600"></i> Voucher Diskon Transaksi
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="posVoucherInput" placeholder="Ketik kode (misal: ALYSA50K)" class="flex-1 px-3 py-2 bg-white border border-sand-300 rounded-xl text-xs font-bold uppercase">
                            <button type="button" id="btnPosApplyVoucher" class="px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold">
                                Klaim
                            </button>
                        </div>
                        <div id="posVoucherStatus" class="hidden text-[10px] p-2 rounded-lg font-bold"></div>
                    </div>

                    <!-- Rincian Billing Item Kasir -->
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-start" id="rowBillFrame">
                            <div>
                                <span class="font-bold text-slate-900 block" id="billFrameName">-</span>
                                <span class="text-[11px] text-slate-400" id="billFrameSublabel">Frame Kacamata</span>
                            </div>
                            <span class="font-bold text-slate-800" id="billFramePrice">Rp 0</span>
                        </div>

                        <div class="flex justify-between items-start" id="rowBillLens">
                            <div>
                                <span class="font-bold text-brand-700 block" id="billLensName">-</span>
                                <span class="text-[11px] text-slate-400">Paket Lensa Optik</span>
                            </div>
                            <span class="font-bold text-brand-700" id="billLensPrice">Rp 0</span>
                        </div>

                        <div class="flex justify-between items-start text-amber-800" id="rowBillService" style="display: none;">
                            <div>
                                <span class="font-bold block" id="billServiceName">Upah Jasa / Servis</span>
                                <span class="text-[11px] text-amber-600">Biaya Pengerjaan</span>
                            </div>
                            <span class="font-bold" id="billServicePrice">Rp 0</span>
                        </div>

                        <div class="flex justify-between items-start text-blue-800" id="rowBillShipping" style="display: none;">
                            <div>
                                <span class="font-bold block" id="billShippingCourier">Ongkos Kirim</span>
                                <span class="text-[11px] text-blue-600">Pengiriman Kacamata</span>
                            </div>
                            <span class="font-bold" id="billShippingCost">+ Rp 0</span>
                        </div>

                        <div class="flex justify-between text-emerald-700 font-bold" id="posRowDiscount" style="display: none;">
                            <span id="posRowDiscountLabel">Potongan Diskon Voucher:</span>
                            <span id="posBillDiscount">- Rp 0</span>
                        </div>

                        <div class="flex justify-between text-slate-400 text-[11px]">
                            <span>Hardcase & Lap Microfiber:</span>
                            <span class="text-emerald-600 font-bold">Termasuk</span>
                        </div>
                    </div>

                    <!-- Grand Total Box -->
                    <div class="p-4 bg-brand-50 rounded-2xl border border-brand-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-800 tracking-wider block">Total Tagihan</span>
                            <span class="text-2xl font-extrabold text-brand-900" id="billGrandTotal">Rp 0</span>
                        </div>
                        <span class="px-2.5 py-1 bg-brand-200 text-brand-900 rounded-lg text-xs font-bold">LUNAS</span>
                    </div>

                    <!-- Form Pembayaran Kasir & Pilihan DP -->
                    <div class="space-y-3 text-xs">
                        
                        <!-- Toggle Tipe Pembayaran: LUNAS vs DOWN PAYMENT (DP) -->
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Tipe Pembayaran Kasir</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition-all has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/70 border-sand-200 hover:bg-sand-50">
                                    <input type="radio" name="payment_type" value="full" checked class="hidden pay-type-radio">
                                    <span class="font-bold text-slate-900 block text-xs">💵 Bayar Lunas</span>
                                    <span class="text-[9px] text-slate-400">Bayar 100% penuh</span>
                                </label>
                                <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition-all has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/70 border-sand-200 hover:bg-sand-50">
                                    <input type="radio" name="payment_type" value="dp" class="hidden pay-type-radio">
                                    <span class="font-bold text-amber-700 block text-xs">🤝 Down Payment (DP)</span>
                                    <span class="text-[9px] text-slate-400">Uang muka / pelunasan nanti</span>
                                </label>
                            </div>
                        </div>

                        <!-- Kotak Input Nominal DP (Tampil jika DP dipilih) -->
                        <div id="boxDpSettings" class="hidden p-3.5 bg-amber-50/70 rounded-2xl border border-amber-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="font-bold text-amber-900 text-xs">Nominal Uang Muka (DP) Rp</label>
                                <div class="flex gap-1">
                                    <button type="button" id="btnDp50" class="px-2 py-0.5 bg-amber-200 hover:bg-amber-300 text-amber-900 rounded font-bold text-[9px]">DP 50%</button>
                                    <button type="button" id="btnDp30" class="px-2 py-0.5 bg-amber-200 hover:bg-amber-300 text-amber-900 rounded font-bold text-[9px]">DP 30%</button>
                                </div>
                            </div>
                            <input type="number" inputmode="numeric" name="dp_amount_input" id="dpAmountInput" placeholder="0" min="0" class="w-full px-3 py-3.5 sm:py-2 bg-white border border-amber-300 rounded-xl font-extrabold text-base sm:text-sm text-amber-900">
                            <div class="flex justify-between items-center text-[10px] text-amber-800 font-semibold pt-1 border-t border-amber-200">
                                <span>Sisa Tagihan Pelunasan:</span>
                                <span class="font-bold text-xs text-rose-700" id="lblRemainingBalance">Rp 0</span>
                            </div>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Metode Pembayaran Kasir</label>
                            <select name="payment_method" id="payMethodSelect" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                                <option value="Cash / Tunai">Cash / Uang Tunai</option>
                                <option value="QRIS Statis Toko (BCA/GoPay)">QRIS Statis Toko (BCA/GoPay)</option>
                                <option value="EDC Debit / Kartu Kredit BCA">Mesin EDC Kartu Debit/Kredit BCA</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-bold text-slate-700">Nominal Diterima Kasir (Rp)</label>
                                <button type="button" id="btnUangPas" class="text-[10px] font-bold text-brand-600 hover:underline">Uang Pas</button>
                            </div>
                            <input type="number" inputmode="numeric" name="amount_paid" id="amountPaidInput" required class="w-full px-4 py-3.5 sm:py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-extrabold text-base text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white" placeholder="0">
                        </div>

                        <!-- Shortcut Cepat Pecahan Uang -->
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            <button type="button" class="quick-cash px-3.5 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold text-[11px]" data-val="50000">50rb</button>
                            <button type="button" class="quick-cash px-3.5 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold text-[11px]" data-val="100000">100rb</button>
                            <button type="button" class="quick-cash px-3.5 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold text-[11px]" data-val="200000">200rb</button>
                            <button type="button" class="quick-cash px-3.5 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold text-[11px]" data-val="500000">500rb</button>
                            <button type="button" class="quick-cash px-3.5 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold text-[11px]" data-val="1000000">1jt</button>
                        </div>

                        <!-- Kembalian -->
                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-800">Kembalian:</span>
                            <span class="text-base font-extrabold text-emerald-900" id="billChange">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tombol Bayar & Struk -->
                    <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-600/25 transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i> Proses Transaksi & Cetak / WA Struk
                    </button>

                    <!-- Bar Bayar Sticky (HP / Tablet) -->
                    <div id="posStickyPay" class="fixed bottom-14 sm:bottom-0 left-0 right-0 z-40 wide:hidden lg:hidden px-3 pb-3 pt-2.5 bg-white/95 backdrop-blur-md border-t border-sand-200 shadow-[0_-8px_24px_rgba(15,23,42,0.10)] rounded-t-2xl">
                        <div class="flex items-center justify-between gap-3 max-w-3xl mx-auto">
                            <div class="min-w-0">
                                <span class="text-[9px] font-bold text-slate-400 uppercase block leading-none mb-0.5">Total Tagihan</span>
                                <span class="text-lg font-extrabold text-slate-900 block truncate" id="billGrandTotalBar">Rp 0</span>
                            </div>
                            <button type="submit" form="posForm" class="shrink-0 px-5 py-3 bg-emerald-600 active:bg-emerald-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i> Bayar & Cetak
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

<script>
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
        document.getElementById('digitalClock').textContent = time;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Mode Radios & Panels
    const modeRadios = document.querySelectorAll('.mode-radio');
    const panelCatalog = document.getElementById('panelCatalog');
    const panelCustomFrame = document.getElementById('panelCustomFrame');
    const panelLensOnly = document.getElementById('panelLensOnly');
    const panelServiceOnly = document.getElementById('panelServiceOnly');
    const sectionLens = document.getElementById('sectionLens');

    // Frame Elements
    const frameRadios = document.querySelectorAll('input[name="product_id"]');
    const customFrameName = document.getElementById('customFrameName');
    const customFrameModel = document.getElementById('customFrameModel');
    const customFramePrice = document.getElementById('customFramePrice');

    // Lens Elements
    const lensRadios = document.querySelectorAll('.lens-radio');
    const customLensPrice = document.getElementById('customLensPrice');

    // Service Elements
    const serviceFeeToggle = document.getElementById('serviceFeeToggle');
    const serviceFeeBox = document.getElementById('serviceFeeBox');
    const serviceNameInput = document.getElementById('serviceNameInput');
    const serviceFeeInput = document.getElementById('serviceFeeInput');

    // Billing Summary Elements
    const billFrameName = document.getElementById('billFrameName');
    const billFrameSublabel = document.getElementById('billFrameSublabel');
    const billFramePrice = document.getElementById('billFramePrice');
    const billLensName = document.getElementById('billLensName');
    const billLensPrice = document.getElementById('billLensPrice');
    const rowBillService = document.getElementById('rowBillService');
    const billServiceName = document.getElementById('billServiceName');
    const billServicePrice = document.getElementById('billServicePrice');
    const posShippingCost = document.getElementById('posShippingCost');
    const posShippingCourier = document.getElementById('posShippingCourier');
    const rowBillShipping = document.getElementById('rowBillShipping');
    const billShippingCourier = document.getElementById('billShippingCourier');
    const billShippingCost = document.getElementById('billShippingCost');
    const billGrandTotal = document.getElementById('billGrandTotal');
    const amountPaidInput = document.getElementById('amountPaidInput');
    const billChange = document.getElementById('billChange');
    const btnUangPas = document.getElementById('btnUangPas');

    // Voucher Elements
    const posRowDiscount = document.getElementById('posRowDiscount');
    const posBillDiscount = document.getElementById('posBillDiscount');
    const posVoucherInput = document.getElementById('posVoucherInput');
    const btnPosApplyVoucher = document.getElementById('btnPosApplyVoucher');
    const posVoucherStatus = document.getElementById('posVoucherStatus');
    const posInputVoucher = document.getElementById('posInputVoucher');

    let currentMode = 'catalog';
    let currentFramePrice = 0;
    let currentLensPrice = 0;
    let currentServiceFee = 0;
    let currentDiscount = 0;
    const BUNDLE_PERCENT = {{ (int) $bundleDiscountPercent }};
    let bundleActive = false;
    let currentBundleDiscount = 0;
    const bundleAppliedInput = document.getElementById('bundleAppliedInput');
    let currentGrandTotal = 0;
    let activeVoucherCode = '';
    let isReevaluatingVoucher = false;

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function switchMode(mode) {
        currentMode = mode;
        panelCatalog.classList.add('hidden');
        panelCustomFrame.classList.add('hidden');
        panelLensOnly.classList.add('hidden');
        panelServiceOnly.classList.add('hidden');
        sectionLens.classList.remove('hidden');

        if (mode === 'catalog') {
            panelCatalog.classList.remove('hidden');
        } else if (mode === 'custom_frame') {
            panelCustomFrame.classList.remove('hidden');
        } else if (mode === 'lens_only') {
            panelLensOnly.classList.remove('hidden');
        } else if (mode === 'service_only') {
            panelServiceOnly.classList.remove('hidden');
            sectionLens.classList.add('hidden');
            // Pastikan checkbox service aktif
            serviceFeeToggle.checked = true;
            serviceFeeBox.classList.remove('hidden');
        }

        updateBundleLensHint();
        calculateBilling();
    }

    modeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) switchMode(this.value);
        });
    });

    serviceFeeToggle.addEventListener('change', function() {
        serviceFeeBox.classList.toggle('hidden', !this.checked);
        calculateBilling();
    });

    function calculateBilling() {
        // 1. Hitung Frame
        if (currentMode === 'catalog') {
            const checkedFrame = document.querySelector('input[name="product_id"]:checked');
            if (checkedFrame) {
                billFrameName.textContent = checkedFrame.getAttribute('data-name');
                currentFramePrice = parseFloat(checkedFrame.getAttribute('data-price')) || 0;
                billFrameSublabel.textContent = 'Frame Katalog Etalase';
                billFramePrice.textContent = formatRupiah(currentFramePrice);
            }
        } else if (currentMode === 'custom_frame') {
            const cName = customFrameName.value.trim() || 'Custom Frame Pelanggan';
            const cModel = customFrameModel.value.trim() ? ' [' + customFrameModel.value.trim() + ']' : '';
            billFrameName.textContent = cName + cModel;
            currentFramePrice = parseFloat(customFramePrice.value) || 0;
            billFrameSublabel.textContent = 'Custom Frame & Model Kasir';
            billFramePrice.textContent = formatRupiah(currentFramePrice);
        } else if (currentMode === 'lens_only') {
            billFrameName.textContent = 'Frame Bawa Sendiri (Ganti Lensa)';
            currentFramePrice = 0;
            billFrameSublabel.textContent = 'Bawa Frame Sendiri';
            billFramePrice.textContent = 'Rp 0';
        } else if (currentMode === 'service_only') {
            billFrameName.textContent = 'Tanpa Pembelian Frame';
            currentFramePrice = 0;
            billFrameSublabel.textContent = 'Layanan Servis';
            billFramePrice.textContent = 'Rp 0';
        }

        // 2. Hitung Lensa
        if (currentMode === 'service_only') {
            currentLensPrice = 0;
            billLensName.textContent = 'Tanpa Ganti Lensa';
            billLensPrice.textContent = 'Rp 0';
        } else {
            const checkedLens = document.querySelector('input[name="lens_type_id"]:checked');
            if (checkedLens) {
                let lensName = checkedLens.getAttribute('data-name');
                let standardPrice = parseFloat(checkedLens.getAttribute('data-price')) || 0;

                // Cek apakah ada override harga lensa manual
                if (customLensPrice.value && parseFloat(customLensPrice.value) >= 0) {
                    currentLensPrice = parseFloat(customLensPrice.value);
                    billLensName.textContent = lensName + ' (Custom Harga)';
                } else {
                    currentLensPrice = standardPrice;
                    billLensName.textContent = lensName;
                }

                billLensPrice.textContent = currentLensPrice > 0 ? '+ ' + formatRupiah(currentLensPrice) : 'Rp 0';
            }
        }

        // 2.5. PAKET BUNDLING: frame katalog punya lensa paket & lensa itu dipilih (tanpa override harga)
        const checkedFrameB = document.querySelector('input[name="product_id"]:checked');
        const bundleLensId = (currentMode === 'catalog' && checkedFrameB) ? (checkedFrameB.getAttribute('data-bundle') || '') : '';
        const checkedLensB = document.querySelector('input[name="lens_type_id"]:checked');
        bundleActive = !!(BUNDLE_PERCENT > 0 && bundleLensId && checkedLensB && checkedLensB.value === bundleLensId && currentLensPrice > 0
            && !(customLensPrice.value && parseFloat(customLensPrice.value) > 0));
        currentBundleDiscount = bundleActive ? Math.round((currentFramePrice + currentLensPrice) * BUNDLE_PERCENT / 100) : 0;
        bundleAppliedInput.value = bundleActive ? '1' : '';

        // 3. Hitung Biaya Jasa / Servis
        if (serviceFeeToggle.checked || currentMode === 'service_only') {
            currentServiceFee = parseFloat(serviceFeeInput.value) || 0;
            const sName = serviceNameInput.value.trim() || 'Upah Jasa Servis / Pasang';
            billServiceName.textContent = sName;
            billServicePrice.textContent = '+ ' + formatRupiah(currentServiceFee);
            rowBillService.style.display = 'flex';
        } else {
            currentServiceFee = 0;
            rowBillService.style.display = 'none';
        }

        // 3.5. Hitung Ongkos Kirim
        const shippingCostVal = parseFloat(posShippingCost.value) || 0;
        if (shippingCostVal > 0) {
            billShippingCourier.textContent = posShippingCourier.value || 'Ongkos Kirim';
            billShippingCost.textContent = '+ ' + formatRupiah(shippingCostVal);
            rowBillShipping.style.display = 'flex';
        } else {
            rowBillShipping.style.display = 'none';
        }

        // 4. Grand Total & Re-evaluasi Voucher jika ada
        const subtotal = currentFramePrice + currentLensPrice + currentServiceFee;
        // Baris diskon: voucher saja, paket saja, atau gabungan (paket tampil lebih dulu di label)
        const totalShownDiscount = currentDiscount + currentBundleDiscount;
        const lblDisc = document.getElementById('posRowDiscountLabel');
        if (totalShownDiscount > 0) {
            posRowDiscount.style.display = 'flex';
            if (lblDisc) {
                lblDisc.innerHTML = currentBundleDiscount > 0 && currentDiscount > 0
                    ? 'Diskon Paket -' + BUNDLE_PERCENT + '% + Voucher:'
                    : (currentBundleDiscount > 0
                        ? '<i class="fa-solid fa-box-open text-violet-500"></i> Potongan Paket Bundling -' + BUNDLE_PERCENT + '%:'
                        : 'Potongan Diskon Voucher:');
            }
            posBillDiscount.textContent = '- ' + formatRupiah(totalShownDiscount);
        } else if (!activeVoucherCode) {
            posRowDiscount.style.display = 'none';
        }
        
        if (activeVoucherCode && isReevaluatingVoucher === false) {
            recheckActiveVoucher(subtotal);
            return;
        }

        currentGrandTotal = Math.max(0, subtotal - currentDiscount - currentBundleDiscount + (parseFloat(posShippingCost.value) || 0));
        billGrandTotal.textContent = formatRupiah(currentGrandTotal);
        const _bar = document.getElementById('billGrandTotalBar'); if (_bar) _bar.textContent = formatRupiah(currentGrandTotal);

        updateDpCalculation();
    }

    const payTypeRadios = document.querySelectorAll('.pay-type-radio');
    const boxDpSettings = document.getElementById('boxDpSettings');
    const dpAmountInput = document.getElementById('dpAmountInput');
    const lblRemainingBalance = document.getElementById('lblRemainingBalance');
    const btnDp50 = document.getElementById('btnDp50');
    const btnDp30 = document.getElementById('btnDp30');
    let currentPaymentType = 'full';

    payTypeRadios.forEach(r => {
        r.addEventListener('change', function() {
            currentPaymentType = this.value;
            if (currentPaymentType === 'dp') {
                boxDpSettings.classList.remove('hidden');
                if (!dpAmountInput.value || parseFloat(dpAmountInput.value) === 0) {
                    dpAmountInput.value = Math.round(currentGrandTotal * 0.5); // Default DP 50%
                }
            } else {
                boxDpSettings.classList.add('hidden');
            }
            updateDpCalculation();
        });
    });

    btnDp50.addEventListener('click', () => {
        dpAmountInput.value = Math.round(currentGrandTotal * 0.5);
        updateDpCalculation();
    });

    btnDp30.addEventListener('click', () => {
        dpAmountInput.value = Math.round(currentGrandTotal * 0.3);
        updateDpCalculation();
    });

    dpAmountInput.addEventListener('input', updateDpCalculation);

    function updateDpCalculation() {
        if (currentPaymentType === 'dp') {
            let dpVal = parseFloat(dpAmountInput.value) || 0;
            if (dpVal > currentGrandTotal) {
                dpVal = currentGrandTotal;
                dpAmountInput.value = dpVal;
            }
            const remaining = Math.max(0, currentGrandTotal - dpVal);
            lblRemainingBalance.textContent = formatRupiah(remaining);
        }

        updateChange();
    }

    function getCurrentTargetBill() {
        if (currentPaymentType === 'dp') {
            const dp = parseFloat(dpAmountInput.value) || 0;
            return Math.min(dp, currentGrandTotal);
        }
        return currentGrandTotal;
    }

    function updateChange() {
        const target = getCurrentTargetBill();
        const paid = parseFloat(amountPaidInput.value) || 0;
        const change = Math.max(0, paid - target);
        billChange.textContent = formatRupiah(change);
    }

    function handleUangPas() {
        const target = getCurrentTargetBill();
        amountPaidInput.value = target;
        updateChange();
    }

    btnUangPas.addEventListener('click', handleUangPas);

    document.querySelectorAll('.quick-cash').forEach(btn => {
        btn.addEventListener('click', function() {
            amountPaidInput.value = this.getAttribute('data-val');
            updateChange();
        });
    });

    // Event listeners input billing
    function updateBundleLensHint() {
        const frame = document.querySelector('input[name="product_id"]:checked');
        const bundleId = frame ? (frame.getAttribute('data-bundle') || '') : '';
        const framePrice = frame ? (parseFloat(frame.getAttribute('data-price')) || 0) : 0;
        document.querySelectorAll('.lens-card').forEach(card => {
            const isMatch = bundleId && card.getAttribute('data-lens-id') === bundleId;
            card.classList.toggle('ring-2', isMatch);
            card.classList.toggle('ring-violet-400', isMatch);
            const hint = card.querySelector('.lens-bundle-hint');
            if (hint) {
                hint.classList.toggle('hidden', !isMatch);
                if (isMatch) {
                    const lensP = parseFloat(hint.getAttribute('data-lensprice')) || 0;
                    const pkg = Math.round((framePrice + lensP) * (1 - BUNDLE_PERCENT / 100));
                    hint.querySelector('span[data-pkgprice]').textContent =
                        ' → harga paket Rp ' + pkg.toLocaleString('id-ID');
                }
            }
        });
    }

    // Auto-pilih lensa paket saat frame ber-paket dipilih (kasir tidak perlu klik lagi)
    function autoSelectBundleLens() {
        const frame = document.querySelector('input[name="product_id"]:checked');
        const bundleId = frame ? (frame.getAttribute('data-bundle') || '') : '';
        if (bundleId) {
            const lensRadio = document.querySelector(`input[name="lens_type_id"][value="${bundleId}"]`);
            if (lensRadio && !lensRadio.checked && BUNDLE_PERCENT > 0 && currentMode === 'catalog') {
                const prevCustom = document.getElementById('customLensPrice');
                if (prevCustom) prevCustom.value = '';
                lensRadio.checked = true;
                lensRadio.dispatchEvent(new Event('change'));
                showToast('Lensa paket dipilih otomatis: diskon ' + BUNDLE_PERCENT + '%', 'success');
            }
        }
    }
    frameRadios.forEach(r => r.addEventListener('change', () => { autoSelectBundleLens(); updateBundleLensHint(); calculateBilling(); }));
    customFrameName.addEventListener('input', calculateBilling);
    customFrameModel.addEventListener('input', calculateBilling);
    customFramePrice.addEventListener('input', calculateBilling);

    lensRadios.forEach(r => r.addEventListener('change', calculateBilling));
    customLensPrice.addEventListener('input', calculateBilling);

    serviceNameInput.addEventListener('input', calculateBilling);
    serviceFeeInput.addEventListener('input', calculateBilling);
    posShippingCost.addEventListener('input', calculateBilling);
    posShippingCost.addEventListener('keyup', calculateBilling);
    posShippingCourier.addEventListener('change', calculateBilling);

    amountPaidInput.addEventListener('input', updateChange);
    amountPaidInput.addEventListener('keyup', updateChange);

    async function recheckActiveVoucher(subtotal) {
        if (!activeVoucherCode) return;
        isReevaluatingVoucher = true;
        try {
            const res = await fetch('/api/vouchers/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: activeVoucherCode, total_amount: subtotal })
            });
            const json = await res.json();
            if (json.success) {
                currentDiscount = parseFloat(json.data.discount);
                posRowDiscount.style.display = 'flex';
                posBillDiscount.textContent = '- ' + formatRupiah(currentDiscount);
            } else {
                currentDiscount = 0;
                activeVoucherCode = '';
                posInputVoucher.value = '';
                posRowDiscount.style.display = 'none';
                posVoucherStatus.className = 'text-[10px] p-2 rounded-lg font-bold bg-amber-100 text-amber-900 block';
                posVoucherStatus.textContent = 'Voucher dibatalkan: nominal belanja belum memenuhi syarat.';
            }
        } catch (e) {
            console.error('Error rechecking voucher', e);
        }
        isReevaluatingVoucher = false;

        currentGrandTotal = Math.max(0, subtotal - currentDiscount - currentBundleDiscount);
        billGrandTotal.textContent = formatRupiah(currentGrandTotal);
        const _bar = document.getElementById('billGrandTotalBar'); if (_bar) _bar.textContent = formatRupiah(currentGrandTotal);
        updateDpCalculation();
    }

    // Voucher logic
    async function applyVoucher(code) {
        if (!code) return;
        const subtotal = currentFramePrice + currentLensPrice + currentServiceFee;

        try {
            const res = await fetch('/api/vouchers/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code, total_amount: subtotal })
            });

            const json = await res.json();
            if (json.success) {
                currentDiscount = parseFloat(json.data.discount);
                activeVoucherCode = json.data.code;
                posInputVoucher.value = json.data.code;

                posVoucherStatus.className = 'text-[10px] p-2 rounded-lg font-bold bg-emerald-100 text-emerald-800 block';
                posVoucherStatus.textContent = json.data.message;

                posRowDiscount.style.display = 'flex';
                posBillDiscount.textContent = '- ' + formatRupiah(currentDiscount);
            } else {
                currentDiscount = 0;
                activeVoucherCode = '';
                posInputVoucher.value = '';
                posRowDiscount.style.display = 'none';

                posVoucherStatus.className = 'text-[10px] p-2 rounded-lg font-bold bg-rose-100 text-rose-800 block';
                posVoucherStatus.textContent = json.message;
            }
        } catch (e) {
            currentDiscount = 0;
            activeVoucherCode = '';
            posInputVoucher.value = '';
            posRowDiscount.style.display = 'none';

            posVoucherStatus.className = 'text-[10px] p-2 rounded-lg font-bold bg-rose-100 text-rose-800 block';
            posVoucherStatus.textContent = 'Gagal validasi kupon.';
        }

        const subtotalCalc = currentFramePrice + currentLensPrice + currentServiceFee;
        currentGrandTotal = Math.max(0, subtotalCalc - currentDiscount);
        billGrandTotal.textContent = formatRupiah(currentGrandTotal);
        const _bar = document.getElementById('billGrandTotalBar'); if (_bar) _bar.textContent = formatRupiah(currentGrandTotal);
        updateChange();
    }

    btnPosApplyVoucher.addEventListener('click', () => {
        const code = posVoucherInput.value.trim();
        applyVoucher(code);
    });

    const posToggle = document.getElementById('posPrescriptionToggle');
    const posBox = document.getElementById('posPrescriptionBox');
    posToggle.addEventListener('change', function() {
        posBox.style.display = this.checked ? 'block' : 'none';
    });

    const frameSearchInput = document.getElementById('frameSearchInput');
    frameSearchInput.addEventListener('input', function() {
        const filter = this.value.toLowerCase().trim();
        document.querySelectorAll('.frame-item').forEach(item => {
            const text = (item.dataset.search || item.textContent).toLowerCase();
            item.style.display = text.includes(filter) ? 'flex' : 'none';
        });
    });


    // FUNGSI AUTO-FILL DATA PELANGGAN & RESEP OPTIK DI KASIR
    window.loadCustomerData = function(selectElem) {
        if (!selectElem) return;
        const opt = selectElem.options[selectElem.selectedIndex];
        if (!opt || !opt.value) return;

        // 1. Data Diri & Kontak
        const inputName = document.getElementById('posCustomerName');
        const inputPhone = document.getElementById('posCustomerPhone');
        const inputAddress = document.getElementById('posCustomerAddress');

        if (inputName) inputName.value = opt.getAttribute('data-name') || '';
        if (inputPhone) inputPhone.value = opt.getAttribute('data-phone') || '';
        if (inputAddress) inputAddress.value = opt.getAttribute('data-address') || '';

        // 2. Data Resep Kacamata (RX)
        const sphR = opt.getAttribute('data-sph-r');
        const cylR = opt.getAttribute('data-cyl-r');
        const axisR = opt.getAttribute('data-axis-r');
        const sphL = opt.getAttribute('data-sph-l');
        const cylL = opt.getAttribute('data-cyl-l');
        const axisL = opt.getAttribute('data-axis-l');
        const pd = opt.getAttribute('data-pd');
        const notes = opt.getAttribute('data-notes');

        if (sphR || sphL || pd) {
            const rxToggle = document.getElementById('posPrescriptionToggle');
            const rxBox = document.getElementById('posPrescriptionBox');
            if (rxToggle) {
                rxToggle.checked = true;
                if (rxBox) rxBox.style.display = 'block';
            }

            const inputSphR = document.querySelector('input[name="sph_r"]');
            if (inputSphR && sphR) inputSphR.value = sphR;
            const inputCylR = document.querySelector('input[name="cyl_r"]');
            if (inputCylR && cylR) inputCylR.value = cylR;
            const inputAxisR = document.querySelector('input[name="axis_r"]');
            if (inputAxisR && axisR) inputAxisR.value = axisR;

            const inputSphL = document.querySelector('input[name="sph_l"]');
            if (inputSphL && sphL) inputSphL.value = sphL;
            const inputCylL = document.querySelector('input[name="cyl_l"]');
            if (inputCylL && cylL) inputCylL.value = cylL;
            const inputAxisL = document.querySelector('input[name="axis_l"]');
            if (inputAxisL && axisL) inputAxisL.value = axisL;

            const inputPd = document.querySelector('input[name="pd"]');
            if (inputPd && pd) inputPd.value = pd;

            const inputNotes = document.querySelector('textarea[name="prescription_notes"]') || document.querySelector('input[name="prescription_notes"]');
            if (inputNotes && notes) inputNotes.value = notes;
        }

        // Efek visual sukses auto-fill
        if (inputName) {
            inputName.classList.add('ring-2', 'ring-emerald-500');
            setTimeout(() => inputName.classList.remove('ring-2', 'ring-emerald-500'), 1000);
        }
    };


    // Inisialisasi
    autoSelectBundleLens();
    updateBundleLensHint();
    calculateBilling();
    amountPaidInput.value = currentGrandTotal;
    updateChange();
</script>

    <script>
        // ===== Scan Barcode Frame: scanner USB (Enter) + kamera HP (html5-qrcode) =====
        (function() {
            const barcodeInput = document.getElementById('frameSearchInput');
            if (!barcodeInput) return;

            if (!window.showToast) {
                window.showToast = function(msg, type) {
                    let box = document.getElementById('posScanToast');
                    if (!box) {
                        box = document.createElement('div');
                        box.id = 'posScanToast';
                        box.style.cssText = 'position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:9999;padding:10px 18px;border-radius:14px;font:700 12px "Plus Jakarta Sans",sans-serif;color:#fff;box-shadow:0 8px 24px rgba(0,0,0,.25);transition:opacity .3s;opacity:0;';
                        document.body.appendChild(box);
                    }
                    box.textContent = msg;
                    box.style.background = type === 'error' ? '#dc2626' : '#059669';
                    box.style.opacity = '1';
                    clearTimeout(box._t);
                    box._t = setTimeout(() => { box.style.opacity = '0'; }, 2200);
                };
            }

            function selectFrameById(id, src) {
                const radio = document.querySelector(`.frame-item input[type="radio"][value="${id}"]`);
                if (!radio) { showToast('Frame tidak ditemukan di etalase (stok habis?)', 'error'); return false; }
                document.querySelectorAll('.frame-item').forEach(el => el.style.display = '');
                barcodeInput.value = '';
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
                const card = radio.closest('.frame-item');
                card.classList.add('ring-2', 'ring-emerald-500');
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                setTimeout(() => card.classList.remove('ring-2', 'ring-emerald-500'), 900);
                showToast('✓ ' + (radio.dataset.name || 'Frame') + ' dipilih via ' + src, 'success');
                return true;
            }

            // Lookup ke server pakai kode barcode persis (AG-#####)
            function lookupAndSelect(code, src) {
                fetch('{{ route("pos.frame.lookup", ["code" => "__CODE__"]) }}'.replace('__CODE__', encodeURIComponent(code)), {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) { selectFrameById(data.id, src); }
                    else { firstVisibleOrToast(code); }
                })
                .catch(() => firstVisibleOrToast(code));
            }

            // Fallback: pakai hasil filter teks lokal
            function firstVisibleOrToast(code) {
                const visible = document.querySelectorAll('.frame-item:not([style*="display: none"]) input[type="radio"]');
                if (visible.length > 0) {
                    visible[0].checked = true;
                    visible[0].dispatchEvent(new Event('change'));
                    const card = visible[0].closest('.frame-item');
                    card.classList.add('ring-2', 'ring-emerald-500');
                    setTimeout(() => card.classList.remove('ring-2', 'ring-emerald-500'), 600);
                    barcodeInput.value = '';
                    document.querySelectorAll('.frame-item').forEach(el => el.style.display = '');
                } else {
                    showToast('Barcode "' + code + '" tidak terdaftar', 'error');
                }
            }

            // 1) Scanner barcode USB / keyboard wedge — Enter menyelesaikan pemindaian
            barcodeInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = this.value.trim();
                    if (!code) return;
                    if (/^AG-\d+$/i.test(code) || /^\d+$/.test(code)) {
                        lookupAndSelect(code, 'scanner');
                    } else {
                        firstVisibleOrToast(code);
                    }
                }
            });

            // 2) Pemindai kamera HP (html5-qrcode via CDN, format Code128/EAN/QR)
            let frameCamReader = null;
            window.openFrameCamScanner = function() {
                const wrap = document.getElementById('frameCamWrap');
                if (wrap.classList.contains('hidden') === false) { closeFrameCamScanner(); return; }
                if (typeof Html5Qrcode === 'undefined') {
                    showToast('Pustaka scanner belum termuat (butuh internet)', 'error');
                    return;
                }
                wrap.classList.remove('hidden');
                frameCamReader = new Html5Qrcode('frameCamReader');
                frameCamReader.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 240, height: 120 } },
                    (decoded) => {
                        closeFrameCamScanner();
                        lookupAndSelect(decoded.trim(), 'kamera');
                    },
                    () => {}
                ).catch(() => {
                    wrap.classList.add('hidden');
                    showToast('Kamera ditolak / tidak tersedia. Gunakan tombol Scan Tag + scanner USB.', 'error');
                });
            };
            window.closeFrameCamScanner = function() {
                const wrap = document.getElementById('frameCamWrap');
                wrap.classList.add('hidden');
                if (frameCamReader) {
                    frameCamReader.stop().then(() => frameCamReader.clear()).catch(() => {});
                    frameCamReader = null;
                }
            };
            // Tampilkan tombol Kamera hanya bila perangkat punya kamera + HTTPS
            const camBtn = document.getElementById('frameCamScanBtn');
            function revealCamBtn() {
                if (camBtn && navigator.mediaDevices && window.isSecureContext && typeof Html5Qrcode !== 'undefined') {
                    camBtn.classList.remove('hidden');
                    camBtn.classList.add('flex');
                }
            }
            window.addEventListener('load', revealCamBtn);
            revealCamBtn();
        })();
    </script>

    <script>
    // ===== Pintu Cepat Kasir: navigasi 4 langkah =====
    function posGoStep(targetId) {
        if (targetId === 'sectionLens') {
            const lensSection = document.getElementById('sectionLens');
            if (lensSection && lensSection.classList.contains('hidden')) {
                targetId = 'posStepTransaksi';
            }
        }
        const el = document.getElementById(targetId);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // tandai tombol langkah aktif
        document.querySelectorAll('.pos-step-btn').forEach(btn => {
            const active = btn.dataset.step === targetId;
            btn.classList.toggle('bg-brand-600', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border-transparent', active);
            btn.classList.toggle('bg-white', !active);
            btn.classList.toggle('text-slate-700', !active);
            btn.classList.toggle('border-sand-300', !active);
        });
    }
    </script>

@endsection
