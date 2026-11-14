<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Tampilkan Buku Kas & Cashflow (Arus Kas Masuk vs Keluar + Laba Bersih)
     */
    public function index(Request $request)
    {
        // Filter rentang bulan/tanggal
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $category = $request->input('category');

        // 1. Hitung Pemasukan (Total Uang Masuk dari Penjualan Kacamata/POS)
        // Menghitung grand_total yang berstatus PAID atau dp_amount yang berstatus DP
        $ordersQuery = Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $totalOmsetMasuk = 0;
        $orders = $ordersQuery->get();
        foreach ($orders as $order) {
            if (strtoupper($order->payment_status) === 'PAID') {
                $totalOmsetMasuk += (float)$order->grand_total;
            } elseif (strtoupper($order->payment_status) === 'DP') {
                $totalOmsetMasuk += (float)$order->dp_amount;
            }
        }

        // 2. Hitung Pengeluaran Modal & Operasional
        $expensesQuery = Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate);

        if ($category) {
            $expensesQuery->where('category', $category);
        }

        $expenses = $expensesQuery->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $totalPengeluaran = Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->sum('amount');

        // 3. Arus Kas Bersih / Laba Bersih Operasional
        $netCashflow = $totalOmsetMasuk - $totalPengeluaran;

        // 4. Rekap Per Kategori Pengeluaran
        $rekapKategori = Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        // 5. Analisis Keterkaitan Omset vs Belanja Modal (HPP)
        $paidStatuses = ['PAID', 'LUNAS'];
        $omsetBruto = (float) \App\Models\Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereIn(DB::raw('UPPER(payment_status)'), $paidStatuses)
            ->sum('grand_total');
        $hppTransaksi = (float) \App\Models\Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereIn(DB::raw('UPPER(payment_status)'), $paidStatuses)
            ->sum('hpp_modal');
        $labaKotor = $omsetBruto - $hppTransaksi;
        $marginKotor = $omsetBruto > 0 ? ($labaKotor / $omsetBruto) * 100 : 0.0;
        $capexPeriod = (float) Expense::whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->where('category', 'Belanja Modal')
            ->sum('amount');
        $breakEvenPct = $omsetBruto > 0 ? ($capexPeriod / $omsetBruto) * 100 : 0.0;

        // Transaksi Lunas utk koreksi HPP manual (tautan belanja modal -> struk)
        $modalTransaksi = \App\Models\Order::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereIn(DB::raw('UPPER(payment_status)'), $paidStatuses)
            ->orderByDesc('grand_total')
            ->take(20)
            ->get(['id', 'order_code', 'customer_name', 'grand_total', 'hpp_modal', 'created_at']);
        $linkedExpenses = Expense::whereNotNull('linked_order_code')
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->pluck('amount', 'linked_order_code');

        return view('admin.expenses.index', compact(
            'expenses',
            'startDate',
            'endDate',
            'category',
            'totalOmsetMasuk',
            'totalPengeluaran',
            'netCashflow',
            'rekapKategori',
            'omsetBruto',
            'hppTransaksi',
            'labaKotor',
            'marginKotor',
            'capexPeriod',
            'breakEvenPct',
            'modalTransaksi',
            'linkedExpenses'
        ));
    }

    /**
     * Catat Pengeluaran Belanja Modal / Operasional Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payment_source' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'receipt_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'linked_order_code' => 'nullable|string|max:40',
        ]);

        $photoPath = null;
        if ($request->hasFile('receipt_photo')) {
            $file = $request->file('receipt_photo');
            $filename = 'nota_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/expenses'), $filename);
            $photoPath = 'uploads/expenses/' . $filename;
        }

        $code = 'EXP-' . date('Ymd') . '-' . sprintf('%03d', Expense::whereDate('created_at', today())->count() + 1);

        Expense::create([
            'expense_code' => $code,
            'expense_date' => $validated['expense_date'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'payment_source' => $validated['payment_source'],
            'linked_order_code' => !empty($validated['linked_order_code']) ? trim($validated['linked_order_code']) : null,
            'receipt_photo' => $photoPath,
            'notes' => $validated['notes'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.expenses.index')->with('success', "Pengeluaran {$validated['title']} (Rp " . number_format($validated['amount'], 0, ',', '.') . ") berhasil dicatat ke pembukuan!");
    }

    /**
     * Koreksi HPP modal satu transaksi POS (menautkan belanja modal ke struk).
     * Omset transaksi tsb dianggap = modal belanja + laba (hpp_modal <= grand_total).
     */
    public function updateModalHpp(Request $request, \App\Models\Order $order)
    {
        abort_unless(Auth::user()->canAccessAdminArea(), 403);
        $validated = $request->validate([
            'hpp_modal' => 'required|numeric|min:0',
        ]);
        $hpp = min((float) $validated['hpp_modal'], (float) $order->grand_total);
        $order->update(['hpp_modal' => (int) round($hpp)]);

        return back()->with('success', 'HPP modal transaksi ' . $order->order_code . ' dikoreksi: Rp ' . number_format($hpp, 0, ',', '.')
            . ' (perkiraan laba: Rp ' . number_format($order->grand_total - $hpp, 0, ',', '.') . ')');
    }

    /**
     * Hapus Catatan Pengeluaran
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        if ($expense->receipt_photo && file_exists(public_path($expense->receipt_photo))) {
            @unlink(public_path($expense->receipt_photo));
        }
        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }
}
