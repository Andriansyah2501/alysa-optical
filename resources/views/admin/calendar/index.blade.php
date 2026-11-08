@extends('layouts.app')

@section('title', 'Manajemen Kalender Promo & Event Optik | Alysa Glasses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-amber-100 text-amber-900 rounded-lg text-[10px] font-black uppercase tracking-wider">
                    <i class="fa-solid fa-calendar-days mr-1 text-amber-600"></i> Marketing & Campaign Manager
                </span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-semibold">Pengaturan Event Promo & Tanggal Flash Sale</span>
            </div>
            <h1 class="font-serif text-3xl font-bold text-slate-900 mt-1">Kelola & Edit Kalender Promo (CRUD)</h1>
            <p class="text-xs text-slate-500 mt-1">Super Admin & Admin dapat membuat, mengubah tanggal, menetapkan kupon diskon, serta mengaktifkan event promosi.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('shop.calendar') }}" target="_blank" class="px-3.5 py-2 bg-white border border-sand-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-sand-50 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-eye text-brand-600"></i> Lihat Tampilan Publik
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl text-xs font-bold transition-all">
                &larr; Antrean Lab
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-bold flex items-center gap-2.5 shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- FORM TAMBAH PROMO EVENT BARU (CREATE) -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
            <i class="fa-solid fa-calendar-plus text-rose-600"></i> Tambah Event Promo Baru ke Kalender (Create)
        </h3>
        <p class="text-xs text-slate-500">Event yang dijadwalkan otomatis muncul pada kalender bulanan publik dan rincian benefit bagi pembeli.</p>

        <form action="{{ route('admin.calendar.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs pt-2">
            @csrf
            <div class="sm:col-span-2">
                <label class="font-bold text-slate-700 block mb-1">Judul Event Promo *</label>
                <input type="text" name="title" required placeholder="Contoh: Flash Sale 11.11 Lensa Bluechromic" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Badge Tag Singkat *</label>
                <input type="text" name="badge" required placeholder="SPECIAL 11.11" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-black uppercase text-slate-900">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Tema Warna Badge</label>
                <select name="color_theme" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                    <option value="rose">Merah / Rose (Flash Sale)</option>
                    <option value="amber">Kuning / Amber (Weekend)</option>
                    <option value="emerald">Hijau / Emerald (Spesial Hari Besar)</option>
                    <option value="sky">Biru / Sky (Member/Pelajar)</option>
                    <option value="purple">Ungu / Purple (Payday Sale)</option>
                </select>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Tanggal Mulai *</label>
                <input type="date" name="start_date" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Tanggal Selesai *</label>
                <input type="date" name="end_date" required class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-900">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Keterangan Diskon / Benefit</label>
                <input type="text" name="discount_info" placeholder="Hemat 30% / Diskon Rp 50rb" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-emerald-700">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Hubungkan Kode Voucher</label>
                <input type="text" name="voucher_code" placeholder="ALYSA1111" class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-mono uppercase text-slate-900">
            </div>
            <div class="sm:col-span-3">
                <label class="font-bold text-slate-700 block mb-1">Deskripsi Lengkap Promo</label>
                <input type="text" name="description" placeholder="Syarat & ketentuan promo, misal: berlaku untuk semua frame cat eye dan lensa antiradiasi..." class="w-full px-3.5 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Event
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL DAFTAR & EDIT EVENT PROMO (READ, UPDATE, DELETE) -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
            <i class="fa-solid fa-calendar-check text-brand-600"></i> Daftar Promo Kalender Aktif ({{ $events->count() }} Jadwal)
        </h3>

        <div class="overflow-x-auto rounded-2xl border border-sand-200">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-sand-100 text-slate-700 font-extrabold uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Event Promo</th>
                        <th class="py-3 px-4">Periode Tanggal</th>
                        <th class="py-3 px-4">Benefit & Voucher</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-100 text-slate-700 font-medium">
                    @foreach($events as $ev)
                        <tr class="hover:bg-sand-50/60">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase text-white
                                        @if($ev->color_theme == 'rose') bg-rose-600
                                        @elseif($ev->color_theme == 'amber') bg-amber-600
                                        @elseif($ev->color_theme == 'emerald') bg-emerald-600
                                        @elseif($ev->color_theme == 'sky') bg-sky-600
                                        @else bg-purple-600 @endif">
                                        {{ $ev->badge }}
                                    </span>
                                    <span class="font-bold text-slate-900 text-sm">{{ $ev->title }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">{{ $ev->description ?: '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800">{{ $ev->start_date->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">s/d {{ $ev->end_date->format('d M Y') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-emerald-700">{{ $ev->discount_info ?: 'Promo Toko' }}</div>
                                @if($ev->voucher_code)
                                    <span class="font-mono text-[10px] bg-sand-100 px-1.5 py-0.5 rounded text-brand-700 font-bold">
                                        {{ $ev->voucher_code }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($ev->status === 'running')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 animate-pulse">Sedang Berjalan</span>
                                @elseif($ev->status === 'upcoming')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800">Akan Datang</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Selesai / Expired</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Tombol Edit Modal -->
                                    <button type="button" onclick="openEditPromoModal('{{ $ev->id }}', '{{ addslashes($ev->title) }}', '{{ $ev->badge }}', '{{ addslashes($ev->description) }}', '{{ $ev->discount_info }}', '{{ $ev->voucher_code }}', '{{ $ev->start_date->format('Y-m-d') }}', '{{ $ev->end_date->format('Y-m-d') }}', '{{ $ev->color_theme }}', {{ $ev->is_active ? 1 : 0 }})" class="p-1.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-lg text-xs font-bold" title="Edit Promo">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Form Hapus -->
                                    <form action="{{ route('admin.calendar.destroy', $ev->id) }}" method="POST" onsubmit="return confirm('Hapus promo {{ $ev->title }} dari kalender?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold" title="Hapus Promo">
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

<!-- MODAL EDIT PROMO EVENT (UPDATE) -->
<div id="modalEditPromo" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-sand-200 space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-sand-200">
            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-brand-600"></i> Edit Jadwal Promo Kalender
            </h4>
            <button type="button" onclick="closeEditPromoModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-base"></i></button>
        </div>

        <form id="formEditPromo" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="font-bold text-slate-700 block mb-1">Judul Event *</label>
                <input type="text" name="title" id="editPromoTitle" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Badge Tag</label>
                    <input type="text" name="badge" id="editPromoBadge" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-black uppercase text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tema Warna</label>
                    <select name="color_theme" id="editPromoColor" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                        <option value="rose">Rose / Merah</option>
                        <option value="amber">Amber / Kuning</option>
                        <option value="emerald">Emerald / Hijau</option>
                        <option value="sky">Sky / Biru</option>
                        <option value="purple">Purple / Ungu</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="editPromoStart" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="editPromoEnd" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Benefit Diskon</label>
                    <input type="text" name="discount_info" id="editPromoDiscount" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-emerald-700">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kode Voucher</label>
                    <input type="text" name="voucher_code" id="editPromoVoucher" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-mono uppercase text-slate-800">
                </div>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Deskripsi Promo</label>
                <textarea name="description" id="editPromoDesc" rows="2" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800"></textarea>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="editPromoActive" value="1" class="rounded text-brand-600">
                <label for="editPromoActive" class="font-bold text-slate-700">Promo Aktif & Tayang di Kalender</label>
            </div>
            <div class="pt-3 border-t border-sand-200 flex justify-end gap-2">
                <button type="button" onclick="closeEditPromoModal()" class="px-4 py-2 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditPromoModal(id, title, badge, desc, discount, voucher, start, end, color, isActive) {
        document.getElementById('formEditPromo').action = '/admin/kalender-promo/' + id + '/update';
        document.getElementById('editPromoTitle').value = title;
        document.getElementById('editPromoBadge').value = badge;
        document.getElementById('editPromoDesc').value = desc || '';
        document.getElementById('editPromoDiscount').value = discount || '';
        document.getElementById('editPromoVoucher').value = voucher || '';
        document.getElementById('editPromoStart').value = start;
        document.getElementById('editPromoEnd').value = end;
        document.getElementById('editPromoColor').value = color;
        document.getElementById('editPromoActive').checked = isActive === 1;
        document.getElementById('modalEditPromo').classList.remove('hidden');
    }

    function closeEditPromoModal() {
        document.getElementById('modalEditPromo').classList.add('hidden');
    }
</script>
@endsection
