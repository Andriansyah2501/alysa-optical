@extends('layouts.app')

@section('title', 'Pesanan Berhasil | Alysa Glasses')

@section('content')

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <div class="bg-white rounded-3xl border border-sand-200 shadow-xl overflow-hidden">
            
            <!-- Header Sukses -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-8 text-center space-y-3">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl mx-auto text-white shadow-inner">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold">Terima Kasih, Pesanan Anda Berhasil!</h1>
                <p class="text-xs sm:text-sm text-emerald-100 max-w-md mx-auto">
                    Kacamata pilihan Anda telah masuk antrean produksi laboratorium optik Alysa Glasses.
                </p>
                <div class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-xs font-mono font-bold tracking-wider">
                    KODE ORDER: {{ $order->order_code }}
                </div>
            </div>

            <!-- Detail Invoice Pesanan -->
            <div class="p-6 sm:p-8 space-y-6 text-xs text-slate-700">
                
                <!-- Status Timeline Bar -->
                <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200">
                    <div class="flex items-center justify-between text-center relative">
                        <div class="space-y-1 relative z-10">
                            <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold mx-auto text-xs"><i class="fa-solid fa-check"></i></div>
                            <span class="font-bold text-[11px] text-emerald-700 block">Dipesan</span>
                            <span class="text-[9px] text-slate-400">Pembayaran Sah</span>
                        </div>
                        <div class="space-y-1 relative z-10">
                            <div class="w-8 h-8 rounded-full bg-brand-500 text-white flex items-center justify-center font-bold mx-auto text-xs animate-pulse"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                            <span class="font-bold text-[11px] text-brand-700 block">Lab Optik</span>
                            <span class="text-[9px] text-slate-400">Potong Lensa Digital</span>
                        </div>
                        <div class="space-y-1 relative z-10">
                            <div class="w-8 h-8 rounded-full bg-sand-200 text-slate-400 flex items-center justify-center font-bold mx-auto text-xs"><i class="fa-solid fa-box"></i></div>
                            <span class="font-bold text-[11px] text-slate-400 block">Quality Check</span>
                            <span class="text-[9px] text-slate-400">Pembersihan & Case</span>
                        </div>
                        <div class="space-y-1 relative z-10">
                            <div class="w-8 h-8 rounded-full bg-sand-200 text-slate-400 flex items-center justify-center font-bold mx-auto text-xs"><i class="fa-solid fa-truck"></i></div>
                            <span class="font-bold text-[11px] text-slate-400 block">Kurir JNE</span>
                            <span class="text-[9px] text-slate-400">Pengiriman Kilat</span>
                        </div>
                    </div>
                </div>

                <!-- Status Otomasi WhatsApp & Notifikasi Terkirim -->
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <i class="fa-brands fa-whatsapp text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-xs sm:text-sm">Notifikasi Otomatis Terkirim ke WhatsApp</h4>
                            <p class="text-[11px] text-slate-500">Rincian invoice pesanan dan tautan pelacakan telah dikirimkan ke nomor <strong>{{ $order->customer_phone }}</strong>.</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold rounded-lg shrink-0">
                        OTOMATIS AKTIF
                    </span>
                </div>

                <!-- Info Produk & Resep -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Detail Frame Kacamata</h4>
                        @foreach($order->items as $it)
                            <div class="p-3 bg-sand-50 rounded-2xl flex items-center gap-3">
                                <img src="{{ $it->product->image_url }}" alt="{{ $it->product_name }}" class="w-14 h-14 rounded-xl object-cover bg-white border border-sand-200">
                                <div>
                                    <h5 class="font-bold text-slate-900 text-xs">{{ $it->product_name }}</h5>
                                    <span class="text-[11px] text-slate-500 block">{{ $it->product->frame_shape }} • {{ $it->product->color_primary }}</span>
                                    <span class="text-xs font-extrabold text-brand-700">Rp {{ number_format($it->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Paket Lensa & Resep Optik</h4>
                        <div class="p-3 bg-brand-50/60 rounded-2xl border border-brand-200 space-y-1.5">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-slate-800">{{ $order->lens_type_name }}</span>
                                <span class="font-extrabold text-brand-800">Rp {{ number_format($order->lens_price, 0, ',', '.') }}</span>
                            </div>
                            @if($order->has_prescription)
                                <div class="pt-2 border-t border-brand-200/60 grid grid-cols-2 gap-2 text-[10px]">
                                    <div class="p-1.5 bg-white rounded-lg">
                                        <span class="text-slate-400 block font-bold">R (Kanan):</span>
                                        <span>Sph: {{ $order->sph_r }} | Cyl: {{ $order->cyl_r }} | Ax: {{ $order->axis_r ?: '-' }}</span>
                                    </div>
                                    <div class="p-1.5 bg-white rounded-lg">
                                        <span class="text-slate-400 block font-bold">L (Kiri):</span>
                                        <span>Sph: {{ $order->sph_l }} | Cyl: {{ $order->cyl_l }} | Ax: {{ $order->axis_l ?: '-' }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-500 block">Jarak Pupil (PD): {{ $order->pd }} mm</span>
                            @else
                                <span class="text-[10px] text-slate-500 block">Kacamata Lensa Netral / Fashion (Tanpa Minus)</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info Pembayaran & Penerima -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-3 border-t border-sand-200">
                    <div>
                        <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] mb-2">Alamat Pengiriman</h4>
                        <p class="font-bold text-slate-800">{{ $order->customer_name }} ({{ $order->customer_phone }})</p>
                        <p class="text-slate-600 mt-1 leading-relaxed">{{ $order->shipping_address }}, Kota {{ $order->city }}</p>
                        <span class="text-[11px] font-semibold text-emerald-700 mt-1 block"><i class="fa-solid fa-truck text-[10px] mr-1"></i> {{ $order->shipping_courier }}</span>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] mb-2">Rincian Pembayaran</h4>
                        <div class="space-y-1.5">
                            <div class="flex justify-between">
                                <span>Subtotal Frame:</span>
                                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Paket Lensa Optik:</span>
                                <span>Rp {{ number_format($order->lens_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Biaya Kirim:</span>
                                <span class="text-emerald-600 font-bold">GRATIS</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-sand-200 text-sm font-extrabold text-slate-900">
                                <span>Grand Total:</span>
                                <span class="text-brand-700">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <span class="text-[10px] text-emerald-600 font-bold block pt-1"><i class="fa-solid fa-circle-check text-[9px] mr-1"></i> Status: {{ $order->payment_status }} via {{ $order->payment_method }}</span>
                        </div>
                    </div>
                </div>

                                <!-- Action Button & WhatsApp Notifikasi -->
                @php
                    $rawPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                    if (str_starts_with($rawPhone, '0')) {
                        $waNum = '62' . substr($rawPhone, 1);
                    } elseif (str_starts_with($rawPhone, '62')) {
                        $waNum = $rawPhone;
                    } else {
                        $waNum = '62' . $rawPhone;
                    }

                    $waMsg = "Halo Kak *" . $order->customer_name . "*,\n"
                           . "Pesanan kacamata Anda telah kami terima di *ALYSA GLASSES*! ✨\n\n"
                           . "🧾 *INVOICE PEMBELIAN OPTIK:*\n"
                           . "• No. Order : *" . $order->order_code . "*\n"
                           . "• Paket Lensa: " . $order->lens_type_name . "\n"
                           . "• Total Bayar: Rp " . number_format($order->grand_total, 0, ',', '.') . " (" . $order->payment_method . ")\n"
                           . "• Status     : " . $order->payment_status . "\n\n"
                           . "🔍 *Cek Status Produksi Lab & Resep:*\n"
                           . url('/lacak-pesanan?code=' . $order->order_code) . "\n\n"
                           . "Terima kasih telah mempercayakan penglihatan Anda kepada Alysa Glasses.";
                    $waCustomerUrl = "https://wa.me/" . $waNum . "?text=" . urlencode($waMsg);
                @endphp

                <div class="pt-6 border-t border-sand-200 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('shop.track', ['code' => $order->order_code]) }}" class="px-5 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold text-xs shadow-md shadow-brand-500/20 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-location-crosshairs"></i> Lacak Status Pesanan
                        </a>

                        @if(!empty($rawPhone) && strlen($rawPhone) >= 9)
                            <a href="{{ $waCustomerUrl }}" target="_blank" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center gap-2">
                                <i class="fa-brands fa-whatsapp text-sm"></i> Kirim Salinan Invoice ke WA Saya
                            </a>
                        @endif

                        <a href="{{ route('pos.receipt', ['orderCode' => $order->order_code]) }}" target="_blank" class="px-5 py-3 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs shadow-md shadow-sky-500/20 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-receipt"></i> Buka Struk Kasir / Print
                        </a>
                    </div>

                    <a href="{{ route('shop.index') }}" class="px-5 py-3 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold text-xs transition-colors">
                        Kembali Belanja
                    </a>
                </div>

            </div>

        </div>

    </div>

@endsection
