@extends('layouts.app')

@section('title', 'Login Staf & Kasir Optik | Alysa Glasses')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl border border-sand-200 shadow-xl p-8 space-y-6">
        
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-brand-500 text-white flex items-center justify-center mx-auto text-xl shadow-md shadow-brand-500/20">
                <i class="fa-solid fa-glasses"></i>
            </div>
            <h2 class="font-serif text-2xl font-bold text-slate-900">Portal Staf Alysa Glasses</h2>
            <p class="text-xs text-slate-500">Pintu masuk Admin Katalog, Kasir POS, dan Refraksionis Optik.</p>
        </div>

        @if($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 space-y-1">
                @foreach($errors->all() as $error)
                    <div><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            
            <div>
                <label class="font-bold text-slate-700 block mb-1">Email Staf</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email', 'admin@alysaglasses.com') }}" required placeholder="staf@alysaglasses.com" class="w-full pl-10 pr-4 py-3 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Kata Sandi (Password)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required value="admin123" placeholder="••••••••" class="w-full pl-10 pr-4 py-3 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                </div>
            </div>

            <div class="flex items-center justify-between text-[11px] pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-brand-600 rounded">
                    <span>Ingat Saya</span>
                </label>
                <span class="text-slate-400">Hubungi IT jika lupa akun</span>
            </div>

            <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-brand-500/25 transition-all">
                Masuk ke Sistem <i class="fa-solid fa-arrow-right-to-bracket ml-1"></i>
            </button>
        </form>

        <!-- Akun Demo Cepat -->
        <div class="pt-4 border-t border-sand-200">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block text-center mb-2">Akun Demo Cepat</span>
            <div class="grid grid-cols-3 gap-2 text-[10px] text-center">
                <button type="button" onclick="fillLogin('admin@alysaglasses.com', 'admin123')" class="p-2 bg-sand-100 hover:bg-brand-100 rounded-lg text-slate-700 font-bold transition-colors">
                    Admin
                </button>
                <button type="button" onclick="fillLogin('kasir@alysaglasses.com', 'kasir123')" class="p-2 bg-sand-100 hover:bg-brand-100 rounded-lg text-slate-700 font-bold transition-colors">
                    Kasir POS
                </button>
                <button type="button" onclick="fillLogin('refraksi@alysaglasses.com', 'refraksi123')" class="p-2 bg-sand-100 hover:bg-brand-100 rounded-lg text-slate-700 font-bold transition-colors">
                    Refraksi
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    function fillLogin(email, pass) {
        document.querySelector('input[name="email"]').value = email;
        document.querySelector('input[name="password"]').value = pass;
    }
</script>
@endsection
