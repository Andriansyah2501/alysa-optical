<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Tampilkan data pelanggan, riwayat resep, dan total pembelian
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('customer_code', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%");
            });
        }

        $customers = $query->orderBy('last_visit_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total_customers' => Customer::count(),
            'total_orders' => Customer::sum('total_orders'),
            'total_revenue' => Customer::sum('total_spent'),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * Simpan data pelanggan baru secara manual
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50|unique:customers,phone',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'gender' => 'nullable|in:L,P,other',
            'birth_date' => 'nullable|date',
            'sph_r' => 'nullable|string|max:20',
            'cyl_r' => 'nullable|string|max:20',
            'axis_r' => 'nullable|string|max:20',
            'sph_l' => 'nullable|string|max:20',
            'cyl_l' => 'nullable|string|max:20',
            'axis_l' => 'nullable|string|max:20',
            'pd' => 'nullable|string|max:20',
            'add_r' => 'nullable|string|max:20',
            'add_l' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $nextCode = sprintf('CUST-%04d', Customer::count() + 1);
        $validated['customer_code'] = $nextCode;
        $validated['city'] = $validated['city'] ?? 'Batam';
        $validated['last_visit_at'] = now();

        $customer = Customer::create($validated);
        WhatsAppNotificationService::sendCustomerNotification($customer, 'customer_registered');

        return redirect()->route('admin.customers.index')->with('success', "Pelanggan {$validated['name']} ({$nextCode}) berhasil didaftarkan!");
    }

    /**
     * Tampilkan detail riwayat resep refraksi dan riwayat transaksi pelanggan
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $orders = Order::where('customer_phone', $customer->phone)
            ->orWhere('customer_name', $customer->name)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    /**
     * Perbarui profil data pelanggan atau resep refraksi
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'gender' => 'nullable|in:L,P,other',
            'birth_date' => 'nullable|date',
            'sph_r' => 'nullable|string|max:20',
            'cyl_r' => 'nullable|string|max:20',
            'axis_r' => 'nullable|string|max:20',
            'sph_l' => 'nullable|string|max:20',
            'cyl_l' => 'nullable|string|max:20',
            'axis_l' => 'nullable|string|max:20',
            'pd' => 'nullable|string|max:20',
            'add_r' => 'nullable|string|max:20',
            'add_l' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);
        WhatsAppNotificationService::sendCustomerNotification($customer, 'prescription_updated');

        return redirect()->back()->with('success', "Data pelanggan {$customer->name} berhasil diperbarui!");
    }

    /**
     * Hapus data pelanggan
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $name = $customer->name;
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', "Data pelanggan {$name} berhasil dihapus dari sistem.");
    }

    /**
     * Kirim notifikasi WA instan / custom ke pelanggan dari halaman profil
     */
    public function sendManualWa(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $event = $request->input('event', 'prescription_reminder');
        $customMsg = $request->input('custom_message');

        $sent = WhatsAppNotificationService::sendCustomerNotification($customer, $event, $customMsg);

        if ($sent) {
            return redirect()->back()->with('success', "Notifikasi WhatsApp otomatis berhasil dikirimkan ke {$customer->name} ({$customer->phone})!");
        } else {
            return redirect()->back()->with('warning', "Pesan sedang diantrekan atau gateway WhatsApp belum siap/terkoneksi.");
        }
    }

}
