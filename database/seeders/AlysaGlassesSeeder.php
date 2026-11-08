<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\LensType;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class AlysaGlassesSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori Toko Kacamata Modern
        $optik = Category::create([
            'name' => 'Kacamata Optik (Minus/Plus)',
            'slug' => 'kacamata-optik',
            'icon' => 'fa-solid fa-glasses',
            'description' => 'Koleksi frame ringan, ergonomis, dan stylish untuk aktivitas kerja serta pemakaian harian.'
        ]);

        $sunglasses = Category::create([
            'name' => 'Sunglasses UV400',
            'slug' => 'sunglasses',
            'icon' => 'fa-solid fa-sun',
            'description' => 'Perlindungan maksimal dari sinar UV dengan lensa polaroid premium dan desain ikonik.'
        ]);

        $titanium = Category::create([
            'name' => 'Titanium Ultralight Series',
            'slug' => 'titanium-series',
            'icon' => 'fa-solid fa-feather-pointed',
            'description' => 'Bahan titanium murni jepang, super lentur, anti karat, dan bobot hanya 8 gram.'
        ]);

        $kids = Category::create([
            'name' => 'Kids & Teen Comfort',
            'slug' => 'kids-teen',
            'icon' => 'fa-solid fa-child',
            'description' => 'Frame lentur silikon tahan patah khusus anak dan remaja yang aktif belajar.'
        ]);

        // Jenis Lensa Pilihan
        LensType::create([
            'name' => 'Lensa Netral (Bawaan Pabrik / Demo)',
            'slug' => 'lensa-netral',
            'additional_price' => 0,
            'description' => 'Lensa standar tanpa ukuran minus/plus untuk kebutuhan gaya/fashion.'
        ]);

        LensType::create([
            'name' => 'Lensa Anti Radiasi Blueray UV420',
            'slug' => 'blueray-uv420',
            'additional_price' => 150000,
            'description' => 'Filter sinar biru laptop & handphone hingga 95%, anti silau, cocok untuk kerja seharian.'
        ]);

        LensType::create([
            'name' => 'Lensa Photochromic Gray (Transisi Matahari)',
            'slug' => 'photochromic-transisi',
            'additional_price' => 220000,
            'description' => 'Otomatis gelap menjadi hitam elegan di luar ruangan saat terpapar sinar matahari UV.'
        ]);

        LensType::create([
            'name' => 'Lensa Bluechromic 2-in-1 (Blueray + Photochromic)',
            'slug' => 'bluechromic-all-in-one',
            'additional_price' => 320000,
            'description' => 'Kombinasi pamungkas: penangkal radiasi gadget indoor + perlindungan sunglasses otomatis outdoor.'
        ]);

        LensType::create([
            'name' => 'Lensa Tipis High Index 1.67 Aspheric',
            'slug' => 'high-index-167',
            'additional_price' => 450000,
            'description' => 'Ketebalan lensa direduksi hingga 40% untuk resep minus tinggi (di atas -4.00).'
        ]);

        // Produk Kacamata Unggulan
        $productsData = [
            [
                'category_id' => $optik->id,
                'name' => 'Alysa Shibuya Minimalist Round',
                'slug' => 'alysa-shibuya-minimalist-round',
                'brand' => 'Alysa Tokyo Collection',
                'frame_shape' => 'Round',
                'frame_material' => 'Titanium & Acetate Core',
                'gender' => 'Unisex',
                'price' => 489000,
                'discount_price' => 379000,
                'stock' => 18,
                'color_primary' => 'Rose Gold Champagne',
                'color_hex' => '#B76E79',
                'size_frame' => '50-19-145 mm',
                'description' => 'Frame bulat vintage dengan sentuhan rose gold modern. Bantalan hidung silikon medis super lembut yang tidak berbekas di hidung meski dipakai 12 jam nonstop.',
                'image_url' => 'https://images.unsplash.com/photo-1591076482161-42ce6da69f67?auto=format&fit=crop&w=700&q=80',
                'is_featured' => true,
                'is_bestseller' => true
            ],
            [
                'category_id' => $optik->id,
                'name' => 'Alysa Cambridge Classic Wayfarer',
                'slug' => 'alysa-cambridge-classic-wayfarer',
                'brand' => 'Alysa Oxford Heritage',
                'frame_shape' => 'Wayfarer',
                'frame_material' => 'Italian Bio-Acetate',
                'gender' => 'Pria / Unisex',
                'price' => 520000,
                'discount_price' => 415000,
                'stock' => 14,
                'color_primary' => 'Deep Tortoise Amber',
                'color_hex' => '#4A3728',
                'size_frame' => '53-18-145 mm',
                'description' => 'Gaya intelektual klasik yang tidak lekang oleh waktu. Dibuat dari asetat Italia organik berkualitas tinggi dengan aksen engsel baja tahan karat 5-barel.',
                'image_url' => 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?auto=format&fit=crop&w=700&q=80',
                'is_featured' => true,
                'is_bestseller' => true
            ],
            [
                'category_id' => $titanium->id,
                'name' => 'Alysa Aerolight Rimless Titanium',
                'slug' => 'alysa-aerolight-rimless-titanium',
                'brand' => 'Alysa AeroTech Pro',
                'frame_shape' => 'Square Rimless',
                'frame_material' => 'Pure Beta-Titanium Grade 5',
                'gender' => 'Unisex',
                'price' => 890000,
                'discount_price' => 699000,
                'stock' => 9,
                'color_primary' => 'Matte Silver Gunmetal',
                'color_hex' => '#718096',
                'size_frame' => '52-17-142 mm',
                'description' => 'Frame tanpa bingkai (bor) paling ringan di kelasnya. Berat total hanya 7.8 gram tanpa lensa. Tampilan profesional, bersih, dan berwibawa.',
                'image_url' => 'https://images.unsplash.com/photo-1508296695146-257a814070b4?auto=format&fit=crop&w=700&q=80',
                'is_featured' => true,
                'is_bestseller' => false
            ],
            [
                'category_id' => $sunglasses->id,
                'name' => 'Alysa Riviera Polarized Aviator',
                'slug' => 'alysa-riviera-polarized-aviator',
                'brand' => 'Alysa SunVoyage',
                'frame_shape' => 'Aviator',
                'frame_material' => 'Stainless Steel Ultra-Fine',
                'gender' => 'Pria / Wanita',
                'price' => 560000,
                'discount_price' => 439000,
                'stock' => 22,
                'color_primary' => 'Gold Frame with Dark Emerald Lens',
                'color_hex' => '#D4AF37',
                'size_frame' => '58-14-140 mm',
                'description' => 'Desain aviator legendaris dengan lensa polaroid HD anti silau. Sangat direkomendasikan untuk mengemudi siang hari dan liburan pantai tropis.',
                'image_url' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=700&q=80',
                'is_featured' => true,
                'is_bestseller' => true
            ],
            [
                'category_id' => $sunglasses->id,
                'name' => 'Alysa Saint-Tropez Oversized Cat-Eye',
                'slug' => 'alysa-saint-tropez-oversized-cat-eye',
                'brand' => 'Alysa Haute Couture',
                'frame_shape' => 'Cat-Eye',
                'frame_material' => 'Glossy Acetate',
                'gender' => 'Wanita',
                'price' => 610000,
                'discount_price' => 475000,
                'stock' => 11,
                'color_primary' => 'Midnight Onyx Black',
                'color_hex' => '#1A202C',
                'size_frame' => '54-18-142 mm',
                'description' => 'Bentuk cat-eye tegas yang menonjolkan garis tulang pipi. Memberikan aura glamor instan ala selebriti Cannes.',
                'image_url' => 'https://images.unsplash.com/photo-1509695507497-903c140c43b0?auto=format&fit=crop&w=700&q=80',
                'is_featured' => false,
                'is_bestseller' => true
            ],
            [
                'category_id' => $titanium->id,
                'name' => 'Alysa Kyoto Polygon Geometric',
                'slug' => 'alysa-kyoto-polygon-geometric',
                'brand' => 'Alysa Tokyo Collection',
                'frame_shape' => 'Geometric Hexagon',
                'frame_material' => 'Beta Titanium Flexible',
                'gender' => 'Unisex',
                'price' => 745000,
                'discount_price' => 589000,
                'stock' => 15,
                'color_primary' => 'Antique Bronze',
                'color_hex' => '#8C6239',
                'size_frame' => '49-20-145 mm',
                'description' => 'Frame bersegi heksagonal artistik dengan sudut halus. Sangat populer di kalangan desainer, arsitek, dan insan kreatif.',
                'image_url' => 'https://images.unsplash.com/photo-1473496169904-658ba7c44d8a?auto=format&fit=crop&w=700&q=80',
                'is_featured' => true,
                'is_bestseller' => false
            ],
            [
                'category_id' => $kids->id,
                'name' => 'Alysa Junior SmartFlex Anti-Blue',
                'slug' => 'alysa-junior-smartflex-anti-blue',
                'brand' => 'Alysa Kids Safety',
                'frame_shape' => 'Rounded Oval',
                'frame_material' => 'Medical Grade TR90 Silicone',
                'gender' => 'Anak-Anak',
                'price' => 380000,
                'discount_price' => 295000,
                'stock' => 25,
                'color_primary' => 'Pastel Sky Blue',
                'color_hex' => '#63B3ED',
                'size_frame' => '46-16-130 mm',
                'description' => 'Bahan lentur anti patah meski ditekuk 180 derajat. Melindungi mata anak dari paparan radiasi gadget saat belajar daring.',
                'image_url' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?auto=format&fit=crop&w=700&q=80',
                'is_featured' => false,
                'is_bestseller' => false
            ],
            [
                'category_id' => $optik->id,
                'name' => 'Alysa Nordic Clean Square',
                'slug' => 'alysa-nordic-clean-square',
                'brand' => 'Alysa Scandinavian Pro',
                'frame_shape' => 'Square Soft Edge',
                'frame_material' => 'Swiss Ultem High-Resilience',
                'gender' => 'Unisex',
                'price' => 450000,
                'discount_price' => 349000,
                'stock' => 30,
                'color_primary' => 'Frosted Crystal Clear',
                'color_hex' => '#CBD5E0',
                'size_frame' => '52-18-142 mm',
                'description' => 'Frame transparan bening (crystal) yang sedang tren besar di Korea dan Eropa. Memberikan kesan wajah segar dan terang.',
                'image_url' => 'https://images.unsplash.com/photo-1577803645773-f96470509666?auto=format&fit=crop&w=700&q=80',
                'is_featured' => true,
                'is_bestseller' => true
            ]
        ];

        foreach ($productsData as $item) {
            Product::create($item);
        }

        // Seeder Pesanan Simulasi Nyata
        $order1 = Order::create([
            'order_code' => 'ALYSA-20261105-8891',
            'customer_name' => 'Clara Nathania',
            'customer_email' => 'clara.nathania@example.com',
            'customer_phone' => '081288997766',
            'shipping_address' => 'Jl. Boulevard Kelapa Gading No. 42, RT 04 / RW 12',
            'city' => 'Jakarta Utara',
            'shipping_courier' => 'JNE YES Express (Same Day)',
            'has_prescription' => true,
            'lens_type_name' => 'Lensa Bluechromic 2-in-1 (Blueray + Photochromic)',
            'lens_price' => 320000,
            'sph_r' => '-1.75',
            'cyl_r' => '-0.50',
            'axis_r' => '90',
            'sph_l' => '-2.00',
            'cyl_l' => '-0.25',
            'axis_l' => '85',
            'pd' => '62',
            'prescription_notes' => 'Tolong lapis anti gores ekstra, sering kerja depan monitor iMac.',
            'subtotal' => 379000,
            'grand_total' => 699000,
            'payment_method' => 'BCA Virtual Account (Auto Verified)',
            'payment_status' => 'PAID',
            'order_status' => 'DIPROSES'
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => 1,
            'product_name' => 'Alysa Shibuya Minimalist Round',
            'price' => 379000,
            'quantity' => 1,
            'total' => 379000
        ]);
    }
}
