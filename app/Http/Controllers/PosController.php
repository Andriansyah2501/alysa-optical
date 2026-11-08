<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\LensType;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\StoreSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WhatsAppNotificationService;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->latest()->get();
        $lensTypes = LensType::all();
        $customers = Customer::orderBy('name', 'asc')->get();
        $storeName = StoreSetting::get('store_name', 'ALYSA GLASSES');
        
        return view('pos.index', compact('products', 'lensTypes', 'customers', 'storeName'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'customer_address' => 'nullable|string|max:500',
            'order_mode' => 'required|in:catalog,custom_frame,lens_only,service_only',
            
            // Mode Catalog
            'product_id' => 'nullable|exists:products,id',
            
            // Mode Custom Frame
            'custom_frame_name' => 'nullable|string|max:200',
            'custom_frame_model' => 'nullable|string|max:100',
            'custom_frame_price' => 'nullable|numeric|min:0',

            // Lensa
            'lens_type_id' => 'nullable',
            'custom_lens_price' => 'nullable|numeric|min:0',

            // Upah Jasa / Servis / Ganti Sparepart
            'has_service_fee' => 'nullable|boolean',
            'service_name' => 'nullable|string|max:150',
            'service_fee' => 'nullable|numeric|min:0',

            // SISTEM DOWN PAYMENT (DP) / PELUNASAN
            'payment_type' => 'required|in:full,dp',
            'dp_amount_input' => 'nullable|numeric|min:0',

            'payment_method' => 'required|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'shipping_courier' => 'nullable|string|max:100',
            'amount_paid' => 'required|numeric|min:0',
            
            // Resep
            'has_prescription' => 'nullable|boolean',
            'sph_r' => 'nullable|string|max:15',
            'cyl_r' => 'nullable|string|max:15',
            'axis_r' => 'nullable|string|max:15',
            'sph_l' => 'nullable|string|max:15',
            'cyl_l' => 'nullable|string|max:15',
            'axis_l' => 'nullable|string|max:15',
            'pd' => 'nullable|string|max:15',
            'prescription_notes' => 'nullable|string|max:500',
            'voucher_code' => 'nullable|string|max:50',
        ]);

        $mode = $validated['order_mode'];
        $framePrice = 0;
        $frameTitle = '';
        $productModel = null;
        $itemType = 'product';

        // 1. Logika Frame
        if ($mode === 'catalog') {
            $productModel = Product::findOrFail($validated['product_id']);
            $framePrice = (float) $productModel->effective_price;
            $frameTitle = $productModel->name . ' (' . $productModel->frame_shape . ')';
            $itemType = 'product';
        } elseif ($mode === 'custom_frame') {
            $framePrice = floatval($validated['custom_frame_price'] ?? 0);
            $cName = trim($validated['custom_frame_name'] ?? 'Custom Frame Pelanggan');
            $cModel = trim($validated['custom_frame_model'] ?? 'Custom');
            $frameTitle = $cName . ' [' . $cModel . ']';
            $itemType = 'custom_frame';
        } elseif ($mode === 'lens_only') {
            $framePrice = 0;
            $frameTitle = 'Hanya Ganti Lensa (Frame Bawa Sendiri)';
            $itemType = 'lens_only';
        } elseif ($mode === 'service_only') {
            $framePrice = 0;
            $frameTitle = 'Jasa Servis / Reparasi Frame Kacamata';
            $itemType = 'service_fee';
        }

        // 2. Logika Lensa
        $lensTitle = null;
        $lensPrice = 0;
        if (in_array($mode, ['catalog', 'custom_frame', 'lens_only'])) {
            if (!empty($validated['lens_type_id']) && $validated['lens_type_id'] !== 'none') {
                $lens = LensType::find($validated['lens_type_id']);
                if ($lens) {
                    $lensTitle = $lens->name;
                    $lensPrice = (float) $lens->additional_price;
                }
            }
            if ($request->filled('custom_lens_price') && floatval($request->custom_lens_price) > 0) {
                $lensPrice = floatval($request->custom_lens_price);
            }
        }

        // 3. Logika Biaya Jasa / Upah Servis
        $serviceFee = 0;
        $serviceName = null;
        if ($mode === 'service_only' || $request->has('has_service_fee')) {
            $serviceFee = floatval($validated['service_fee'] ?? 0);
            $serviceName = trim($validated['service_name'] ?? 'Jasa Pasang / Servis Frame');
        }

        $subtotal = $framePrice;
        $totalBeforeDiscount = $subtotal + $lensPrice + $serviceFee;

        // 4. Validasi Voucher POS
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

        $shippingCost = floatval($request->input('shipping_cost', 0));
        $grandTotal = max(0, $totalBeforeDiscount - $discountAmount + $shippingCost);

        // 5. Logika Down Payment (DP) vs Full Payment
        $paymentType = $validated['payment_type'];
        $dpAmount = 0;
        $remainingBalance = 0;
        $paymentStatus = 'Lunas';
        $orderStatus = 'Selesai / Dibayar di Kasir';

        $amountPaid = floatval($validated['amount_paid']);

        if ($paymentType === 'dp') {
            $dpAmount = floatval($validated['dp_amount_input'] ?? $amountPaid);
            if ($dpAmount > $grandTotal) {
                $dpAmount = $grandTotal;
            }
            $remainingBalance = max(0, $grandTotal - $dpAmount);

            if ($remainingBalance > 0) {
                $paymentStatus = 'DP (Uang Muka Terbayar)';
                $orderStatus = 'Proses Potong Lensa (Lab Optik)';
            } else {
                $paymentStatus = 'Lunas';
                $orderStatus = 'Selesai / Dibayar di Kasir';
            }
            $change = max(0, $amountPaid - $dpAmount);
        } else {
            // Full Payment
            $dpAmount = $grandTotal;
            $remainingBalance = 0;
            $paymentStatus = 'Lunas';
            $change = max(0, $amountPaid - $grandTotal);
        }

        $orderCode = 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Eksekusi Atomic Database Transaction
        DB::transaction(function () use (
            $orderCode, $mode, $validated, $request, $lensTitle, $lensPrice, $serviceFee,
            $serviceName, $appliedVoucherCode, $discountAmount, $subtotal, $grandTotal,
            $paymentType, $dpAmount, $remainingBalance, $paymentStatus, $orderStatus,
            $productModel, $itemType, $frameTitle, $framePrice, $shippingCost
        ) {
            // 6. Simpan Order
            $order = Order::create([
                'order_code' => $orderCode,
                'transaction_type' => $mode,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?: 'Walk-in Customer',
                'customer_email' => 'kasir@alysaglasses.com',
                'shipping_address' => $request->filled('customer_address') ? $request->customer_address : 'Transaksi Langsung di Store (POS Kasir)',
                'city' => $request->filled('customer_city') ? $request->customer_city : 'Batam',
                'shipping_courier' => $request->filled('shipping_courier') ? $request->shipping_courier : ($shippingCost > 0 ? 'Kurir Lokal / Ekspedisi' : 'Pickup / Ambil Langsung'),
                'shipping_cost' => $shippingCost,
                'lens_type_name' => $lensTitle,
                'lens_price' => $lensPrice,
                'service_fee' => $serviceFee,
                'service_name' => $serviceName,
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
                'payment_type' => $paymentType,
                'dp_amount' => $dpAmount,
                'remaining_balance' => $remainingBalance,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
            ]);

            // 7. Simpan Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productModel ? $productModel->id : null,
                'item_type' => $itemType,
                'product_name' => $frameTitle,
                'price' => $framePrice,
                'quantity' => 1,
                'total' => $framePrice,
            ]);

            // Potong stok produk jika ada
            if ($productModel && $productModel->stock > 0) {
                $productModel->decrement('stock');
            }

            // Sinkronisasi data ke database Pelanggan (Customer CRM & Refraksi)
            $phone = trim($validated['customer_phone']);
            $customer = Customer::where('phone', $phone)->first();
            if (!$customer) {
                $custCount = Customer::count() + 1;
                $customer = new Customer();
                $customer->customer_code = sprintf('CUST-%04d', $custCount);
                $customer->phone = $phone;
                $customer->total_orders = 0;
                $customer->total_spent = 0;
            }
            $customer->name = $validated['customer_name'];
            $customer->city = 'Batam Store';
            $customer->total_orders += 1;
            $customer->total_spent += (float)$grandTotal;
            $customer->last_visit_at = now();
            if (!empty($validated['sph_r']) || !empty($validated['pd'])) {
                $customer->sph_r = $validated['sph_r'] ?? $customer->sph_r;
                $customer->cyl_r = $validated['cyl_r'] ?? $customer->cyl_r;
                $customer->axis_r = $validated['axis_r'] ?? $customer->axis_r;
                $customer->sph_l = $validated['sph_l'] ?? $customer->sph_l;
                $customer->cyl_l = $validated['cyl_l'] ?? $customer->cyl_l;
                $customer->axis_l = $validated['axis_l'] ?? $customer->axis_l;
                $customer->pd = $validated['pd'] ?? $customer->pd;
            }
            $customer->save();

        });

        // Trigger notifikasi WhatsApp otomatis jika gateway aktif
        $createdOrder = Order::where('order_code', $orderCode)->first();
        if ($createdOrder) {
            WhatsAppNotificationService::sendOrderNotification($createdOrder, 'order_created');
        }

        return redirect()->route('pos.receipt', [
            'orderCode' => $orderCode,
            'paid' => $amountPaid,
            'change' => $change
        ]);
    }

    public function receipt(Request $request, $orderCode)
    {
        $order = Order::with('items.product')->where('order_code', $orderCode)->firstOrFail();
        
        $settings = StoreSetting::all()->pluck('value', 'key');
        
        // Jika DP, default paid adalah dp_amount, jika Full adalah grand_total
        $defaultPaid = $order->payment_type === 'dp' ? $order->dp_amount : $order->grand_total;
        $paid = $request->query('paid', $defaultPaid);
        $change = $request->query('change', 0);

        return view('pos.receipt', compact('order', 'settings', 'paid', 'change'));
    }

    // 8. Pelunasan Sisa Pembayaran DP (Saat Kacamata Diambil Pelanggan)
    public function settlePayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'settlement_payment_method' => 'required|string',
            'settlement_amount_paid' => 'required|numeric|min:' . $order->remaining_balance,
        ]);

        $settlePaid = floatval($validated['settlement_amount_paid']);
        $settleChange = max(0, $settlePaid - $order->remaining_balance);

        $order->dp_amount = $order->grand_total;
        $order->remaining_balance = 0;
        $order->payment_status = 'Lunas (Telah Dilunasi)';
        $order->order_status = 'Selesai / Diterima Pelanggan';
        $order->settled_at = now();
        $order->settlement_payment_method = $validated['settlement_payment_method'];
        $order->save();

        return redirect()->route('pos.receipt', [
            'orderCode' => $order->order_code,
            'paid' => $settlePaid,
            'change' => $settleChange
        ])->with('success', 'Pelunasan pesanan ' . $order->order_code . ' berhasil diproses!');
    }

    /**
     * Endpoint API JSON resmi untuk aplikasi THERMER (Bluetooth Thermal Printer - mate.bluetoothprint)
     */
    public function getThermerJson($orderCode)
    {
        $order = Order::with('items')->where('order_code', $orderCode)->firstOrFail();
        $settings = StoreSetting::all()->pluck('value', 'key');
        $storeName = $settings['store_name'] ?? 'ALYSA GLASSES';

        $a = [];

        // 1. Header Toko
        $a[] = (object)['type' => 0, 'content' => $storeName, 'bold' => 1, 'align' => 1, 'format' => 2];
        $a[] = (object)['type' => 0, 'content' => $settings['store_tagline'] ?? 'Eyewear That Completes Your Look', 'bold' => 0, 'align' => 1, 'format' => 4];
        $a[] = (object)['type' => 0, 'content' => $settings['store_address'] ?? 'Batam • Tanjung Pinang • Bintan', 'bold' => 0, 'align' => 1, 'format' => 4];
        $a[] = (object)['type' => 0, 'content' => 'Telp: ' . ($settings['store_phone'] ?? '0812-3456-7890'), 'bold' => 0, 'align' => 1, 'format' => 4];
        $a[] = (object)['type' => 0, 'content' => '--------------------------------', 'bold' => 0, 'align' => 1, 'format' => 0];

        // 2. Status Transaksi
        if ($order->payment_type === 'dp' && $order->remaining_balance > 0) {
            $a[] = (object)['type' => 0, 'content' => '*** STRUK UANG MUKA (DP) ***', 'bold' => 1, 'align' => 1, 'format' => 0];
        } else {
            $a[] = (object)['type' => 0, 'content' => '*** PEMBAYARAN LUNAS ***', 'bold' => 1, 'align' => 1, 'format' => 0];
        }

        $a[] = (object)['type' => 0, 'content' => 'No. Struk : ' . $order->order_code, 'bold' => 1, 'align' => 0, 'format' => 0];
        $a[] = (object)['type' => 0, 'content' => 'Tanggal   : ' . $order->created_at->format('d/m/Y H:i') . ' WIB', 'bold' => 0, 'align' => 0, 'format' => 0];
        $a[] = (object)['type' => 0, 'content' => 'Pelanggan : ' . $order->customer_name, 'bold' => 1, 'align' => 0, 'format' => 0];
        if (!empty($order->customer_phone) && $order->customer_phone !== 'Walk-in Customer') {
            $a[] = (object)['type' => 0, 'content' => 'No. HP    : ' . $order->customer_phone, 'bold' => 0, 'align' => 0, 'format' => 0];
        }
        $a[] = (object)['type' => 0, 'content' => '--------------------------------', 'bold' => 0, 'align' => 1, 'format' => 0];

        // 3. Rincian Item
        foreach ($order->items as $item) {
            $a[] = (object)['type' => 0, 'content' => $item->product_name, 'bold' => 1, 'align' => 0, 'format' => 0];
            $a[] = (object)['type' => 0, 'content' => '1 x Rp ' . number_format($item->price, 0, ',', '.') . '  = Rp ' . number_format($item->subtotal, 0, ',', '.') . '', 'bold' => 0, 'align' => 0, 'format' => 0];
        }

        if ($order->lens_price > 0 || !empty($order->lens_type_name)) {
            $a[] = (object)['type' => 0, 'content' => '+ Lensa: ' . ($order->lens_type_name ?: 'CRMC') . ' (Rp ' . number_format($order->lens_price, 0, ',', '.') . ')', 'bold' => 0, 'align' => 0, 'format' => 0];
        }

        if ($order->service_fee > 0) {
            $a[] = (object)['type' => 0, 'content' => '+ Jasa: ' . ($order->service_name ?: 'Pasang/Servis') . ' (Rp ' . number_format($order->service_fee, 0, ',', '.') . ')', 'bold' => 0, 'align' => 0, 'format' => 0];
        }

        // 4. Resep Refraksi Lensa
        if ($order->has_prescription) {
            $a[] = (object)['type' => 0, 'content' => '--------------------------------', 'bold' => 0, 'align' => 1, 'format' => 0];
            $a[] = (object)['type' => 0, 'content' => 'UKURAN RESEP LENSA (RX):', 'bold' => 1, 'align' => 1, 'format' => 4];
            $a[] = (object)['type' => 0, 'content' => 'R: Sph ' . ($order->sph_r ?: '0.00') . ' | Cyl ' . ($order->cyl_r ?: '0.00') . ' | Ax ' . ($order->axis_r ?: '0') . '', 'bold' => 0, 'align' => 0, 'format' => 4];
            $a[] = (object)['type' => 0, 'content' => 'L: Sph ' . ($order->sph_l ?: '0.00') . ' | Cyl ' . ($order->cyl_l ?: '0.00') . ' | Ax ' . ($order->axis_l ?: '0') . '', 'bold' => 0, 'align' => 0, 'format' => 4];
            $a[] = (object)['type' => 0, 'content' => 'PD (Pupil Distance): ' . $order->pd . ' mm', 'bold' => 1, 'align' => 0, 'format' => 4];
        }

        $a[] = (object)['type' => 0, 'content' => '--------------------------------', 'bold' => 0, 'align' => 1, 'format' => 0];
        $a[] = (object)['type' => 0, 'content' => 'Subtotal : Rp ' . number_format($order->subtotal, 0, ',', '.') . '', 'bold' => 0, 'align' => 0, 'format' => 0];

        if ($order->discount_amount > 0) {
            $a[] = (object)['type' => 0, 'content' => 'Diskon   : - Rp ' . number_format($order->discount_amount, 0, ',', '.') . '', 'bold' => 1, 'align' => 0, 'format' => 0];
        }

        $a[] = (object)['type' => 0, 'content' => 'TOTAL    : Rp ' . number_format($order->grand_total, 0, ',', '.') . '', 'bold' => 1, 'align' => 0, 'format' => 1];

        if ($order->payment_type === 'dp' && $order->remaining_balance > 0) {
            $a[] = (object)['type' => 0, 'content' => 'DP BAYAR : Rp ' . number_format($order->dp_amount, 0, ',', '.') . '', 'bold' => 1, 'align' => 0, 'format' => 0];
            $a[] = (object)['type' => 0, 'content' => 'SISA DP  : Rp ' . number_format($order->remaining_balance, 0, ',', '.') . '', 'bold' => 1, 'align' => 0, 'format' => 1];
            $a[] = (object)['type' => 0, 'content' => 'STATUS   : BELUM LUNAS', 'bold' => 1, 'align' => 1, 'format' => 0];
        } else {
            $a[] = (object)['type' => 0, 'content' => 'Metode   : ' . $order->payment_method, 'bold' => 0, 'align' => 0, 'format' => 0];
            $a[] = (object)['type' => 0, 'content' => 'STATUS   : LUNAS', 'bold' => 1, 'align' => 1, 'format' => 0];
        }

        // 5. Barcode No. Struk (Type 2)
        $a[] = (object)['type' => 0, 'content' => '--------------------------------', 'bold' => 0, 'align' => 1, 'format' => 0];
        $a[] = (object)['type' => 2, 'value' => $order->order_code, 'width' => 100, 'height' => 50, 'align' => 1];

        // 6. Footer & Garansi
        $a[] = (object)['type' => 0, 'content' => 'Garansi Lensa & Frame 30 Hari', 'bold' => 0, 'align' => 1, 'format' => 4];
        $a[] = (object)['type' => 0, 'content' => 'Terima Kasih atas Kunjungan Anda', 'bold' => 0, 'align' => 1, 'format' => 4];
        $a[] = (object)['type' => 0, 'content' => ' ', 'bold' => 0, 'align' => 0, 'format' => 0];
        $a[] = (object)['type' => 0, 'content' => ' ', 'bold' => 0, 'align' => 0, 'format' => 0];

        return response()->json($a, 200, [], JSON_FORCE_OBJECT);
    }


    /**
     * Download Struk Pembayaran Format Dokumen PDF (Ukuran Thermal Kertas 58mm / 80mm)
     */
    public function receiptPdf($orderCode)
    {
        $order = Order::with('items')->where('order_code', $orderCode)->firstOrFail();
        $settings = StoreSetting::all()->pluck('value', 'key');

        $paperWidth = 226;
        $paperHeight = $order->has_prescription ? 395 : 320;

        $pdf = Pdf::loadView('pos.receipt_pdf', compact('order', 'settings'))
            ->setPaper([0, 0, $paperWidth, $paperHeight], 'portrait');

        $filename = 'Struk_' . $order->order_code . '.pdf';

        return $pdf->stream($filename);
    }

}
