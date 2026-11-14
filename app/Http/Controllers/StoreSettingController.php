<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\LensType;
use App\Models\Voucher;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StoreSettingController extends Controller
{
    public function index()
    {
        // Simpan barcode QR WhatsApp segar HANYA saat sesi menunggu scan
        try {
            if ($this->wahaSessionStatus() === 'SCAN_QR_CODE') {
                $qr = $this->wahaRequest('GET', '/api/default/auth/qr', 4);
                if ($qr && strlen($qr) > 100) {
                    file_put_contents(public_path('uploads/whatsapp_qr.png'), $qr);
                }
            }
        } catch (\Throwable $e) {}

        $settings = StoreSetting::all();
        $users = User::latest()->get();
        $lenses = LensType::all();
        $vouchers = Voucher::latest()->get();
        $productsCount = Product::count();

        return view('admin.settings.index', compact('settings', 'users', 'lenses', 'vouchers', 'productsCount'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token']);

        // Checkbox role POS: array -> CSV; tidak ada yang dicentang -> 'cashier' default aman (admin/super_admin tetap selalu bisa)
        if ($request->has('pos_enabled_roles')) {
            $roles = array_intersect(
                (array) $request->input('pos_enabled_roles', []),
                ['cashier', 'optician']
            );
            $data['pos_enabled_roles'] = implode(',', $roles);
        } else {
            $data['pos_enabled_roles'] = '';
        }

        foreach ($data as $key => $value) {
            StoreSetting::set($key, $value);
        }

        return back()->with('success', 'Semua pengaturan toko, identitas struk, dan catatan operasional berhasil diperbarui.');
    }

    // ==========================================
    // 1. CRUD PENGGUNA & ROLE (USERS)
    // ==========================================
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:super_admin,admin,cashier,optician',
            'password' => 'required|min:6',
            'phone' => 'nullable|string',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        return back()->with('success', 'Akun pengguna baru (' . strtoupper($validated['role']) . ') berhasil dibuat.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin,cashier,optician',
            'phone' => 'nullable|string',
            'password' => 'nullable|min:6',
            'is_active' => 'nullable|boolean',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'],
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "Akun {$userName} berhasil dihapus dari sistem.");
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan saat ini.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    // ==========================================
    // 2. CRUD MASTER LENSA (LENS TYPES)
    // ==========================================
    public function storeLens(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'additional_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        LensType::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'additional_price' => $validated['additional_price'],
            'description' => $validated['description'],
        ]);

        return back()->with('success', "Paket lensa '{$validated['name']}' berhasil ditambahkan ke katalog & kasir.");
    }

    public function updateLens(Request $request, LensType $lens)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'additional_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $lens->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'additional_price' => $validated['additional_price'],
            'description' => $validated['description'],
        ]);

        return back()->with('success', "Data paket lensa '{$lens->name}' berhasil diperbarui.");
    }

    public function destroyLens(LensType $lens)
    {
        $lensName = $lens->name;
        $lens->delete();

        return back()->with('success', "Paket lensa '{$lensName}' berhasil dihapus.");
    }

    // ==========================================
    // 3. CRUD VOUCHER DISKON PROMO
    // ==========================================
    public function storeVoucher(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'amount' => 'required|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        Voucher::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'min_spend' => $validated['min_spend'] ?? 0,
            'max_discount' => $validated['max_discount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? 100,
            'used_count' => 0,
            'is_active' => true,
        ]);

        return back()->with('success', "Kode voucher '{$validated['code']}' berhasil diterbitkan.");
    }

    public function updateVoucher(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'amount' => 'required|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $voucher->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'min_spend' => $validated['min_spend'] ?? 0,
            'max_discount' => $validated['max_discount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? 100,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', "Voucher '{$voucher->code}' berhasil diperbarui.");
    }

    public function destroyVoucher(Voucher $voucher)
    {
        $vCode = $voucher->code;
        $voucher->delete();

        return back()->with('success', "Kode voucher '{$vCode}' telah dihapus.");
    }

    // =========================================
    // 4. INTEGRASI WHATSAPP (WAHA ENGINE)
    // =========================================

    private const WAHA_BASE = 'http://127.0.0.1:3008';
    private const WAHA_KEY = 'alysa2026';

    /**
     * Helper HTTP ke WAHA; ignore_errors agar body 4xx/5xx tetap terbaca.
     * Return [httpCode, body] — tidak pernah melempar exception.
     */
    private function wahaHttp(string $method, string $path, int $timeout = 4, array $payload = null): array
    {
        $headers = ["X-Api-Key: " . self::WAHA_KEY];
        $options = ['http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers) . "\r\n",
            'timeout' => $timeout,
            'ignore_errors' => true,
        ]];
        if ($payload !== null) {
            $options['http']['header'] .= "Content-Type: application/json\r\n";
            $options['http']['content'] = json_encode($payload);
        }
        $body = @file_get_contents(self::WAHA_BASE . $path, false, stream_context_create($options));

        $code = 0;
        foreach ($http_response_header ?? [] as $h) {
            if (preg_match('#^HTTP/\S+\s(\d{3})#', $h, $m)) {
                $code = (int) $m[1];
            }
        }

        return [$code, $body ?: ''];
    }

    /** Status sesi default: WORKING / SCAN_QR_CODE / STARTING / STOPPED / FAILED / OFFLINE */
    private function wahaSessionStatus(): string
    {
        [$code, $body] = $this->wahaHttp('GET', '/api/sessions/default', 3);
        if ($code !== 200) {
            return $code === 0 ? 'OFFLINE' : 'UNKNOWN';
        }
        $data = json_decode($body, true);
        return (string) ($data['status'] ?? 'UNKNOWN');
    }

    /**
     * Hentikan lalu jalankan ulang sesi dan tunggu sampai QR siap.
     * Dipakai tombol Generate — bisa berkali-kali, tidak hanya sekali.
     */
    private function wahaRestartAndWait(): string
    {
        $status = $this->wahaSessionStatus();
        if ($status === 'WORKING') {
            return 'WORKING'; // jangan ganggu sesi yang sudah tersambung
        }

        // Stop (abaikan hasil — normal jika sudah STOPPED/FAILED)
        $this->wahaHttp('POST', '/api/sessions/stop', 4, ['name' => 'default']);
        usleep(700000);

        // Start — jika ditolak (mis. status STARTING), tunggu sedikit lalu coba lagi
        [$code] = $this->wahaHttp('POST', '/api/sessions/start', 6, ['name' => 'default']);
        if ($code >= 400) {
            sleep(3);
            $this->wahaHttp('POST', '/api/sessions/start', 6, ['name' => 'default']);
        }

        // Poll sampai SCAN_QR_CODE / WORKING (maks ±18 detik)
        for ($i = 0; $i < 9; $i++) {
            sleep(2);
            $status = $this->wahaSessionStatus();
            if (in_array($status, ['SCAN_QR_CODE', 'WORKING'], true)) {
                return $status;
            }
        }

        return $status;
    }

    /**
     * JSON status WA — dipakai tombol/panel "Sudah Terhubung".
     */
    public function waStatus()
    {
        $status = $this->wahaSessionStatus();

        $me = null;
        if ($status === 'WORKING') {
            [$code, $body] = $this->wahaHttp('GET', '/api/sessions/default', 3);
            $data = json_decode($body, true);
            $me = [
                'id' => $data['me']['id'] ?? null,
                'pushName' => $data['me']['pushName'] ?? null,
            ];
        }

        return response()->json([
            'status' => $status,
            'connected' => $status === 'WORKING',
            'me' => $me,
            'at' => now()->toIso8601String(),
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Stream Barcode QR WhatsApp dari WAHA Engine (validasi magic bytes PNG).
     */
    public function streamWaQr()
    {
        $status = $this->wahaSessionStatus();

        // Jika sesi mati total, bangkitkan dulu
        if (in_array($status, ['STOPPED', 'FAILED', 'UNKNOWN', 'OFFLINE'], true)) {
            $status = $this->wahaRestartAndWait();
        }

        if ($status === 'SCAN_QR_CODE') {
            [$code, $qrData] = $this->wahaHttp('GET', '/api/default/auth/qr', 5);
            if ($code === 200 && strlen($qrData) > 100 && str_starts_with($qrData, "\x89PNG")) {
                return response($qrData, 200)
                    ->header('Content-Type', 'image/png')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            }
        }

        // Sudah WORKING / belum siap: sajikan snapshot terakhir bila ada
        $qrPath = public_path('uploads/whatsapp_qr.png');
        if (is_file($qrPath)) {
            return response()->file($qrPath, [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        }

        return response()->json(['message' => 'QR belum tersedia', 'status' => $status], 503);
    }

    /**
     * Restart Sesi WAHA secara manual (tombol Generate) — mengembalikan JSON.
     */
    public function restartWaSession()
    {
        $status = $this->wahaRestartAndWait();

        $messages = [
            'SCAN_QR_CODE' => ['success', 'Barcode aktif siap discan. Silakan scan sekarang!'],
            'WORKING' => ['success', 'WhatsApp sudah tersambung — tidak perlu scan lagi.'],
            'STARTING' => ['error', 'Engine masih menyala, coba klik Generate lagi 5 detik lagi.'],
            'OFFLINE' => ['error', 'WAHA engine tidak merespons — hubungi admin server.'],
        ];
        [$type, $msg] = $messages[$status] ?? ['error', 'Sesi gagal di-refresh (status: ' . $status . ').'];

        if (request()->expectsJson()) {
            return response()->json(['ok' => $type === 'success', 'status' => $status, 'message' => $msg]);
        }

        return back()->with($type, $msg);
    }

}
