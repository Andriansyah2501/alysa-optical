@extends('layouts.app')

@section('title', 'Tentang Alysa Glasses — Kontak, Layanan & FAQ | Alysa Glasses')
@section('meta_description', 'Kenali Alysa Glasses — toko kacamata modern Batam, Tanjung Pinang & Bintan. Kontak WhatsApp, Instagram, jam operasional, dan jawaban pertanyaan seputar order resep, lensa, pengiriman, garansi, dan lacak pesanan.')

@section('content')

    {{-- Hero Tentang --}}
    <section class="bg-gradient-to-b from-brand-100/70 via-sand-50 to-sand-100/50 py-14 lg:py-20 border-b border-sand-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-100 text-amber-900 text-xs font-extrabold tracking-wide border border-amber-200 shadow-sm">
                <i class="fa-solid fa-heart text-amber-600"></i> TENTANG KAMI
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 mt-5 leading-tight">
                {{ $settings['store_name'] }} —
                <span class="text-brand-600 italic font-medium">{{ $settings['tagline'] }}</span>
            </h1>
            <p class="text-slate-600 text-sm sm:text-base max-w-2xl mx-auto mt-4 leading-relaxed">
                {{ $settings['about_text'] }} Melayani {{ $settings['address'] }}.
            </p>
        </div>
    </section>

    {{-- Kartu Kontak --}}
    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white rounded-2xl border border-sand-200 shadow-sm p-6 text-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">WhatsApp</h3>
                    <p class="text-xs text-slate-500 mt-1 break-all">{{ $settings['phone'] ?: 'Konsultasi resep & order' }}</p>
                    <a href="{{ $settings['wa_link'] }}" target="_blank" rel="noopener"
                       class="inline-block mt-3 text-xs font-bold text-emerald-600 hover:text-emerald-700">Chat sekarang <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1"></i></a>
                </div>

                <div class="bg-white rounded-2xl border border-sand-200 shadow-sm p-6 text-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center mb-4">
                        <i class="fa-brands fa-instagram text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Instagram</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $settings['instagram'] }}</p>
                    <a href="{{ $settings['instagram_url'] }}" target="_blank" rel="noopener"
                       class="inline-block mt-3 text-xs font-bold text-pink-600 hover:text-pink-700">Ikuti <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1"></i></a>
                </div>

                <div class="bg-white rounded-2xl border border-sand-200 shadow-sm p-6 text-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-envelope text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Email</h3>
                    <p class="text-xs text-slate-500 mt-1 break-all">{{ $settings['email'] ?: '—' }}</p>
                    @if($settings['email'])
                        <a href="mailto:{{ $settings['email'] }}"
                           class="inline-block mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">Kirim email <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1"></i></a>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-sand-200 shadow-sm p-6 text-center hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-clock text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Jam Operasional</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $settings['hours'] }}</p>
                    @if($settings['tiktok'])
                        <a href="{{ $settings['tiktok'] }}" target="_blank" rel="noopener"
                           class="inline-block mt-3 text-xs font-bold text-slate-700 hover:text-slate-900">TikTok @alysa_glasses <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1"></i></a>
                    @endif
                </div>
            </div>

            {{-- Area layanan + channel WA --}}
            <div class="mt-8 bg-gradient-to-r from-brand-600 to-brand-700 rounded-3xl p-8 lg:p-10 text-white shadow-xl shadow-brand-500/20 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="text-center lg:text-left">
                    <h2 class="font-serif text-xl lg:text-2xl font-bold flex items-center justify-center lg:justify-start gap-3">
                        <i class="fa-solid fa-location-dot"></i> {{ $settings['address'] }}
                    </h2>
                    <p class="text-brand-100 text-sm mt-2">Order online 24 jam • Dikirim dengan pengemasan aman • Cek status pesanan lewat kode POS kamu.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('shop.customer.portal') }}" class="px-5 py-2.5 bg-white text-brand-700 rounded-2xl text-sm font-bold hover:bg-brand-50 transition-colors shadow">
                        <i class="fa-solid fa-cart-shopping mr-2"></i>Pesan Online
                    </a>
                    @if($settings['wa_channel'])
                        <a href="{{ $settings['wa_channel'] }}" target="_blank" rel="noopener" class="px-5 py-2.5 bg-brand-500/40 border border-white/30 text-white rounded-2xl text-sm font-bold hover:bg-brand-500/60 transition-colors">
                            <i class="fa-brands fa-whatsapp mr-2"></i>Channel WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="pb-14 lg:pb-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="font-serif text-2xl lg:text-3xl font-bold text-slate-900">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-slate-500 text-sm mt-2">Belum terjawab? Sampaikan lewat WhatsApp, tim kami siap bantu. 🫶🏻</p>
            </div>

            <div class="space-y-3">
                @foreach($faqs as $i => $faq)
                    <details class="group bg-white rounded-2xl border border-sand-200 shadow-sm open:shadow-md transition-shadow" {{ $i === 0 ? 'open' : '' }}>
                        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-5 py-4">
                            <span class="text-sm font-bold text-slate-900">{{ $faq['q'] }}</span>
                            <span class="shrink-0 w-7 h-7 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center group-open:rotate-45 transition-transform">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </span>
                        </summary>
                        <p class="px-5 pb-5 pt-1 text-sm text-slate-600 leading-relaxed border-t border-sand-100">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

@endsection
