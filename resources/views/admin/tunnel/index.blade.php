@extends('layouts.app')

@section('title', 'Manajemen Tunneling Cloudflare - Alysa Optical')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                    Cloudflare Zero Trust / Quick Tunnel
                </span>
                <span class="text-xs text-slate-400">• Akses Darurat & Remote Router</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-slate-900 mt-1">
                Pusat Akses Tunneling Server
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola, restart, dan pantau jalur tunneling Cloudflare publik saat terjadi gangguan akses internet atau pemadaman domain utama.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tunnel.index') }}" class="px-3.5 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl text-xs font-bold border border-sand-300 transition-colors flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-arrows-rotate text-brand-600"></i> Refresh Status Tunnel
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Session -->
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Panduan Pemulihan Darurat -->
    <div class="bg-amber-50/60 border border-amber-200/80 rounded-3xl p-5 text-xs text-amber-900 space-y-2">
        <div class="flex items-center gap-2 font-bold text-sm text-amber-950">
            <i class="fa-solid fa-shield-halved text-amber-600"></i>
            Prosedur Pemulihan Saat Akses Web Bermasalah / Terputus
        </div>
        <p class="text-amber-800/90 leading-relaxed">
            Jika domain utama <span class="font-mono font-bold">alysa.andrian-syah.my.id</span> tidak dapat diakses dari luar, tekan tombol <strong>"Restart Tunnel"</strong> pada baris Alysa Optical di bawah. Sistem akan secara otomatis menginisiasi ulang tunneling Cloudflare Quick Tunnel dan menyediakan URL darurat <span class="font-mono text-xs">*.trycloudflare.com</span>. Anda juga dapat menggunakan akses langsung LAN pada IP <strong>http://192.168.0.30:8084</strong>.
        </p>
    </div>

    <!-- TABEL MANAJEMEN TUNNEL -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-sand-200 flex items-center justify-between">
            <h3 class="font-serif font-bold text-base text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-tower-broadcast text-brand-600"></i> Daftar Layanan Tunnel Cloudflare
            </h3>
            <span class="text-xs text-slate-400 font-mono">Total: {{ count($tunnels) }} Service Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-sand-50/80 text-slate-500 font-bold uppercase tracking-wider border-b border-sand-200 text-[11px]">
                        <th class="py-3.5 px-5">Nama Layanan</th>
                        <th class="py-3.5 px-4">Port Lokal</th>
                        <th class="py-3.5 px-4">Status Layanan</th>
                        <th class="py-3.5 px-4">Domain Utama / Quick URL</th>
                        <th class="py-3.5 px-5 text-center">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100">
                    @forelse($tunnels as $tun)
                    <tr class="hover:bg-sand-50/50 transition-colors">
                        <!-- Nama Layanan -->
                        <td class="py-4 px-5">
                            <div class="font-bold text-slate-800 text-sm">{{ $tun['name'] }}</div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $tun['service'] }}</div>
                            <div class="text-[11px] text-slate-500 mt-1">{{ $tun['desc'] }}</div>
                        </td>

                        <!-- Port Lokal -->
                        <td class="py-4 px-4 font-mono font-bold text-slate-700">
                            <span class="px-2.5 py-1 bg-sand-100 border border-sand-300 rounded-lg">
                                :{{ $tun['port'] }}
                            </span>
                        </td>

                        <!-- Status Layanan -->
                        <td class="py-4 px-4">
                            @if($tun['is_active'])
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Berjalan (Active)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Berhenti (Inactive)
                                </span>
                            @endif
                        </td>

                        <!-- Domain & Quick URL -->
                        <td class="py-4 px-4 space-y-1.5">
                            @if($tun['custom_domain'])
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase w-12">Utama:</span>
                                    <a href="{{ $tun['custom_domain'] }}" target="_blank" class="font-mono text-brand-600 hover:text-brand-800 font-bold hover:underline flex items-center gap-1">
                                        {{ $tun['custom_domain'] }} <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                    </a>
                                </div>
                            @endif

                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold text-slate-400 uppercase w-12">Darurat:</span>
                                @if(str_starts_with($tun['quick_url'], 'http'))
                                    <a href="{{ $tun['quick_url'] }}" target="_blank" class="font-mono text-sky-600 hover:text-sky-800 font-bold hover:underline flex items-center gap-1">
                                        {{ $tun['quick_url'] }} <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic font-mono">{{ $tun['quick_url'] }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Aksi Kontrol (Start, Stop, Restart) -->
                        <td class="py-4 px-5 text-center">
                            <div class="inline-flex items-center gap-1 bg-sand-100 p-1 rounded-xl border border-sand-200">
                                @if($tun['is_active'])
                                    <form action="{{ route('admin.tunnel.control') }}" method="POST" class="inline" onsubmit="return confirm('Restart tunnel {{ $tun['name'] }}?');">
                                        @csrf
                                        <input type="hidden" name="service" value="{{ $tun['service'] }}">
                                        <input type="hidden" name="action" value="restart">
                                        <button type="submit" class="p-2 hover:bg-white text-amber-600 hover:text-amber-700 rounded-lg transition-colors font-bold text-xs" title="Restart Tunnel">
                                            <i class="fa-solid fa-arrows-rotate"></i> Restart
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.tunnel.control') }}" method="POST" class="inline" onsubmit="return confirm('Hentikan tunnel {{ $tun['name'] }}?');">
                                        @csrf
                                        <input type="hidden" name="service" value="{{ $tun['service'] }}">
                                        <input type="hidden" name="action" value="stop">
                                        <button type="submit" class="p-2 hover:bg-white text-rose-600 hover:text-rose-700 rounded-lg transition-colors font-bold text-xs" title="Stop Tunnel">
                                            <i class="fa-solid fa-stop"></i> Stop
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.tunnel.control') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="service" value="{{ $tun['service'] }}">
                                        <input type="hidden" name="action" value="start">
                                        <button type="submit" class="p-2 hover:bg-white text-emerald-600 hover:text-emerald-700 rounded-lg transition-colors font-bold text-xs" title="Start Tunnel">
                                            <i class="fa-solid fa-play"></i> Jalankan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">
                            Tidak ada konfigurasi tunnel yang terdeteksi di server.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
