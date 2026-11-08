@extends('layouts.app')

@section('title', $product->name . ' | Alysa Glasses')

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8">
            <a href="{{ route('shop.index') }}" class="hover:text-brand-600 transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('shop.index', ['kategori' => $product->category->slug]) }}" class="hover:text-brand-600 transition-colors">{{ $product->category->name }}</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-900 font-semibold truncate">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left: Gallery Showcase -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-3xl border border-sand-200 overflow-hidden shadow-sm aspect-[4/3] relative group">
                    <img id="mainImage" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                    
                    @if($product->is_bestseller)
                        <span class="absolute top-4 left-4 px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg shadow-sm">
                            <i class="fa-solid fa-fire mr-1"></i> Terlaris
                        </span>
                    @endif
                </div>

                <!-- Mini Trust Badges -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 bg-white rounded-2xl border border-sand-200 text-center">
                        <i class="fa-solid fa-feather text-brand-600 mb-1"></i>
                        <p class="text-[11px] font-bold text-slate-800">Ultra Light</p>
                        <p class="text-[9px] text-slate-500">Bobot seringan 8g</p>
                    </div>
                    <div class="p-3 bg-white rounded-2xl border border-sand-200 text-center">
                        <i class="fa-solid fa-shield-halved text-brand-600 mb-1"></i>
                        <p class="text-[11px] font-bold text-slate-800">Garansi 30 Hari</p>
                        <p class="text-[9px] text-slate-500">Ganti lensa gratis</p>
                    </div>
                    <div class="p-3 bg-white rounded-2xl border border-sand-200 text-center">
                        <i class="fa-solid fa-box text-brand-600 mb-1"></i>
                        <p class="text-[11px] font-bold text-slate-800">Free Hardcase</p>
                        <p class="text-[9px] text-slate-500">+ Lap Microfiber</p>
                    </div>
                </div>
            </div>

            <!-- Right: Product Purchasing & Lens Configurator -->
            <div class="lg:col-span-5 space-y-6">
                
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-6">
                    
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-brand-600 uppercase tracking-widest">{{ $product->brand }}</span>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full"><i class="fa-solid fa-circle-check mr-1"></i> Stok Tersedia ({{ $product->stock }} pcs)</span>
                        </div>
                        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900 mt-1">{{ $product->name }}</h1>
                        
                        <!-- Rating Preview -->
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex text-amber-400 text-xs">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star-half-stroke"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-700">4.9</span>
                            <span class="text-xs text-slate-400">• 128 Ulasan Terverifikasi</span>
                        </div>
                    </div>

                    <!-- Price Card -->
                    <div class="p-4 bg-sand-50 rounded-2xl border border-sand-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-500 block">Harga Frame Eksklusif</span>
                            @if($product->discount_price)
                                <div class="flex items-baseline gap-2">
                                    <span class="text-2xl font-extrabold text-brand-700">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                    <span class="text-xs text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <span class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <span class="text-[11px] font-bold text-brand-800 bg-brand-200/60 px-3 py-1.5 rounded-xl">Bisa Pasang Lensa</span>
                    </div>

                    <!-- Spesifikasi Dimensi -->
                    <div class="space-y-3 pt-2 border-t border-sand-200">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Spesifikasi Frame</h4>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2.5 bg-sand-50 rounded-xl">
                                <span class="text-slate-400 block text-[10px]">Bentuk:</span>
                                <span class="font-bold text-slate-800">{{ $product->frame_shape }}</span>
                            </div>
                            <div class="p-2.5 bg-sand-50 rounded-xl">
                                <span class="text-slate-400 block text-[10px]">Material:</span>
                                <span class="font-bold text-slate-800">{{ $product->frame_material }}</span>
                            </div>
                            <div class="p-2.5 bg-sand-50 rounded-xl">
                                <span class="text-slate-400 block text-[10px]">Ukuran Frame:</span>
                                <span class="font-bold text-slate-800">{{ $product->size_frame }}</span>
                            </div>
                            <div class="p-2.5 bg-sand-50 rounded-xl">
                                <span class="text-slate-400 block text-[10px]">Warna Utama:</span>
                                <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full border border-slate-300" style="background-color: {{ $product->color_hex }}"></span>
                                    {{ $product->color_primary }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi Frame -->
                    <div class="space-y-2 pt-2 border-t border-sand-200">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Deskripsi & Kenyamanan</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $product->description }}</p>
                    </div>

                    <!-- Step Pembelian: Custom Resep Lensa -->
                    <div class="space-y-3 pt-4 border-t border-sand-200">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center justify-between">
                            <span>Pilih Paket Lensa Optik</span>
                            <span class="text-[10px] text-brand-600 normal-case font-semibold">Bisa minus / normal</span>
                        </h4>

                        <!-- Form Selector Direct to Checkout -->
                        <form action="{{ route('shop.checkout') }}" method="GET" class="space-y-4">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="space-y-2">
                                @foreach($lensTypes as $index => $lens)
                                    <label class="p-3.5 rounded-2xl border border-sand-200 hover:border-brand-400 flex items-start gap-3 cursor-pointer transition-all hover:bg-sand-50 has-[:checked]:bg-brand-50 has-[:checked]:border-brand-500 has-[:checked]:ring-1 has-[:checked]:ring-brand-500">
                                        <input type="radio" name="lens_id" value="{{ $lens->id }}" {{ $index == 1 ? 'checked' : '' }} class="mt-1 text-brand-600 focus:ring-brand-500">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-slate-900">{{ $lens->name }}</span>
                                                <span class="text-xs font-extrabold text-brand-700">
                                                    {{ $lens->additional_price > 0 ? '+ Rp ' . number_format($lens->additional_price, 0, ',', '.') : 'Gratis / Bawaan' }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">{{ $lens->description }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <button type="submit" class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-brand-500/25 transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                                <i class="fa-solid fa-glasses"></i>
                                Lanjut Isi Ukuran Resep Mata & Checkout
                            </button>
                        </form>

                        <p class="text-[11px] text-slate-400 text-center">
                            <i class="fa-solid fa-lock text-[10px] mr-1"></i> Data resep Anda aman dan diperiksa langsung oleh refraksionis optik berijazah resmi.
                        </p>
                    </div>

                </div>

            </div>

        </div>

        <!-- Koleksi Terkait -->
        @if($relatedProducts->count() > 0)
            <div class="mt-20 pt-10 border-t border-sand-200">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <span class="text-xs font-bold tracking-widest text-brand-600 uppercase">Rekomendasi Serupa</span>
                        <h3 class="font-serif text-2xl font-bold text-slate-900 mt-1">Pilihan Frame Lainnya</h3>
                    </div>
                    <a href="{{ route('shop.index', ['kategori' => $product->category->slug]) }}" class="text-xs font-bold text-brand-600 hover:underline">Lihat Semua Kategori Ini &rarr;</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $rel)
                        <div class="bg-white rounded-3xl border border-sand-200 overflow-hidden hover-lift flex flex-col group">
                            <a href="{{ route('shop.show', $rel->slug) }}" class="block bg-sand-100/60 aspect-[4/3] overflow-hidden">
                                <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-brand-600 uppercase">{{ $rel->brand }}</span>
                                    <h4 class="font-bold text-xs text-slate-900 line-clamp-1 mt-0.5">
                                        <a href="{{ route('shop.show', $rel->slug) }}" class="hover:text-brand-600">{{ $rel->name }}</a>
                                    </h4>
                                    <span class="text-xs font-extrabold text-slate-900 mt-2 block">
                                        Rp {{ number_format($rel->effective_price, 0, ',', '.') }}
                                    </span>
                                </div>
                                <a href="{{ route('shop.show', $rel->slug) }}" class="mt-3 w-full py-2 bg-sand-100 hover:bg-brand-600 hover:text-white rounded-xl text-xs font-bold text-center text-slate-700 transition-colors">
                                    Pilih Frame Ini
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

@endsection
