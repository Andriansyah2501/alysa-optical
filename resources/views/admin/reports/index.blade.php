@extends('layouts.app')

@section('title', 'Laporan Omset & Grafik Fluktuasi Penjualan | Alysa Glasses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-brand-100 text-brand-800 rounded-lg text-[10px] font-extrabold uppercase tracking-wider">Analytics & Financial Intelligence</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Data Realtime Kasir & Lab Optik</span>
            </div>
            <h1 class="font-serif text-xl sm:text-2xl font-bold text-slate-900 mt-0.5">Laporan Penjualan & Grafik Fluktuasi</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau performa omset harian, mingguan, bulanan, dan tahunan kacamata Alysa Glasses dengan visualisasi kurva naik-turun yang akurat.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Dropdown / Tombol Download PDF Cepat -->
                    <!-- Tombol Download Excel / CSV -->
        <a href="{{ route('admin.reports.excel', ['period' => request('period', 'monthly')]) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-bold shadow-md transition-all flex items-center gap-1.5" title="Export Spreadsheet Excel">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </a>
        <div class="relative inline-block text-left" id="pdfDropdownContainer">
                <button type="button" onclick="document.getElementById('pdfDropdownMenu').classList.toggle('hidden')" class="px-3 py-1.5 bg-rose-600 text-[11px] hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-file-pdf"></i> Download PDF Laporan <i class="fa-solid fa-chevron-down text-[10px] ml-1"></i>
                </button>
                <div id="pdfDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-sand-200 z-50 py-2 divide-y divide-sand-100 text-xs">
                    <div class="px-3 py-1.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        Pilih Periode PDF
                    </div>
                    <div class="py-1">
                        <a href="{{ route('admin.reports.pdf', ['period' => 'daily']) }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-700 font-bold transition-colors">
                            <i class="fa-solid fa-calendar-day text-rose-500 w-4"></i> Laporan Harian (Hari Ini)
                        </a>
                        <a href="{{ route('admin.reports.pdf', ['period' => 'weekly']) }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-700 font-bold transition-colors">
                            <i class="fa-solid fa-calendar-week text-emerald-500 w-4"></i> Laporan Mingguan (7 Hari)
                        </a>
                        <a href="{{ route('admin.reports.pdf', ['period' => 'monthly']) }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-700 font-bold transition-colors">
                            <i class="fa-solid fa-calendar text-sky-500 w-4"></i> Laporan Bulanan (Bulan Ini)
                        </a>
                        <a href="{{ route('admin.reports.pdf', ['period' => 'yearly']) }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-700 font-bold transition-colors">
                            <i class="fa-solid fa-chart-pie text-purple-500 w-4"></i> Laporan Tahunan (Tahun 2026)
                        </a>
                        <a href="{{ route('admin.reports.pdf', ['period' => 'all']) }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 hover:bg-sand-100 font-bold transition-colors">
                            <i class="fa-solid fa-layer-group text-slate-500 w-4"></i> Rekap Semua Transaksi
                        </a>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 bg-white text-[11px] border border-sand-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-sand-50 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-list-check text-brand-600"></i> Antrean Lab Optik
            </a>
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-cash-register"></i> Mesin POS Kasir
            </a>
        </div>
    </div>

    <!-- 4 KARTU STATISTIK UTAMA (Harian, Mingguan, Bulanan, Tahunan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Kartu 1: Harian -->
        <div class="p-5 bg-white rounded-3xl border border-sand-200 shadow-sm relative overflow-hidden group hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Laporan Hari Ini</span><a href="{{ route('admin.reports.pdf', ['period' => 'daily']) }}" class="text-[10px] font-bold text-rose-600 hover:underline flex items-center gap-1" title="Unduh PDF Harian"><i class="fa-solid fa-file-pdf"></i> PDF</a></div>
                <span class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-calendar-day"></i>
                </span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-slate-900 tracking-tight block">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</span>
                <div class="flex items-center justify-between text-xs mt-1.5 pt-2 border-t border-sand-100">
                    <span class="text-slate-500 font-semibold">{{ $dailyCount }} Transaksi</span>
                    <span class="font-bold {{ $dailyGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }} flex items-center gap-1 text-[11px]">
                        <i class="fa-solid {{ $dailyGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ $dailyGrowth > 0 ? '+' : '' }}{{ $dailyGrowth }}% vs kmrn
                    </span>
                </div>
            </div>
            <div class="absolute -right-3 -bottom-3 text-brand-500/5 text-6xl font-black pointer-events-none select-none">1D</div>
        </div>

        <!-- Kartu 2: Mingguan (7 Hari) -->
        <div class="p-5 bg-white rounded-3xl border border-sand-200 shadow-sm relative overflow-hidden group hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Laporan 7 Hari Terakhir</span><a href="{{ route('admin.reports.pdf', ['period' => 'weekly']) }}" class="text-[10px] font-bold text-rose-600 hover:underline flex items-center gap-1" title="Unduh PDF Mingguan"><i class="fa-solid fa-file-pdf"></i> PDF</a></div>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-calendar-week"></i>
                </span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-emerald-700 tracking-tight block">Rp {{ number_format($weeklyRevenue, 0, ',', '.') }}</span>
                <div class="flex items-center justify-between text-xs mt-1.5 pt-2 border-t border-sand-100">
                    <span class="text-slate-500 font-semibold">{{ $weeklyCount }} Transaksi</span>
                    <span class="font-bold {{ $weeklyGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }} flex items-center gap-1 text-[11px]">
                        <i class="fa-solid {{ $weeklyGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ $weeklyGrowth > 0 ? '+' : '' }}{{ $weeklyGrowth }}% vs 7hr lalu
                    </span>
                </div>
            </div>
            <div class="absolute -right-3 -bottom-3 text-emerald-500/5 text-6xl font-black pointer-events-none select-none">7D</div>
        </div>

        <!-- Kartu 3: Bulanan (Bulan Berjalan) -->
        <div class="p-5 bg-white rounded-3xl border border-sand-200 shadow-sm relative overflow-hidden group hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Laporan Bulan Ini</span><a href="{{ route('admin.reports.pdf', ['period' => 'monthly']) }}" class="text-[10px] font-bold text-rose-600 hover:underline flex items-center gap-1" title="Unduh PDF Bulanan"><i class="fa-solid fa-file-pdf"></i> PDF</a></div>
                <span class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-calendar"></i>
                </span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-sky-800 tracking-tight block">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</span>
                <div class="flex items-center justify-between text-xs mt-1.5 pt-2 border-t border-sand-100">
                    <span class="text-slate-500 font-semibold">{{ $monthlyCount }} Transaksi</span>
                    <span class="font-bold {{ $monthlyGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }} flex items-center gap-1 text-[11px]">
                        <i class="fa-solid {{ $monthlyGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ $monthlyGrowth > 0 ? '+' : '' }}{{ $monthlyGrowth }}% vs bln lalu
                    </span>
                </div>
            </div>
            <div class="absolute -right-3 -bottom-3 text-sky-500/5 text-6xl font-black pointer-events-none select-none">1M</div>
        </div>

        <!-- Kartu 4: Tahunan (Tahun 2026) -->
        <div class="p-5 bg-white rounded-3xl border border-sand-200 shadow-sm relative overflow-hidden group hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Omset Tahun 2026</span><a href="{{ route('admin.reports.pdf', ['period' => 'yearly']) }}" class="text-[10px] font-bold text-rose-600 hover:underline flex items-center gap-1" title="Unduh PDF Tahunan"><i class="fa-solid fa-file-pdf"></i> PDF</a></div>
                <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-purple-900 tracking-tight block">Rp {{ number_format($yearlyRevenue, 0, ',', '.') }}</span>
                <div class="flex items-center justify-between text-xs mt-1.5 pt-2 border-t border-sand-100">
                    <span class="text-slate-500 font-semibold">{{ $yearlyCount }} Transaksi</span>
                    <span class="font-bold text-amber-600 text-[11px] font-bold" title="Sisa tagihan pesanan DP">
                        Sisa DP: Rp {{ number_format($totalUnpaidDp, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <div class="absolute -right-3 -bottom-3 text-purple-500/5 text-6xl font-black pointer-events-none select-none">1Y</div>
        </div>

    </div>

    <!-- SECTION GRAFIK UTAMA REKAPITULASI DENGAN TABS (Harian, Mingguan, Bulanan, Tahunan) -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sand-200">
            <div>
                <h2 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-brand-600"></i> Kurva Fluktuasi Naik-Turun Omset
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Visualisasi lonjakan dan penurunan penjualan kacamata serta volume transaksi optik.</p>
            </div>

            <!-- Tab Selector Grafik Periode -->
            <div class="flex p-1 bg-sand-100 rounded-2xl gap-1 text-xs font-bold text-slate-600">
                <button type="button" onclick="switchChartTab('daily')" id="btnTabDaily" class="chart-tab px-3.5 py-1.5 rounded-xl transition-all">
                    Harian (Jam)
                </button>
                <button type="button" onclick="switchChartTab('weekly')" id="btnTabWeekly" class="chart-tab px-3.5 py-1.5 rounded-xl transition-all active-chart-tab bg-white text-brand-700 shadow-sm">
                    Mingguan (7 Hari)
                </button>
                <button type="button" onclick="switchChartTab('monthly')" id="btnTabMonthly" class="chart-tab px-3.5 py-1.5 rounded-xl transition-all">
                    Bulanan (1-30)
                </button>
                <button type="button" onclick="switchChartTab('yearly')" id="btnTabYearly" class="chart-tab px-3.5 py-1.5 rounded-xl transition-all">
                    Tahunan (12 Bln)
                </button>
            </div>
        </div>

        <!-- Canvas Container Chart.js -->
        <div class="relative w-full h-80 sm:h-96">
            <canvas id="salesTrendChart"></canvas>
        </div>

        <!-- Indikator Keterangan Grafik -->
        <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 pt-3 border-t border-sand-100">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-md bg-brand-600 inline-block shadow-sm"></span>
                    <span class="font-bold text-slate-700">Omset Penjualan (Rp)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-md bg-amber-400 inline-block shadow-sm"></span>
                    <span class="font-bold text-slate-700">Volume Transaksi (Qty Order)</span>
                </div>
            </div>
            <span class="text-[11px] text-slate-400 italic">*Grafik reaktif menampilkan kurva spline naik-turun dan volume kacamata terjual.</span>
        </div>

    </div>

    <!-- 2 KOLOM BREAKDOWN (Breakdown Jenis Transaksi & Paket Lensa Terlaris) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Breakdown Tipe Transaksi Kasir -->
        <div class="p-6 bg-white rounded-3xl border border-sand-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-shapes text-brand-600"></i> Distribusi Tipe Transaksi Optik
            </h3>
            <p class="text-xs text-slate-500">Perbandingan transaksi pembelian katalog frame, custom frame, ganti lensa saja, dan upah servis reparasi.</p>
            
            <div class="space-y-3 pt-2">
                @php
                    $allTypesCount = max(1, $catalogCount + $customFrameCount + $lensOnlyCount + $serviceOnlyCount);
                @endphp
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-700">Katalog Frame Toko</span>
                        <span class="text-brand-700">{{ $catalogCount }} order ({{ round(($catalogCount/$allTypesCount)*100) }}%)</span>
                    </div>
                    <div class="w-full bg-sand-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-brand-600 h-full rounded-full transition-all" style="width: {{ ($catalogCount/$allTypesCount)*100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-700">Custom Frame & Model Bebas</span>
                        <span class="text-sky-700">{{ $customFrameCount }} order ({{ round(($customFrameCount/$allTypesCount)*100) }}%)</span>
                    </div>
                    <div class="w-full bg-sand-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-sky-500 h-full rounded-full transition-all" style="width: {{ ($customFrameCount/$allTypesCount)*100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-700">Ganti Kaca / Pasang Lensa Saja</span>
                        <span class="text-emerald-700">{{ $lensOnlyCount }} order ({{ round(($lensOnlyCount/$allTypesCount)*100) }}%)</span>
                    </div>
                    <div class="w-full bg-sand-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ ($lensOnlyCount/$allTypesCount)*100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-700">Upah Jasa Servis / Reparasi</span>
                        <span class="text-amber-700">{{ $serviceOnlyCount }} order ({{ round(($serviceOnlyCount/$allTypesCount)*100) }}%)</span>
                    </div>
                    <div class="w-full bg-sand-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all" style="width: {{ ($serviceOnlyCount/$allTypesCount)*100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paket Lensa Paling Populer (11.11 Promo & Digital) -->
        <div class="p-6 bg-white rounded-3xl border border-sand-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-sand-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-glasses text-brand-600"></i> Top 5 Paket Lensa Paling Diminati
                    </h3>
                    <p class="text-xs text-slate-500">Peringkat paket lensa resep yang paling sering dipesan oleh pelanggan toko & online.</p>
                </div>
                <!-- Tombol Download PDF Langsung untuk Laporan Lensa -->
                <a href="{{ route('admin.reports.pdf', ['period' => request('period', 'monthly'), 'type' => 'lensa']) }}" target="_blank" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-bold border border-rose-200 transition-all flex items-center gap-1.5 shadow-sm" title="Download PDF Rekap Paket Lensa">
                    <i class="fa-solid fa-file-pdf text-rose-600"></i> Download PDF
                </a>
            </div>

            <div class="divide-y divide-sand-100 text-xs">
                @forelse($topLenses as $index => $lens)
                    <div class="py-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-sand-100 text-slate-700 font-extrabold flex items-center justify-center text-[11px]">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <span class="font-bold text-slate-800 block">{{ $lens->lens_type_name }}</span>
                                <span class="text-[10px] text-slate-400">Total Nilai Lensa: Rp {{ number_format($lens->total_lens_rev, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 font-black rounded-lg text-xs">
                            {{ $lens->count }}x Terpasang
                        </span>
                    </div>
                @empty
                    <div class="py-4 text-center text-slate-400 italic">Belum ada data paket lensa terpasang.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- FILTER & REKAP TABEL TRANSAKSI PENJUALAN -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-6">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-sand-200">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-brand-600"></i> Rekap Riwayat Transaksi Optik
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Filter transaksi berdasarkan periode harian, mingguan, bulanan, tahunan, atau tanggal kustom.</p>
            </div>

            <!-- Tombol Filter Cepat -->
            <div class="flex flex-wrap items-center gap-1.5">
                <a href="{{ route('admin.reports.index', ['period' => 'all']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterPeriod === 'all' ? 'bg-brand-600 text-white shadow-sm' : 'bg-sand-100 text-slate-700 hover:bg-sand-200' }}">
                    Semua
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'today']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterPeriod === 'today' ? 'bg-brand-600 text-white shadow-sm' : 'bg-sand-100 text-slate-700 hover:bg-sand-200' }}">
                    Hari Ini
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'weekly']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterPeriod === 'weekly' ? 'bg-brand-600 text-white shadow-sm' : 'bg-sand-100 text-slate-700 hover:bg-sand-200' }}">
                    7 Hari
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'monthly']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterPeriod === 'monthly' ? 'bg-brand-600 text-white shadow-sm' : 'bg-sand-100 text-slate-700 hover:bg-sand-200' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('admin.reports.index', ['period' => 'yearly']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterPeriod === 'yearly' ? 'bg-brand-600 text-white shadow-sm' : 'bg-sand-100 text-slate-700 hover:bg-sand-200' }}">
                    Tahun 2026
                </a>
            </div>
        </div>

        <!-- Form Filter Custom Date Range -->
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-center gap-3 p-3.5 bg-sand-50 rounded-2xl border border-sand-200 text-xs">
            <span class="font-bold text-slate-700"><i class="fa-solid fa-filter mr-1"></i> Rentang Tanggal Khusus:</span>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-1.5 bg-white border border-sand-300 rounded-xl text-xs font-medium focus:ring-1 focus:ring-brand-500">
            <span class="text-slate-400 font-bold">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-1.5 bg-white border border-sand-300 rounded-xl text-xs font-medium focus:ring-1 focus:ring-brand-500">
            <button type="submit" class="px-4 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-sm transition-all">
                Terapkan Filter
            </button>
            @if(request()->has('start_date') || $filterPeriod !== 'all')
                <a href="{{ route('admin.reports.index') }}" class="px-3 py-1.5 text-slate-500 hover:text-rose-600 font-bold">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset Filter
                </a>
            @endif

            <div class="ml-auto flex items-center gap-3 text-xs font-bold">
                <!-- Tombol Unduh PDF Sesuai Filter Aktif -->
                @php
                    $pdfQuery = ['period' => $filterPeriod];
                    if(request('start_date') && request('end_date')) {
                        $pdfQuery['start_date'] = request('start_date');
                        $pdfQuery['end_date'] = request('end_date');
                    }
                @endphp
                <a href="{{ route('admin.reports.pdf', $pdfQuery) }}" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-sm flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-file-pdf"></i> Unduh PDF Filter Ini
                </a>
                <span class="text-slate-600">Total Ditemukan: <strong class="text-slate-900">{{ $filteredTotalOrders }} Order</strong></span>
                <span class="text-slate-600">Total Omset: <strong class="text-emerald-700 text-sm">Rp {{ number_format($filteredTotalRevenue, 0, ',', '.') }}</strong></span>
            </div>
        </form>

        <!-- Tabel Transaksi -->
        <div class="overflow-x-auto rounded-2xl border border-sand-200">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-sand-100 text-slate-700 font-extrabold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">No. Order & Tanggal</th>
                        <th class="py-3 px-4">Pelanggan & Kontak</th>
                        <th class="py-3 px-4">Tipe & Rincian Pembelian</th>
                        <th class="py-3 px-4">Lensa / Resep</th>
                        <th class="py-3 px-4 text-right">Total Transaksi</th>
                        <th class="py-3 px-4 text-center">Status Pembayaran</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 font-medium text-slate-700">
                    @forelse($filteredOrders as $order)
                        <tr class="hover:bg-sand-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.order.detail', $order->id) }}" class="font-extrabold text-brand-700 hover:underline">
                                    {{ $order->order_code }}
                                </a>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $order->created_at->format('d M Y, H:i') }} WIB
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $order->customer_name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $order->customer_phone ?: 'Walk-in' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                    @if($order->transaction_type == 'catalog') bg-blue-50 text-blue-700
                                    @elseif($order->transaction_type == 'custom_frame') bg-sky-50 text-sky-700
                                    @elseif($order->transaction_type == 'lens_only') bg-emerald-50 text-emerald-700
                                    @else bg-amber-50 text-amber-700 @endif">
                                    {{ strtoupper($order->transaction_type ?: 'CATALOG') }}
                                </span>
                                <div class="text-xs text-slate-800 font-semibold mt-1">
                                    @foreach($order->items as $it)
                                        {{ $it->product_name }}@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($order->lens_type_name)
                                    <div class="font-bold text-slate-800">{{ $order->lens_type_name }}</div>
                                    @if($order->has_prescription)
                                        <div class="text-[10px] text-brand-700 font-semibold">
                                            R: {{ $order->sph_r ?: '0' }}/{{ $order->cyl_r ?: '0' }} | L: {{ $order->sph_l ?: '0' }}/{{ $order->cyl_l ?: '0' }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Tanpa Lensa</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="font-extrabold text-slate-900 text-sm">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-slate-400">{{ $order->payment_method }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($order->payment_type === 'dp' && $order->remaining_balance > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                                        DP Rp {{ number_format($order->dp_amount, 0, ',', '.') }}
                                    </span>
                                    <div class="text-[9px] text-rose-600 font-bold mt-0.5">Sisa: Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</div>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                        Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('pos.receipt', $order->order_code) }}" target="_blank" class="p-1.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-lg text-xs font-bold" title="Cetak Struk">
                                        <i class="fa-solid fa-receipt"></i>
                                    </a>
                                    <a href="{{ route('admin.order.detail', $order->id) }}" class="p-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg text-xs font-bold" title="Detail Validasi Order">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                                Tidak ditemukan data transaksi pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $filteredOrders->links() }}
        </div>

    </div>

