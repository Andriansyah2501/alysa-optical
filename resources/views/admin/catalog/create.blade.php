@extends('layouts.app')

@section('title', 'Tambah Kacamata Baru | Alysa Glasses')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
    
    <div class="flex items-center justify-between pb-4 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-brand-600 font-bold uppercase tracking-wider">
                <a href="{{ route('admin.katalog.index') }}" class="hover:underline">Katalog Kacamata</a>
                <span>/</span>
                <span>Tambah Model Baru</span>
            </div>
            <h1 class="font-serif text-2xl font-bold text-slate-900 mt-1">Form Registrasi Model Kacamata</h1>
        </div>
        <a href="{{ route('admin.katalog.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <form action="{{ route('admin.katalog.store') }}" method="POST" class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 sm:p-8 space-y-6 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Model Kacamata <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Alysa Ginza Minimalist Rimless" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Kategori Kacamata <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="font-bold text-slate-700 block mb-1">Merk / Brand <span class="text-rose-500">*</span></label>
                <input type="text" name="brand" value="Alysa Eyewear" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Bentuk Frame (Shape) <span class="text-rose-500">*</span></label>
                <select name="frame_shape" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    <option value="Round">Round (Bulat)</option>
                    <option value="Wayfarer">Wayfarer</option>
                    <option value="Square">Square (Kotak)</option>
                    <option value="Aviator">Aviator</option>
                    <option value="Cat-Eye">Cat-Eye</option>
                    <option value="Geometric">Geometric</option>
                </select>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Target Gender <span class="text-rose-500">*</span></label>
                <select name="gender" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    <option value="Unisex">Unisex</option>
                    <option value="Pria">Pria</option>
                    <option value="Wanita">Wanita</option>
                    <option value="Anak">Anak-Anak</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="font-bold text-slate-700 block mb-1">Material Frame <span class="text-rose-500">*</span></label>
                <input type="text" name="frame_material" placeholder="Pure Titanium / Bio-Acetate / TR90" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Ukuran Dimensi (Size) <span class="text-rose-500">*</span></label>
                <input type="text" name="size_frame" placeholder="Contoh: 50-19-142 mm" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Warna Utama & Kode Hex</label>
                <div class="flex gap-2">
                    <input type="text" name="color_primary" placeholder="Rose Gold" required class="flex-1 px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    <input type="color" name="color_hex" value="#B76E79" class="w-12 h-10 p-1 bg-white border border-sand-300 rounded-xl cursor-pointer">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="font-bold text-slate-700 block mb-1">Harga Normal (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="price" required placeholder="350000" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Harga Diskon / Promo (Rp)</label>
                <input type="number" name="discount_price" placeholder="Kosongkan jika normal" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-brand-700">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Jumlah Stok Fisik <span class="text-rose-500">*</span></label>
                <input type="number" name="stock" value="15" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
            </div>
        </div>

        <div>
            <label class="font-bold text-slate-700 block mb-1">URL Foto Kacamata</label>
            <input type="url" name="image_url" placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            <span class="text-[10px] text-slate-400 mt-1 block">Bisa gunakan link gambar Unsplash / hosting gambar publik. Jika kosong akan memakai default.</span>
        </div>

        <div>
            <label class="font-bold text-slate-700 block mb-1">Deskripsi Lengkap & Kenyamanan Frame <span class="text-rose-500">*</span></label>
            <textarea name="description" rows="3" required placeholder="Jelaskan kenyamanan hidung, kelenturan tangkai, dan kecocokan untuk aktivitas harian..." class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800"></textarea>
        </div>

        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                <input type="checkbox" name="is_bestseller" value="1" class="w-4 h-4 text-brand-600 rounded">
                <span>Tandai sebagai Best Seller</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                <input type="checkbox" name="is_new" value="1" checked class="w-4 h-4 text-brand-600 rounded">
                <span>Tandai sebagai Koleksi Baru (New)</span>
            </label>
        </div>

        <div class="pt-4 border-t border-sand-200 flex justify-end gap-3">
            <a href="{{ route('admin.katalog.index') }}" class="px-5 py-2.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md shadow-brand-500/20">
                Simpan ke Katalog
            </button>
        </div>
    </form>

</div>
@endsection
