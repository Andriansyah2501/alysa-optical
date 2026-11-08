<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class TunnelManagerController extends Controller
{
    /**
     * Daftar service tunnel yang dikelola di server
     */
    protected $tunnelList = [
        [
            'id' => 'alysa',
            'name' => 'Alysa Optical E-Commerce',
            'service' => 'cloudflared-alysa.service',
            'port' => '8084',
            'custom_domain' => 'https://alysa.andrian-syah.my.id',
            'desc' => 'Tunnel utama toko online, kasir POS & katalog kacamata'
        ],
        [
            'id' => 'portal',
            'name' => 'Portal Lab ITEBA',
            'service' => 'cloudflared-portal.service',
            'port' => '8000',
            'custom_domain' => 'https://portal.andrian-syah.my.id',
            'desc' => 'Sistem peminjaman alat & praktikum laboratorium ITEBA'
        ],
        [
            'id' => 'n8n',
            'name' => 'n8n Automation Engine',
            'service' => 'cloudflared-n8n.service',
            'port' => '5678',
            'custom_domain' => 'https://n8n.andrian-syah.my.id',
            'desc' => 'Otomasi webhook, notifikasi WhatsApp & alur kerja sistem'
        ],
        [
            'id' => 'obsidian',
            'name' => 'Obsidian Web Vault',
            'service' => 'cloudflared-obsidian.service',
            'port' => '8083',
            'custom_domain' => null,
            'desc' => 'Pencatatan riset, dokumentasi lab & rekam medis keinsinyuran'
        ],
        [
            'id' => 'research',
            'name' => 'SmartPepper IoT Farm',
            'service' => 'cloudflared-research.service',
            'port' => '8081',
            'custom_domain' => null,
            'desc' => 'Sensor telemetri pertanian presisi & kontrol aktuator IoT'
        ],
    ];

    /**
     * Tampilkan Halaman Manajemen Tunnel Cloudflare
     */
    public function index()
    {
        $tunnels = [];

        foreach ($this->tunnelList as $t) {
            $serviceName = $t['service'];
            $isActive = trim(shell_exec("systemctl is-active {$serviceName}") ?? '') === 'active';

            // Ambil URL Quick Tunnel dari journalctl
            $quickUrl = 'Nonaktif';
            if ($isActive) {
                $journal = shell_exec("journalctl -u {$serviceName} -n 60 --no-pager 2>/dev/null") ?? '';
                if (preg_match_all('/https:\/\/[a-zA-Z0-9-]+\.trycloudflare\.com/', $journal, $matches)) {
                    $quickUrl = end($matches[0]);
                } else {
                    $quickUrl = 'Tunnel Terhubung (Sedang Inisialisasi DNS)';
                }
            }

            $tunnels[] = [
                'id' => $t['id'],
                'name' => $t['name'],
                'service' => $serviceName,
                'port' => $t['port'],
                'custom_domain' => $t['custom_domain'],
                'quick_url' => $quickUrl,
                'is_active' => $isActive,
                'desc' => $t['desc'],
            ];
        }

        return view('admin.tunnel.index', compact('tunnels'));
    }

    /**
     * Kontrol Tunnel (Start / Stop / Restart)
     */
    public function control(Request $request)
    {
        $service = $request->input('service');
        $action = $request->input('action'); // start, stop, restart

        // Keamanan: Validasi nama service agar hanya service yang terdaftar
        $validServices = array_column($this->tunnelList, 'service');
        if (!in_array($service, $validServices)) {
            return redirect()->back()->with('error', 'Nama service tunnel tidak diizinkan.');
        }

        if (!in_array($action, ['start', 'stop', 'restart'])) {
            return redirect()->back()->with('error', 'Aksi tunnel tidak valid.');
        }

        // Eksekusi systemctl
        $cmd = "systemctl {$action} {$service} 2>&1";
        exec($cmd, $output, $returnCode);

        // Jika start/restart, beri jeda 3 detik agar URL tunnel terbentuk
        if (in_array($action, ['start', 'restart'])) {
            sleep(3);
        }

        $actionVerb = ($action === 'start') ? 'dijalankan' : (($action === 'stop') ? 'dihentikan' : 'direstart');
        
        if ($returnCode === 0) {
            return redirect()->route('admin.tunnel.index')->with('success', "Layanan {$service} berhasil {$actionVerb}!");
        } else {
            return redirect()->route('admin.tunnel.index')->with('error', "Gagal {$actionVerb} {$service}: " . implode(' ', $output));
        }
    }
}
