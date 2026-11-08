@extends('layouts.app')

@section('title', 'Lacak Pesanan Kacamata | Alysa Glasses')

@section('content')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <div class="text-center max-w-lg mx-auto mb-10 space-y-2">
            <span class="text-xs font-bold text-brand-600 uppercase tracking-widest">Tracking Otomatis Real-time</span>
            <h1 class="font-serif text-3xl font-bold text-slate-900">Lacak Status Pesanan Kacamata</h1>
            <p class="text-xs text-slate-500">Masukkan kode pesanan yang Anda terima saat checkout atau dari kasir (contoh: ALYSA-20261105-HV92A atau POS-20261105-OQUMN).</p>
        </div>

        <!-- Form Input Resi / Order Code -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm max-w-xl mx-auto">
            <form action="{{ route('shop.track') }}" method="GET" class="flex gap-2">
                <input type="text" name="code" value="{{ request('code') }}" required placeholder="Contoh: ALYSA-20261105-8891" class="flex-1 px-4 py-3 bg-sand-50 border border-sand-300 rounded-2xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white uppercase tracking-wider">
                <button type="submit" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl text-xs font-bold shadow-md shadow-brand-500/20 transition-all shrink-0">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Lacak
                </button>
            </form>
        </div>

        @if(request('code') && !$order)
            <div class="mt-8 p-6 bg-rose-50 border border-rose-200 rounded-3xl text-center max-w-xl mx-auto">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-2xl mb-2"></i>
                <h4 class="font-bold text-sm text-rose-900">Pesanan Tidak Ditemukan</h4>
                <p class="text-xs text-rose-700 mt-1">Pastikan kode pesanan "{{ request('code') }}" sudah benar atau hubungi admin WhatsApp kami untuk bantuan.</p>
            </div>
        @endif

        @if($order)
            @php
                // Logika Penentuan Progress Tahapan (1 s/d 5)
                $st = strtolower($order->order_status);
                $step = 1;

                if (str_contains($st, 'selesai') || str_contains($st, 'diterima pelanggan') || str_contains($st, 'dibayar di kasir')) {
                    $step = 5;
                } elseif (str_contains($st, 'sedang dikirim') || str_contains($st, 'kurir') || str_contains($st, 'perjalanan')) {
                    $step = 4;
                } elseif (str_contains($st, 'siap dikirim') || str_contains($st, 'diserahkan') || str_contains($st, 'packing') || str_contains($st, 'quality control') || str_contains($st, 'qc')) {
                    $step = 3;
                } elseif (str_contains($st, 'potong lensa') || str_contains($st, 'lab optik') || str_contains($st, 'assembling') || str_contains($st, 'perakitan')) {
                    $step = 2;
                } else {
                    $step = 1;
                }

                $badgeBg = 'bg-amber-100 text-amber-800 border-amber-300';
                if ($step >= 5) {
                    $badgeBg = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                } elseif ($step >= 3) {
                    $badgeBg = 'bg-sky-100 text-sky-800 border-sky-300';
                }
            @endphp

            <div class="mt-10 bg-white rounded-3xl border border-sand-200 shadow-sm p-6 sm:p-8 space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-sand-200">
                    <div>
                        <span class="text-xs font-mono font-bold text-brand-600 bg-brand-50 px-3 py-1 rounded-lg">KODE ORDER: {{ $order->order_code }}</span>
                        <h3 class="font-bold text-lg text-slate-900 mt-2">Pelanggan: {{ $order->customer_name }}</h3>
                        <p class="text-xs text-slate-500">
                            {{ $order->city }} • Dipesan: {{ $order->created_at->format('d M Y, H:i') }} WIB
                            @if($order->validated_at)
                                • <span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle-check"></i> Divalidasi Admin {{ \Carbon\Carbon::parse($order->validated_at)->format('d/m H:i') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="sm:text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Status Terkini</span>
                        <span class="inline-block mt-1 px-4 py-1.5 rounded-full text-xs font-extrabold border {{ $badgeBg }}">
                            <i class="fa-solid fa-circle-dot mr-1"></i> {{ strtoupper($order->order_status) }}
                        </span>
                        <span class="text-[11px] text-slate-400 block mt-1">Pembayaran: <strong>{{ $order->payment_status }}</strong> ({{ $order->payment_method }})</span>
                    </div>
                </div>

                <!-- Timeline Progress Visual Real-time (Sesuai Validasi Admin) -->
                <div class="py-4">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center text-xs">
                        
                        <!-- Step 1: Diterima -->
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mx-auto shadow-sm {{ $step >= 1 ? 'bg-emerald-500 text-white' : 'bg-sand-100 text-slate-400' }}">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <span class="font-bold {{ $step >= 1 ? 'text-slate-900' : 'text-slate-400' }} text-[11px] block">1. Order Masuk</span>
                            <span class="text-[9px] {{ $step >= 1 ? 'text-emerald-600 font-bold' : 'text-slate-400' }}">
                                {{ $step > 1 ? 'Selesai' : ($step == 1 ? 'Sedang Diverifikasi' : 'Menunggu') }}
                            </span>
                        </div>

                        <!-- Step 2: Lab Optik -->
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mx-auto shadow-sm {{ $step >= 2 ? ($step == 2 ? 'bg-brand-600 text-white ring-4 ring-brand-100 animate-pulse' : 'bg-emerald-500 text-white') : 'bg-sand-100 text-slate-400' }}">
                                <i class="fa-solid fa-microscope"></i>
                            </div>
                            <span class="font-bold {{ $step >= 2 ? 'text-slate-900' : 'text-slate-400' }} text-[11px] block">2. Lab & Lensa</span>
                            <span class="text-[9px] {{ $step >= 2 ? ($step == 2 ? 'text-brand-600 font-bold' : 'text-emerald-600 font-bold') : 'text-slate-400' }}">
                                {{ $step > 2 ? 'Selesai Diasah' : ($step == 2 ? 'Sedang Dikerjakan' : 'Menunggu') }}
                            </span>
                        </div>

                        <!-- Step 3: QC & Packing -->
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mx-auto shadow-sm {{ $step >= 3 ? ($step == 3 ? 'bg-sky-600 text-white ring-4 ring-sky-100 animate-pulse' : 'bg-emerald-500 text-white') : 'bg-sand-100 text-slate-400' }}">
                                <i class="fa-solid fa-box-archive"></i>
                            </div>
                            <span class="font-bold {{ $step >= 3 ? 'text-slate-900' : 'text-slate-400' }} text-[11px] block">3. QC & Packing</span>
                            <span class="text-[9px] {{ $step >= 3 ? ($step == 3 ? 'text-sky-600 font-bold' : 'text-emerald-600 font-bold') : 'text-slate-400' }}">
                                {{ $step > 3 ? 'QC Lolos' : ($step == 3 ? 'Pemeriksaan Frame' : 'Menunggu') }}
                            </span>
                        </div>

                        <!-- Step 4: Kurir / Pengiriman -->
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mx-auto shadow-sm {{ $step >= 4 ? ($step == 4 ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 animate-pulse' : 'bg-emerald-500 text-white') : 'bg-sand-100 text-slate-400' }}">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <span class="font-bold {{ $step >= 4 ? 'text-slate-900' : 'text-slate-400' }} text-[11px] block">4. Pengiriman</span>
                            <span class="text-[9px] {{ $step >= 4 ? ($step == 4 ? 'text-indigo-600 font-bold' : 'text-emerald-600 font-bold') : 'text-slate-400' }}">
                                {{ $step > 4 ? 'Terkirim' : ($step == 4 ? 'Dalam Perjalanan' : 'Menunggu') }}
                            </span>
                        </div>

                        <!-- Step 5: Selesai -->
                        <div class="space-y-2">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center mx-auto shadow-sm {{ $step >= 5 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-sand-100 text-slate-400' }}">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <span class="font-bold {{ $step >= 5 ? 'text-emerald-900' : 'text-slate-400' }} text-[11px] block">5. Selesai</span>
                            <span class="text-[9px] {{ $step >= 5 ? 'text-emerald-600 font-bold' : 'text-slate-400' }}">
                                {{ $step >= 5 ? 'Sudah Diterima' : 'Menunggu' }}
                            </span>
                        </div>

                    </div>
                </div>

                <!-- Informasi Nomor Resi Pengiriman Jika Ada -->
                @if(!empty($order->tracking_number))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-emerald-800 uppercase block tracking-wider">
                                <i class="fa-solid fa-truck mr-1"></i> Nomor Resi Kurir Pengiriman:
                            </span>
                            <span class="font-mono font-extrabold text-base text-emerald-950 block mt-0.5">{{ $order->tracking_number }}</span>
                            <span class="text-[11px] text-emerald-700">Ekspedisi: {{ $order->shipping_courier }} • Tujuan: {{ $order->city }}</span>
                        </div>
                        <a href="https://cekresi.com/?noresi={{ $order->tracking_number }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-sm transition-all text-center shrink-0">
                            Cek Resi Ekspedisi &rarr;
                        </a>
                    </div>
                @endif

                <!-- Catatan Laboratorium / Admin Jika Ada -->
                @if(!empty($order->admin_notes))
                    <div class="p-4 bg-brand-50 border border-brand-200 rounded-2xl text-xs space-y-1">
                        <span class="text-[10px] font-bold text-brand-800 uppercase block tracking-wider">
                            <i class="fa-solid fa-clipboard-list mr-1"></i> Catatan Laboratorium Optik:
                        </span>
                        <p class="text-brand-950 font-medium italic">"{{ $order->admin_notes }}"</p>
                    </div>
                @endif

                <!-- Rincian Produk & Resep Mata -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    
                    <!-- Frame & Lensa -->
                    <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 space-y-3">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kacamata & Lensa</span>
                        @foreach($order->items as $it)
                            <div class="flex items-center gap-3">
                                @if($it->product && $it->product->image_url)
                                    <img src="{{ $it->product->image_url }}" alt="{{ $it->product_name }}" class="w-12 h-12 rounded-xl object-cover bg-white border border-sand-200">
                                @endif
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $it->product_name }}</span>
                                    <span class="text-[11px] text-brand-700 font-semibold">{{ $order->lens_type_name ?: 'CRMC Minus Standar' }}</span>
                                </div>
                            </div>
                        @endforeach

                        <div class="pt-3 border-t border-sand-200 space-y-1 text-[11px]">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Subtotal Frame:</span>
                                <span class="font-bold text-slate-800">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Paket Lensa:</span>
                                <span class="font-bold text-brand-700">+ Rp {{ number_format($order->lens_price, 0, ',', '.') }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-emerald-700 font-bold">
                                    <span>Diskon Voucher ({{ $order->voucher_code }}):</span>
                                    <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-1 border-t border-sand-200 text-xs font-extrabold text-slate-900">
                                <span>Total Pembayaran:</span>
                                <span class="text-brand-700">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Resep Mata (Jika Ada) -->
                    <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 space-y-3">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ukuran Resep Klinis (RX)</span>
                        @if($order->has_prescription)
                            <div class="space-y-1.5 font-mono text-[11px]">
                                <div class="p-2 bg-blue-50 text-blue-900 rounded-lg">
                                    <strong>Mata Kanan (R):</strong> Sph {{ $order->sph_r ?: '0.00' }} | Cyl {{ $order->cyl_r ?: '0.00' }} | Ax {{ $order->axis_r ?: '0' }}°
                                </div>
                                <div class="p-2 bg-amber-50 text-amber-900 rounded-lg">
                                    <strong>Mata Kiri (L):</strong> Sph {{ $order->sph_l ?: '0.00' }} | Cyl {{ $order->cyl_l ?: '0.00' }} | Ax {{ $order->axis_l ?: '0' }}°
                                </div>
                                <div class="text-slate-600 pt-1">
                                    Jarak Pupil (PD): <strong>{{ $order->pd ?: '62' }} mm</strong>
                                </div>
                            </div>
                        @else
                            <div class="text-slate-400 italic py-4 text-center">
                                Lensa normal / Plano (tanpa minus).
                            </div>
                        @endif

                        <div class="pt-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Alamat Penerima:</span>
                            <p class="text-slate-700 text-[11px] mt-0.5">{{ $order->shipping_address }}</p>
                        </div>
                    </div>

                </div>

                <!-- Action Button -->
                <div class="pt-4 border-t border-sand-200 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('pos.receipt', $order->order_code) }}" target="_blank" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-receipt"></i> Buka / Cetak Struk Digital
                    </a>

                    <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin Alysa Glasses, saya ingin bertanya status pesanan ' . $order->order_code) }}" target="_blank" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-brands fa-whatsapp text-sm"></i> Bantuan CS WhatsApp
                    </a>
                </div>

            </div>
        @endif

    </div>

@endsection
