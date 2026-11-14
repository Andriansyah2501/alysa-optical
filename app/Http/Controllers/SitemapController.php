<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PromoEvent;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Halaman statis & SEO — murni aditif (file baru), tidak mengubah alur toko yang sudah ada.
 * - /sitemap.xml : peta situs untuk mesin pencari
 * - /tentang     : halaman Tentang Kami, Kontak & FAQ
 */
class SitemapController extends Controller
{
    public function index(Request $request)
    {
        $urls = [];

        // Halaman utama
        $urls[] = [
            'loc' => route('shop.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Halaman statis toko
        foreach ([
            ['route' => 'shop.about',            'freq' => 'monthly', 'prio' => '0.6'],
            ['route' => 'shop.customer.portal',  'freq' => 'weekly',  'prio' => '0.8'],
            ['route' => 'shop.track',            'freq' => 'weekly',  'prio' => '0.5'],
            ['route' => 'shop.calendar',         'freq' => 'daily',   'prio' => '0.7'],
        ] as $statik) {
            $urls[] = [
                'loc' => route($statik['route']),
                'lastmod' => now()->toAtomString(),
                'changefreq' => $statik['freq'],
                'priority' => $statik['prio'],
            ];
        }

        // Detail produk yang masih tersedia
        Product::where('stock', '>', 0)->orderByDesc('updated_at')->chunkById(200, function ($products) use (&$urls) {
            foreach ($products as $product) {
                $urls[] = [
                    'loc' => route('shop.show', $product->slug),
                    'lastmod' => optional($product->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }
        });

        $xml = view('shop.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function about()
    {
        $phone = (string) StoreSetting::get('store_phone', '');
        $waNumber = preg_replace('/\D/', '', $phone);
        $waNumber = str_starts_with($waNumber, '0')
            ? '62' . substr($waNumber, 1)
            : $waNumber;

        $settings = [
            'store_name'    => StoreSetting::get('store_name', 'Alysa Glasses'),
            'tagline'       => StoreSetting::get('store_tagline', 'Eyewear That Completes Your Look'),
            'about_text'    => StoreSetting::get('footer_about_text', 'Pusat kacamata modern dan optik digital Batam.'),
            'address'       => StoreSetting::get('store_address', 'Batam • Tanjung Pinang • Bintan, Kepulauan Riau'),
            'phone'         => $phone,
            'wa_link'       => $waNumber !== '' ? 'https://wa.me/' . $waNumber : '#',
            'email'         => StoreSetting::get('store_email', ''),
            'instagram'     => StoreSetting::get('store_instagram', ''),
            'instagram_url' => StoreSetting::get('store_instagram_url', 'https://www.instagram.com/alysa_glasses/'),
            'tiktok'        => StoreSetting::get('store_tiktok', ''),
            'wa_channel'    => StoreSetting::get('whatsapp_channel_url', ''),
            'hours'         => StoreSetting::get('footer_hours', '10.00 - 22.00 WIB (Buka Setiap Hari)'),
        ];

        $faqs = [
            [
                'q' => 'Bagaimana cara memesan kacamata dengan resep dokter?',
                'a' => 'Kirim foto resep kacamata Anda melalui tombol WhatsApp “Konsultasi Resep” di halaman utama, atau pesan langsung lewat menu Pesan Online. Tim kami akan membantu mencocokkan frame dan lensa yang sesuai.',
            ],
            [
                'q' => 'Saya belum pernah periksa mata, apakah bisa tetap order online?',
                'a' => 'Bisa. Anda dapat melakukan pemeriksaan di optik/refraksionis terdekat lalu mengirimkan hasilnya, atau konsultasi dulu via WhatsApp untuk rekomendasi langkah termudahnya.',
            ],
            [
                'q' => 'Lensa apa saja yang tersedia?',
                'a' => 'Tersedia lensa single vision, blueray (anti cahaya biru), photocromic (gelap otomatis), bluecromic kombinasi, progresif, hingga anti fog. Detail harga tiap lensa dapat dilihat pada Tabel Harga Lensa di halaman utama dan Kalender Promo.',
            ],
            [
                'q' => 'Apakah pengiriman ke luar Batam bisa?',
                'a' => 'Bisa. Kami melayani area Batam, Tanjung Pinang, dan Bintan. Pesanan luar kota dikirim dengan kurir/ekspedisi yang aman; rincinya dikonfirmasi melalui WhatsApp setelah pesanan dibuat.',
            ],
            [
                'q' => 'Bagaimana melacak pesanan saya?',
                'a' => 'Setiap pesanan mendapat kode unik (contoh: POS-20261113-XXXXX). Masukkan kode tersebut pada halaman Lacak Pesanan untuk melihat status terbaru secara langsung.',
            ],
            [
                'q' => 'Apakah ada garansi?',
                'a' => 'Ya. Kami memberikan jaminan keakuratan resep dan pemeriksaan sebelum frame dipakai. Jika ada ketidaksesuaian hasil lensa dengan resep, konsultasikan melalui WhatsApp — kami bantu penyelesaiannya.',
            ],
            [
                'q' => 'Jam berapa Alysa Glasses melayani order?',
                'a' => 'Layanan online 24 jam melalui website; konsultasi dan pemesanan via WhatsApp pada jam operasional ' . $settings['hours'] . '.',
            ],
        ];

        return view('shop.about', compact('settings', 'faqs'));
    }
}
