<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\LensType;
use App\Services\BarcodeService;
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
        $lensTypes = LensType::all();
        return view('admin.catalog.create', compact('categories', 'lensTypes'));
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
            'barcode' => 'nullable|string|max:32|unique:products,barcode',
            'bundle_lens_type_id' => 'nullable|integer|exists:lens_types,id',
            'bundle_includes_lens' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        $data['color_hex'] = $data['color_hex'] ?: '#1E293B';
        $data['image_url'] = $data['image_url'] ?: 'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&w=800&q=80';
        $data['is_bestseller'] = $request->has('is_bestseller');
        $data['is_new'] = $request->has('is_new');
        $data['bundle_lens_type_id'] = !empty($data['bundle_lens_type_id']) ? (int) $data['bundle_lens_type_id'] : null;
        $data['bundle_includes_lens'] = $data['bundle_lens_type_id'] ? (bool) ($request->input('bundle_includes_lens') === '1' || $data['bundle_includes_lens'] ?? false) : false;
        if (empty($data['barcode'])) {
            unset($data['barcode']); // biarkan model generate AG-##### otomatis
        } else {
            $data['barcode'] = strtoupper(trim($data['barcode']));
        }

        Product::create($data);

        return redirect()->route('admin.katalog.index')->with('success', 'Model kacamata "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $lensTypes = LensType::all();
        return view('admin.catalog.edit', compact('product', 'categories', 'lensTypes'));
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
            'barcode' => 'nullable|string|max:32|unique:products,barcode,' . $product->id,
            'bundle_lens_type_id' => 'nullable|integer|exists:lens_types,id',
            'bundle_includes_lens' => 'nullable|boolean',
        ]);

        $data['is_bestseller'] = $request->has('is_bestseller');
        $data['is_new'] = $request->has('is_new');
        $data['bundle_lens_type_id'] = !empty($data['bundle_lens_type_id']) ? (int) $data['bundle_lens_type_id'] : null;
        $data['bundle_includes_lens'] = $data['bundle_lens_type_id'] ? (bool) ($request->input('bundle_includes_lens') === '1' || $data['bundle_includes_lens'] ?? false) : false;
        $data['barcode'] = !empty($data['barcode'])
            ? strtoupper(trim($data['barcode']))
            : sprintf('AG-%05d', $product->id);

        $product->update($data);

        return redirect()->route('admin.katalog.index')->with('success', 'Katalog kacamata berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        return redirect()->route('admin.katalog.index')->with('success', "Kacamata {$name} telah dihapus dari katalog.");
    }

    /**
     * Lembar cetak label barcode (satu frame atau seluruh katalog).
     */
    public function barcodeLabels(Request $request)
    {
        $query = Product::query()->whereNotNull('barcode')->orderBy('id');

        if ($request->filled('product')) {
            $query->whereIn('id', (array) $request->input('product'));
        }

        $products = $query->get()->each(function ($p) {
            $p->barcode_svg = BarcodeService::svg($p->barcode);
        });

        return view('admin.catalog.labels', compact('products'));
    }

    /**
     * Generate kode barcode yang hilang/belum pernah dibuat untuk produk lama.
     */
    public function generateBarcodes()
    {
        $count = 0;
        Product::where(function ($q) {
            $q->whereNull('barcode')->orWhere('barcode', '');
        })->chunkById(100, function ($products) use (&$count) {
            foreach ($products as $product) {
                BarcodeService::ensureFor($product);
                $count++;
            }
        });

        return back()->with('success', $count . ' kode barcode berhasil dibuat. '
            . 'Total ' . Product::whereNotNull('barcode')->count() . ' produk siap dipindai di kasir.');
    }
}
