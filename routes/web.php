<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlassesShopController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PromoCalendarController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\StoreSettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ServerNetworkController;
use App\Http\Controllers\TunnelManagerController;
use App\Http\Controllers\AuthController;

// Autentikasi Pengguna & Role
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman Publik Toko Kacamata Alysa
Route::get('/', [GlassesShopController::class, 'index'])->name('shop.index');
Route::get('/kacamata/{slug}', [GlassesShopController::class, 'show'])->name('shop.show');
Route::get('/order-online', [GlassesShopController::class, 'customerOrderPortal'])->name('shop.customer.portal');
Route::post('/order-online/quick-order', [GlassesShopController::class, 'storeCustomerOrderPortal'])->name('shop.customer.portal.store');
Route::get('/checkout', [GlassesShopController::class, 'checkout'])->name('shop.checkout');
Route::post('/checkout/proses', [GlassesShopController::class, 'storeOrder'])->name('shop.order.store');
Route::get('/pesanan/berhasil/{orderCode}', [GlassesShopController::class, 'orderSuccess'])->name('shop.order.success');
Route::get('/pos/struk/{orderCode}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('/pos/struk-pdf/{orderCode}', [PosController::class, 'receiptPdf'])->name('pos.receipt.pdf');
    Route::get('/pos/thermer-data/{orderCode}', [PosController::class, 'getThermerJson'])->name('pos.thermer.json');

Route::get('/lacak-pesanan', [GlassesShopController::class, 'track'])->name('shop.track');
Route::get('/kalender-promo', [PromoCalendarController::class, 'publicIndex'])->name('shop.calendar');

// Rute Kasir POS (Akses: Cashier & Admin)
Route::middleware(['auth', 'role:cashier,admin'])->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/bayar', [PosController::class, 'store'])->name('pos.store');
    Route::post('/pos/pelunasan/{id}', [PosController::class, 'settlePayment'])->name('pos.settle');
// Struk dipindahkan ke akses umum agar pelanggan bisa melihat struk digital dari link WA
});

