<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\LensType;
use App\Models\Order;
use App\Services\WhatsAppNotificationService;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GlassesShopController extends Controller
{
    // 1. Katalog Utama & Filter Kacamata
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->get();
        $query = Product::with('category')->where('stock', '>', 0);

        if ($request->filled('kategori')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }

        if ($request->filled('bentuk')) {
            $query->where('frame_shape', $request->bentuk);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->get('sort') == 'termurah') {
            $query->orderBy('price', 'asc');
        } elseif ($request->get('sort') == 'termahal') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $lensTypes = LensType::all();

        return view('shop.index', compact('categories', 'products', 'lensTypes'));
    }

    // 2. Detail Frame Kacamata & Pilihan Lensa
    public function show($slug)
    {
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        $lensTypes = LensType::all();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'lensTypes', 'relatedProducts'));
    }

    // 3. Checkout & Form Resep Kacamata
    public function checkout(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $lens = null;
        if ($request->filled('lens_id')) {
            $lens = LensType::find($request->lens_id);
        }
        $allLenses = LensType::all();
        $availableVouchers = Voucher::where('is_active', true)->get();

        return view('shop.checkout', compact('product', 'lens', 'allLenses', 'availableVouchers'));
    }

    // 4. Proses Transaksi Order Pembelian Kacamata + Voucher Diskon
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'lens_type_id' => 'required|exists:lens_types,id',
            'customer_name' => 'required|string|max:150',
            'customer_email' => 'required|email|max:150',
            'customer_phone' => 'required|string|max:30',
            'shipping_address' => 'required|string',
            'city' => 'required|string|max:100',
            'payment_method' => 'required|string',
            'has_prescription' => 'nullable|boolean',
            'sph_r' => 'nullable|string|max:10',
            'cyl_r' => 'nullable|string|max:10',
            'axis_r' => 'nullable|string|max:10',
            'sph_l' => 'nullable|string|max:10',
            'cyl_l' => 'nullable|string|max:10',
            'axis_l' => 'nullable|string|max:10',
            'pd' => 'nullable|string|max:10',
            'prescription_notes' => 'nullable|string|max:500',
            'voucher_code' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $lens = LensType::findOrFail($validated['lens_type_id']);

        $productPrice = $product->effective_price;
        $lensPrice = $lens->additional_price;
        $subtotal = $productPrice;
        $totalBeforeDiscount = $subtotal + $lensPrice;

        // Validasi Voucher Diskon
        $discountAmount = 0;
        $appliedVoucherCode = null;

        if ($request->filled('voucher_code')) {
            $vCode = strtoupper(trim($request->voucher_code));
            $voucher = Voucher::where('code', $vCode)->first();
            if ($voucher) {
                $check = $voucher->isValidFor($totalBeforeDiscount);
                if ($check['valid']) {
                    $discountAmount = $check['discount'];
                    $appliedVoucherCode = $voucher->code;
                    $voucher->increment('used_count');
                }
            }
        }

        $grandTotal = max(0, $totalBeforeDiscount - $discountAmount);

        $orderCode = 'ALYSA-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $order = Order::create([
            'order_code' => $orderCode,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'city' => $validated['city'],
            'shipping_courier' => 'JNE Express (Bebas Ongkir Promo)',
            'lens_type_name' => $lens->name,
            'lens_price' => $lensPrice,
            'voucher_code' => $appliedVoucherCode,
            'discount_amount' => $discountAmount,
            'has_prescription' => $request->has('has_prescription'),
            'sph_r' => $request->sph_r,
            'cyl_r' => $request->cyl_r,
            'axis_r' => $request->axis_r,
            'sph_l' => $request->sph_l,
            'cyl_l' => $request->cyl_l,
            'axis_l' => $request->axis_l,
            'pd' => $request->pd ?: '62',
            'prescription_notes' => $request->prescription_notes,
            'subtotal' => $subtotal,
            'grand_total' => $grandTotal,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'Lunas (Terverifikasi Otomatis)',
            'order_status' => 'Diproses di Lab Optik',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $productPrice,
            'quantity' => 1,
            'total' => $productPrice,
        ]);

        if ($product->stock > 0) {
            $product->decrement('stock');
        }

        // OTOMASI KONSUMEN CHECKOUT:
        // 1. Sinkronisasi otomatis profil pelanggan ke modul data pelanggan (CRM)
        try {
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['customer_phone']);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            $existingCust = \App\Models\Customer::where('phone', $cleanPhone)->orWhere('phone', $validated['customer_phone'])->first();
            if (!$existingCust) {
                $nextCode = sprintf('CUST-%04d', \App\Models\Customer::count() + 1);
                \App\Models\Customer::create([
                    'customer_code' => $nextCode,
                    'name' => $validated['customer_name'],
                    'phone' => $cleanPhone,
                    'email' => $validated['customer_email'],
                    'address' => $validated['shipping_address'],
                    'city' => $validated['city'],
                    'sph_r' => $request->sph_r,
                    'cyl_r' => $request->cyl_r,
                    'axis_r' => $request->axis_r,
                    'sph_l' => $request->sph_l,
                    'cyl_l' => $request->cyl_l,
                    'axis_l' => $request->axis_l,
                    'pd' => $request->pd ?: '62',
                    'notes' => $request->prescription_notes,
                    'total_orders' => 1,
                    'total_spent' => $grandTotal,
                    'last_visit_at' => now(),
                ]);
            } else {
                $existingCust->increment('total_orders');
                $existingCust->increment('total_spent', $grandTotal);
                $existingCust->update([
                    'last_visit_at' => now(),
                    'sph_r' => $request->sph_r ?: $existingCust->sph_r,
                    'cyl_r' => $request->cyl_r ?: $existingCust->cyl_r,
                    'axis_r' => $request->axis_r ?: $existingCust->axis_r,
                    'sph_l' => $request->sph_l ?: $existingCust->sph_l,
                    'cyl_l' => $request->cyl_l ?: $existingCust->cyl_l,
                    'axis_l' => $request->axis_l ?: $existingCust->axis_l,
                    'pd' => $request->pd ?: $existingCust->pd,
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal sinkron CRM konsumen checkout: " . $e->getMessage());
        }

        // 2. Kirim notifikasi WhatsApp otomatis ke konsumen checkout
        try {
            WhatsAppNotificationService::sendOrderNotification($order, 'order_created');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal kirim WA order checkout: " . $e->getMessage());
        }

        return redirect()->route('shop.order.success', $orderCode);
    }

    // 5. Konfirmasi Pembayaran Berhasil
    public function orderSuccess($orderCode)
    {
        $order = Order::with('items.product')->where('order_code', $orderCode)->firstOrFail();
        return view('shop.success', compact('order'));
    }

    // 6. Lacak Status Resep & Pengiriman Kacamata
    public function track(Request $request)
    {
        $order = null;
        if ($request->filled('code')) {
            $order = Order::with('items.product')->where('order_code', trim($request->code))->first();
        }
        return view('shop.track', compact('order'));
    }

    // 7. Panel Manajemen Lab Optik & Kasir
    public function adminDashboard()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('grand_total');
        $totalProducts = Product::count();
        $recentOrders = Order::with('items')->latest()->take(15)->get();
        $products = Product::with('category')->latest()->get();

        return view('admin.optik', compact('totalOrders', 'totalRevenue', 'totalProducts', 'recentOrders', 'products'));
    }

    // 8. Tampilan Detail Validasi Pemesan Lengkap (Admin & Refraksionis)
    public function adminOrderDetail($id)
    {
        $order = Order::with(['items.product.category'])->findOrFail($id);
        $settings = \App\Models\StoreSetting::all()->pluck('value', 'key');
        
        return view('admin.orders.show', compact('order', 'settings'));
    }

    // 9. Update Status Pesanan, Resi, Validasi Resep & Notifikasi
    public function adminUpdateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'payment_status' => 'required|string',
            'order_status' => 'required|string',
            'tracking_number' => 'nullable|string|max:100',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $order->payment_status = $validated['payment_status'];
        $order->order_status = $validated['order_status'];
        $order->tracking_number = $validated['tracking_number'] ?? $order->tracking_number;
        $order->admin_notes = $validated['admin_notes'] ?? $order->admin_notes;

        if ($validated['order_status'] !== 'Menunggu Pembayaran' && is_null($order->validated_at)) {
            $order->validated_at = now();
        }

        $order->save();

        // Kirim update status otomatis via WA
        WhatsAppNotificationService::sendOrderNotification($order, 'status_updated');

        return redirect()->route('admin.order.detail', $order->id)
            ->with('success', 'Status pesanan ' . $order->order_code . ' berhasil diperbarui dan divalidasi!');
    }

    // ==========================================
    // CRUD TRANSAKSI LENGKAP (SUPER ADMIN & ADMIN)
    // ==========================================

    // CREATE: Form Tambah Transaksi Manual Backoffice
    public function adminOrderCreate()
    {
        $products = Product::where('stock', '>', 0)->latest()->get();
        $lensTypes = LensType::all();
        $vouchers = Voucher::where('is_active', true)->get();

        return view('admin.orders.create', compact('products', 'lensTypes', 'vouchers'));
    }

    // STORE: Simpan Transaksi Manual Baru
    public function adminOrderStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:50',
            'customer_email' => 'nullable|email|max:150',
            'customer_address' => 'nullable|string|max:500',
            'customer_address' => 'nullable|string|max:500',
            'transaction_type' => 'required|in:catalog,custom_frame,lens_only,service_only',
            'product_id' => 'nullable',
            'custom_frame_name' => 'nullable|string|max:200',
            'frame_price' => 'required|numeric|min:0',
            'lens_type_id' => 'nullable',
            'lens_price' => 'nullable|numeric|min:0',
            'service_name' => 'nullable|string|max:150',
            'service_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_type' => 'required|in:full,dp',
            'dp_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|string',
            'order_status' => 'required|string',
            'has_prescription' => 'nullable|boolean',
            'sph_r' => 'nullable|string|max:15',
            'cyl_r' => 'nullable|string|max:15',
            'axis_r' => 'nullable|string|max:15',
            'sph_l' => 'nullable|string|max:15',
            'cyl_l' => 'nullable|string|max:15',
            'axis_l' => 'nullable|string|max:15',
            'pd' => 'nullable|string|max:15',
            'prescription_notes' => 'nullable|string|max:500',
            'admin_notes' => 'nullable|string|max:1000',
            'tracking_number' => 'nullable|string|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $subtotal = floatval($validated['frame_price']);
        $lensPrice = floatval($validated['lens_price'] ?? 0);
        $serviceFee = floatval($validated['service_fee'] ?? 0);
        $discount = floatval($validated['discount_amount'] ?? 0);

        $grandTotal = max(0, ($subtotal + $lensPrice + $serviceFee) - $discount);

        $paymentType = $validated['payment_type'];
        $dpAmount = $paymentType === 'dp' ? floatval($validated['dp_amount'] ?? 0) : $grandTotal;
        $remaining = max(0, $grandTotal - $dpAmount);

        $orderCode = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        $lensTypeName = null;
        if (!empty($validated['lens_type_id'])) {
            $lens = LensType::find($validated['lens_type_id']);
            $lensTypeName = $lens ? $lens->name : null;
        }

        $order = Order::create([
            'order_code' => $orderCode,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?: 'Walk-in Customer',
            'customer_email' => $validated['customer_email'] ?: 'admin@alysaglasses.com',
            'shipping_address' => $request->filled('customer_address') ? $request->customer_address : 'Store Batam (Ambil Sendiri)',
            'city' => 'Batam Store',
            'shipping_courier' => 'Pickup / Ambil Sendiri',
            'transaction_type' => $validated['transaction_type'],
            'lens_type_name' => $lensTypeName,
            'lens_price' => $lensPrice,
            'service_fee' => $serviceFee,
            'service_name' => $validated['service_name'],
            'discount_amount' => $discount,
            'subtotal' => $subtotal,
            'grand_total' => $grandTotal,
            'payment_type' => $paymentType,
            'dp_amount' => $dpAmount,
            'remaining_balance' => $remaining,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_status'],
            'order_status' => $validated['order_status'],
            'has_prescription' => $request->has('has_prescription'),
            'sph_r' => $request->sph_r,
            'cyl_r' => $request->cyl_r,
            'axis_r' => $request->axis_r,
            'sph_l' => $request->sph_l,
            'cyl_l' => $request->cyl_l,
            'axis_l' => $request->axis_l,
            'pd' => $request->pd ?: '62',
            'prescription_notes' => $request->prescription_notes,
            'admin_notes' => $request->admin_notes,
            'tracking_number' => $request->tracking_number,
            'validated_at' => now(),
        ]);

        // Simpan item transaksi
        $productModel = !empty($validated['product_id']) ? Product::find($validated['product_id']) : null;
        $frameTitle = $productModel ? $productModel->name : ($validated['custom_frame_name'] ?: 'Frame Custom Pelanggan');

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $productModel ? $productModel->id : null,
            'item_type' => $validated['transaction_type'],
            'product_name' => $frameTitle,
            'price' => $subtotal,
            'quantity' => 1,
            'total' => $subtotal,
        ]);

        return redirect()->route('admin.order.detail', $order->id)->with('success', "Transaksi baru {$order->order_code} berhasil dibuat.");
    }

    // EDIT: Form Edit Data Transaksi Lengkap
    public function adminOrderEdit($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $products = Product::all();
        $lensTypes = LensType::all();

        return view('admin.orders.edit', compact('order', 'products', 'lensTypes'));
    }

    // UPDATE: Simpan Perubahan Data Transaksi Lengkap
    public function adminOrderUpdate(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:50',
            'customer_email' => 'nullable|email|max:150',
            'customer_address' => 'nullable|string|max:500',
            'customer_address' => 'nullable|string|max:500',
            'transaction_type' => 'required|string',
            'item_product_name' => 'required|string|max:200',
            'subtotal' => 'required|numeric|min:0',
            'lens_type_name' => 'nullable|string|max:150',
            'lens_price' => 'nullable|numeric|min:0',
            'service_name' => 'nullable|string|max:150',
            'service_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'payment_type' => 'required|in:full,dp',
            'dp_amount' => 'nullable|numeric|min:0',
            'remaining_balance' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
            'order_status' => 'required|string',
            'tracking_number' => 'nullable|string|max:100',
            'admin_notes' => 'nullable|string|max:1000',
            'has_prescription' => 'nullable|boolean',
            'sph_r' => 'nullable|string|max:15',
            'cyl_r' => 'nullable|string|max:15',
            'axis_r' => 'nullable|string|max:15',
            'sph_l' => 'nullable|string|max:15',
            'cyl_l' => 'nullable|string|max:15',
            'axis_l' => 'nullable|string|max:15',
            'pd' => 'nullable|string|max:15',
            'prescription_notes' => 'nullable|string|max:500',
        ]);

        $order->customer_name = $validated['customer_name'];
        $order->customer_phone = $validated['customer_phone'];
        $order->customer_email = $validated['customer_email'];
        $order->shipping_address = $request->filled('customer_address') ? $request->customer_address : $order->shipping_address;
        $order->transaction_type = $validated['transaction_type'];
        $order->subtotal = $validated['subtotal'];
        $order->lens_type_name = $validated['lens_type_name'];
        $order->lens_price = floatval($validated['lens_price'] ?? 0);
        $order->service_name = $validated['service_name'];
        $order->service_fee = floatval($validated['service_fee'] ?? 0);
        $order->discount_amount = floatval($validated['discount_amount'] ?? 0);
        $order->grand_total = $validated['grand_total'];
        $order->payment_type = $validated['payment_type'];
        $order->dp_amount = floatval($validated['dp_amount'] ?? 0);
        $order->remaining_balance = floatval($validated['remaining_balance'] ?? 0);
        $order->payment_method = $validated['payment_method'];
        $order->payment_status = $validated['payment_status'];
        $order->order_status = $validated['order_status'];
        $order->tracking_number = $validated['tracking_number'];
        $order->admin_notes = $validated['admin_notes'];
        
        $order->has_prescription = $request->has('has_prescription');
        $order->sph_r = $request->sph_r;
        $order->cyl_r = $request->cyl_r;
        $order->axis_r = $request->axis_r;
        $order->sph_l = $request->sph_l;
        $order->cyl_l = $request->cyl_l;
        $order->axis_l = $request->axis_l;
        $order->pd = $request->pd;
        $order->prescription_notes = $request->prescription_notes;
        $order->save();

        // Update item pertama
        if ($order->items->isNotEmpty()) {
            $item = $order->items->first();
            $item->product_name = $validated['item_product_name'];
            $item->price = $validated['subtotal'];
            $item->total = $validated['subtotal'];
            $item->item_type = $validated['transaction_type'];
            $item->save();
        }

        return redirect()->route('admin.order.detail', $order->id)->with('success', "Transaksi {$order->order_code} berhasil diperbarui.");
    }

    // DESTROY: Hapus Transaksi Secara Permanen
    public function adminOrderDestroy($id)
    {
        DB::transaction(function () use ($id, &$orderCode) {
            $order = Order::with('items')->findOrFail($id);
            $orderCode = $order->order_code;

            // Kembalikan stok produk jika ada item terkait
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $prod = Product::find($item->product_id);
                    if ($prod) {
                        $prod->increment('stock', $item->quantity ?? 1);
                    }
                }
            }

            $order->items()->delete();
            $order->delete();
        });

        return redirect()->route('admin.dashboard')->with('success', "Transaksi {$orderCode} berhasil dihapus dan stok produk telah dikembalikan.");
    }

    // BATCH DESTROY: Hapus Banyak Transaksi Sekaligus Berdasarkan Checklist dengan Rollback Stok
    public function adminOrderBatchDestroy(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        if (empty($orderIds)) {
            return redirect()->route('admin.dashboard')->with('error', 'Tidak ada data pesanan yang dipilih untuk dihapus.');
        }

        $count = 0;
        DB::transaction(function () use ($orderIds, &$count) {
            foreach ($orderIds as $id) {
                $order = Order::with('items')->find($id);
                if ($order) {
                    foreach ($order->items as $item) {
                        if ($item->product_id) {
                            $prod = Product::find($item->product_id);
                            if ($prod) {
                                $prod->increment('stock', $item->quantity ?? 1);
                            }
                        }
                    }
                    $order->items()->delete();
                    $order->delete();
                    $count++;
                }
            }
        });

        return redirect()->route('admin.dashboard')->with('success', "Berhasil menghapus {$count} transaksi dan memulihkan stok inventaris fisik.");
    }


    // 2b. Landing Page & Panel Khusus Pemesanan Online Pelanggan
    public function customerOrderPortal(Request $request)
    {
        $products = Product::orderBy("name")->get();
        $lensTypes = LensType::all();
        $vouchers = Voucher::where("is_active", true)->get();
        $selectedProductId = $request->query("frame_id");
        $selectedLensId = $request->query("lens_id");

        return view("shop.order_portal", compact("products", "lensTypes", "vouchers", "selectedProductId", "selectedLensId"));
    }

    // 2c. Simpan Pesanan Online dari Panel Pelanggan
    public function storeCustomerOrderPortal(Request $request)
    {
        $validated = $request->validate([
            "order_mode" => "required|in:complete,lens_only,frame_only,service",
            "product_id" => "nullable|exists:products,id",
            "custom_frame_name" => "nullable|string|max:150",
            "frame_price" => "nullable|numeric|min:0",
            "lens_type_id" => "nullable|exists:lens_types,id",
            "customer_name" => "required|string|max:150",
            "customer_phone" => "required|string|max:30",
            "customer_email" => "nullable|email|max:150",
            "shipping_address" => "required|string|max:500",
            "city" => "nullable|string|max:100",
            "payment_type" => "required|in:full,dp",
            "dp_amount" => "nullable|numeric|min:0",
            "payment_method" => "required|string",
            "has_prescription" => "nullable|boolean",
            "sph_r" => "nullable|string|max:10",
            "cyl_r" => "nullable|string|max:10",
            "axis_r" => "nullable|string|max:10",
            "sph_l" => "nullable|string|max:10",
            "cyl_l" => "nullable|string|max:10",
            "axis_l" => "nullable|string|max:10",
            "pd" => "nullable|string|max:10",
            "notes" => "nullable|string|max:500",
            "voucher_code" => "nullable|string|max:50",
        ]);

        $mode = $validated["order_mode"];
        $framePrice = 0;
        $frameTitle = "-";

        if ($mode === "complete" || $mode === "frame_only") {
            if (!empty($validated["product_id"])) {
                $p = Product::findOrFail($validated["product_id"]);
                $framePrice = $p->effective_price;
                $frameTitle = $p->name;
            } elseif (!empty($validated["custom_frame_name"])) {
                $framePrice = (float) ($validated["frame_price"] ?? 0);
                $frameTitle = $validated["custom_frame_name"];
            }
        } elseif ($mode === "lens_only") {
            $frameTitle = "Ganti Kaca / Lensa Saja (Frame Sendiri)";
            $framePrice = 0;
        } elseif ($mode === "service") {
            $frameTitle = "Jasa Servis / Reparasi / Fitting Kacamata";
            $framePrice = (float) ($validated["frame_price"] ?? 35000);
        }

        $lensPrice = 0;
        $lensTitle = "-";
        $lensId = null;
        if (($mode === "complete" || $mode === "lens_only") && !empty($validated["lens_type_id"])) {
            $lens = LensType::findOrFail($validated["lens_type_id"]);
            $lensPrice = (float) $lens->additional_price;
            $lensTitle = $lens->name;
            $lensId = $lens->id;
        }

        $subtotal = $framePrice + $lensPrice;
        $discount = 0;
        $voucherCode = null;

        if (!empty($validated["voucher_code"])) {
            $vc = Voucher::where("code", strtoupper(trim($validated["voucher_code"])))->where("is_active", true)->first();
            if ($vc && $subtotal >= (float)$vc->min_spend) {
                $voucherCode = $vc->code;
                $discount = (float) $vc->discount_amount;
                if ($discount > $subtotal) $discount = $subtotal;
            }
        }

        $grandTotal = max(0, $subtotal - $discount);
        $paymentType = $validated["payment_type"];
        $dpAmount = ($paymentType === "dp") ? (float)($validated["dp_amount"] ?? ($grandTotal * 0.5)) : 0;
        if ($paymentType === "full") {
            $remaining = 0;
            $paymentStatus = "paid";
        } else {
            $remaining = max(0, $grandTotal - $dpAmount);
            $paymentStatus = "partially_paid";
        }

        $orderCode = "WEB-" . date("Ymd") . "-" . strtoupper(Str::random(5));
        $email = !empty($validated["customer_email"]) ? $validated["customer_email"] : "cust." . strtolower(Str::slug($validated["customer_name"])) . "@alysa.customer";

        DB::transaction(function () use (
            $orderCode, $validated, $email, $mode, $paymentType, $dpAmount, $remaining,
            $paymentStatus, $request, $lensTitle, $lensPrice, $framePrice, $frameTitle,
            $voucherCode, $discount, $subtotal, $grandTotal
        ) {
            $order = Order::create([
                "order_code" => $orderCode,
                "customer_name" => $validated["customer_name"],
                "customer_email" => $email,
                "customer_phone" => $validated["customer_phone"],
                "shipping_address" => $validated["shipping_address"],
                "city" => $validated["city"] ?? "Batam",
                "transaction_type" => ($mode === "service") ? "service" : (($mode === "lens_only") ? "lens_only" : "retail"),
                "payment_type" => $paymentType,
                "dp_amount" => $dpAmount,
                "remaining_balance" => $remaining,
                "payment_method" => $validated["payment_method"],
                "payment_status" => $paymentStatus,
                "order_status" => "pending",
                "has_prescription" => $request->has("has_prescription"),
                "lens_type_name" => $lensTitle,
                "lens_price" => $lensPrice,
                "service_fee" => ($mode === "service") ? $framePrice : 0,
                "service_name" => ($mode === "service") ? $frameTitle : null,
                "voucher_code" => $voucherCode,
                "discount_amount" => $discount,
                "sph_r" => $validated["sph_r"] ?? null,
                "cyl_r" => $validated["cyl_r"] ?? null,
                "axis_r" => $validated["axis_r"] ?? null,
                "sph_l" => $validated["sph_l"] ?? null,
                "cyl_l" => $validated["cyl_l"] ?? null,
                "axis_l" => $validated["axis_l"] ?? null,
                "pd" => $validated["pd"] ?? null,
                "prescription_notes" => $validated["notes"] ?? null,
                "subtotal" => $subtotal,
                "grand_total" => $grandTotal,
            ]);

            OrderItem::create([
                "order_id" => $order->id,
                "product_id" => !empty($validated["product_id"]) ? $validated["product_id"] : null,
                "item_type" => ($mode === "service") ? "service" : (($mode === "lens_only") ? "lens" : "frame"),
                "product_name" => $frameTitle . ($lensTitle !== "-" ? " + " . $lensTitle : ""),
                "price" => $subtotal,
                "quantity" => 1,
                "total" => $subtotal,
            ]);

            // Potong stok produk etalase jika dipesan
            if (!empty($validated["product_id"])) {
                $p = Product::find($validated["product_id"]);
                if ($p && $p->stock > 0) {
                    $p->decrement('stock');
                }
            }
        });

        return redirect()->route("shop.order.success", ["orderCode" => $orderCode])
            ->with("success", "Pesanan online Anda berhasil dibuat dengan kode {$orderCode}!");
    }
    
}
