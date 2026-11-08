@extends('layouts.app')

@section('title', 'Super Admin Control Center & Full CRUD | Alysa Glasses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-purple-100 text-purple-900 rounded-lg text-[10px] font-black uppercase tracking-wider">
                    <i class="fa-solid fa-crown mr-1 text-amber-500"></i> Super Admin Supreme Control
                </span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-semibold">Akses Penuh Seluruh Database & Kustomisasi</span>
            </div>
            <h1 class="font-serif text-3xl font-bold text-slate-900 mt-1">Pusat Kendali & Kustomisasi Sistem (CRUD)</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola identitas optik, format struk, akun staf/admin, master paket lensa resep, kode voucher diskon, dan katalog kacamata.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.katalog.index') }}" class="px-3.5 py-2 bg-white border border-sand-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-sand-50 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-glasses text-brand-600"></i> Kelola Katalog Frame ({{ $productsCount }})
            </a>
            <a href="{{ route('admin.reports.index') }}" class="px-3.5 py-2 bg-sky-50 text-sky-800 border border-sky-200 rounded-xl text-xs font-bold hover:bg-sky-100 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-chart-line"></i> Laporan & PDF
            </a>
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-cash-register"></i> Buka Kasir POS
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Flash Message -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-bold flex items-center gap-2.5 shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 font-bold flex items-center gap-2.5 shadow-sm">
            <i class="fa-solid fa-circle-xmark text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Terjadi kesalahan validasi:
            </div>
            <ul class="list-disc pl-5 text-[11px]">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TAB NAVIGASI MODUL KONTROL -->
    <div class="flex overflow-x-auto p-1.5 bg-sand-100 rounded-2xl gap-1 text-xs font-bold text-slate-600" id="adminControlTabs">
        <button type="button" onclick="openAdminTab('tabSettings')" id="btnTabSettings" class="control-tab px-4 py-2.5 rounded-xl transition-all active-tab bg-white text-brand-700 shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-store"></i> 1. Identitas Toko & Struk
        </button>
        <button type="button" onclick="openAdminTab('tabUsers')" id="btnTabUsers" class="control-tab px-4 py-2.5 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-users-gear"></i> 2. CRUD Pengguna & Staf ({{ $users->count() }})
        </button>
        <button type="button" onclick="openAdminTab('tabLenses')" id="btnTabLenses" class="control-tab px-4 py-2.5 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-glasses"></i> 3. CRUD Paket Lensa ({{ $lenses->count() }})
        </button>
        <button type="button" onclick="openAdminTab('tabVouchers')" id="btnTabVouchers" class="control-tab px-4 py-2.5 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-ticket"></i> 4. CRUD Voucher Diskon ({{ $vouchers->count() }})
        </button>
        <button type="button" onclick="openAdminTab('tabWaGateway')" id="btnTabWaGateway" class="control-tab px-4 py-2.5 rounded-xl transition-all flex items-center gap-2">
            <i class="fa-brands fa-whatsapp text-emerald-600"></i> 5. Otomasi WhatsApp Gateway
        </button>
    </div>

    <!-- TAB 1: IDENTITAS TOKO & STRUK KASIR -->
    <div id="tabSettings" class="tab-content space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-sand-200">
                <div>
                    <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-brand-600"></i> Kustomisasi Data Toko & Format Struk Thermal
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Seluruh nilai di bawah langsung mengubah teks struk cetak kasir, WhatsApp faktur, dan hero banner toko publik.</p>
                </div>
                <span class="text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-xl">
                    <i class="fa-solid fa-bolt text-emerald-600 mr-1"></i> Live Update
                </span>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                @csrf

                @foreach($settings as $st)
                    <div class="{{ in_array($st->key, ['store_bio', 'receipt_footer_notes', 'store_address']) ? 'md:col-span-2' : '' }}">
                        <label class="font-bold text-slate-800 block mb-1">
                            {{ $st->label }} <span class="text-[10px] text-slate-400 font-mono">({{ $st->key }})</span>
                        </label>
                        @if($st->type === 'textarea')
                            <textarea name="{{ $st->key }}" rows="3" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">{{ $st->value }}</textarea>
                        @elseif($st->key === 'receipt_printer_width')
                            <select name="{{ $st->key }}" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                                <option value="58mm" {{ $st->value === '58mm' ? 'selected' : '' }}>58mm (Printer Kasir Mini Portabel / Bluetooth)</option>
                                <option value="80mm" {{ $st->value === '80mm' ? 'selected' : '' }}>80mm (Printer Thermal Desktop Standar Supermarket)</option>
                            </select>
                        @else
                            <input type="text" name="{{ $st->key }}" value="{{ $st->value }}" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                        @endif
                    </div>
                @endforeach

                <div class="md:col-span-2 pt-3 border-t border-sand-200 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan Toko & Struk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 2: FULL CRUD PENGGUNA & ROLE (USERS) -->
    <div id="tabUsers" class="tab-content hidden space-y-6">
        
        <!-- Form Tambah User Baru -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-purple-600"></i> Tambah Pengguna Baru (Create User)
            </h3>
            <p class="text-xs text-slate-500">Super Admin dapat membuat akun baru dengan peran Super Admin, Admin Backoffice, Kasir POS, maupun Refraksionis Lab.</p>

            <form action="{{ route('admin.settings.user.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 text-xs items-end pt-2">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Contoh: Rian Staff Kasir" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Email Login</label>
                    <input type="email" name="email" required placeholder="kasir2@alysaglasses.com" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Password</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Peran (Role)</label>
                    <select name="role" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="cashier">Kasir POS (cashier)</option>
                        <option value="optician">Refraksionis Lab (optician)</option>
                        <option value="admin">Admin Backoffice (admin)</option>
                        <option value="super_admin">Super Admin (super_admin)</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold shadow-sm transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-plus"></i> Buat User
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar & Edit Pengguna -->
        <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-users text-purple-600"></i> Kelola & Edit Pengguna Aktif (Read, Update, Delete)
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-sand-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-sand-100 text-slate-700 font-extrabold uppercase text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Nama & Email</th>
                            <th class="py-3 px-4">Peran (Role)</th>
                            <th class="py-3 px-4">No. Telp</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sand-100 text-slate-700 font-medium">
                        @foreach($users as $u)
                            <tr class="hover:bg-sand-50/60">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $u->email }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                        @if($u->role === 'super_admin') bg-purple-100 text-purple-800
                                        @elseif($u->role === 'admin') bg-blue-100 text-blue-800
                                        @elseif($u->role === 'cashier') bg-emerald-100 text-emerald-800
                                        @else bg-amber-100 text-amber-800 @endif">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ $u->phone ?: '-' }}</td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('admin.settings.user.toggle', $u->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-all {{ $u->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-100 text-rose-800 hover:bg-rose-200' }}">
                                            {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Buka Modal Edit -->
                                        <button type="button" onclick="openEditUserModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->role }}', '{{ $u->phone }}', {{ $u->is_active ? 1 : 0 }})" class="p-1.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-lg text-xs font-bold" title="Edit Pengguna">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        
                                        <!-- Form Delete User -->
                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('admin.settings.user.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $u->name }} secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold" title="Hapus Pengguna">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- TAB 3: FULL CRUD PAKET LENSA (LENS TYPES) -->
    <div id="tabLenses" class="tab-content hidden space-y-6">
        
        <!-- Form Tambah Lensa Baru -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-brand-600"></i> Tambah Paket Lensa Baru (Create Lens)
            </h3>
            <p class="text-xs text-slate-500">Paket lensa yang ditambahkan di sini otomatis muncul pada pilihan lensa e-commerce dan dropdown kasir POS.</p>

            <form action="{{ route('admin.settings.lens.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 text-xs items-end pt-2">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Paket Lensa</label>
                    <input type="text" name="name" required placeholder="Contoh: Lensa Photochromic Plus" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Harga Tambahan (Rp)</label>
                    <input type="number" name="additional_price" required min="0" placeholder="250000" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Deskripsi & Keunggulan</label>
                    <input type="text" name="description" placeholder="Bisa berubah gelap di terik matahari" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                </div>
                <div>
                    <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-sm transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-plus"></i> Tambah Lensa
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar & Edit Lensa -->
        <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-glasses text-brand-600"></i> Master Katalog Lensa Optik (Read, Update, Delete)
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-sand-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-sand-100 text-slate-700 font-extrabold uppercase text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Nama Lensa & Slug</th>
                            <th class="py-3 px-4">Harga Tambahan (Rp)</th>
                            <th class="py-3 px-4">Deskripsi / Fitur Lensa</th>
                            <th class="py-3 px-4 text-center">Aksi Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sand-100 text-slate-700 font-medium">
                        @foreach($lenses as $lens)
                            <tr class="hover:bg-sand-50/60">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $lens->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $lens->slug }}</div>
                                </td>
                                <td class="py-3 px-4 font-bold text-emerald-700">
                                    Rp {{ number_format($lens->additional_price, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $lens->description ?: '-' }}</td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Edit Lensa -->
                                        <button type="button" onclick="openEditLensModal('{{ $lens->id }}', '{{ addslashes($lens->name) }}', '{{ $lens->additional_price }}', '{{ addslashes($lens->description) }}')" class="p-1.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-lg text-xs font-bold" title="Edit Lensa">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Form Delete Lensa -->
                                        <form action="{{ route('admin.settings.lens.destroy', $lens->id) }}" method="POST" onsubmit="return confirm('Hapus paket lensa {{ $lens->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold" title="Hapus Lensa">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    
    <!-- TAB 5: KONFIGURASI OTOMASI WHATSAPP GATEWAY (N8N / FONNTE) -->
    <div id="tabWaGateway" class="tab-content hidden space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-sand-200">
                <div>
                    <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-lg"></i> Integrasi Otomasi Notifikasi WhatsApp
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Kirim otomatis notifikasi invoice pembelian, rincian resep kacamata, dan link tracking ke WhatsApp pelanggan saat order dibuat atau status lab diperbarui.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-800 rounded-full text-xs font-bold border border-emerald-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle text-[8px] text-emerald-500 animate-pulse"></i> n8n Engine Active (Port 5678)
                    </span>
                </div>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5 text-xs">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Scanner Barcode QR WAHA Local -->
                    <div class="md:col-span-2 p-6 bg-emerald-50/60 rounded-3xl border-2 border-emerald-300 space-y-4 shadow-sm">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div>
                                <span class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-2xl"></i> Hubungkan WhatsApp Toko (Sekali Scan)
                                </span>
                                <p class="text-xs text-slate-600 mt-1">Cukup klik tombol di bawah untuk membuat barcode aktif, lalu langsung scan menggunakan HP Anda.</p>
                            </div>
                            <button type="button" onclick="buatQrSekarang()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-2xl text-xs font-bold transition-all shadow-md flex items-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-qrcode text-sm"></i> KLIK UNTUK GENERATE BARCODE
                            </button>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-8 pt-3 bg-white p-5 rounded-2xl border border-emerald-200">
                            <div class="relative p-2 bg-white rounded-2xl border-2 border-sand-300 shadow-sm flex items-center justify-center min-w-[210px] min-h-[210px]">
                                <img id="waQrImage" src="/admin/pengaturan/wa-qr-stream?t=1" alt="Scan Barcode" class="w-48 h-48 object-contain rounded-xl">
                                <div id="waQrPlaceholder" class="absolute inset-0 bg-white/95 rounded-xl flex flex-col items-center justify-center p-4 text-center">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-emerald-600 text-2xl mb-2 animate-bounce"></i>
                                    <span class="text-xs font-bold text-slate-700">Tekan tombol hijau di atas untuk memunculkan barcode aktif</span>
                                </div>
                            </div>
                            <div class="text-xs space-y-2.5 text-slate-700 leading-relaxed">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-mobile-screen-button text-emerald-600"></i> Cara Mudah Scan:
                                </div>
                                <ol class="list-decimal pl-4 space-y-1.5">
                                    <li>Buka <strong>WhatsApp</strong> di HP Anda.</li>
                                    <li>Pilih <strong>Perangkat Tertaut (Linked Devices)</strong> &gt; <strong>Tautkan Perangkat</strong>.</li>
                                    <li>Klik tombol hijau <strong>"KLIK UNTUK GENERATE BARCODE"</strong> di atas.</li>
                                    <li>Langsung arahkan kamera HP ke barcode yang muncul di samping. Selesai!</li>
                                </ol>
                                <div id="waConnectStatus" class="mt-2 text-xs font-semibold text-slate-500">
                                    Status Engine: <span class="text-emerald-700 font-bold">Siap Digunakan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Gateway Otomatis</label>
                        <select name="wa_gateway_enabled" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                            <option value="1" {{ ($globalSettings['wa_gateway_enabled'] ?? '0') === '1' ? 'selected' : '' }}>Aktif (Kirim Otomatis ke WA Pembeli)</option>
                            <option value="0" {{ ($globalSettings['wa_gateway_enabled'] ?? '0') === '0' ? 'selected' : '' }}>Nonaktif (Gunakan Tombol Kirim Manual)</option>
                        </select>
                        <span class="text-[10px] text-slate-400 mt-1 block">Bila aktif, setiap order masuk atau update lab, notifikasi langsung terkirim.</span>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Penyedia Gateway (Engine / Provider)</label>
                        <select name="wa_gateway_provider" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                            <option value="n8n" {{ ($globalSettings['wa_gateway_provider'] ?? 'n8n') === 'n8n' ? 'selected' : '' }}>n8n Workflow Engine (Lokal Server - Port 5678)</option>
                            <option value="fonnte" {{ ($globalSettings['wa_gateway_provider'] ?? '') === 'fonnte' ? 'selected' : '' }}>Fonnte API (Cloud Gateway)</option>
                            <option value="wppconnect" {{ ($globalSettings['wa_gateway_provider'] ?? '') === 'wppconnect' ? 'selected' : '' }}>WPPConnect / Baileys Self-Hosted</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Webhook URL n8n / Endpoint WhatsApp</label>
                        <input type="url" name="wa_gateway_webhook_url" value="{{ $globalSettings['wa_gateway_webhook_url'] ?? 'http://127.0.0.1:5678/webhook/alysa-wa-notify' }}" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-mono text-slate-800">
                        <span class="text-[10px] text-slate-400 mt-1 block">Endpoint n8n webhook lokal container atau URL gateway WhatsApp Anda.</span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">API Token WhatsApp Gateway (Opsional)</label>
                        <input type="text" name="wa_gateway_api_token" value="{{ $globalSettings['wa_gateway_api_token'] ?? '' }}" placeholder="Masukkan token jika menggunakan provider seperti Fonnte" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-mono text-slate-800">
                    </div>
                </div>

                <div class="pt-4 border-t border-sand-200 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- TAB 4: FULL CRUD VOUCHER DISKON PROMO -->
    <div id="tabVouchers" class="tab-content hidden space-y-6">
        
        <!-- Form Tambah Voucher Baru -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-ticket text-amber-600"></i> Buat Kode Voucher Promo Baru (Create Voucher)
            </h3>
            <p class="text-xs text-slate-500">Voucher dapat digunakan pelanggan pada keranjang checkout maupun oleh kasir pada billing POS.</p>

            <form action="{{ route('admin.settings.voucher.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 text-xs items-end pt-2">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kode Voucher</label>
                    <input type="text" name="code" required placeholder="Contoh: PROMO50K" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-black uppercase text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Promo</label>
                    <input type="text" name="name" required placeholder="Diskon Grand Opening" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tipe Diskon</label>
                    <select name="type" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="fixed">Nominal Tetap (Rp)</option>
                        <option value="percentage">Persentase (%)</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nilai Diskon</label>
                    <input type="number" name="amount" required min="1" placeholder="50000 atau 15" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Min. Belanja (Rp)</label>
                    <input type="number" name="min_spend" value="0" min="0" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                </div>
                <div>
                    <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold shadow-sm transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-plus"></i> Buat Voucher
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar & Edit Voucher -->
        <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-tags text-amber-600"></i> Master Voucher Diskon Aktif (Read, Update, Delete)
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-sand-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-sand-100 text-slate-700 font-extrabold uppercase text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Kode & Nama Voucher</th>
                            <th class="py-3 px-4">Potongan Diskon</th>
                            <th class="py-3 px-4">Min. Belanja</th>
                            <th class="py-3 px-4 text-center">Pemakaian</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sand-100 text-slate-700 font-medium">
                        @foreach($vouchers as $vc)
                            <tr class="hover:bg-sand-50/60">
                                <td class="py-3 px-4">
                                    <div class="font-black text-amber-700 font-mono text-sm">{{ $vc->code }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $vc->name }}</div>
                                </td>
                                <td class="py-3 px-4 font-bold text-emerald-700">
                                    {{ $vc->type === 'percentage' ? $vc->amount . '%' : 'Rp ' . number_format($vc->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-700">
                                    Rp {{ number_format($vc->min_spend, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-slate-600">
                                    {{ $vc->used_count }} / {{ $vc->usage_limit ?: '∞' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $vc->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $vc->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Tombol Edit Voucher -->
                                        <button type="button" onclick="openEditVoucherModal('{{ $vc->id }}', '{{ $vc->code }}', '{{ addslashes($vc->name) }}', '{{ $vc->type }}', '{{ $vc->amount }}', '{{ $vc->min_spend }}', {{ $vc->is_active ? 1 : 0 }})" class="p-1.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-lg text-xs font-bold" title="Edit Voucher">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Form Delete Voucher -->
                                        <form action="{{ route('admin.settings.voucher.destroy', $vc->id) }}" method="POST" onsubmit="return confirm('Hapus voucher {{ $vc->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold" title="Hapus Voucher">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- MODAL EDIT PENGGUNA (UPDATE USER) -->
<div id="modalEditUser" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-sand-200 space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-sand-200">
            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-purple-600"></i> Edit Data Pengguna
            </h4>
            <button type="button" onclick="closeEditUserModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-base"></i></button>
        </div>

        <form id="formEditUser" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="editUserName" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Email</label>
                <input type="email" name="email" id="editUserEmail" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Peran (Role)</label>
                <select name="role" id="editUserRole" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                    <option value="cashier">Kasir POS (cashier)</option>
                    <option value="optician">Refraksionis Lab (optician)</option>
                    <option value="admin">Admin Backoffice (admin)</option>
                    <option value="super_admin">Super Admin (super_admin)</option>
                </select>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Nomor WhatsApp / Telp</label>
                <input type="text" name="phone" id="editUserPhone" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Ganti Password (Kosongkan jika tetap)</label>
                <input type="password" name="password" minlength="6" placeholder="Biarkan kosong jika tidak diubah" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="editUserActive" value="1" class="rounded text-purple-600">
                <label for="editUserActive" class="font-bold text-slate-700">Akun Aktif (Dapat Login)</label>
            </div>
            <div class="pt-3 border-t border-sand-200 flex justify-end gap-2">
                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT LENSA (UPDATE LENS) -->
<div id="modalEditLens" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-sand-200 space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-sand-200">
            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-glasses text-brand-600"></i> Edit Paket Lensa Kacamata
            </h4>
            <button type="button" onclick="closeEditLensModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-base"></i></button>
        </div>

        <form id="formEditLens" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Paket Lensa</label>
                <input type="text" name="name" id="editLensName" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Harga Tambahan (Rp)</label>
                <input type="number" name="additional_price" id="editLensPrice" required min="0" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Deskripsi / Keunggulan</label>
                <textarea name="description" id="editLensDesc" rows="3" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800"></textarea>
            </div>
            <div class="pt-3 border-t border-sand-200 flex justify-end gap-2">
                <button type="button" onclick="closeEditLensModal()" class="px-4 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-sm">Perbarui Lensa</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT VOUCHER (UPDATE VOUCHER) -->
<div id="modalEditVoucher" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-sand-200 space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-sand-200">
            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-tags text-amber-600"></i> Edit Voucher Promo
            </h4>
            <button type="button" onclick="closeEditVoucherModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-base"></i></button>
        </div>

        <form id="formEditVoucher" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="font-bold text-slate-700 block mb-1">Kode Voucher</label>
                <input type="text" name="code" id="editVoucherCode" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-black uppercase text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Promo</label>
                <input type="text" name="name" id="editVoucherName" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tipe</label>
                    <select name="type" id="editVoucherType" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="fixed">Tetap (Rp)</option>
                        <option value="percentage">Persen (%)</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nilai Diskon</label>
                    <input type="number" name="amount" id="editVoucherAmount" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                </div>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Min. Belanja (Rp)</label>
                <input type="number" name="min_spend" id="editVoucherMin" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="editVoucherActive" value="1" class="rounded text-amber-600">
                <label for="editVoucherActive" class="font-bold text-slate-700">Voucher Aktif & Dapat Digunakan</label>
            </div>
            <div class="pt-3 border-t border-sand-200 flex justify-end gap-2">
                <button type="button" onclick="closeEditVoucherModal()" class="px-4 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAdminTab(tabId) {
        // Sembunyikan semua konten tab
        document.querySelectorAll('.tab-content').forEach(function(el) {
            el.classList.add('hidden');
        });

        // Reset styling semua tombol tab
        document.querySelectorAll('.control-tab').forEach(function(btn) {
            btn.classList.remove('bg-white', 'text-brand-700', 'shadow-sm');
            btn.classList.add('text-slate-600');
        });

        // Tampilkan konten tab yang dipilih
        var targetContent = document.getElementById(tabId);
        if (targetContent) {
            targetContent.classList.remove('hidden');
        }

        // Aktifkan styling tombol yang diklik
        var targetBtn = document.getElementById('btn' + tabId.charAt(0).toUpperCase() + tabId.slice(1));
        if (targetBtn) {
            targetBtn.classList.remove('text-slate-600');
            targetBtn.classList.add('bg-white', 'text-brand-700', 'shadow-sm');
        }

        // Simpan tab aktif di sessionStorage agar tidak reset saat refresh
        sessionStorage.setItem('active_admin_settings_tab', tabId);
    }

    // Auto-buka tab terakhir yang dibuka saat refresh halaman
    document.addEventListener('DOMContentLoaded', function() {
        var lastTab = sessionStorage.getItem('active_admin_settings_tab');
        if (lastTab && document.getElementById(lastTab)) {
            openAdminTab(lastTab);
        }
    });

    // Modal Edit User
    function openEditUserModal(id, name, email, role, phone, isActive) {
        document.getElementById('formEditUser').action = '/admin/pengaturan/user/' + id + '/update';
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserRole').value = role;
        document.getElementById('editUserPhone').value = phone || '';
        document.getElementById('editUserActive').checked = isActive === 1;
        document.getElementById('modalEditUser').classList.remove('hidden');
    }
    function closeEditUserModal() {
        document.getElementById('modalEditUser').classList.add('hidden');
    }

    // Modal Edit Lensa
    function openEditLensModal(id, name, price, desc) {
        document.getElementById('formEditLens').action = '/admin/pengaturan/lensa/' + id + '/update';
        document.getElementById('editLensName').value = name;
        document.getElementById('editLensPrice').value = price;
        document.getElementById('editLensDesc').value = desc || '';
        document.getElementById('modalEditLens').classList.remove('hidden');
    }
    function closeEditLensModal() {
        document.getElementById('modalEditLens').classList.add('hidden');
    }

    // Modal Edit Voucher
    function openEditVoucherModal(id, code, name, type, amount, minSpend, isActive) {
        document.getElementById('formEditVoucher').action = '/admin/pengaturan/voucher/' + id + '/update';
        document.getElementById('editVoucherCode').value = code;
        document.getElementById('editVoucherName').value = name;
        document.getElementById('editVoucherType').value = type;
        document.getElementById('editVoucherAmount').value = amount;
        document.getElementById('editVoucherMin').value = minSpend || 0;
        document.getElementById('editVoucherActive').checked = isActive === 1;
        document.getElementById('modalEditVoucher').classList.remove('hidden');
    }
    function closeEditVoucherModal() {
        document.getElementById('modalEditVoucher').classList.add('hidden');
    }
</script>
@endsection


<script>
function buatQrSekarang() {
    const placeholder = document.getElementById('waQrPlaceholder');
    const img = document.getElementById('waQrImage');
    const statusText = document.getElementById('waConnectStatus');

    if (placeholder) {
        placeholder.innerHTML = '<i class="fa-solid fa-arrows-rotate fa-spin text-emerald-600 text-3xl mb-2"></i><span class="text-xs font-bold text-slate-700">Sedang membuat barcode aktif...</span>';
    }

    // Panggil restart sesi agar mendapatkan barcode segar
    fetch("{{ route('admin.settings.wa_restart') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    }).finally(() => {
        setTimeout(() => {
            if (img) {
                img.src = "{{ route('admin.settings.wa_qr') }}?t=" + new Date().getTime();
                img.onload = function() {
                    if (placeholder) placeholder.classList.add('hidden');
                    if (statusText) statusText.innerHTML = 'Status: <span class="text-emerald-700 font-bold animate-pulse">Barcode Aktif - Segera Scan Sekarang!</span>';
                };
            }
        }, 1500);
    });
}
</script>