// Rute Admin & Lab Refraksionis
Route::middleware(['auth', 'role:admin,optician'])->prefix('admin')->group(function () {
    Route::get('/optik', [GlassesShopController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/kalender-promo', [PromoCalendarController::class, 'adminIndex'])->name('admin.calendar.index');
        Route::post('/kalender-promo', [PromoCalendarController::class, 'store'])->name('admin.calendar.store');
        Route::post('/kalender-promo/{promo}/update', [PromoCalendarController::class, 'update'])->name('admin.calendar.update');
        Route::delete('/kalender-promo/{promo}', [PromoCalendarController::class, 'destroy'])->name('admin.calendar.destroy');
        Route::get('/laporan', [ReportController::class, 'index'])->name('admin.reports.index');
                Route::get('/laporan/download-pdf', [ReportController::class, 'downloadPdf'])->name('admin.reports.pdf');
        Route::get('/laporan/download-excel', [ReportController::class, 'downloadExcel'])->name('admin.reports.excel');
        Route::get('/pesanan/{id}', [GlassesShopController::class, 'adminOrderDetail'])->name('admin.order.detail');
    Route::post('/pesanan/{id}/update-status', [GlassesShopController::class, 'adminUpdateOrderStatus'])->name('admin.order.updateStatus');

        // CRUD Transaksi Lengkap (Create, Read, Update, Delete)
        Route::get('/transaksi/tambah', [GlassesShopController::class, 'adminOrderCreate'])->name('admin.order.create');
        Route::post('/transaksi/simpan', [GlassesShopController::class, 'adminOrderStore'])->name('admin.order.store');
        Route::get('/transaksi/{id}/edit', [GlassesShopController::class, 'adminOrderEdit'])->name('admin.order.edit');
        Route::put('/transaksi/{id}', [GlassesShopController::class, 'adminOrderUpdate'])->name('admin.order.update');
        Route::delete('/transaksi/{id}', [GlassesShopController::class, 'adminOrderDestroy'])->name('admin.order.destroy');
        Route::post('/transaksi/batch-destroy', [GlassesShopController::class, 'adminOrderBatchDestroy'])->name('admin.order.batchDestroy');

    // Manajemen Katalog Kacamata Lengkap (CRUD)
    Route::resource('katalog', ProductCatalogController::class, [
        'as' => 'admin',
        'parameters' => ['katalog' => 'product']
    ]);


    // Manajemen Database Pelanggan & Rekam Medis Resep Kacamata
    Route::get('/pelanggan', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::post('/pelanggan', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/pelanggan/{id}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::post('/pelanggan/{id}/update', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::post('/pelanggan/{id}/send-wa', [CustomerController::class, 'sendManualWa'])->name('admin.customers.send_wa');
    Route::delete('/pelanggan/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');

    
    // Buku Kas, Belanja Modal & Arus Kas (Cashflow Toko)
    Route::get('/pengeluaran', [ExpenseController::class, 'index'])->name('admin.expenses.index');
    Route::post('/pengeluaran', [ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::delete('/pengeluaran/{id}', [ExpenseController::class, 'destroy'])->name('admin.expenses.destroy');

    
    // Manajemen Cadangan Sistem (Full Backup, Database, Harian & Bulanan)
    Route::middleware('role:admin,super_admin')->group(function() {
        Route::get('/backup', [BackupController::class, 'index'])->name('admin.backups.index');
        Route::post('/backup/create', [BackupController::class, 'create'])->name('admin.backups.create');
        Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('admin.backups.download');
        Route::delete('/backup/destroy/{filename}', [BackupController::class, 'destroy'])->name('admin.backups.destroy');
    });

    

    // Manajemen Tunnel Cloudflare & Akses Publik Darurat
    Route::middleware('role:admin,super_admin')->group(function() {
        Route::get('/tunnels', [TunnelManagerController::class, 'index'])->name('admin.tunnel.index');
        Route::post('/tunnels/control', [TunnelManagerController::class, 'control'])->name('admin.tunnel.control');
    });

        // Monitoring Server, Koneksi Jaringan & Layanan Web (Port & Ping)
    Route::middleware('role:admin,super_admin')->group(function() {
        Route::get('/server-status', [ServerNetworkController::class, 'index'])->name('admin.server.index');
        Route::post('/server-status/ping', [ServerNetworkController::class, 'testPing'])->name('admin.server.ping');
    });

        // Pengaturan Toko & Custom Struk & Manajemen User Role
    Route::middleware('role:admin,super_admin')->group(function() {
        Route::get('/pengaturan', [StoreSettingController::class, 'index'])->name('admin.settings.index');
        Route::get('/pengaturan/wa-qr-stream', [StoreSettingController::class, 'streamWaQr'])->name('admin.settings.wa_qr');
        Route::post('/pengaturan/wa-restart-session', [StoreSettingController::class, 'restartWaSession'])->name('admin.settings.wa_restart');
        Route::post('/pengaturan', [StoreSettingController::class, 'updateSettings'])->name('admin.settings.update');

        // CRUD Users / Staf Kasir / Refraksi / Admin
        Route::post('/pengaturan/user', [StoreSettingController::class, 'storeUser'])->name('admin.settings.user.store');
        Route::post('/pengaturan/user/{user}/update', [StoreSettingController::class, 'updateUser'])->name('admin.settings.user.update');
        Route::delete('/pengaturan/user/{user}', [StoreSettingController::class, 'destroyUser'])->name('admin.settings.user.destroy');
        Route::post('/pengaturan/user/{user}/toggle', [StoreSettingController::class, 'toggleUserStatus'])->name('admin.settings.user.toggle');

        // CRUD Lensa Kacamata
        Route::post('/pengaturan/lensa', [StoreSettingController::class, 'storeLens'])->name('admin.settings.lens.store');
        Route::post('/pengaturan/lensa/{lens}/update', [StoreSettingController::class, 'updateLens'])->name('admin.settings.lens.update');
        Route::delete('/pengaturan/lensa/{lens}', [StoreSettingController::class, 'destroyLens'])->name('admin.settings.lens.destroy');

        // CRUD Voucher Diskon Promo
        Route::post('/pengaturan/voucher', [StoreSettingController::class, 'storeVoucher'])->name('admin.settings.voucher.store');
        Route::post('/pengaturan/voucher/{voucher}/update', [StoreSettingController::class, 'updateVoucher'])->name('admin.settings.voucher.update');
        Route::delete('/pengaturan/voucher/{voucher}', [StoreSettingController::class, 'destroyVoucher'])->name('admin.settings.voucher.destroy');
    });
});

// Endpoint Internal API Laporan Lengkap untuk n8n & Owner
Route::get('/api/internal/laporan-summary', [\App\Http\Controllers\ReportController::class, 'apiSummary'])->name('api.reports.summary');
