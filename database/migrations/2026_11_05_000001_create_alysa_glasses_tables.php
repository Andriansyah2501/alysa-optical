<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories (Kategori Kacamata)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Lens Types (Jenis Lensa)
        Schema::create('lens_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('additional_price', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Products (Kacamata Frame / Sunglasses)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->default('Alysa Signature');
            $table->string('frame_shape'); // Aviator, Square, Round, Cat-Eye, Oval, Wayfarer
            $table->string('frame_material'); // Titanium, Acetate, Ultem, Stainless
            $table->string('gender'); // Unisex, Pria, Wanita, Anak
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->integer('stock')->default(10);
            $table->string('color_primary');
            $table->string('color_hex')->default('#2D3748');
            $table->string('size_frame')->default('52-18-142 mm');
            $table->text('description');
            $table->string('image_url');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->timestamps();
        });

        // 4. Orders (Pesanan)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('shipping_address');
            $table->string('city');
            $table->string('shipping_courier')->default('JNE REG (Gratis Ongkir)');
            
            // Prescription Details (Data Resep Lensa Optik)
            $table->boolean('has_prescription')->default(false);
            $table->string('lens_type_name')->nullable();
            $table->decimal('lens_price', 12, 2)->default(0);
            $table->string('sph_r')->nullable();
            $table->string('cyl_r')->nullable();
            $table->string('axis_r')->nullable();
            $table->string('sph_l')->nullable();
            $table->string('cyl_l')->nullable();
            $table->string('axis_l')->nullable();
            $table->string('pd')->nullable(); // Pupil Distance
            $table->text('prescription_notes')->nullable();

            $table->decimal('subtotal', 12, 2);
            $table->decimal('grand_total', 12, 2);
            $table->string('payment_method')->default('QRIS Instant (BCA / Mandiri / ShopeePay)');
            $table->string('payment_status')->default('PAID');
            $table->string('order_status')->default('DIPROSES'); // DIPROSES, DIKIRIM, SELESAI
            $table->timestamps();
        });

        // 5. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('product_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('lens_types');
        Schema::dropIfExists('categories');
    }
};
