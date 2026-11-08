@extends('layouts.app')

@section('title', 'Koleksi Kacamata Alysa Glasses & Promo 11.11 Lensa Optik | Alysa Glasses')

@section('content')

    <!-- 1. Hero Banner Warm & Friendly -->
    <section class="relative bg-gradient-to-b from-brand-100/70 via-sand-50 to-sand-100/50 py-16 lg:py-24 overflow-hidden border-b border-sand-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-100 text-amber-900 text-xs font-extrabold tracking-wide border border-amber-200 shadow-sm">
                        <i class="fa-solid fa-fire text-amber-600"></i> ALYSA GLASSES – 11.11 SPECIAL PRICE
                    </div>
                    <h1 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 leading-[1.15]">
                        Eyewear That Completes <br>
                        <span class="text-brand-600 italic font-medium">Your Look 🫶🏻</span>
                    </h1>
                    <p class="text-slate-600 text-sm sm:text-base max-w-xl leading-relaxed mx-auto lg:mx-0">
                        📍 <strong>Batam • Tanjung Pinang • Bintan</strong> | ✨ <strong>Premium Quality • Affordable Price</strong>.<br>
                        Temukan koleksi frame favorit (Cat Eye, Bulat, Segi) lengkap dengan paket lensa 11.11: Blueray, Photocromic, Bluecromic, Progresif, dan Anti Fog.
                    </p>
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('shop.customer.portal') }}" class="px-4 py-2 bg-brand-600 text-xs hover:bg-brand-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-brand-500/25 transition-all hover:scale-105 flex items-center gap-2">
                            <i class="fa-solid fa-cart-shopping"></i> Pesan Online Sekarang
                        </a>
                         class="px-4 py-2 bg-brand-600 text-xs hover:bg-brand-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-brand-500/25 transition-all hover:scale-105">
                            Lihat Semua Frame <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </a>
                        <a href="#promo-lensa" class="px-4 py-2 bg-white text-xs hover:bg-sand-50 text-slate-800 rounded-2xl text-sm font-bold border border-sand-300 shadow-sm transition-all">
                            Tabel Harga Lensa 11.11 <i class="fa-solid fa-tags ml-1 text-brand-600"></i>
                        </a>
                    </div>
                    
                    <!-- Stats Mini -->
                    <div class="pt-6 grid grid-cols-3 gap-6 max-w-md mx-auto lg:mx-0 border-t border-sand-300/60">
                        <div>
                            <span class="block text-xl font-bold text-slate-900">100%</span>
                            <span class="text-[11px] text-slate-500 font-medium">Jaminan Akurat</span>
                        </div>
                        <div>
                            <span class="block text-xl font-bold text-slate-900">6 Paket</span>
                            <span class="text-[11px] text-slate-500 font-medium">Lensa 11.11</span>
                        </div>
                        <div>
                            <span class="block text-xl font-bold text-slate-900">3 Voucher</span>
                            <span class="text-[11px] text-slate-500 font-medium">Klaim Promo</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Image Showcase -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md">
                        <!-- Floating Pill Badge 1 -->
                        <div class="absolute -top-4 -left-4 z-20 bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-lg border border-sand-200 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                                <i class="fa-solid fa-tag"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-slate-900">Voucher ALYSA50K</p>
                                <p class="text-[9px] text-slate-500">Hemat langsung Rp 50.000</p>
                            </div>
                        </div>

                        <!-- Main Image Container -->
                        <div class="overflow-hidden rounded-3xl shadow-2xl border-4 border-white bg-white">
                            <img src="https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&w=800&q=80" alt="Alysa Glasses Showcase" class="w-full h-96 object-cover object-center transform hover:scale-105 transition-transform duration-500">
                            <div class="p-4 bg-white flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold text-brand-600 uppercase tracking-wider">Koleksi Terlaris 11.11</span>
                                    <h4 class="text-sm font-bold text-slate-900">Alysa Shibuya Rose Gold</h4>
                                </div>
                                <span class="text-sm font-extrabold text-slate-900">Rp 379.000</span>
                            </div>
                        </div>

                        <!-- Floating Pill Badge 2 -->
                        <div class="absolute -bottom-4 -right-4 z-20 bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-lg border border-sand-200 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                <i class="fa-solid fa-shield-heart"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-slate-900">Garansi Resep 30 Hari</p>
                                <p class="text-[9px] text-slate-500">Bebas ganti lensa gratis</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. TABEL HARGA ALYSA GLASSES – 11.11 SPECIAL PRICE (RESMI) -->
    <section id="promo-lensa" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
        <div class="bg-white rounded-3xl border border-sand-300 shadow-xl p-6 sm:p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-sand-200">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Rekomendasi Harga Promo
                    </div>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900 mt-2">
                        👓 ALYSA GLASSES – 11.11 SPECIAL PRICE
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Daftar harga promo paket lensa optik resmi berlaku selama periode promo 11.11.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-brand-600 bg-brand-50 px-3.5 py-2 rounded-xl border border-brand-200">
                        <i class="fa-solid fa-clock mr-1"></i> Promo Terbatas 11.11
                    </span>
                </div>
            </div>

            <!-- Grid 6 Paket Promo 11.11 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                <!-- 1. CRMC Minus -->
                <div class="p-4 bg-sand-50/70 hover:bg-brand-50/60 rounded-2xl border border-sand-200 hover:border-brand-400 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900">CRMC Minus</span>
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-md">Hemat Rp 21.000</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Lensa anti pantul standar optik (Multi Coating) jernih untuk minus/silinder.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-sand-200 flex items-baseline justify-between">
                        <span class="text-xs text-slate-400 line-through">Rp 200.000</span>
                        <span class="text-base font-extrabold text-brand-700">Rp 179.000</span>
                    </div>
                </div>

                <!-- 2. BlueRay -->
                <div class="p-4 bg-sand-50/70 hover:bg-brand-50/60 rounded-2xl border border-sand-200 hover:border-brand-400 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900">BlueRay Anti Radiasi</span>
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-md">Hemat Rp 31.000</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Blokir radiasi sinar biru layar gadget/laptop 95%, anti pusing & mata lelah.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-sand-200 flex items-baseline justify-between">
                        <span class="text-xs text-slate-400 line-through">Rp 280.000</span>
                        <span class="text-base font-extrabold text-brand-700">Rp 249.000</span>
                    </div>
                </div>

                <!-- 3. Photochromic -->
                <div class="p-4 bg-sand-50/70 hover:bg-brand-50/60 rounded-2xl border border-sand-200 hover:border-brand-400 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900">Photochromic Grey</span>
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-md">Hemat Rp 31.000</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Bening di dalam ruangan dan otomatis bertransisi menjadi gelap saat kena matahari.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-sand-200 flex items-baseline justify-between">
                        <span class="text-xs text-slate-400 line-through">Rp 280.000</span>
                        <span class="text-base font-extrabold text-brand-700">Rp 249.000</span>
                    </div>
                </div>

                <!-- 4. Bluecromic -->
                <div class="p-4 bg-brand-50/80 rounded-2xl border-2 border-brand-400 shadow-sm flex flex-col justify-between relative">
                    <span class="absolute -top-2.5 right-4 px-2 py-0.5 bg-brand-600 text-white text-[9px] font-extrabold uppercase rounded-full">Best Value</span>
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-brand-900">Bluecromic 2-in-1</span>
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-md">Hemat Rp 31.000</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">Kombinasi maksimal Blueray gadget indoor + Photochromic adaptif outdoor.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-brand-200 flex items-baseline justify-between">
                        <span class="text-xs text-slate-400 line-through">Rp 370.000</span>
                        <span class="text-base font-extrabold text-brand-700">Rp 339.000</span>
                    </div>
                </div>

                <!-- 5. Night Drive -->
                <div class="p-4 bg-sand-50/70 hover:bg-brand-50/60 rounded-2xl border border-sand-200 hover:border-brand-400 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900">Night Drive</span>
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-md">Hemat Rp 31.000</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Lensa khusus pengendara malam hari, meredam silau lampu sorot kendaraan.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-sand-200 flex items-baseline justify-between">
                        <span class="text-xs text-slate-400 line-through">Rp 450.000</span>
                        <span class="text-base font-extrabold text-brand-700">Rp 419.000</span>
                    </div>
                </div>

                <!-- 6. Anti Fog -->
                <div class="p-4 bg-sand-50/70 hover:bg-brand-50/60 rounded-2xl border border-sand-200 hover:border-brand-400 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900">Anti Fog Anti Embun</span>
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-md">Hemat Rp 31.000</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Lapisan hidrofobik nano anti embun saat memakai masker atau minum panas.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-sand-200 flex items-baseline justify-between">
                        <span class="text-xs text-slate-400 line-through">Rp 470.000</span>
                        <span class="text-base font-extrabold text-brand-700">Rp 439.000</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3. Filter Bar & Katalog Kacamata -->
    <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-sand-200">
            <div>
                <span class="text-xs font-bold tracking-widest text-brand-600 uppercase">Katalog Resmi Alysa Glasses</span>
                <h2 class="font-serif text-3xl font-bold text-slate-900 mt-1">Eksplorasi Frame Pilihan</h2>
                <p class="text-xs text-slate-600 mt-1">Gunakan filter untuk menemukan bentuk kacamata yang cocok dengan bentuk wajah Anda.</p>
            </div>

            <!-- Filter Controls Form -->
            <form action="{{ route('shop.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif

                <select name="bentuk" onchange="this.form.submit()" class="bg-white border border-sand-300 rounded-xl text-xs font-semibold px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">Semua Bentuk</option>
                    <option value="Round" {{ request('bentuk') == 'Round' ? 'selected' : '' }}>Bulat (Round)</option>
                    <option value="Wayfarer" {{ request('bentuk') == 'Wayfarer' ? 'selected' : '' }}>Wayfarer Classic</option>
                    <option value="Square" {{ request('bentuk') == 'Square' ? 'selected' : '' }}>Kotak (Square)</option>
                    <option value="Aviator" {{ request('bentuk') == 'Aviator' ? 'selected' : '' }}>Aviator Pilot</option>
                    <option value="Cat-Eye" {{ request('bentuk') == 'Cat-Eye' ? 'selected' : '' }}>Cat-Eye</option>
                    <option value="Geometric" {{ request('bentuk') == 'Geometric' ? 'selected' : '' }}>Geometric / Segi</option>
                </select>

                <select name="gender" onchange="this.form.submit()" class="bg-white border border-sand-300 rounded-xl text-xs font-semibold px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">Semua Gender</option>
                    <option value="Unisex" {{ request('gender') == 'Unisex' ? 'selected' : '' }}>Unisex</option>
                    <option value="Pria" {{ request('gender') == 'Pria' ? 'selected' : '' }}>Pria</option>
                    <option value="Wanita" {{ request('gender') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                    <option value="Anak" {{ request('gender') == 'Anak' ? 'selected' : '' }}>Anak-Anak</option>
                </select>

                <select name="sort" onchange="this.form.submit()" class="bg-white border border-sand-300 rounded-xl text-xs font-semibold px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                    <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                </select>

                @if(request()->hasAny(['bentuk', 'gender', 'sort', 'q', 'kategori']))
                    <a href="{{ route('shop.index') }}" class="px-3 py-2 bg-rose-50 text-rose-600 rounded-xl text-xs font-bold hover:bg-rose-100 transition-colors" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-8">
            @forelse($products as $prod)
                <div class="bg-white rounded-3xl border border-sand-200 overflow-hidden hover-lift flex flex-col group relative">
                    
                    <!-- Badges -->
                    <div class="absolute top-3.5 left-3.5 z-10 flex flex-col gap-1.5">
                        @if($prod->is_bestseller)
                            <span class="px-2.5 py-1 bg-amber-600 text-white text-[10px] font-bold rounded-lg tracking-wider uppercase shadow-sm">
                                Best Seller
                            </span>
                        @endif
                        @if($prod->discount_price)
                            <span class="px-2.5 py-1 bg-rose-600 text-white text-[10px] font-bold rounded-lg tracking-wider uppercase shadow-sm">
                                Hemat Rp {{ number_format($prod->price - $prod->discount_price, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Color Dot Preview -->
                    <div class="absolute top-3.5 right-3.5 z-10 flex items-center gap-1 bg-white/90 backdrop-blur-md px-2 py-1 rounded-full shadow-sm border border-sand-200">
                        <span class="w-3 h-3 rounded-full border border-slate-300" style="background-color: {{ $prod->color_hex }}"></span>
                        <span class="text-[10px] font-bold text-slate-700">{{ $prod->gender }}</span>
                    </div>

                    <!-- Product Image -->
                    <a href="{{ route('shop.show', $prod->slug) }}" class="relative block bg-sand-100/60 overflow-hidden aspect-[4/3]">
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                    </a>

                    <!-- Product Content -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-brand-600 uppercase tracking-widest">{{ $prod->brand }}</span>
                            <h3 class="font-bold text-sm text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-1 mt-0.5">
                                <a href="{{ route('shop.show', $prod->slug) }}">{{ $prod->name }}</a>
                            </h3>

                            <div class="flex items-center gap-2 mt-2 text-[11px] text-slate-600">
                                <span><i class="fa-solid fa-shapes text-brand-500 mr-1"></i>{{ $prod->frame_shape }}</span>
                                <span>•</span>
                                <span><i class="fa-solid fa-ruler-horizontal text-brand-500 mr-1"></i>{{ $prod->size_frame }}</span>
                            </div>

                            <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                {{ $prod->description }}
                            </p>
                        </div>

                        <!-- Price & Action -->
                        <div class="pt-4 mt-4 border-t border-sand-100 flex items-center justify-between">
                            <div>
                                @if($prod->discount_price)
                                    <span class="text-[11px] text-slate-600 line-through block">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                    <span class="text-base font-extrabold text-brand-700">Rp {{ number_format($prod->discount_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-base font-extrabold text-slate-900">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                @endif
                            </div>

                            <a href="{{ route('shop.show', $prod->slug) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-700 hover:text-white transition-all shadow-sm" title="Lihat Detail & Pasang Lensa 11.11">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-sand-200">
                    <div class="w-16 h-16 rounded-full bg-sand-100 text-slate-600 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-base">Model Kacamata Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto">Coba ubah filter bentuk atau gender kacamata.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="pt-10">
            {{ $products->links() }}
        </div>

    </section>

@endsection
