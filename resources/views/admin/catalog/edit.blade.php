@extends('layouts.app')

@section('title', 'Edit Kacamata: ' . $product->name . ' | Alysa Glasses')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
    
    <div class="flex items-center justify-between pb-4 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-brand-600 font-bold uppercase tracking-wider">
                <a href="{{ route('admin.katalog.index') }}" class="hover:underline">Katalog Kacamata</a>
                <span>/</span>
                <span>Edit Model</span>
            </div>
            <h1 class="font-serif text-2xl font-bold text-slate-900 mt-1">Perbarui Detail: {{ $product->name }}</h1>
        </div>
        <a href="{{ route('admin.katalog.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('admin.katalog.update', $product->id) }}" method="POST" class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 sm:p-8 space-y-6 text-xs">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Model Kacamata <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1">Kategori Kacamata <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="font-bold text-slate-700 block mb-1">Merk / Brand <span class="text-rose-500">*</span></label>
                <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Bentuk Frame <span class="text-rose-500">*</span></label>
                <select name="frame_shape" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    @foreach(['Round', 'Wayfarer', 'Square', 'Aviator', 'Cat-Eye', 'Geometric'] as $shape)
                        <option value="{{ $shape }}" {{ $product->frame_shape == $shape ? 'selected' : '' }}>{{ $shape }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Target Gender <span class="text-rose-500">*</span></label>
                <select name="gender" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    @foreach(['Unisex', 'Pria', 'Wanita', 'Anak'] as $gen)
                        <option value="{{ $gen }}" {{ $product->gender == $gen ? 'selected' : '' }}>{{ $gen }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="font-bold text-slate-700 block mb-1">Material Frame <span class="text-rose-500">*</span></label>
                <input type="text" name="frame_material" value="{{ old('frame_material', $product->frame_material) }}" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Ukuran (Size) <span class="text-rose-500">*</span></label>
                <input type="text" name="size_frame" value="{{ old('size_frame', $product->size_frame) }}" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Warna Utama & Hex</label>
                <div class="flex gap-2">
                    <input type="text" name="color_primary" value="{{ old('color_primary', $product->color_primary) }}" required class="flex-1 px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                    <input type="color" name="color_hex" value="{{ old('color_hex', $product->color_hex) }}" class="w-12 h-10 p-1 bg-white border border-sand-300 rounded-xl cursor-pointer">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="font-bold text-slate-700 block mb-1">Harga Normal (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Harga Diskon (Rp)</label>
                <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-brand-700">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Stok Gudang <span class="text-rose-500">*</span></label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
            </div>
        </div>

        <div>
            <label class="font-bold text-slate-700 block mb-1">URL Foto Kacamata</label>
            <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
        </div>

        <div>
            <label class="font-bold text-slate-700 block mb-1">Deskripsi & Keunggulan <span class="text-rose-500">*</span></label>
            <textarea name="description" rows="3" required class="w-full px-4 py-2.5 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                <input type="checkbox" name="is_bestseller" value="1" {{ $product->is_bestseller ? 'checked' : '' }} class="w-4 h-4 text-brand-600 rounded">
                <span>Tandai sebagai Best Seller</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                <input type="checkbox" name="is_new" value="1" {{ $product->is_new ? 'checked' : '' }} class="w-4 h-4 text-brand-600 rounded">
                <span>Tandai sebagai Koleksi Baru (New)</span>
            </label>
        </div>

        <div class="pt-4 border-t border-sand-200 flex justify-end gap-3">
            <a href="{{ route('admin.katalog.index') }}" class="px-5 py-2.5 bg-sand-100 hover:bg-sand-200 text-slate-700 rounded-xl font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md shadow-brand-500/20">
                Perbarui Kacamata
            </button>
        </div>
    </form>

</div>
@endsection
