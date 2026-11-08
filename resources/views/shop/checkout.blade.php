@extends('layouts.app')

@section('title', 'Checkout & Form Resep Kacamata | Alysa Glasses')

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-bold text-brand-600 uppercase tracking-widest">Langkah Terakhir</span>
            <h1 class="font-serif text-3xl font-bold text-slate-900 mt-1">Konfirmasi Pesanan & Resep Kacamata</h1>
            <p class="text-xs text-slate-500 mt-1">Pilih paket lensa promo 11.11, pasang voucher potongan harga, dan isi ukuran mata Anda.</p>
        </div>

        <form action="{{ route('shop.order.store') }}" method="POST" id="checkoutForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="voucher_code" id="inputHiddenVoucher" value="">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: Form Pemesan & Resep Optik -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- 1. Data Pemesan & Pengiriman -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-sand-200">
                            <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm">1</div>
                            <h3 class="font-bold text-slate-900 text-base">Data Pengiriman Kacamata</h3>
                        </div>

                        <div class="space-y-4 text-xs">
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Nama Lengkap Penerima <span class="text-rose-500">*</span></label>
                                <input type="text" name="customer_name" required placeholder="Contoh: Annisa Larasati" class="w-full px-4 py-3 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP <span class="text-rose-500">*</span></label>
                                    <input type="tel" name="customer_phone" required placeholder="08123456789" class="w-full px-4 py-3 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                                    <span class="text-[10px] text-slate-400 mt-0.5 block">Struk digital & resi akan dikirim ke WhatsApp ini</span>
                                </div>
                                <div>
                                    <label class="font-bold text-slate-700 block mb-1">Email <span class="text-rose-500">*</span></label>
                                    <input type="email" name="customer_email" required placeholder="annisa@example.com" class="w-full px-4 py-3 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="font-bold text-slate-700 block mb-1">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                                    <input type="text" name="city" required placeholder="Contoh: Jakarta Selatan / Batam" class="w-full px-4 py-3 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                                </div>
                                <div>
                                    <label class="font-bold text-slate-700 block mb-1">Pilihan Kurir</label>
                                    <input type="text" readonly value="JNE REG Express (Promo Bebas Ongkir)" class="w-full px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-bold cursor-not-allowed">
                                </div>
                            </div>

                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Alamat Lengkap Rumah / Kantor <span class="text-rose-500">*</span></label>
                                <textarea name="shipping_address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, patokan..." class="w-full px-4 py-3 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Form Resep Optik Kacamata (Sesuai 11.11 Special Price) -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-sand-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm">2</div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base">Pilihan Lensa & Resep Mata</h3>
                                    <span class="text-[10px] text-brand-600 font-extrabold uppercase">✨ 11.11 Special Price Applied</span>
                                </div>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Harga Promo Spesial</span>
                        </div>

                        <!-- Pilihan Tipe Lensa Saat Ini -->
                        <div>
                            <label class="font-bold text-xs text-slate-700 block mb-1.5">Pilih Paket Lensa Kacamata:</label>
                            <select name="lens_type_id" id="lensSelector" class="w-full px-4 py-3 bg-brand-50/60 border border-brand-300 rounded-xl font-bold text-xs text-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-400">
                                @foreach($allLenses as $l)
                                    <option value="{{ $l->id }}" data-price="{{ $l->additional_price }}" {{ ($lens && $lens->id == $l->id) ? 'selected' : ($loop->first ? 'selected' : '') }}>
                                        {{ $l->name }} — Rp {{ number_format($l->additional_price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Checkbox Resep Khusus -->
                        <div class="p-3 bg-sand-50 rounded-2xl border border-sand-200 flex items-center gap-3">
                            <input type="checkbox" id="togglePrescription" name="has_prescription" value="1" checked class="w-4 h-4 text-brand-600 rounded focus:ring-brand-500">
                            <label for="togglePrescription" class="text-xs font-bold text-slate-800 cursor-pointer">
                                Ya, saya ingin memasang ukuran lensa minus / plus / silinder khusus
                            </label>
                        </div>

                        <!-- Input Matriks Resep R dan L -->
                        <div id="prescriptionMatrix" class="space-y-4 pt-2">
                            <!-- OD R -->
                            <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-2">
                                <span class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-eye text-blue-600"></i> Mata Kanan (R / OD)
                                </span>
                                <div class="grid grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600 block mb-1">Sph / Minus (-/+)</label>
                                        <input type="text" name="sph_r" value="-1.50" placeholder="-1.50" class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl font-semibold text-slate-800 text-center">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600 block mb-1">Cylinder (Cyl)</label>
                                        <input type="text" name="cyl_r" value="0.00" placeholder="0.00" class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl font-semibold text-slate-800 text-center">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600 block mb-1">Axis (Derajat)</label>
                                        <input type="text" name="axis_r" value="" placeholder="180" class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl font-semibold text-slate-800 text-center">
                                    </div>
                                </div>
                            </div>

                            <!-- OS L -->
                            <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-100 space-y-2">
                                <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-eye text-amber-600"></i> Mata Kiri (L / OS)
                                </span>
                                <div class="grid grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600 block mb-1">Sph / Minus (-/+)</label>
                                        <input type="text" name="sph_l" value="-1.75" placeholder="-1.75" class="w-full px-3 py-2 bg-white border border-amber-200 rounded-xl font-semibold text-slate-800 text-center">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600 block mb-1">Cylinder (Cyl)</label>
                                        <input type="text" name="cyl_l" value="0.00" placeholder="0.00" class="w-full px-3 py-2 bg-white border border-amber-200 rounded-xl font-semibold text-slate-800 text-center">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-600 block mb-1">Axis (Derajat)</label>
                                        <input type="text" name="axis_l" value="" placeholder="175" class="w-full px-3 py-2 bg-white border border-amber-200 rounded-xl font-semibold text-slate-800 text-center">
                                    </div>
                                </div>
                            </div>

                            <!-- PD & Notes -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div>
                                    <label class="font-bold text-slate-700 block mb-1">Pupil Distance (PD mm)</label>
                                    <input type="text" name="pd" value="62" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800 text-center">
                                </div>
                                <div>
                                    <label class="font-bold text-slate-700 block mb-1">Catatan Tambahan untuk Refraksi</label>
                                    <input type="text" name="prescription_notes" placeholder="Contoh: sering depan monitor" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-medium text-slate-800">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Metode Pembayaran Aman -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-sand-200 shadow-sm space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-sand-200">
                            <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm">3</div>
                            <h3 class="font-bold text-slate-900 text-base">Metode Pembayaran Cepat</h3>
                        </div>

                        <div class="space-y-2 text-xs">
                            <label class="p-3.5 rounded-2xl border border-sand-200 flex items-center justify-between cursor-pointer hover:bg-sand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="QRIS Instant (BCA / Mandiri / GoPay / ShopeePay)" checked class="text-brand-600 focus:ring-brand-500">
                                    <div>
                                        <span class="font-bold text-slate-900 block">QRIS Instant (Semua Bank & E-Wallet)</span>
                                        <span class="text-[11px] text-slate-500">BCA, Mandiri, BNI, BRI, ShopeePay, GoPay, OVO</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded-md">Bebas Biaya Admin</span>
                            </label>

                            <label class="p-3.5 rounded-2xl border border-sand-200 flex items-center justify-between cursor-pointer hover:bg-sand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="BCA Virtual Account (Auto Check)" class="text-brand-600 focus:ring-brand-500">
                                    <div>
                                        <span class="font-bold text-slate-900 block">BCA Virtual Account</span>
                                        <span class="text-[11px] text-slate-500">Konfirmasi otomatis 24 Jam nonstop</span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-building-columns text-slate-400 text-base"></i>
                            </label>

                            <label class="p-3.5 rounded-2xl border border-sand-200 flex items-center justify-between cursor-pointer hover:bg-sand-50 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50/50">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="Cash On Delivery (COD Jabodetabek & Batam)" class="text-brand-600 focus:ring-brand-500">
                                    <div>
                                        <span class="font-bold text-slate-900 block">COD (Bayar di Tempat Saat Kacamata Tiba)</span>
                                        <span class="text-[11px] text-slate-500">Khusus area Jabodetabek dan Batam</span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-hand-holding-dollar text-slate-400 text-base"></i>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Right: Ringkasan Belanja & Input Kupon Voucher Promo -->
                <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-28">
                    
                    <div class="bg-white p-6 rounded-3xl border border-sand-200 shadow-xl space-y-6">
                        <h3 class="font-serif text-lg font-bold text-slate-900 pb-3 border-b border-sand-200">Ringkasan Kacamata Anda</h3>

                        <!-- Item Detail Card -->
                        <div class="flex gap-4 items-center">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 rounded-2xl object-cover bg-sand-100 border border-sand-200 shrink-0">
                            <div>
                                <span class="text-[10px] font-bold text-brand-600 uppercase">{{ $product->brand }}</span>
                                <h4 class="font-bold text-sm text-slate-900">{{ $product->name }}</h4>
                                <span class="text-xs text-slate-500 block mt-0.5">{{ $product->frame_shape }} • {{ $product->color_primary }}</span>
                                <span class="text-xs font-extrabold text-slate-900 mt-1 block">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- MODUL KLAIM KUPON / VOUCHER DISKON -->
                        <div class="p-4 bg-gradient-to-br from-sand-50 to-brand-50/50 rounded-2xl border border-sand-300 space-y-3">
                            <label class="font-bold text-xs text-slate-800 flex items-center justify-between">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-ticket text-brand-600"></i> Punya Kode Voucher Promo?</span>
                            </label>
                            
                            <div class="flex gap-2">
                                <input type="text" id="voucherCodeInput" placeholder="Ketik kode (misal: ALYSA50K)" class="flex-1 px-3.5 py-2.5 bg-white border border-sand-300 rounded-xl text-xs font-bold text-slate-800 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-brand-400">
                                <button type="button" id="btnApplyVoucher" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                    Terapkan
                                </button>
                            </div>

                            <!-- Pesan Status Voucher -->
                            <div id="voucherStatusBox" class="hidden text-[11px] p-2.5 rounded-xl font-semibold"></div>

                            <!-- Rekomendasi Voucher Aktif -->
                            <div class="pt-2 border-t border-sand-200/60">
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1.5">Voucher Tersedia:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($availableVouchers as $voc)
                                        <button type="button" onclick="setVoucher('{{ $voc->code }}')" class="px-2.5 py-1 bg-white hover:bg-brand-100 border border-sand-300 hover:border-brand-400 rounded-lg text-[10px] font-bold text-slate-700 transition-colors flex items-center gap-1">
                                            <i class="fa-solid fa-tag text-brand-600"></i> {{ $voc->code }} ({{ $voc->type == 'fixed' ? 'Potongan Rp ' . number_format($voc->amount, 0, ',', '.') : 'Diskon ' . intval($voc->amount) . '%' }})
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Kalkulasi Rincian Biaya -->
                        <div class="space-y-3 pt-2 text-xs text-slate-600">
                            <div class="flex justify-between">
                                <span>Harga Frame:</span>
                                <span class="font-bold text-slate-800" id="summaryFramePrice">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Paket Lensa (11.11 Promo):</span>
                                <span class="font-bold text-brand-700" id="summaryLensPrice">+ Rp 0</span>
                            </div>
                            <div class="flex justify-between text-emerald-700 font-bold" id="rowVoucherDiscount" style="display: none;">
                                <span class="flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Diskon Voucher:</span>
                                <span id="summaryDiscountPrice">- Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Hardcase Kulit & Lap Microfiber:</span>
                                <span class="font-bold text-emerald-600">GRATIS</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Ongkos Kirim Asuransi:</span>
                                <span class="font-bold text-emerald-600">BEBAS ONGKIR</span>
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div class="p-4 bg-brand-50 rounded-2xl border border-brand-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-brand-800 block">Total Akhir Pembayaran</span>
                                <span class="text-2xl font-extrabold text-brand-900" id="summaryGrandTotal">Rp 0</span>
                            </div>
                            <i class="fa-solid fa-receipt text-brand-600 text-2xl"></i>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-600/25 transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-shield-check"></i>
                            Bayar Sekarang & Konfirmasi Lab Optik
                        </button>

                        <div class="space-y-1.5 text-[11px] text-slate-500 pt-2 border-t border-sand-100">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-emerald-500"></i> Garansi 30 Hari kenyamanan penglihatan Anda
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-emerald-500"></i> Lensa dipotong menggunakan mesin digital computerized
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </form>

    </div>

    <script>
        const framePrice = {{ $product->effective_price }};
        const lensSelector = document.getElementById('lensSelector');
        const summaryFramePrice = document.getElementById('summaryFramePrice');
        const summaryLensPrice = document.getElementById('summaryLensPrice');
        const summaryGrandTotal = document.getElementById('summaryGrandTotal');
        const rowVoucherDiscount = document.getElementById('rowVoucherDiscount');
        const summaryDiscountPrice = document.getElementById('summaryDiscountPrice');
        
        const voucherInput = document.getElementById('voucherCodeInput');
        const btnApplyVoucher = document.getElementById('btnApplyVoucher');
        const voucherStatusBox = document.getElementById('voucherStatusBox');
        const inputHiddenVoucher = document.getElementById('inputHiddenVoucher');

        let currentLensPrice = 0;
        let appliedDiscount = 0;

        function formatRupiah(num) {
            return 'Rp ' . concat(Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        }

        function calculateTotal() {
            const selectedOpt = lensSelector.options[lensSelector.selectedIndex];
            currentLensPrice = parseFloat(selectedOpt.getAttribute('data-price')) || 0;
            summaryLensPrice.textContent = '+ ' + formatRupiah(currentLensPrice);

            const totalBeforeDiscount = framePrice + currentLensPrice;
            const grand = Math.max(0, totalBeforeDiscount - appliedDiscount);
            summaryGrandTotal.textContent = formatRupiah(grand);
        }

        lensSelector.addEventListener('change', () => {
            calculateTotal();
            if (inputHiddenVoucher.value) {
                checkVoucherCode(inputHiddenVoucher.value);
            }
        });

        // Set Voucher dari Tombol Rekomendasi
        function setVoucher(code) {
            voucherInput.value = code;
            checkVoucherCode(code);
        }

        btnApplyVoucher.addEventListener('click', () => {
            const code = voucherInput.value.trim();
            if (code) {
                checkVoucherCode(code);
            }
        });

        async function checkVoucherCode(code) {
            const totalBeforeDiscount = framePrice + currentLensPrice;
            voucherStatusBox.className = 'text-[11px] p-2.5 rounded-xl font-semibold bg-sand-100 text-slate-700 block';
            voucherStatusBox.textContent = 'Memeriksa kupon voucher...';

            try {
                const res = await fetch('/api/vouchers/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code,
                        total_amount: totalBeforeDiscount
                    })
                });

                const json = await res.json();

                if (json.success) {
                    appliedDiscount = parseFloat(json.data.discount);
                    inputHiddenVoucher.value = json.data.code;

                    voucherStatusBox.className = 'text-[11px] p-2.5 rounded-xl font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 block';
                    voucherStatusBox.innerHTML = '<i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i> ' + json.data.message;

                    rowVoucherDiscount.style.display = 'flex';
                    summaryDiscountPrice.textContent = '- ' + formatRupiah(appliedDiscount);
                } else {
                    appliedDiscount = 0;
                    inputHiddenVoucher.value = '';
                    rowVoucherDiscount.style.display = 'none';

                    voucherStatusBox.className = 'text-[11px] p-2.5 rounded-xl font-semibold bg-rose-50 text-rose-800 border border-rose-200 block';
                    voucherStatusBox.innerHTML = '<i class="fa-solid fa-circle-xmark mr-1 text-rose-600"></i> ' + json.message;
                }
            } catch (err) {
                voucherStatusBox.className = 'text-[11px] p-2.5 rounded-xl font-semibold bg-rose-50 text-rose-800 block';
                voucherStatusBox.textContent = 'Gagal memvalidasi voucher. Coba lagi.';
            }

            calculateTotal();
        }

        // Toggle Preskripsi
        const togglePrescription = document.getElementById('togglePrescription');
        const prescriptionMatrix = document.getElementById('prescriptionMatrix');
        togglePrescription.addEventListener('change', function() {
            prescriptionMatrix.style.display = this.checked ? 'block' : 'none';
        });

        // Hitung awal
        calculateTotal();
    </script>

@endsection