</div>

<!-- SCRIPT CHART.JS UNTUK GRAFIK NAIK TURUN (FLUKTUASI) OMSET & VOLUME -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data Server
    const chartDatasets = {
        daily: {
            labels: {!! json_encode($hourlyLabels) !!},
            revenue: {!! json_encode($hourlyRevenue) !!},
            volume: {!! json_encode($hourlyVolume) !!},
            title: 'Fluktuasi Omset Per Jam (Hari Ini)'
        },
        weekly: {
            labels: {!! json_encode($daily7Labels) !!},
            revenue: {!! json_encode($daily7Revenue) !!},
            volume: {!! json_encode($daily7Volume) !!},
            title: 'Fluktuasi Omset 7 Hari Terakhir'
        },
        monthly: {
            labels: {!! json_encode($monthDaysLabels) !!},
            revenue: {!! json_encode($monthDaysRevenue) !!},
            volume: {!! json_encode($monthDaysVolume) !!},
            title: 'Fluktuasi Omset Harian Sepanjang Bulan Berjalan'
        },
        yearly: {
            labels: {!! json_encode($yearMonthsLabels) !!},
            revenue: {!! json_encode($year2026Revenue) !!},
            revenuePrev: {!! json_encode($year2025Revenue) !!},
            volume: [4, 5, 6, 8, 10, 14, 16, 19, 21, 24, 28, 0],
            title: 'Tren Omset Tahunan (2026 vs 2025)'
        }
    };

    let activePeriod = 'weekly';
    const ctx = document.getElementById('salesTrendChart').getContext('2d');

    // Gradient warna kurva naik turun
    const brandGradient = ctx.createLinearGradient(0, 0, 0, 350);
    brandGradient.addColorStop(0, 'rgba(194, 91, 58, 0.45)');
    brandGradient.addColorStop(1, 'rgba(194, 91, 58, 0.02)');

    const config = {
        type: 'line',
        data: {
            labels: chartDatasets.weekly.labels,
            datasets: [
                {
                    label: 'Omset Penjualan (Rp)',
                    data: chartDatasets.weekly.revenue,
                    borderColor: '#c25b3a',
                    backgroundColor: brandGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.38, // Curvature spline yang halus dan elegan
                    pointBackgroundColor: '#c25b3a',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y'
                },
                {
                    label: 'Volume Order (Qty)',
                    data: chartDatasets.weekly.volume,
                    type: 'bar',
                    backgroundColor: 'rgba(245, 158, 11, 0.35)',
                    borderColor: 'rgba(245, 158, 11, 0.8)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    barThickness: 16,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false // Menggunakan legend kustom di atas
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.yAxisID === 'y') {
                                return 'Omset: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            } else {
                                return 'Volume: ' + context.raw + ' Transaksi';
                            }
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { size: 11, weight: 'bold' },
                        color: '#64748b'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: function(val) {
                            if (val >= 1000000) return 'Rp ' + (val / 1000000) + ' Jt';
                            if (val >= 1000) return 'Rp ' + (val / 1000) + ' Rb';
                            return 'Rp ' + val;
                        },
                        font: { size: 10 },
                        color: '#64748b'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 },
                        color: '#94a3b8'
                    }
                }
            }
        }
    };

    let salesChart = new Chart(ctx, config);

    function switchChartTab(period) {
        activePeriod = period;
        
        // Update tab styles
        document.querySelectorAll('.chart-tab').forEach(b => {
            b.classList.remove('bg-white', 'text-brand-700', 'shadow-sm');
            b.classList.add('text-slate-600');
        });

        const activeBtn = document.getElementById('btnTab' + period.charAt(0).toUpperCase() + period.slice(1));
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-600');
            activeBtn.classList.add('bg-white', 'text-brand-700', 'shadow-sm');
        }

        const data = chartDatasets[period];
        salesChart.data.labels = data.labels;
        salesChart.data.datasets[0].data = data.revenue;
        salesChart.data.datasets[1].data = data.volume;

        if (period === 'yearly' && data.revenuePrev) {
            // Bisa menampilkan line kedua untuk tahun 2025
            if (salesChart.data.datasets.length === 2) {
                salesChart.data.datasets.push({
                    label: 'Tahun Lalu (2025)',
                    data: data.revenuePrev,
                    borderColor: '#94a3b8',
                    borderDash: [5, 5],
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 3,
                    yAxisID: 'y'
                });
            } else {
                salesChart.data.datasets[2].data = data.revenuePrev;
            }
        } else {
            if (salesChart.data.datasets.length > 2) {
                salesChart.data.datasets.pop();
            }
        }

        salesChart.update();
    }
</script>
@endsection
