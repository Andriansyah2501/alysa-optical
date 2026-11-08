<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class ServerNetworkController extends Controller
{
    /**
     * Tampilkan Dasbor Monitoring Server, Jaringan & Layanan Web
     */
    public function index()
    {
        // 1. Informasi Host & OS
        $serverInfo = [
            'hostname' => gethostname(),
            'os' => php_uname('s') . ' ' . php_uname('r') . ' (' . php_uname('m') . ')',
            'uptime' => $this->getCommandOutput('uptime -p') ?: 'N/A',
            'local_ip' => $this->getCommandOutput("hostname -I | awk '{print $1}'") ?: '127.0.0.1',
            'public_ip' => $this->getPublicIp(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone') . ' (' . date('d M Y H:i:s') . ' WIB)',
        ];

        // 2. Utilisasi Hardware (CPU, RAM, DISK)
        $cpuLoad = sys_getloadavg();
        $memInfo = $this->getMemoryUsage();
        $diskInfo = $this->getDiskUsage();

        $metrics = [
            'cpu_load_1m' => $cpuLoad[0] ?? 0.0,
            'cpu_load_5m' => $cpuLoad[1] ?? 0.0,
            'cpu_load_15m' => $cpuLoad[2] ?? 0.0,
            'ram_used' => $memInfo['used'] ?? '0 MB',
            'ram_total' => $memInfo['total'] ?? '0 MB',
            'ram_percent' => $memInfo['percent'] ?? 0,
            'disk_used' => $diskInfo['used'] ?? '0 GB',
            'disk_total' => $diskInfo['total'] ?? '0 GB',
            'disk_percent' => $diskInfo['percent'] ?? 0,
        ];

        // 3. Status Layanan Internal (System Daemons)
        $services = [
            ['name' => 'Nginx Web Server', 'cmd' => 'systemctl is-active nginx', 'port' => '80/443'],
            ['name' => 'MariaDB Database', 'cmd' => 'systemctl is-active mariadb', 'port' => '3306'],
            ['name' => 'PHP-FPM Daemon', 'cmd' => 'systemctl is-active php8.1-fpm', 'port' => 'Socket'],
            ['name' => 'OpenSSH Server', 'cmd' => 'systemctl is-active ssh', 'port' => '22'],
            ['name' => 'Docker Engine', 'cmd' => 'systemctl is-active docker', 'port' => 'Daemon'],
        ];

        foreach ($services as &$svc) {
            $status = $this->getCommandOutput($svc['cmd']);
            $svc['status'] = ($status === 'active') ? 'Online' : 'Offline';
            $svc['is_ok'] = ($status === 'active');
        }

        // 4. Status Port & Microservices Lokal
        $internalEndpoints = [
            ['name' => 'Alysa Optical (Web)', 'url' => 'http://127.0.0.1:8084'],
            ['name' => 'Portal Lab ITEBA', 'url' => 'http://127.0.0.1:8000'],
            ['name' => 'n8n Automation Engine', 'url' => 'http://127.0.0.1:5678/healthz'],
            ['name' => 'Obsidian Web Vault', 'url' => 'http://127.0.0.1:8083'],
            ['name' => 'SmartPepper IoT Farm', 'url' => 'http://127.0.0.1:8081'],
        ];

        foreach ($internalEndpoints as &$ep) {
            $check = $this->checkUrlHealth($ep['url']);
            $ep['code'] = $check['code'];
            $ep['latency'] = $check['latency'];
            $ep['is_up'] = $check['is_up'];
        }

        // 5. Ping Jaringan (Cloudflare DNS & Gateway Lokal)
        $pingGateway = $this->pingHost('192.168.0.1');
        $pingInternet = $this->pingHost('1.1.1.1');

        $network = [
            'gateway_ip' => '192.168.0.1',
            'gateway_ping' => $pingGateway,
            'dns_server' => '1.1.1.1 (Cloudflare)',
            'internet_ping' => $pingInternet,
            'cloudflare_tunnel' => 'https://alysa.andrian-syah.my.id',
        ];

        return view('admin.server.index', compact(
            'serverInfo',
            'metrics',
            'services',
            'internalEndpoints',
            'network'
        ));
    }

    /**
     * Jalankan Ping atau Uji Koneksi Kustom via AJAX
     */
    public function testPing(Request $request)
    {
        $target = $request->input('target', '1.1.1.1');
        // Validasi input untuk keamanan perintah
        if (!filter_var($target, FILTER_VALIDATE_IP) && !filter_var($target, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return response()->json(['error' => 'Alamat IP atau domain target tidak valid.'], 422);
        }

        $ping = $this->pingHost($target);

        return response()->json([
            'target' => $target,
            'ping_ms' => $ping,
            'status' => ($ping !== 'RTO') ? 'Connected' : 'Unreachable',
        ]);
    }

    private function getCommandOutput(string $cmd): string
    {
        $out = trim(shell_exec($cmd) ?? '');
        return $out;
    }

    private function getPublicIp(): string
    {
        try {
            $ip = trim(shell_exec('curl -s --connect-timeout 2 https://ifconfig.me') ?? '');
            return $ip ?: '103.124.196.179';
        } catch (\Throwable $e) {
            return '103.124.196.179';
        }
    }

    private function getMemoryUsage(): array
    {
        try {
            $free = shell_exec('free -m');
            $lines = explode("\n", trim($free));
            if (isset($lines[1])) {
                $parts = preg_split('/\s+/', $lines[1]);
                $total = (int)($parts[1] ?? 1);
                $used = (int)($parts[2] ?? 0);
                $percent = round(($used / $total) * 100, 1);
                return [
                    'used' => number_format($used) . ' MB',
                    'total' => number_format($total) . ' MB',
                    'percent' => $percent,
                ];
            }
        } catch (\Throwable $e) {}

        return ['used' => 'N/A', 'total' => 'N/A', 'percent' => 0];
    }

    private function getDiskUsage(): array
    {
        try {
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            $used = $total - $free;
            $percent = round(($used / $total) * 100, 1);

            return [
                'used' => round($used / 1073741824, 1) . ' GB',
                'total' => round($total / 1073741824, 1) . ' GB',
                'percent' => $percent,
            ];
        } catch (\Throwable $e) {
            return ['used' => 'N/A', 'total' => 'N/A', 'percent' => 0];
        }
    }

    private function checkUrlHealth(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $latency = round(curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000, 1);
        curl_close($ch);

        return [
            'code' => $code,
            'latency' => $latency . ' ms',
            'is_up' => ($code >= 200 && $code < 400),
        ];
    }

    private function pingHost(string $host): string
    {
        $safeHost = escapeshellarg($host);
        $cmd = "ping -c 1 -W 1 {$safeHost} | grep 'time=' | awk -F'time=' '{print $2}' | awk '{print $1}'";
        $res = trim(shell_exec($cmd) ?? '');

        return !empty($res) ? $res . ' ms' : 'RTO';
    }
}
