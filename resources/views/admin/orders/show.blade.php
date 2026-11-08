@extends('layouts.app')

@section('title', 'Validasi Pemesan: ' . $order->order_code . ' | Alysa Glasses Backoffice')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-sand-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-brand-600 font-bold uppercase tracking-wider">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Antrean Lab Optik</a>
                <span>/</span>
                <span>Validasi Pemesan</span>
            </div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-slate-900 mt-1">Detail Order: {{ $order->order_code }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                Dipesan pada {{ $order->created_at->format('d F Y, H:i') }} WIB 
                @if($order->validated_at)
                    • <span class="text-emerald-700 font-semibold"><i class="fa-solid fa-circle-check"></i> Divalidasi Admin {{ \Carbon\Carbon::parse($order->validated_at)->format('d/m/Y H:i') }}</span>
                @else
                    • <span class="text-amber-600 font-semibold"><i class="fa-solid fa-clock"></i> Belum Divalidasi</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.order.edit', $order->id) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-pen-to-square"></i> Edit Transaksi (CRUD)
            </a>
            <form action="{{ route('admin.order.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus permanen transaksi {{ $order->order_code }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can"></i> Hapus
                </button>
            </form>
            <a href="{{ route('pos.receipt', $order->order_code) }}" target="_blank" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-print"></i> Cetak Struk
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-white border border-sand-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-sand-50 transition-colors">
                &larr; Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-base"></i> {{ session('success') }}
        </div>
    @endif

    @php
        $rawPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($rawPhone, '0')) {
            $waNumber = '62' . substr($rawPhone, 1);
        } elseif (str_starts_with($rawPhone, '62')) {
            $waNumber = $rawPhone;
        } else {
            $waNumber = '62' . $rawPhone;
        }

        $waMsg = "Halo Kak *" . $order->customer_name . "*,\n"
               . "Berikut update terbaru status pesanan kacamata Anda di *ALYSA GLASSES*:\n\n"
               . "📋 *INFO PESANAN:*\n"
               . "• No. Order : *" . $order->order_code . "*\n"
               . "• Status Pembayaran : *" . $order->payment_status . "* (" . $order->payment_method . ")\n"
               . "• Status Produksi   : *" . $order->order_status . "*\n";

        if (!empty($order->tracking_number)) {
            $waMsg .= "• No. Resi Kurir    : *" . $order->tracking_number . "* (" . $order->shipping_courier . ")\n";
        }

        if (!empty($order->admin_notes)) {
            $waMsg .= "• Catatan Optik/Lab : _" . $order->admin_notes . "_\n";
        }

        $waMsg .= "\n🔍 *Pantau Proses Perakitan & Resep Kacamata:*\n"
                . url('/lacak-pesanan?code=' . $order->order_code) . "\n\n"
                . "Terima kasih telah berbelanja di Alysa Glasses (Eyewear That Completes Your Look 🫶🏻).\n"
                . "Info/Bantuan CS: " . ($settings['store_phone'] ?? '0812-3456-7890');

        $waUrl = "https://wa.me/" . $waNumber . "?text=" . urlencode($waMsg);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Detail Informasi Pemesan, Resep, dan Produk (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6 text-xs">
            
            <!-- Kartu Pelanggan & Alamat -->
            <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-sand-200">
                    <h3 class="font-serif text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-user text-brand-600"></i> Informasi Pemesan
                    </h3>
                    @if(strlen($rawPhone) >= 9)
                        <a href="{{ $waUrl }}" target="_blank" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold flex items-center gap-1.5 shadow-sm transition-all">
                            <i class="fa-brands fa-whatsapp text-sm"></i> Kirim Info Update ke WA Pemesan
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Nama Lengkap</span>
                        <span class="text-sm font-extrabold text-slate-900 block mt-0.5">{{ $order->customer_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Nomor WhatsApp / Telp</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-bold text-slate-800">{{ $order->customer_phone }}</span>
                            @if(strlen($rawPhone) >= 9)
                                <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="text-emerald-600 hover:underline font-bold text-[11px]">
                                    (Chat Langsung)
                                </a>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Email</span>
                        <span class="font-medium text-slate-700 block mt-0.5">{{ $order->customer_email ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Kurir & Kota</span>
                        <span class="font-semibold text-slate-800 block mt-0.5">{{ $order->shipping_courier }} • {{ $order->city }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-sand-200">
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Alamat Lengkap Pengiriman</span>
                    <p class="font-medium text-slate-800 mt-1 leading-relaxed bg-sand-50 p-3 rounded-2xl border border-sand-200">
                        {{ $order->shipping_address }}
                    </p>
                </div>
            </div>

            <!-- Kartu Frame & Paket Lensa -->
            <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-4">
                <h3 class="font-serif text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-sand-200">
                    <i class="fa-solid fa-glasses text-brand-600"></i> Frame Kacamata & Lensa Pilihan
                </h3>

                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="p-3 bg-sand-50 rounded-2xl flex items-center justify-between border border-sand-200">
                            <div class="flex items-center gap-3">
                                @if($item->product && $item->product->image_url)
                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded-xl object-cover bg-white border border-sand-200">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-sand-200 flex items-center justify-center text-slate-400 text-lg">
                                        <i class="fa-solid fa-glasses"></i>
                                    </div>
                                @endif
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">{{ $item->product_name }}</span>
                                    <span class="text-[10px] text-slate-500">Jumlah: {{ $item->quantity }} pcs</span>
                                </div>
                            </div>
                            <span class="font-extrabold text-slate-900 text-xs">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <div class="p-3 bg-brand-50/70 rounded-2xl border border-brand-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-brand-900 block text-xs">{{ $order->lens_type_name ?: 'CRMC Minus Standar' }}</span>
                            <span class="text-[10px] text-brand-700">Paket Lensa Optik Pilihan Pemesan</span>
                        </div>
                        <span class="font-extrabold text-brand-800 text-xs">Rp {{ number_format($order->lens_price, 0, ',', '.') }}</span>
                    </div>

                    @if($order->discount_amount > 0)
                        <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center justify-between text-emerald-800">
                            <span class="font-bold">Potongan Diskon Voucher ({{ $order->voucher_code }})</span>
                            <span class="font-extrabold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($order->payment_type === 'dp')
                    <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 space-y-1">
                        <div class="flex justify-between items-center text-amber-900 font-bold">
                            <span>Down Payment (Uang Muka Diterima):</span>
                            <span>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-rose-700 font-extrabold text-xs">
                            <span>Sisa Tagihan Belum Lunas:</span>
                            <span>Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</span>
                        </div>
                        @if($order->remaining_balance > 0)
                            <div class="pt-2 mt-2 border-t border-amber-200">
                                <form action="{{ route('pos.settle', $order->id) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="settlement_payment_method" value="Cash / Tunai">
                                    <input type="hidden" name="settlement_amount_paid" value="{{ $order->remaining_balance }}">
                                    <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-[11px] shadow-sm flex items-center gap-1.5">
                                        <i class="fa-solid fa-hand-holding-dollar"></i> Lunasi Sisa Tagihan Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="text-[10px] text-emerald-700 font-bold mt-1">
                                <i class="fa-solid fa-circle-check"></i> Telah Dilunasi {{ $order->settled_at ? \Carbon\Carbon::parse($order->settled_at)->format('d/m/Y H:i') : '' }}
                            </div>
                        @endif
                    </div>
                @endif
                <div class="pt-3 border-t border-sand-200 flex items-center justify-between text-sm">
                        <span class="font-bold text-slate-800">Total Tagihan Pemesan</span>
                        <span class="font-extrabold text-brand-700 text-base">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Kartu Resep Mata Klinis (RX) -->
            <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-4">
                <h3 class="font-serif text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-sand-200">
                    <i class="fa-solid fa-eye text-brand-600"></i> Resep Lensa Refraksionis Optik (RX)
                </h3>

                @if($order->has_prescription)
                    <div class="overflow-x-auto">
                        <table class="w-full text-center border-collapse">
                            <thead>
                                <tr class="bg-sand-100 text-slate-700 font-bold text-[11px]">
                                    <th class="p-2.5 rounded-l-xl">Mata</th>
                                    <th class="p-2.5">SPH (Sferis)</th>
                                    <th class="p-2.5">CYL (Silinder)</th>
                                    <th class="p-2.5">AXIS (Derajat)</th>
                                    <th class="p-2.5 rounded-r-xl">PD (Pupil)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sand-200 font-mono text-xs">
                                <tr>
                                    <td class="p-3 font-bold bg-blue-50/50 text-blue-900">R (Kanan / OD)</td>
                                    <td class="p-3 font-bold">{{ $order->sph_r ?: '0.00' }}</td>
                                    <td class="p-3">{{ $order->cyl_r ?: '0.00' }}</td>
                                    <td class="p-3">{{ $order->axis_r ?: '0' }}°</td>
                                    <td class="p-3 row-span-2 font-bold text-slate-800" rowspan="2" style="vertical-align: middle;">
                                        {{ $order->pd ?: '62' }} mm
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-bold bg-amber-50/50 text-amber-900">L (Kiri / OS)</td>
                                    <td class="p-3 font-bold">{{ $order->sph_l ?: '0.00' }}</td>
                                    <td class="p-3">{{ $order->cyl_l ?: '0.00' }}</td>
                                    <td class="p-3">{{ $order->axis_l ?: '0' }}°</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if($order->prescription_notes)
                        <div class="mt-3 p-3 bg-sand-50 rounded-2xl border border-sand-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Catatan Khusus Pemesan:</span>
                            <p class="text-slate-700 italic mt-0.5">"{{ $order->prescription_notes }}"</p>
                        </div>
                    @endif
                @else
                    <div class="p-4 bg-sand-50 rounded-2xl text-center text-slate-500 italic">
                        Kacamata dipesan dengan lensa netral / plano (tanpa minus atau resep).
                    </div>
                @endif
            </div>

        </div>

        <!-- Kolom Kanan: Form Validasi & Update Status (1 Kolom) -->
        <div class="space-y-6 text-xs">
            
            <div class="bg-white rounded-3xl border border-sand-200 shadow-sm p-6 space-y-5">
                <h3 class="font-serif text-base font-bold text-slate-900 pb-3 border-b border-sand-200 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-brand-600"></i> Panel Validasi & Update
                </h3>

                <form action="{{ route('admin.order.updateStatus', $order->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Status Pembayaran -->
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Pembayaran</label>
                        <select name="payment_status" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                            <option value="Lunas" {{ $order->payment_status == 'Lunas' ? 'selected' : '' }}>✅ Lunas / Terbayar</option>
                            <option value="Menunggu Verifikasi" {{ $order->payment_status == 'Menunggu Verifikasi' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                            <option value="Menunggu Pembayaran" {{ $order->payment_status == 'Menunggu Pembayaran' ? 'selected' : '' }}>⚠️ Belum Dibayar</option>
                            <option value="Dibatalkan" {{ $order->payment_status == 'Dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan / Batal</option>
                        </select>
                    </div>

                    <!-- Status Produksi / Order -->
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Tahapan Kacamata</label>
                        <select name="order_status" required class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-bold text-slate-800">
                            <option value="Pesanan Diterima" {{ $order->order_status == 'Pesanan Diterima' ? 'selected' : '' }}>📥 1. Pesanan Diterima</option>
                            <option value="Proses Potong Lensa (Lab Optik)" {{ $order->order_status == 'Proses Potong Lensa (Lab Optik)' ? 'selected' : '' }}>⚙️ 2. Proses Potong Lensa (Lab Optik)</option>
                            <option value="Quality Control & Pembersihan" {{ $order->order_status == 'Quality Control & Pembersihan' ? 'selected' : '' }}>🔍 3. Quality Control & Pembersihan</option>
                            <option value="Siap Dikirim / Diserahkan" {{ $order->order_status == 'Siap Dikirim / Diserahkan' ? 'selected' : '' }}>📦 4. Siap Dikirim / Diserahkan</option>
                            <option value="Sedang Dikirim oleh Kurir" {{ $order->order_status == 'Sedang Dikirim oleh Kurir' ? 'selected' : '' }}>🚚 5. Sedang Dikirim oleh Kurir</option>
                            <option value="Selesai / Diterima Pelanggan" {{ $order->order_status == 'Selesai / Diterima Pelanggan' || $order->order_status == 'Selesai / Dibayar di Kasir' ? 'selected' : '' }}>🎉 6. Selesai / Diterima Pelanggan</option>
                        </select>
                    </div>

                    <!-- No Resi Pengiriman -->
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nomor Resi Kurir (Opsional)</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Contoh: JNE889912345" class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl font-mono text-slate-800">
                        <span class="text-[10px] text-slate-400 mt-1 block">Akan otomatis tampil di pelacakan pembeli.</span>
                    </div>

                    <!-- Catatan Admin & Optik -->
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Catatan Refraksionis / Lab Optik</label>
                        <textarea name="admin_notes" rows="3" placeholder="Contoh: Lensa sudah diasah presisi PD 62. Frame disetel nyaman." class="w-full px-3 py-2 bg-sand-50 border border-sand-300 rounded-xl text-slate-800">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md shadow-brand-500/20 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan & Validasi Pemesan
                    </button>
                </form>

                @if(strlen($rawPhone) >= 9)
                    <div class="pt-4 border-t border-sand-200">
                        <span class="text-slate-500 block mb-2 font-medium">Setelah status disimpan, infokan ke pelanggan:</span>
                        <a href="{{ $waUrl }}" target="_blank" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow-sm transition-all text-center">
                            <i class="fa-brands fa-whatsapp text-base"></i> Kirim Notifikasi WA ke Pemesan
                        </a>
                    </div>
                @endif
            </div>

            <!-- Tautan Cepat Pelacakan Pelanggan -->
            <div class="bg-sand-50 rounded-3xl border border-sand-200 p-5 space-y-2">
                <span class="font-bold text-slate-800 block text-xs">Tautan Pelacakan Publik:</span>
                <a href="{{ route('shop.track', ['code' => $order->order_code]) }}" target="_blank" class="text-brand-600 hover:underline font-mono text-[11px] block break-all">
                    {{ url('/lacak-pesanan?code=' . $order->order_code) }}
                </a>
                <span class="text-[10px] text-slate-500 block">Pelanggan dapat mengecek resep dan tahapan produksi melalui tautan ini kapan saja.</span>
            </div>

        </div>

    </div>

</div>
@endsection
