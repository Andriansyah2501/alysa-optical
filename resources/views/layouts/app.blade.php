<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Alysa Glasses | Toko Kacamata & Lensa Optik Modern')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        sand: {
                            50: '#FDFBF7',
                            100: '#F7F3EB',
                            200: '#EFE7DA',
                            300: '#E2D5C0',
                            400: '#CFC0A4',
                            500: '#B8A383',
                            900: '#2C261E',
                        },
                        brand: {
                            50: '#FBF5F2',
                            100: '#F6EAE4',
                            200: '#ECD5CA',
                            300: '#DFB6A5',
                            400: '#CF917A',
                            500: '#BE6F54', // Rose Terracotta Utama
                            600: '#A9583E',
                            700: '#8C442F',
                            800: '#723929',
                            900: '#5F3225',
                        },
                        amber: {
                            50: '#FFFBEB',
                            100: '#FEF3C7',
                            500: '#F59E0B',
                            600: '#D97706',
                        },
                        emerald: {
                            50: '#ECFDF5',
                            100: '#D1FAE5',
                            500: '#10B981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #FDFBF7;
            color: #1E293B;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 213, 192, 0.5);
        }
        .hover-lift {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -10px rgba(190, 111, 84, 0.18);
        }
    </style>
    <!-- PWA & Mobile Web App Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#BE6F54">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Alysa Optical">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(err => {
                    console.log('SW registration failed: ', err);
                });
            });
        }
    </script>

