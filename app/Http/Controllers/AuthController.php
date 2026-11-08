<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan oleh Administrator.']);
            }

            if ($user->role === 'cashier') {
                return redirect()->route('pos.index')->with('success', 'Selamat bertugas, ' . $user->name);
            }

            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
