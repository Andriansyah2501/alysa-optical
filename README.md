# 👓 Alysa Glasses — Web POS & Sistem Manajemen Toko Kacamata

> **Eyewear That Completes Your Look.**
> Aplikasi web lengkap untuk toko kacamata: katalog online, order dengan resep lensa, kasir POS, manajemen stok & pelanggan, laporan penjualan, dan notifikasi WhatsApp otomatis.

🌐 **Live:** https://alysa.andrian-syah.my.id
📍 **Area layanan:** Batam • Tanjung Pinang • Bintan, Kepulauan Riau

---

## ✨ Fitur

### 🛍️ Toko Online (Publik)
- Katalog frame (optik, sunglasses polarized, titanium, cat eye, wayfarer, dst.) dengan filter kategori, bentuk frame, gender, dan pencarian
- Detail produk per frame: harga, diskon, stok, warna, ukuran, deskripsi
- **Order Online** — pelanggan memesan dengan resep mata (SP/Si, addisi), pilih frame + jenis lensa, apply voucher
- **Lacak Pesanan** berbasis kode resi (format `POS-YYYYMMDD-XXXXX`)
- **Kalender Promo** publik + tabel harga lensa (blueray, photocromic, bluecromic, progresif, anti fog, night drive)
- Halaman **Tentang / Kontak / FAQ**
- **PWA**: `manifest.json` + `service worker` + APK wrapper (`Alysa_Optical.apk`)
- SEO: `sitemap.xml` otomatis, meta description + Open Graph per halaman, canonical URL

### 🧾 Kasir / POS (Cashier)
- Transaksi cepat: produk stok + item custom/jasa non-stok
- Pembayaran tunai/debit + **uang muka (DP)** dan pelunasan
- Struk digital (link via WhatsApp) + struk PDF + **endpoint data thermal 58 mm** untuk printer kasir

### 🛠️ Admin & Optik
- **Dashboard optik**: ringkasan order & status produksi
- **Katalog**: CRUD produk + upload gambar + tanda featured/bestseller
- **Pesanan**: update status (menunggu → proses → selesai), detail resep per order
- **Pelanggan**: data pelanggan + riwayat + kirim notifikasi WA manual
- **Pengaturan Toko**: profil toko (nama, alamat, IG, TikTok, WA channel, jam operasional), lensa (CRUD), user & role (CRUD), voucher (CRUD + aktif/nonaktif)
- **Kalender Promo** (CRUD event promo)
- **Laporan Penjualan**: filter periode, unduh **PDF & Excel**, + endpoint API internal untuk dashboard n8n
- **Pemasukan/Pengeluaran**: pencatatan arus kas sederhana
- **Backup Database** on-demand + unduh
- **Server Status**: ping test & monitoring
- **Tunnel Manager**: kontrol Cloudflare Tunnel dari panel

### 📲 Integrasi WhatsApp
- Gateway WA lokal via **WAHA** (WhatsApp API HTTP, container Docker) — QR login dari panel admin
- Alternatif provider: n8n webhook / Fonnte / WPPConnect (konfigurasi via `store_settings`)
- Notifikasi otomatis: order masuk → WA owner/pelanggan, konfirmasi pembayaran, status pesanan

---

## 🧱 Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | **Laravel 10** (PHP ≥ 8.1) |
| Database | **MySQL/MariaDB** (14 tabel) |
| PDF | barryvdh/laravel-dompdf |
| HTTP client | Guzzle (integrasi WA gateway) |
| Auth | Session + role-based middleware (`RoleCheck`), Laravel Sanctum |
| Frontend | Blade + TailwindCSS (CDN) + Font Awesome |
| PWA | manifest + service worker + APK |
| Infra | Nginx → PHP-FPM 8.1 → Docker (WAHA), Cloudflare Tunnel |