</head>
<body class="min-h-screen flex flex-col selection:bg-brand-200 selection:text-brand-900">

    <!-- Top Announcement Bar (Editable by Super Admin) -->
    @if(($globalSettings['header_announcement_enabled'] ?? '1') !== '0')
        <div class="bg-gradient-to-r from-brand-600 via-brand-500 to-amber-600 text-white text-[10px] font-semibold tracking-wide py-1 px-3 text-center">
            <span class="inline-flex items-center gap-2">
                <i class="fa-solid fa-sparkles text-amber-200"></i>
                {!! $globalSettings['header_announcement'] ?? '<strong>PROMO HARI INI:</strong> Gratis Ongkir Seluruh Indonesia + Free Hardcase Kulit & Lap Pembersih Lensa' !!}
            </span>
        </div>
    @endif

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-sand-200/80 transition-all shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-12 sm:h-13">
                
                <!-- Brand Logo & Identity (Compact & Mobile Friendly) -->
                <div class="flex items-center gap-4 sm:gap-8">
                    <a href="{{ route('shop.index') }}" class="flex items-center gap-2 sm:gap-2.5 group">
                        <!-- Logo Icon Dikecilkan & Elegan -->
                        <div class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white text-xs shadow-sm shadow-brand-500/15 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-glasses"></i>
                        </div>
                        <div>
                            <!-- Nama Brand Dikecilkan -->
                            <span class="font-serif text-xs sm:text-sm font-bold tracking-tight text-slate-900 block leading-tight">
                                {{ $globalSettings['store_name'] ?? 'Alysa Optical' }}
                            </span>
                            <span class="text-[7.5px] sm:text-[8px] uppercase tracking-wider text-brand-600 font-semibold block">
                                {{ $globalSettings['store_tagline'] ?? 'Eyewear Studio Batam' }}
                            </span>
                        </div>
                    </a>

                    <!-- Nav Link Kategori (Dikecilkan) -->
                    <nav class="hidden md:flex items-center gap-4 text-[11px] font-bold text-slate-700">
                        <a href="{{ route('shop.index') }}" class="hover:text-brand-600 transition-colors py-1 {{ request()->routeIs('shop.index') && !request('kategori') ? 'text-brand-600 border-b-2 border-brand-500' : '' }}">
                            Semua Frame
                        </a>
                        <a href="{{ route('shop.index', ['kategori' => 'kacamata-optik']) }}" class="hover:text-brand-600 transition-colors py-1 {{ request('kategori') == 'kacamata-optik' ? 'text-brand-600 border-b-2 border-brand-500' : '' }}">
                            Kacamata Optik
                        </a>
                        <a href="{{ route('shop.index', ['kategori' => 'kacamata-hitam']) }}" class="hover:text-brand-600 transition-colors py-1 {{ request('kategori') == 'kacamata-hitam' ? 'text-brand-600 border-b-2 border-brand-500' : '' }}">
                            Sunglasses Polarized
                        </a>
                        <a href="{{ route('shop.index', ['kategori' => 'titanium-series']) }}" class="hover:text-brand-600 transition-colors py-1 {{ request('kategori') == 'titanium-series' ? 'text-brand-600 border-b-2 border-brand-500' : '' }}">
                            Titanium 8G
                        </a>
                        <a href="{{ route('shop.customer.portal') }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-[10.5px] font-bold shadow-xs transition-all {{ request()->routeIs('shop.customer.portal') ? 'ring-1 ring-brand-400' : '' }}">
                            <i class="fa-solid fa-cart-shopping text-[10px]"></i> Pesan Online
                        </a>
                        <a href="{{ route('shop.calendar') }}" class="hover:text-brand-600 transition-colors py-1 {{ request()->routeIs('shop.calendar') ? 'text-brand-600 border-b-2 border-brand-500' : '' }}">
                            <i class="fa-regular fa-calendar-days text-amber-500 mr-0.5"></i> Promo
                        </a>
                        <a href="{{ route('shop.track') }}" class="hover:text-brand-600 transition-colors py-1 {{ request()->routeIs('shop.track') ? 'text-brand-600 border-b-2 border-brand-500' : '' }}">
                            <i class="fa-solid fa-location-crosshairs mr-0.5 text-brand-500"></i> Lacak
                        </a>
                    </nav>
                </div>

                <!-- Right Action Bar: Clean Dropdown Menu -->
                <div class="flex items-center gap-2">
                    
                    @auth
                        <!-- Tombol Cepat POS Kasir -->
                        @if(in_array(Auth::user()->role, ['cashier', 'admin', 'super_admin']))
                            <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-xs transition-all">
                                <i class="fa-solid fa-cash-register text-[10px]"></i>
                                <span class="hidden sm:inline">Kasir</span>
                            </a>
                        @endif

                        <!-- Menu Dropdown Manajemen Staf / Admin -->
                        <div class="relative inline-block text-left" id="adminDropdownContainer">
                            <button type="button" onclick="document.getElementById('adminDropdownMenu').classList.toggle('hidden')" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sand-100 hover:bg-sand-200 text-slate-800 rounded-lg text-[11px] font-bold border border-sand-300 transition-colors">
                                <i class="fa-solid fa-user-tie text-brand-600 text-xs"></i>
                                <span>Menu Admin</span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 ml-0.5"></i>
                            </button>

                            <div id="adminDropdownMenu" class="hidden absolute right-0 mt-1.5 w-52 bg-white rounded-xl shadow-xl border border-sand-200 z-50 py-1 divide-y divide-sand-100 text-xs">
                                <div class="px-3 py-2 bg-sand-50/60">
                                    <div class="font-bold text-slate-900 truncate">{{ Auth::user()->name }}</div>
                                    <div class="text-[10px] text-brand-600 font-semibold uppercase tracking-wider">{{ Auth::user()->role }}</div>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                        <i class="fa-solid fa-glasses text-brand-500 w-4"></i> Lab Optik & Antrean
                                    </a>
                                    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                        <i class="fa-solid fa-chart-line text-sky-500 w-4"></i> Laporan & Grafik Omset
                                    </a>
                                    <a href="{{ route('admin.calendar.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                        <i class="fa-solid fa-calendar-days text-amber-500 w-4"></i> Kelola Kalender Promo
                                    </a>
                                    <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                        <i class="fa-solid fa-users text-emerald-600 w-4"></i> Database Pelanggan & Resep
                                    </a>
                                    <a href="{{ route('admin.expenses.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                        <i class="fa-solid fa-money-bill-transfer text-rose-500 w-4"></i> Belanja Modal & Cashflow
                                    </a>
                                    @if(in_array(Auth::user()->role, ['admin', 'super_admin']))
                                        <a href="{{ route('admin.katalog.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                            <i class="fa-solid fa-boxes-stacked text-purple-500 w-4"></i> Katalog Kacamata
                                        </a>
                                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                            <i class="fa-solid fa-gear text-slate-500 w-4"></i> Pengaturan Toko
                                        </a>
                                        <a href="{{ route('admin.backups.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                            <i class="fa-solid fa-cloud-arrow-down text-purple-600 w-4"></i> Cadangan Data & Database
                                        </a>
                                        <a href="{{ route('admin.server.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                            <i class="fa-solid fa-server text-sky-600 w-4"></i> Status Server & Jaringan
                                        </a>
                                        <a href="{{ route('admin.tunnel.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-brand-50 hover:text-brand-700 font-medium transition-colors">
                                            <i class="fa-solid fa-tower-broadcast text-amber-600 w-4"></i> Manajemen Tunneling
                                        </a>
                                    @endif
                                </div>
                                <div class="py-1">
                                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-1.5 text-rose-600 hover:bg-rose-50 font-bold transition-colors">
                                            <i class="fa-solid fa-power-off text-rose-500 w-4"></i> Keluar (Logout)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @else
                        <!-- Jika Pengunjung Belum Login -->
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-lg text-[11px] font-bold border border-sand-300 transition-colors">
                            <i class="fa-solid fa-lock text-[10px]"></i>
                            <span>Login</span>
                        </a>
                    @endauth

                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $globalSettings['store_phone'] ?? '087888883318');
                        $waTarget = str_starts_with($cleanPhone, '0') ? '62' . substr($cleanPhone, 1) : $cleanPhone;
                    @endphp
                    <a href="https://wa.me/{{ $waTarget }}?text=Halo%20Alysa%20Optical" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-lg text-[11px] font-bold border border-emerald-200 transition-colors">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-xs"></i>
                        <span>WA</span>
                    </a>

                </div>

            </div>
        </div>
            <!-- Mobile Sub-Navigation Bar (Horizontal Scrollable for Easy Touch) -->
        <div class="md:hidden border-t border-sand-100 bg-sand-50/80 px-2 py-1 overflow-x-auto flex items-center gap-1.5 text-[10px] font-bold no-scrollbar">
            <a href="{{ route('shop.index') }}" class="whitespace-nowrap px-2 py-0.5 rounded-md {{ request()->routeIs('shop.index') && !request('kategori') ? 'bg-brand-600 text-white' : 'bg-white text-slate-700 border border-sand-200' }}">
                <i class="fa-solid fa-glasses mr-0.5 text-[9px]"></i> Semua Frame
            </a>
            <a href="{{ route('shop.customer.portal') }}" class="whitespace-nowrap px-2 py-0.5 rounded-md {{ request()->routeIs('shop.customer.portal') ? 'bg-brand-600 text-white' : 'bg-brand-50 text-brand-700 border border-brand-200' }}">
                <i class="fa-solid fa-cart-shopping mr-0.5 text-[9px]"></i> Pesan
            </a>
            <a href="{{ route('shop.calendar') }}" class="whitespace-nowrap px-2 py-0.5 rounded-md {{ request()->routeIs('shop.calendar') ? 'bg-amber-500 text-white' : 'bg-white text-slate-700 border border-sand-200' }}">
                <i class="fa-regular fa-calendar-days mr-0.5 text-[9px] text-amber-500"></i> Promo
            </a>
            <a href="{{ route('shop.track') }}" class="whitespace-nowrap px-2 py-0.5 rounded-md {{ request()->routeIs('shop.track') ? 'bg-brand-600 text-white' : 'bg-white text-slate-700 border border-sand-200' }}">
                <i class="fa-solid fa-location-crosshairs mr-0.5 text-[9px]"></i> Lacak
            </a>
            @php
                $cleanPhoneMobile = preg_replace('/[^0-9]/', '', $globalSettings['store_phone'] ?? '087888883318');
                $waMobile = str_starts_with($cleanPhoneMobile, '0') ? '62' . substr($cleanPhoneMobile, 1) : $cleanPhoneMobile;
            @endphp
            <a href="https://wa.me/{{ $waMobile }}?text=Halo%20Alysa%20Optical,%20saya%20ingin%20tanya%20kacamata" target="_blank" class="whitespace-nowrap px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i class="fa-brands fa-whatsapp mr-1 text-emerald-600"></i> WhatsApp
            </a>
        </div>
    </header>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Content Slot -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Warm Light -->
    
    <!-- Saluran WhatsApp Promo 11.11 Banner -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-8 relative z-20">
        <div class="bg-gradient-to-r from-emerald-800 via-teal-800 to-emerald-900 rounded-3xl p-6 sm:p-8 text-white shadow-2xl flex flex-col md:flex-row items-center justify-between gap-6 border border-emerald-700/50">
            <div class="space-y-2 text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-emerald-300 text-xs font-bold">
                    <i class="fa-brands fa-whatsapp"></i> Saluran WhatsApp Resmi {{ $globalSettings['store_name'] ?? 'Alysa Glasses' }}
                </div>
                <h3 class="font-serif text-xl sm:text-2xl font-bold tracking-tight">{{ $globalSettings['footer_channel_headline'] ?? 'Wishlist Frame-mu, Saatnya Jadi Kenyataan! 🔥' }}</h3>
                <p class="text-xs sm:text-sm text-emerald-100 max-w-xl">
                    {{ $globalSettings['footer_channel_subtext'] ?? 'Follow dan Join Saluran WhatsApp Alysa Glasses untuk info promo 11.11 spesial, restock frame viral, dan voucher eksklusif.' }}
                </p>
            </div>
            <a href="{{ $globalSettings['whatsapp_channel_url'] ?? 'https://whatsapp.com/channel/0029VbDeAYNInlqON8uS8h0F' }}" target="_blank" class="shrink-0 px-6 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-emerald-900/40 transition-all flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-lg"></i> Gabung Saluran WA Sekarang
            </a>
        </div>
    </div>

    <footer class="bg-white border-t border-sand-200/80 pt-16 pb-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-sand-200">
                
                <!-- Brand Info -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand-500 text-white flex items-center justify-center text-lg">
                            <i class="fa-solid fa-glasses"></i>
                        </div>
                        <span class="font-serif text-xl font-bold text-slate-900">{{ $globalSettings['store_name'] ?? 'Alysa Glasses' }}</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ $globalSettings['footer_about_text'] ?? ($globalSettings['store_bio'] ?? 'Toko kacamata dan laboratorium optik digital. Menyediakan frame ultralight, lensa anti radiasi bluechromic, dan perakitan lensa presisi.') }}
                    </p>
                    <div class="flex items-center gap-3 text-brand-600 text-sm">
                        <a href="{{ $globalSettings['store_instagram_url'] ?? 'https://www.instagram.com/alysa_glasses/' }}" target="_blank" class="w-8 h-8 rounded-full bg-sand-100 flex items-center justify-center hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-instagram"></i></a>
                        <a href="{{ $globalSettings['store_tiktok'] ?? 'https://www.tiktok.com/@alysa_glasses' }}" target="_blank" class="w-8 h-8 rounded-full bg-sand-100 flex items-center justify-center hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="https://wa.me/{{ $waTarget }}" target="_blank" class="w-8 h-8 rounded-full bg-sand-100 flex items-center justify-center hover:bg-brand-500 hover:text-white transition-colors"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Tipe Lensa -->
                <div class="space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900">Pilihan Lensa Klinis</h4>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-brand-600">Single Vision (Minus / Plus)</a></li>
                        <li><a href="{{ route('shop.index') }}" class="hover:text-brand-600">Anti Radiasi Blueray UV420</a></li>
                        <li><a href="{{ route('shop.index') }}" class="hover:text-brand-600">Photochromic Grey Transisi</a></li>
                        <li><a href="{{ route('shop.index') }}" class="hover:text-brand-600">Polarized UV400 Sunglasses</a></li>
                        <li><a href="{{ route('shop.index') }}" class="hover:text-brand-600">Progressive Multifocal Office</a></li>
                    </ul>
                </div>

                <!-- Layanan & Garansi -->
                <div class="space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900">Jaminan & Garansi</h4>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li><a href="{{ route('shop.track') }}" class="hover:text-brand-600">Pelacakan Pesanan Resi</a></li>
                        <li><span class="text-slate-500">Garansi Lensa Nyaman 30 Hari</span></li>
                        <li><span class="text-slate-500">Free Penyetelan Hidung & Baut</span></li>
                        <li><span class="text-slate-500">Free Cuci Kacamata Ultrasonik</span></li>
                        <li><a href="{{ route('login') }}" class="hover:text-brand-600 font-semibold text-brand-700">Masuk Portal Staf / POS</a></li>
                    </ul>
                </div>

                <!-- Store & Jam Operasional -->
                <div class="space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-900">Store & Laboratorium</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ $globalSettings['store_address'] ?? 'Batam • Tanjung Pinang • Bintan' }}<br>
                        <strong>Jam Operasional:</strong> {{ $globalSettings['footer_hours'] ?? '09.00 - 21.30 WIB' }}
                    </p>
                    <div class="pt-2">
                        <span class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                            <i class="fa-solid fa-circle text-[8px] text-emerald-500 animate-pulse"></i> Layanan Konsultasi Aktif
                        </span>
                    </div>
                </div>

            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>{{ $globalSettings['footer_copyright'] ?? '© 2026 Alysa Glasses & Eyewear Studio. Seluruh Hak Cipta Dilindungi.' }}</p>
                <div class="flex items-center gap-4">
                    <span>Clean Light Optical Theme</span>
                    <span>•</span>
                    <span>Production Grade Laravel 10</span>
                </div>
            </div>
        </div>
    </footer>


    <!-- Mobile Floating Bottom Bar for Quick Actions (User Friendly Mobile Experience) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-t border-sand-200 px-3 py-2 flex items-center justify-around shadow-[0_-4px_16px_rgba(0,0,0,0.06)]">
        <a href="{{ route('shop.index') }}" class="flex flex-col items-center gap-1 text-[10px] font-bold {{ request()->routeIs('shop.index') ? 'text-brand-600' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-store text-base"></i>
            <span>Katalog</span>
        </a>
        <a href="{{ route('shop.customer.portal') }}" class="flex flex-col items-center gap-1 text-[10px] font-bold {{ request()->routeIs('shop.customer.portal') ? 'text-brand-600' : 'text-slate-500 hover:text-slate-800' }}">
            <div class="relative">
                <i class="fa-solid fa-bag-shopping text-base"></i>
                <span class="absolute -top-1 -right-2 w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
            </div>
            <span>Pesan</span>
        </a>
        <a href="{{ route('shop.calendar') }}" class="flex flex-col items-center gap-1 text-[10px] font-bold {{ request()->routeIs('shop.calendar') ? 'text-amber-600' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-regular fa-calendar-check text-base"></i>
            <span>Promo</span>
        </a>
        <a href="{{ route('shop.track') }}" class="flex flex-col items-center gap-1 text-[10px] font-bold {{ request()->routeIs('shop.track') ? 'text-brand-600' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fa-solid fa-truck-fast text-base"></i>
            <span>Lacak</span>
        </a>
        @auth
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 text-[10px] font-bold text-emerald-600">
                <i class="fa-solid fa-user-gear text-base"></i>
                <span>Admin</span>
            </a>
        @else
            <a href="https://wa.me/{{ $waMobile }}?text=Halo%20Alysa%20Optical" target="_blank" class="flex flex-col items-center gap-1 text-[10px] font-bold text-emerald-600">
                <i class="fa-brands fa-whatsapp text-base"></i>
                <span>Chat WA</span>
            </a>
        @endauth
    </div>

    <!-- Tambahkan padding bawah di body agar konten tidak tertutup bottom bar di mobile -->
    <div class="h-14 md:hidden"></div>


    <!-- Mobile PWA Install Banner (Otomatis Muncul di Browser HP) -->
    <div id="pwa-install-banner" class="hidden md:hidden fixed top-3 left-3 right-3 z-[60] bg-white/95 backdrop-blur-md border border-brand-200 rounded-2xl p-3 shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <img src="/icons/icon-192.png" alt="App Icon" class="w-10 h-10 rounded-xl shadow-sm">
                <div>
                    <h5 class="text-xs font-bold text-slate-900 leading-tight">Pasang Aplikasi Alysa</h5>
                    <p class="text-[10px] text-slate-500">Akses cepat seperti aplikasi Android</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5">
                <button id="pwa-install-btn" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Pasang
                </button>
                <button id="pwa-close-btn" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        let deferredPrompt;
        const installBanner = document.getElementById('pwa-install-banner');
        const installBtn = document.getElementById('pwa-install-btn');
        const closeBtn = document.getElementById('pwa-close-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (!sessionStorage.getItem('pwa_dismissed')) {
                installBanner.classList.remove('hidden');
            }
        });

        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    deferredPrompt = null;
                    installBanner.classList.add('hidden');
                }
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                installBanner.classList.add('hidden');
                sessionStorage.setItem('pwa_dismissed', '1');
            });
        }
    </script>


    <!-- Scroll Position Restoration (Menjaga Posisi Scroll Layar Pasca-Reload / Eksekusi Form) -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Pulihkan posisi scroll jika tersimpan untuk path URL saat ini
            var scrollKey = 'scroll_pos_' + window.location.pathname;
            var savedPos = sessionStorage.getItem(scrollKey);
            if (savedPos !== null) {
                window.scrollTo({
                    top: parseInt(savedPos, 10),
                    behavior: 'instant'
                });
                sessionStorage.removeItem(scrollKey);
            }

            // Tangkap saat form di-submit untuk menyimpan posisi scroll
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function() {
                    sessionStorage.setItem(scrollKey, window.scrollY || window.pageYOffset);
                });
            });

            // Tangkap klik tombol form submit
            window.addEventListener('beforeunload', function() {
                // Simpan posisi terakhir sebelum berpindah/refresh
                if (sessionStorage.getItem(scrollKey + '_pending')) {
                    sessionStorage.setItem(scrollKey, window.scrollY || window.pageYOffset);
                    sessionStorage.removeItem(scrollKey + '_pending');
                }
            });
        });

        // Helper untuk menandai penyimpanan scroll sebelum eksekusi aksi
        window.saveScrollPos = function() {
            var scrollKey = 'scroll_pos_' + window.location.pathname;
            sessionStorage.setItem(scrollKey, window.scrollY || window.pageYOffset);
        };
    </script>

</body>
</html>
