<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        
        // 1. KPI CARD METRICS
        // Harian (Hari ini)
        $dailyOrders = Order::whereDate('created_at', $today)->get();
        $dailyRevenue = $dailyOrders->sum('grand_total');
        $dailyCount = $dailyOrders->count();
        $dailyDp = $dailyOrders->where('payment_type', 'dp')->sum('dp_amount');

        // Kemarin (untuk perbandingan tren %)
        $yesterdayRevenue = Order::whereDate('created_at', Carbon::yesterday()->format('Y-m-d'))->sum('grand_total');
        $dailyGrowth = $yesterdayRevenue > 0 ? round((($dailyRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1) : 100;

        // Mingguan (7 Hari Terakhir)
        $startOfWeek = Carbon::now()->subDays(6)->startOfDay();
        $weeklyOrders = Order::where('created_at', '>=', $startOfWeek)->get();
        $weeklyRevenue = $weeklyOrders->sum('grand_total');
        $weeklyCount = $weeklyOrders->count();

        // 7 Hari sebelumnya (untuk perbandingan tren %)
        $prevWeekRevenue = Order::whereBetween('created_at', [Carbon::now()->subDays(13)->startOfDay(), Carbon::now()->subDays(7)->endOfDay()])->sum('grand_total');
        $weeklyGrowth = $prevWeekRevenue > 0 ? round((($weeklyRevenue - $prevWeekRevenue) / $prevWeekRevenue) * 100, 1) : 100;

        // Bulanan (Bulan Berjalan / November 2026)
        $startOfMonth = Carbon::now()->startOfMonth();
        $monthlyOrders = Order::where('created_at', '>=', $startOfMonth)->get();
        $monthlyRevenue = $monthlyOrders->sum('grand_total');
        $monthlyCount = $monthlyOrders->count();

        // Bulan lalu (Oktober 2026)
        $prevMonthRevenue = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('grand_total');
        $monthlyGrowth = $prevMonthRevenue > 0 ? round((($monthlyRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1) : 100;

        // Tahunan (Tahun 2026)
        $startOfYear = Carbon::now()->startOfYear();
        $yearlyOrders = Order::where('created_at', '>=', $startOfYear)->get();
        $yearlyRevenue = $yearlyOrders->sum('grand_total');
        $yearlyCount = $yearlyOrders->count();

        // Tahun lalu (2025)
        $prevYearRevenue = Order::whereYear('created_at', Carbon::now()->subYear()->year)->sum('grand_total');
        $yearlyGrowth = $prevYearRevenue > 0 ? round((($yearlyRevenue - $prevYearRevenue) / $prevYearRevenue) * 100, 1) : 100;

        // Sisa Tagihan DP yang belum lunas
        $totalUnpaidDp = Order::where('payment_type', 'dp')->where('remaining_balance', '>', 0)->sum('remaining_balance');

        // 2. DATASET GRAFIK HARIAN (Jam 08:00 - 22:00 Hari Ini)
        $hourlyLabels = [];
        $hourlyRevenue = [];
        $hourlyVolume = [];
        for ($hour = 8; $hour <= 22; $hour += 2) {
            $hStart = Carbon::today()->setHour($hour)->setMinute(0)->setSecond(0);
            $hEnd = Carbon::today()->setHour($hour + 1)->setMinute(59)->setSecond(59);
            $label = sprintf('%02d:00 - %02d:00', $hour, $hour + 2);
            
            $rev = Order::whereBetween('created_at', [$hStart, $hEnd])->sum('grand_total');
            $cnt = Order::whereBetween('created_at', [$hStart, $hEnd])->count();
            
            $hourlyLabels[] = $label;
            $hourlyRevenue[] = (int) $rev;
            $hourlyVolume[] = (int) $cnt;
        }

        // 3. DATASET GRAFIK MINGGUAN (7 Hari Terakhir: Tanggal & Nama Hari)
        $daily7Labels = [];
        $daily7Revenue = [];
        $daily7Volume = [];
        for ($i = 6; $i >= 0; $i--) {
            $dayTarget = Carbon::now()->subDays($i);
            $dayStr = $dayTarget->format('Y-m-d');
            $label = $dayTarget->translatedFormat('D, d M');

            $rev = Order::whereDate('created_at', $dayStr)->sum('grand_total');
            $cnt = Order::whereDate('created_at', $dayStr)->count();

            $daily7Labels[] = $label;
            $daily7Revenue[] = (int) $rev;
            $daily7Volume[] = (int) $cnt;
        }

        // 4. DATASET GRAFIK BULANAN (Tren Harian Sepanjang Bulan Berjalan 1-30)
        $monthDaysLabels = [];
        $monthDaysRevenue = [];
        $monthDaysVolume = [];
        $daysInMonth = Carbon::now()->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $targetDate = Carbon::now()->setDay($d)->format('Y-m-d');
            $label = (string) $d;

            $rev = Order::whereDate('created_at', $targetDate)->sum('grand_total');
            $cnt = Order::whereDate('created_at', $targetDate)->count();

            $monthDaysLabels[] = 'Tgl ' . $label;
            $monthDaysRevenue[] = (int) $rev;
            $monthDaysVolume[] = (int) $cnt;
        }

        // 5. DATASET GRAFIK TAHUNAN (Tren 12 Bulan Jan - Des 2026 vs 2025)
        $yearMonthsLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $year2026Revenue = [];
        $year2025Revenue = [];
        for ($m = 1; $m <= 12; $m++) {
            $revCurrent = Order::whereYear('created_at', 2026)->whereMonth('created_at', $m)->sum('grand_total');
            $revPast = Order::whereYear('created_at', 2025)->whereMonth('created_at', $m)->sum('grand_total');
            
            $year2026Revenue[] = (int) $revCurrent;
            $year2025Revenue[] = (int) $revPast;
        }

        // 6. BREAKDOWN KATEGORI TRANSAKSI
        $catalogCount = Order::where('transaction_type', 'catalog')->count();
        $customFrameCount = Order::where('transaction_type', 'custom_frame')->count();
        $lensOnlyCount = Order::where('transaction_type', 'lens_only')->count();
        $serviceOnlyCount = Order::where('transaction_type', 'service_only')->count();

        // 7. BREAKDOWN LENSA TERLARIS
        $topLenses = Order::whereNotNull('lens_type_name')
            ->select('lens_type_name', DB::raw('count(*) as count'), DB::raw('sum(lens_price) as total_lens_rev'))
            ->groupBy('lens_type_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // 8. FILTER & TABEL DAFTAR TRANSAKSI
        $filterPeriod = $request->query('period', 'all');
        $query = Order::with('items')->latest();

        if ($filterPeriod === 'today') {
            $query->whereDate('created_at', $today);
        } elseif ($filterPeriod === 'weekly') {
            $query->where('created_at', '>=', $startOfWeek);
        } elseif ($filterPeriod === 'monthly') {
            $query->where('created_at', '>=', $startOfMonth);
        } elseif ($filterPeriod === 'yearly') {
            $query->where('created_at', '>=', $startOfYear);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
            $filterPeriod = 'custom';
        }

        $filteredOrders = $query->paginate(15)->withQueryString();
        $filteredTotalRevenue = (clone $query)->sum('grand_total');
        $filteredTotalOrders = (clone $query)->count();

        return view('admin.reports.index', compact(
            'dailyRevenue', 'dailyCount', 'dailyGrowth', 'dailyDp',
            'weeklyRevenue', 'weeklyCount', 'weeklyGrowth',
            'monthlyRevenue', 'monthlyCount', 'monthlyGrowth',
            'yearlyRevenue', 'yearlyCount', 'yearlyGrowth',
            'totalUnpaidDp',
            // Data Grafik
            'hourlyLabels', 'hourlyRevenue', 'hourlyVolume',
            'daily7Labels', 'daily7Revenue', 'daily7Volume',
            'monthDaysLabels', 'monthDaysRevenue', 'monthDaysVolume',
            'yearMonthsLabels', 'year2026Revenue', 'year2025Revenue',
            // Breakdown
            'catalogCount', 'customFrameCount', 'lensOnlyCount', 'serviceOnlyCount',
            'topLenses',
            // Filter
            'filteredOrders', 'filterPeriod', 'filteredTotalRevenue', 'filteredTotalOrders'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $period = $request->query('period', 'daily');
        $now = Carbon::now();
        $query = Order::with('items')->latest();

        $reportHeader = 'LAPORAN PENJUALAN HARIAN';
        $dateRangeText = $now->translatedFormat('d F Y');
        $filenamePrefix = 'Laporan-Harian-' . $now->format('Ymd');

        if ($period === 'daily' || $period === 'today') {
            $reportHeader = 'LAPORAN TRANSAKSI HARIAN';
            $dateRangeText = 'Hari Ini (' . $now->translatedFormat('d F Y') . ')';
            $query->whereDate('created_at', $now->format('Y-m-d'));
            $filenamePrefix = 'Laporan-Harian-' . $now->format('Ymd');
        } elseif ($period === 'weekly') {
            $reportHeader = 'LAPORAN TRANSAKSI MINGGUAN (7 HARI)';
            $startOfWeek = Carbon::now()->subDays(6)->startOfDay();
            $dateRangeText = $startOfWeek->translatedFormat('d M Y') . ' s/d ' . $now->translatedFormat('d M Y');
            $query->where('created_at', '>=', $startOfWeek);
            $filenamePrefix = 'Laporan-Mingguan-' . $now->format('Ymd');
        } elseif ($period === 'monthly') {
            $reportHeader = 'LAPORAN TRANSAKSI BULANAN';
            $startOfMonth = Carbon::now()->startOfMonth();
            $dateRangeText = 'Bulan ' . $now->translatedFormat('F Y');
            $query->where('created_at', '>=', $startOfMonth);
            $filenamePrefix = 'Laporan-Bulanan-' . $now->format('Ym');
        } elseif ($period === 'yearly') {
            $reportHeader = 'LAPORAN TRANSAKSI TAHUNAN';
            $startOfYear = Carbon::now()->startOfYear();
            $dateRangeText = 'Tahun Buku ' . $now->format('Y');
            $query->where('created_at', '>=', $startOfYear);
            $filenamePrefix = 'Laporan-Tahunan-' . $now->format('Y');
        } elseif ($period === 'custom' || ($request->filled('start_date') && $request->filled('end_date'))) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $reportHeader = 'LAPORAN TRANSAKSI RENTANG KHUSUS';
            $dateRangeText = $start->translatedFormat('d M Y') . ' s/d ' . $end->translatedFormat('d M Y');
            $query->whereBetween('created_at', [$start, $end]);
            $filenamePrefix = 'Laporan-Custom-' . $start->format('Ymd') . '-sd-' . $end->format('Ymd');
        } else {
            // All
            $reportHeader = 'LAPORAN REKAPITULASI KESELURUHAN';
            $dateRangeText = 'Semua Riwayat Transaksi';
            $filenamePrefix = 'Laporan-Rekap-Semua-' . $now->format('Ymd');
        }

        $orders = $query->get();

        $totalRevenue = $orders->sum('grand_total');
        $totalOrders = $orders->count();
        $totalCollected = $orders->sum(function($o) {
            return $o->payment_type === 'dp' ? $o->dp_amount : $o->grand_total;
        });
        $unpaidBalance = $orders->where('payment_type', 'dp')->sum('remaining_balance');

        $distribution = [
            'catalog' => $orders->filter(function($o) { return in_array($o->transaction_type, ['catalog', 'kacamata_lengkap', null, '']); })->count(),
            'custom_frame' => $orders->where('transaction_type', 'custom_frame')->count(),
            'lens_only' => $orders->where('transaction_type', 'lens_only')->count(),
            'service_only' => $orders->where('transaction_type', 'service_only')->count(),
        ];

        $orderIds = $orders->pluck('id');
        $topLensesSummary = Order::whereIn('id', $orderIds)
            ->whereNotNull('lens_type_name')
            ->select('lens_type_name', DB::raw('count(*) as count'), DB::raw('sum(lens_price) as total_lens_rev'))
            ->groupBy('lens_type_name')
            ->orderByDesc('count')
            ->limit(4)
            ->get();

        $settings = StoreSetting::all()->pluck('value', 'key');
        $title = $reportHeader;
        $generatedAt = $now->format('d/m/Y H:i');
        $printedBy = Auth::check() ? Auth::user()->name : 'Super Administrator';

        $type = $request->query('type');
        if ($type === 'lensa') {
            $reportHeader = 'LAPORAN REKAPITULASI PENJUALAN PAKET LENSA';
            $title = $reportHeader;
            $filenamePrefix = 'Laporan-Paket-Lensa-' . $now->format('Ymd');
        }

        $pdf = Pdf::loadView('admin.reports.pdf', compact(
            'orders', 'title', 'reportHeader', 'dateRangeText', 'generatedAt', 'printedBy',
            'totalRevenue', 'totalOrders', 'totalCollected', 'unpaidBalance',
            'distribution', 'topLensesSummary', 'settings'
        ));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($filenamePrefix . '.pdf');
    }

    public function downloadExcel(Request $request)
    {
        $period = $request->query('period', 'monthly');
        $now = Carbon::now();
        $query = Order::with('items')->latest();

        if ($period === 'daily' || $period === 'today') {
            $query->whereDate('created_at', $now->format('Y-m-d'));
            $filename = 'Export-Transaksi-Harian-' . $now->format('Ymd') . '.csv';
        } elseif ($period === 'weekly') {
            $startOfWeek = Carbon::now()->subDays(6)->startOfDay();
            $query->where('created_at', '>=', $startOfWeek);
            $filename = 'Export-Transaksi-Mingguan-' . $now->format('Ymd') . '.csv';
        } elseif ($period === 'monthly') {
            $startOfMonth = Carbon::now()->startOfMonth();
            $query->where('created_at', '>=', $startOfMonth);
            $filename = 'Export-Transaksi-Bulanan-' . $now->format('Ym') . '.csv';
        } elseif ($period === 'yearly') {
            $startOfYear = Carbon::now()->startOfYear();
            $query->where('created_at', '>=', $startOfYear);
            $filename = 'Export-Transaksi-Tahunan-' . $now->format('Y') . '.csv';
        } else {
            $filename = 'Export-Transaksi-Semua-' . $now->format('Ymd') . '.csv';
        }

        $orders = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            // Menulis UTF-8 BOM agar Excel membacanya tanpa merusak karakter khusus
            fputs($file, "ï»¿");

            // Header Kolom CSV/Excel
            fputcsv($file, [
                'ID', 'Kode Pesanan', 'Tanggal Transaksi', 'Tipe Transaksi', 'Nama Pelanggan',
                'No WhatsApp / Telp', 'Kota', 'Paket Lensa', 'Biaya Lensa (Rp)',
                'Biaya Servis (Rp)', 'Voucher', 'Diskon (Rp)', 'Total Tagihan (Rp)',
                'Tipe Bayar', 'DP Terbayar (Rp)', 'Sisa Pelunasan (Rp)', 'Metode Bayar',
                'Status Bayar', 'Status Pesanan'
            ]);

            foreach ($orders as $ord) {
                fputcsv($file, [
                    $ord->id,
                    $ord->order_code,
                    $ord->created_at->format('d/m/Y H:i'),
                    $ord->transaction_type,
                    $ord->customer_name,
                    $ord->customer_phone,
                    $ord->city,
                    $ord->lens_type_name ?: '-',
                    $ord->lens_price ?: 0,
                    $ord->service_fee ?: 0,
                    $ord->voucher_code ?: '-',
                    $ord->discount_amount ?: 0,
                    $ord->grand_total,
                    strtoupper($ord->payment_type),
                    $ord->dp_amount ?: 0,
                    $ord->remaining_balance ?: 0,
                    $ord->payment_method,
                    $ord->payment_status,
                    $ord->order_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API Ringkasan Seluruh Laporan Alysa Optical untuk Otomasi n8n & Owner
     */
    public function apiSummary(Request $request)
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        // 1. Laporan Finansial & Transaksi
        $dailyOrders = Order::whereDate('created_at', $today)->get();
        $dailyRevenue = (int) $dailyOrders->sum('grand_total');
        $dailyCount = $dailyOrders->count();

        $startOfWeek = Carbon::now()->subDays(6)->startOfDay();
        $weeklyOrders = Order::where('created_at', '>=', $startOfWeek)->get();
        $weeklyRevenue = (int) $weeklyOrders->sum('grand_total');
        $weeklyCount = $weeklyOrders->count();

        $startOfMonth = Carbon::now()->startOfMonth();
        $monthlyOrders = Order::where('created_at', '>=', $startOfMonth)->get();
        $monthlyRevenue = (int) $monthlyOrders->sum('grand_total');
        $monthlyCount = $monthlyOrders->count();

        // 2. Piutang DP Belum Lunas
        $totalUnpaidDp = (int) Order::where('payment_type', 'dp')->where('remaining_balance', '>', 0)->sum('remaining_balance');

        // 3. Laporan Pengeluaran Operasional
        $dailyExpense = (int) \App\Models\Expense::whereDate('expense_date', $today)->sum('amount');
        $monthlyExpense = (int) \App\Models\Expense::whereMonth('expense_date', $now->month)->whereYear('expense_date', $now->year)->sum('amount');

        // 4. Laporan Status Lab Optik
        $labPending = Order::whereIn('order_status', ['Diproses di Lab Optik', 'Pemasangan Lensa', 'Menunggu Validasi Resep'])->count();
        $labReady = Order::where('order_status', 'Siap Diambil')->count();

        // 5. Laporan Stok Kritis
        $lowStockProducts = \App\Models\Product::where('stock', '<=', 3)->get(['name', 'stock'])->toArray();

        // 6. Laporan Pelanggan Baru
        $newCustomersToday = \App\Models\Customer::whereDate('created_at', $today)->count();
        $totalCustomers = \App\Models\Customer::count();

        return response()->json([
            'status' => 'success',
            'timestamp' => $now->toIso8601String(),
            'date_formatted' => $now->translatedFormat('l, d F Y H:i') . ' WIB',
            'financial' => [
                'daily_revenue' => $dailyRevenue,
                'daily_orders' => $dailyCount,
                'weekly_revenue' => $weeklyRevenue,
                'weekly_orders' => $weeklyCount,
                'monthly_revenue' => $monthlyRevenue,
                'monthly_orders' => $monthlyCount,
                'net_monthly_profit' => ($monthlyRevenue - $monthlyExpense),
                'unpaid_dp_balance' => $totalUnpaidDp,
            ],
            'expenses' => [
                'daily_expense' => $dailyExpense,
                'monthly_expense' => $monthlyExpense,
            ],
            'lab_optik' => [
                'in_progress' => $labPending,
                'ready_pickup' => $labReady,
            ],
            'inventory' => [
                'low_stock_count' => count($lowStockProducts),
                'low_stock_items' => $lowStockProducts,
            ],
            'crm' => [
                'new_customers_today' => $newCustomersToday,
                'total_customers' => $totalCustomers,
            ]
        ]);
    }

}
