@extends('layouts.app')

@section('title', 'Status Server & Koneksi Jaringan - Alysa Optical')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 uppercase tracking-wider">
                    Infrastructure & Telemetri
                </span>
                <span class="text-xs text-slate-400">• Node LXC 113 (hermes-ian)</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-slate-900 mt-1">
                Koneksi Server & Jaringan
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan real-time performa sistem, memori, disk, latensi gateway, port, dan status layanan web.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.server.index') }}" class="px-3.5 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl text-xs font-bold border border-sand-300 transition-colors flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-arrows-rotate text-brand-600"></i> Refresh Status
            </a>
        </div>
    </div>

    <!-- 3 KARTU HARDWARE METRICS (LOAD CPU, RAM, STORAGE) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- CPU Load -->
        <div class="bg-white p-5 rounded-3xl border border-sand-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Beban CPU (Load Avg)</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-microchip"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono text-slate-900">{{ $metrics['cpu_load_1m'] }}</span>
                <span class="text-xs text-slate-400 font-mono">(5m: {{ $metrics['cpu_load_5m'] }} | 15m: {{ $metrics['cpu_load_15m'] }})</span>
            </div>
            <div class="w-full bg-sand-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min(100, $metrics['cpu_load_1m'] * 25) }}%"></div>
            </div>
        </div>

        <!-- RAM Memory -->
        <div class="bg-white p-5 rounded-3xl border border-sand-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Penggunaan RAM</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-memory"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-bold font-mono text-slate-900">{{ $metrics['ram_used'] }}</span>
                <span class="text-xs text-slate-400 font-mono">Total: {{ $metrics['ram_total'] }} ({{ $metrics['ram_percent'] }}%)</span>
            </div>
            <div class="w-full bg-sand-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $metrics['ram_percent'] }}%"></div>
            </div>
        </div>

        <!-- Disk Storage -->
        <div class="bg-white p-5 rounded-3xl border border-sand-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kapasitas Disk Storage</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-hard-drive"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-bold font-mono text-slate-900">{{ $metrics['disk_used'] }}</span>
                <span class="text-xs text-slate-400 font-mono">Total: {{ $metrics['disk_total'] }} ({{ $metrics['disk_percent'] }}%)</span>
            </div>
            <div class="w-full bg-sand-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $metrics['disk_percent'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- 2 KOLOM: INFORMASI JARINGAN & STATUS SERVICE SYSTEM -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KIRI: INFORMASI JARINGAN & PING TEST (7 Kolom) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Identitas Jaringan & Host -->
            <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                <h3 class="font-serif font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-network-wired text-sky-600"></i> Identitas Jaringan Server
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">IP Address Lokal (LAN)</span>
                        <span class="text-sm font-mono font-bold text-slate-800">{{ $serverInfo['local_ip'] }}</span>
                    </div>

                    <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">IP Publik Server</span>
                        <span class="text-sm font-mono font-bold text-brand-700">{{ $serverInfo['public_ip'] }}</span>
                    </div>

                    <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Latensi Gateway (192.168.0.1)</span>
                        <span class="text-sm font-mono font-bold text-emerald-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle text-[8px] text-emerald-500"></i> {{ $network['gateway_ping'] }}
                        </span>
                    </div>

                    <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Koneksi Internet (Cloudflare DNS)</span>
                        <span class="text-sm font-mono font-bold text-emerald-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle text-[8px] text-emerald-500"></i> {{ $network['internet_ping'] }}
                        </span>
                    </div>

                    <div class="sm:col-span-2 p-3 bg-sand-50 rounded-2xl border border-sand-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Domain Cloudflare Tunnel</span>
                            <span class="text-xs font-mono font-bold text-slate-800">{{ $network['cloudflare_tunnel'] }}</span>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold">HTTPS Active</span>
                    </div>
                </div>

                <!-- Form Uji Ping Kustom -->
                <div class="pt-2 border-t border-sand-200">
                    <label class="font-bold text-slate-700 block text-xs mb-1.5">Uji Latensi Ping ke Host Lain</label>
                    <div class="flex gap-2">
                        <input type="text" id="pingTargetInput" value="google.com" placeholder="Contoh: 192.168.0.20 atau google.com" class="flex-1 px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white">
                        <button type="button" onclick="runPingTest()" id="btnRunPing" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-xs">
                            <i class="fa-solid fa-satellite-dish text-xs"></i> Uji Ping
                        </button>
                    </div>
                    <div id="pingResultBox" class="hidden mt-2 p-2.5 bg-sand-100 rounded-xl text-xs font-mono text-slate-800 flex items-center justify-between">
                        <span id="pingResultText">Menjalankan ping...</span>
                        <span id="pingStatusBadge" class="font-bold"></span>
                    </div>
                </div>
            </div>

            <!-- Detail Spesifikasi Server Host -->
            <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-3 text-xs">
                <h3 class="font-serif font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-server text-purple-600"></i> Detail Spesifikasi Host
                </h3>
                <div class="divide-y divide-sand-100">
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Hostname / Node:</span>
                        <span class="font-mono font-bold text-slate-800">{{ $serverInfo['hostname'] }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Kernel & OS:</span>
                        <span class="font-mono text-slate-800">{{ $serverInfo['os'] }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Masa Aktif Server (Uptime):</span>
                        <span class="font-semibold text-emerald-600">{{ $serverInfo['uptime'] }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Versi PHP & Framework:</span>
                        <span class="font-mono text-slate-800">PHP {{ $serverInfo['php_version'] }} / Laravel {{ $serverInfo['laravel_version'] }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Zona Waktu Sistem:</span>
                        <span class="font-semibold text-slate-800">{{ $serverInfo['timezone'] }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- KANAN: STATUS SERVICE SYSTEM & WEB ENDPOINTS (5 Kolom) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Status Service Systemd (Nginx, MariaDB, SSH, Docker) -->
            <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                <h3 class="font-serif font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-gears text-emerald-600"></i> Layanan Sistem Core (Daemons)
                </h3>

                <div class="space-y-2.5 text-xs">
                    @foreach($services as $svc)
                    <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full {{ $svc['is_ok'] ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                            <div>
                                <span class="font-bold text-slate-800 block">{{ $svc['name'] }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">Port: {{ $svc['port'] }}</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $svc['is_ok'] ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $svc['status'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Health Check Endpoint Microservices (Port 8000, 5678, 8083, 8081) -->
            <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                <h3 class="font-serif font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-satellite text-blue-600"></i> Responsivitas Port & Layanan Web
                </h3>

                <div class="space-y-2.5 text-xs">
                    @foreach($internalEndpoints as $ep)
                    <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800 block">{{ $ep['name'] }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $ep['url'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ep['is_up'] ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }} block">
                                HTTP {{ $ep['code'] }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono mt-0.5 block">{{ $ep['latency'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    async function runPingTest() {
        const target = document.getElementById('pingTargetInput').value.trim();
        const btn = document.getElementById('btnRunPing');
        const box = document.getElementById('pingResultBox');
        const text = document.getElementById('pingResultText');
        const badge = document.getElementById('pingStatusBadge');

        if (!target) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Ping...';
        box.classList.remove('hidden');
        text.textContent = 'Mengirim ICMP paket ke ' + target + '...';
        badge.textContent = '';

        try {
            const res = await fetch('{{ route("admin.server.ping") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ target: target })
            });

            const data = await res.json();
            if (res.ok) {
                text.textContent = 'Hasil Ping ke ' + data.target + ': ' + data.ping_ms;
                badge.className = data.status === 'Connected' ? 'font-bold text-emerald-600' : 'font-bold text-rose-600';
                badge.textContent = data.status;
            } else {
                text.textContent = data.error || 'Gagal melakukan uji ping.';
                badge.className = 'font-bold text-rose-600';
                badge.textContent = 'Error';
            }
        } catch (e) {
            text.textContent = 'Koneksi gagal: ' + e.message;
            badge.className = 'font-bold text-rose-600';
            badge.textContent = 'Failed';
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-satellite-dish text-xs"></i> Uji Ping';
    }
</script>
@endsection
