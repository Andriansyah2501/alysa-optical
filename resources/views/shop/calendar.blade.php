@extends('layouts.app')

@section('title', 'Kalender Promo & Flash Sale Kacamata | Alysa Glasses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Hero Header -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-rose-50 border border-rose-200 rounded-full text-rose-700 text-xs font-extrabold uppercase tracking-wider">
            <i class="fa-solid fa-fire text-amber-500 animate-pulse"></i> Jadwal Promo & Event Optik
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-slate-900 tracking-tight">
            Kalender Promo Spesial <span class="text-brand-600">Alysa Glasses</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            Catat tanggalnya! Dapatkan diskon paket lensa, free frame ultralight, gratis upah potong lensa, serta cashback voucher eksklusif sepanjang bulan.
        </p>
    </div>

    <!-- Navigasi Bulan & Tahun -->
    @php
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();
    @endphp
    <div class="flex items-center justify-between p-4 bg-white rounded-3xl border border-sand-200 shadow-sm">
        <a href="{{ route('shop.calendar', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="px-4 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold text-xs flex items-center gap-2 transition-all">
            <i class="fa-solid fa-chevron-left"></i> {{ $prevMonth->translatedFormat('F Y') }}
        </a>

        <div class="text-center">
            <h2 class="font-serif text-xl sm:text-2xl font-bold text-slate-900">
                {{ $currentDate->translatedFormat('F Y') }}
            </h2>
            <span class="text-[11px] font-semibold text-slate-400">Jadwal Event Aktif</span>
        </div>

        <a href="{{ route('shop.calendar', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="px-4 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold text-xs flex items-center gap-2 transition-all">
            {{ $nextMonth->translatedFormat('F Y') }} <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>

    <!-- KALENDER GRID VIEW (7 HARI MINGGU-SABTU) -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
        <!-- Header Hari -->
        <div class="grid grid-cols-7 border-b border-sand-200 bg-sand-50/80 text-center text-xs font-black uppercase text-slate-600 py-3">
            <span class="text-rose-600">Minggu</span>
            <span>Senin</span>
            <span>Selasa</span>
            <span>Rabu</span>
            <span>Kamis</span>
            <span>Jumat</span>
            <span class="text-brand-600">Sabtu</span>
        </div>

        <!-- Grid Tanggal -->
        <div class="grid grid-cols-7 auto-rows-fr divide-x divide-y divide-sand-100 min-h-[500px]">
            @php
                $dayCounter = 1;
                $todayDate = \Carbon\Carbon::today();
            @endphp

            <!-- Kotak kosong sebelum tanggal 1 -->
            @for ($i = 0; $i < $startDayOfWeek; $i++)
                <div class="bg-sand-50/30 p-2 min-h-[95px]"></div>
            @endfor

            <!-- Tanggal 1 sampai akhir bulan -->
            @while ($dayCounter <= $daysInMonth)
                @php
                    $thisDate = $currentDate->copy()->setDay($dayCounter);
                    $isToday = $thisDate->isSameDay($todayDate);
                    
                    // Filter event yang aktif di tanggal ini
                    $activeEventsToday = $events->filter(function($ev) use ($thisDate) {
                        return $thisDate->between($ev->start_date, $ev->end_date);
                    });
                @endphp

                <div class="p-2 sm:p-2.5 min-h-[105px] flex flex-col justify-between transition-colors {{ $isToday ? 'bg-brand-50/40 ring-2 ring-inset ring-brand-400' : 'hover:bg-sand-50/40' }}">
                    <div class="flex items-center justify-between">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold {{ $isToday ? 'bg-brand-600 text-white' : ($thisDate->isWeekend() ? 'text-rose-600' : 'text-slate-800') }}">
                            {{ $dayCounter }}
                        </span>
                        @if($isToday)
                            <span class="text-[9px] font-black text-brand-700 bg-brand-100 px-1.5 py-0.5 rounded">Hari Ini</span>
                        @endif
                    </div>

                    <!-- Event Badges di Tanggal Tersebut -->
                    <div class="mt-1 space-y-1 overflow-hidden">
                        @foreach($activeEventsToday as $ev)
                            @php
                                $badgeColor = [
                                    'rose' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'sky' => 'bg-sky-100 text-sky-800 border-sky-200',
                                    'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
                                ][$ev->color_theme] ?? 'bg-rose-100 text-rose-800 border-rose-200';
                            @endphp
                            <div class="text-[9px] font-extrabold px-1.5 py-1 rounded-md border truncate {{ $badgeColor }}" title="{{ $ev->title }}: {{ $ev->discount_info }}">
                                <i class="fa-solid fa-tag text-[8px] mr-0.5"></i> {{ $ev->badge }}
                            </div>
                        @endforeach
                    </div>
                </div>

                @php $dayCounter++; @endphp
            @endwhile

            <!-- Kotak kosong setelah tanggal terakhir -->
            @php
                $remainingBoxes = (7 - (($startDayOfWeek + $daysInMonth) % 7)) % 7;
            @endphp
            @for ($j = 0; $j < $remainingBoxes; $j++)
                <div class="bg-sand-50/30 p-2 min-h-[95px]"></div>
            @endfor
        </div>
    </div>

    <!-- KARTU DAFTAR DETAIL PROMO AKTIF BULAN INI -->
    <div class="space-y-4">
        <h3 class="font-serif text-2xl font-bold text-slate-900 flex items-center gap-2">
            <i class="fa-solid fa-gift text-brand-600"></i> Rincian Penawaran & Promo Mendatang
        </h3>
        <p class="text-xs text-slate-500">Klik klaim voucher atau tunjukkan halaman ini ke kasir toko saat bertransaksi.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 pt-2">
            @forelse($allUpcomingEvents as $ev)
                @php
                    $themeBorder = [
                        'rose' => 'border-rose-300 hover:border-rose-400',
                        'amber' => 'border-amber-300 hover:border-amber-400',
                        'emerald' => 'border-emerald-300 hover:border-emerald-400',
                        'sky' => 'border-sky-300 hover:border-sky-400',
                        'purple' => 'border-purple-300 hover:border-purple-400',
                    ][$ev->color_theme] ?? 'border-sand-200';

                    $badgeBg = [
                        'rose' => 'bg-rose-600',
                        'amber' => 'bg-amber-600',
                        'emerald' => 'bg-emerald-600',
                        'sky' => 'bg-sky-600',
                        'purple' => 'bg-purple-600',
                    ][$ev->color_theme] ?? 'bg-brand-600';
                @endphp
                <div class="bg-white rounded-3xl border {{ $themeBorder }} p-5 shadow-sm flex flex-col justify-between hover:-translate-y-1 transition-all">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 text-white text-[10px] font-black uppercase tracking-wider rounded-lg {{ $badgeBg }}">
                                {{ $ev->badge }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400">
                                {{ $ev->start_date->format('d M') }} - {{ $ev->end_date->format('d M Y') }}
                            </span>
                        </div>

                        <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ $ev->title }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $ev->description }}</p>

                        <div class="p-2.5 bg-sand-50 rounded-xl border border-sand-200 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-600">Benefit:</span>
                            <span class="text-emerald-700 font-extrabold">{{ $ev->discount_info }}</span>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-sand-100 flex items-center justify-between">
                        @if($ev->voucher_code)
                            <div class="flex items-center gap-1.5 bg-sand-100 px-2.5 py-1 rounded-lg">
                                <span class="text-[9px] text-slate-400 font-bold">Kode:</span>
                                <span class="font-mono text-xs font-black text-brand-700">{{ $ev->voucher_code }}</span>
                            </div>
                        @else
                            <span class="text-[10px] text-slate-400 italic">Otomatis di Toko</span>
                        @endif

                        <a href="{{ route('shop.index') }}" class="px-3 py-1.5 bg-slate-900 hover:bg-brand-600 text-white rounded-xl text-xs font-bold transition-colors">
                            Belanja
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-4 p-8 bg-white rounded-3xl border border-sand-200 text-center text-slate-400">
                    Belum ada event promo terjadwal.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
