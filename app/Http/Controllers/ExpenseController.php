<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('admin.expenses.index', compact(
            'expenses',
            'startDate',
            'endDate',
            'category',
            'totalOmsetMasuk',
            'totalPengeluaran',
            'netCashflow',
            'rekapKategori'
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
            'receipt_photo' => $photoPath,
            'notes' => $validated['notes'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.expenses.index')->with('success', "Pengeluaran {$validated['title']} (Rp " . number_format($validated['amount'], 0, ',', '.') . ") berhasil dicatat ke pembukuan!");
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
