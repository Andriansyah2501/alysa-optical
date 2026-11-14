<?php

namespace App\Http\Middleware;

use App\Models\StoreSetting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kontrol Akses Modul Kasir (POS).
 *
 * - super_admin & admin SELALU boleh masuk (jangan sampai owner terkunci sendiri).
 * - Role lain (cashier, optician, dst.) hanya boleh jika role-nya tercantum
 *   di pengaturan 'pos_enabled_roles' (CSV) — diatur dari Admin → Pengaturan.
 * - Default lama dipertahankan: 'cashier'.
 */
class PosAccess
{
    /**
     * Daftar penuh role yang boleh masuk POS.
     * Satu sumber kebenaran: middleware, tombol menu, dan halaman struk.
     */
    public static function allowedRoles(): array
    {
        // Admin & Super Admin selalu boleh — pemilik tidak boleh terkunci sendiri
        $always = ['super_admin', 'admin'];

        $extra = array_filter(array_map(
            'trim',
            explode(',', (string) StoreSetting::get('pos_enabled_roles', 'cashier'))
        ));

        return array_values(array_unique(array_merge($always, $extra)));
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (!$user instanceof User || !in_array($user->role, self::allowedRoles(), true)) {
            return redirect()
                ->route('shop.index')
                ->with('error', 'Akses transaksi kasir (POS) dibatasi hanya untuk role yang ditentukan pengelola.');
        }

        return $next($request);
    }
}
