@extends('layouts.app')

@section('title', 'Katalog Kacamata & Stok Frame | Alysa Glasses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-brand-600 font-bold uppercase tracking-wider">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Backoffice</a>
                <span>/</span>
                <span>Manajemen Produk</span>
            </div>
            <h1 class="font-serif text-3xl font-bold text-slate-900 mt-1">Katalog Kacamata & Lensa</h1>
            <p class="text-xs text-slate-500 mt-1">Tambah model kacamata baru, atur varian bentuk, warna, harga promo, dan stok gudang.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.katalog.create') }}" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-500/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Kacamata Baru
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian Cepat -->
    <div class="bg-white p-4 rounded-2xl border border-sand-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.katalog.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama frame, merk, bentuk..." class="w-full pl-9 pr-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <select name="category_id" onchange="this.form.submit()" class="bg-sand-50 border border-sand-300 rounded-xl text-xs font-medium px-3 py-2 text-slate-800">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-sand-200 hover:bg-sand-300 text-slate-700 rounded-xl text-xs font-bold">
                Filter
            </button>
            @if(request()->hasAny(['q', 'category_id']))
                <a href="{{ route('admin.katalog.index') }}" class="px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold">Reset</a>
            @endif
        </form>

        <span class="text-xs text-slate-500 font-semibold">{{ $products->total() }} Produk Terdaftar</span>
    </div>

    <!-- Tabel Daftar Produk -->
    <div class="bg-white rounded-3xl border border-sand-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-sand-50/80 text-slate-500 uppercase tracking-wider font-bold text-[10px] border-b border-sand-200">
                    <tr>
                        <th class="px-6 py-4">Foto & Kacamata</th>
                        <th class="px-6 py-4">Kategori & Spesifikasi</th>
                        <th class="px-6 py-4">Warna & Bahan</th>
                        <th class="px-6 py-4">Harga & Promo</th>
                        <th class="px-6 py-4">Stok</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-200">
                    @forelse($products as $prod)
                        <tr class="hover:bg-sand-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-12 h-12 rounded-xl object-cover bg-sand-100 border border-sand-200 shrink-0">
                                    <div>
                                        <span class="text-[10px] font-bold text-brand-600 uppercase">{{ $prod->brand }}</span>
                                        <h4 class="font-bold text-slate-900 text-xs">{{ $prod->name }}</h4>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $prod->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block">{{ $prod->category->name }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $prod->frame_shape }} • {{ $prod->gender }}</span>
                                <span class="text-[10px] text-slate-400">Size: {{ $prod->size_frame }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-300" style="background-color: {{ $prod->color_hex }}"></span>
                                    <span class="font-semibold text-slate-800">{{ $prod->color_primary }}</span>
                                </div>
                                <span class="text-[11px] text-slate-500 block mt-0.5">{{ $prod->frame_material }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($prod->discount_price)
                                    <span class="text-[10px] text-slate-400 line-through block">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                    <span class="font-extrabold text-brand-700">Rp {{ number_format($prod->discount_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="font-extrabold text-slate-900">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($prod->stock > 5)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-lg text-[11px]">
                                        {{ $prod->stock }} pcs
                                    </span>
                                @elseif($prod->stock > 0)
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-lg text-[11px]">
                                        {{ $prod->stock }} pcs (Menipis)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-rose-50 text-rose-700 font-bold rounded-lg text-[11px]">
                                        Habis
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.katalog.edit', $prod->id) }}" class="inline-flex items-center px-3 py-1.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.katalog.destroy', $prod->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kacamata {{ $prod->name }} dari katalog?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Tidak ada produk kacamata ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-sand-200">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