## 🗂️ Struktur Proyek

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          # login/logout multi-role
│   │   ├── GlassesShopController.php   # toko publik, checkout, lacak, dashboard optik
│   │   ├── PosController.php           # kasir, struk, data thermal
│   │   ├── ProductCatalogController.php# CRUD katalog
│   │   ├── CustomerController.php      # pelanggan + kirim WA
│   │   ├── StoreSettingController.php  # pengaturan, lensa, user, voucher, WA QR
│   │   ├── ReportController.php        # laporan PDF/Excel + API n8n
│   │   ├── ExpenseController.php       # pemasukan/pengeluaran
│   │   ├── PromoCalendarController.php # kalender promo
│   │   ├── SitemapController.php       # sitemap.xml + halaman Tentang
│   │   ├── BackupController.php        # backup DB
│   │   ├── ServerNetworkController.php # server status & ping
│   │   └── TunnelManagerController.php # kontrol cloudflared
│   └── Middleware/
│       └── RoleCheck.php               # gate per role
├── Models/                             # Product, Order, OrderItem, Customer,
│                                       # Voucher, LensType, PromoEvent, StoreSetting, dll.
resources/views/
├── layouts/app.blade.php               # layout utama + SEO meta
├── shop/                               # halaman publik (+ about & sitemap)
├── pos/                                # kasir + struk
├── admin/                              # modul admin
└── auth/                               # login
routes/web.php                          # seluruh rute
database/migrations/                    # 12 migrasi
tests/                                  # PHPUnit (kerangka)
```

## 👥 Role & Hak Akses

| Role | Akses |
|---|---|
| `super_admin` | Mutlak seluruh modul |
| `admin` | Semua modul operasional toko |
| `optician` | Dashboard optik, status produksi pesanan |
| `cashier` | POS/kasir + struk |

Dikelola dari **Admin → Pengaturan → User**. Akun nonaktif otomatis ditolak login.

## 🚀 Instalasi (Development)

```bash
git clone https://github.com/Andriansyah2501/alysa-optical.git
cd alysa-optical

composer install
cp .env.example .env        # lalu isi DB_*, APP_URL
php artisan key:generate

# konfigurasi koneksi MySQL di .env, lalu:
php artisan migrate --seed  # tabel + data awal (lensa, voucher, pengaturan toko)

php artisan serve           # http://localhost:8000
```

**Deploy produksi (referensi):** Nginx site mengarah ke `public/`, PHP-FPM via unix socket,
`php artisan config:cache route:cache view:cache`, storage 775 untuk `www-data`.

## 🔗 Rute Utama

| Rute | Method | Keterangan |
|---|---|---|
| `/` | GET | Katalog & landing toko |
| `/kacamata/{slug}` | GET | Detail frame |
| `/order-online` | GET/POST | Portal order pelanggan |
| `/checkout` → `/checkout/proses` | GET/POST | Keranjang & simpan order |
| `/lacak-pesanan` | GET | Cek status via kode order |
| `/kalender-promo` | GET | Promo berjalan |
| `/tentang` | GET | Tentang, kontak, FAQ |
| `/sitemap.xml` | GET | Sitemap dinamis |
| `/pos` , `/pos/bayar`, `/pos/struk/{kode}` | GET/POST | Modul kasir |
| `/pos/thermer-data/{kode}` | GET | JSON struk thermal 58 mm |
| `/admin/*` | GET/POST | Modul admin (auth + role) |
| `/api/internal/laporan-summary` | GET | API laporan untuk n8n/owner |

## 📸 Screenshots

> *(tambahkan screenshot katalog, POS, dashboard admin, dan halaman Tentang di folder `docs/`)*

## 🧪 Testing

```bash
php artisan test
```

## 📝 Catatan Keamanan

- Jangan pernah commit `.env` (sudah di-gitignore) — token WA & kredensial DB ada di sana
- `APP_DEBUG=false` di produksi
- Endpoint `/api/internal/*` sebaiknya hanya diakses dari jaringan lokal/tunnel tepercaya

---

<div align="center">

Dibangun untuk **Alysa Glasses & Eyewear Studio Batam** 👓✨

</div>
