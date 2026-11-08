<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda nonaktif.');
        }

        // Super Admin memiliki akses mutlak ke SELURUH rute dan fitur sistem
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return $next($request);
        }

        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak: Peran akun Anda (' . $user->role . ') tidak memiliki wewenang untuk membuka modul ini.');
    }
}
