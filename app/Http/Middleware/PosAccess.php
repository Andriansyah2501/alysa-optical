<?php

namespace App\Http\Middleware;

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
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (in_array($user->role, ['super_admin', 'admin'], true)) {
            return $next($request);
        }

        if (!$user instanceof User || !$user->canAccessPos()) {
            return redirect()
                ->route('shop.index')
                ->with('error', 'Akses transaksi kasir (POS) dibatasi hanya untuk role yang ditentukan pengelola.');
        }

        return $next($request);
    }
}
