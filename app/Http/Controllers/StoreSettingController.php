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
        // Auto-refresh barcode QR WhatsApp dari engine lokal
        try {
            $qrPath = public_path('uploads/whatsapp_qr.png');
            $wahaQr = @file_get_contents('http://127.0.0.1:3008/api/default/auth/qr', false, stream_context_create([
                'http' => ['header' => "X-Api-Key: alysa2026\r\n", 'timeout' => 2]
            ]));
            if ($wahaQr && strlen($wahaQr) > 100) {
                file_put_contents($qrPath, $wahaQr);
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

    /**
     * Stream Live Barcode QR WhatsApp dari WAHA Engine
     */
    public function streamWaQr()
    {
        try {
            // Cek status sesi
            $statusResp = @file_get_contents('http://127.0.0.1:3008/api/sessions', false, stream_context_create([
                'http' => ['header' => "X-Api-Key: alysa2026\r\n", 'timeout' => 2]
            ]));
            $sessions = json_decode($statusResp ?: '[]', true);
            $default = null;
            foreach ($sessions as $s) {
                if ($s['name'] === 'default') {
                    $default = $s;
                    break;
                }
            }

            if (!$default || in_array($default['status'] ?? '', ['STOPPED', 'FAILED'])) {
                // Stop dulu jika FAILED agar bisa clean start
                @file_get_contents('http://127.0.0.1:3008/api/sessions/stop', false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "X-Api-Key: alysa2026\r\nContent-Type: application/json\r\n",
                        'content' => json_encode(['name' => 'default']),
                        'timeout' => 2
                    ]
                ]));
                // Start ulang sesi agar QR baru selalu aktif dan valid
                @file_get_contents('http://127.0.0.1:3008/api/sessions/start', false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "X-Api-Key: alysa2026\r\nContent-Type: application/json\r\n",
                        'content' => json_encode(['name' => 'default']),
                        'timeout' => 2
                    ]
                ]));
                sleep(2);
            }

            // Ambil QR Code PNG segar
            $qrData = @file_get_contents('http://127.0.0.1:3008/api/default/auth/qr', false, stream_context_create([
                'http' => ['header' => "X-Api-Key: alysa2026\r\n", 'timeout' => 3]
            ]));

            if ($qrData && strlen($qrData) > 100) {
                return response($qrData, 200)->header('Content-Type', 'image/png')->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            }
        } catch (\Throwable $e) {}

        // Fallback jika sudah terhubung (WORKING)
        return response()->file(public_path('uploads/whatsapp_qr.png'));
    }

    /**
     * Restart Sesi WAHA secara Manual
     */
    public function restartWaSession()
    {
        try {
            @file_get_contents('http://127.0.0.1:3008/api/sessions/start', false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "X-Api-Key: alysa2026\r\nContent-Type: application/json\r\n",
                    'content' => json_encode(['name' => 'default']),
                    'timeout' => 3
                ]
            ]));
            sleep(2);
            return redirect()->back()->with('success', 'Sesi WhatsApp berhasil direfresh. Silakan scan barcode baru!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal me-refresh sesi: ' . $e->getMessage());
        }
    }

}
