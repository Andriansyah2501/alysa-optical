<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                  ->orWhere('brand', 'like', "%{$q}%")
                  ->orWhere('frame_shape', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(15);
        $categories = Category::all();

        return view('admin.catalog.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.catalog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'frame_shape' => 'required|string',
            'frame_material' => 'required|string',
            'gender' => 'required|string',
            'size_frame' => 'required|string',
            'color_primary' => 'required|string',
            'color_hex' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
            'is_bestseller' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        $data['color_hex'] = $data['color_hex'] ?: '#1E293B';
        $data['image_url'] = $data['image_url'] ?: 'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&w=800&q=80';
        $data['is_bestseller'] = $request->has('is_bestseller');
        $data['is_new'] = $request->has('is_new');

        Product::create($data);

        return redirect()->route('admin.katalog.index')->with('success', 'Model kacamata "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.catalog.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'frame_shape' => 'required|string',
            'frame_material' => 'required|string',
            'gender' => 'required|string',
            'size_frame' => 'required|string',
            'color_primary' => 'required|string',
            'color_hex' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $data['is_bestseller'] = $request->has('is_bestseller');
        $data['is_new'] = $request->has('is_new');

        $product->update($data);

        return redirect()->route('admin.katalog.index')->with('success', 'Katalog kacamata berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        return redirect()->route('admin.katalog.index')->with('success', "Kacamata {$name} telah dihapus dari katalog.");
    }
}
