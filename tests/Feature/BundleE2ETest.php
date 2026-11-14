<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\LensType;
use App\Models\Order;
use App\Models\StoreSetting;
use Tests\TestCase;

class BundleE2ETest extends TestCase
{
    public function test_bundle_transaction_discount_and_hpp(): void
    {
        $u = User::where('role', 'super_admin')->first();
        $frame = Product::where('stock', '>', 1)->first();
        $lens = LensType::first();
        $this->assertNotNull($frame); $this->assertNotNull($lens);

        // set paket: frame ini + lensa ini, diskon 10%
        $frame->update(['bundle_lens_type_id' => $lens->id]);
        StoreSetting::updateOrCreate(['key' => 'bundle_discount_percent'], ['value' => '10']);

        $framePrice = (float) $frame->effective_price;
        $lensPrice = (float) $lens->additional_price;
        $expectBundle = round(($framePrice + $lensPrice) * 10 / 100);

        $resp = $this->actingAs($u)->post('/pos/bayar', [
            'source_type' => 'offline_pos',
            'order_mode' => 'catalog',
            'product_id' => $frame->id,
            'lens_type_id' => $lens->id,
            'bundle_applied' => '1',
            'customer_name' => 'Uji Bundling',
            'customer_phone' => '081234567890',
            'payment_type' => 'full',
            'payment_method' => 'Tunai',
            'amount_paid' => $framePrice + $lensPrice,
            'shipping_cost' => 0,
        ]);
        $resp->assertRedirect();

        $order = Order::latest('id')->first();
        $this->assertSame('Uji Bundling', $order->customer_name);
        $this->assertSame($expectBundle, (float) $order->discount_amount, 'diskon paket salah');
        $this->assertSame('PAKET BUNDLING', $order->voucher_code);
        $this->assertSame(max(0, $framePrice + $lensPrice - $expectBundle), (float) $order->grand_total);
        // HPP estimasi: margin 30% => hpp = total/1.3
        $expectHpp = round($order->grand_total * 100 / 130);
        $this->assertEqualsWithDelta($expectHpp, (float) $order->hpp_modal, 2);

        // cleanup
        $order->delete();
        $frame->update(['bundle_lens_type_id' => null]);
        print("BUNDLE E2E OK: frame {$framePrice} + lens {$lensPrice} - bundle {$expectBundle} = {$order->grand_total}, hpp {$order->hpp_modal}\n");
    }

    public function test_bundle_not_applied_for_wrong_lens(): void
    {
        $u = User::where('role', 'super_admin')->first();
        $frame = Product::where('stock', '>', 1)->first();
        $lenses = LensType::take(2)->get();
        if ($lenses->count() < 2) { $this->markTestSkipped('butuh 2 lensa'); }
        $frame->update(['bundle_lens_type_id' => $lenses[0]->id]);

        $resp = $this->actingAs($u)->post('/pos/bayar', [
            'source_type' => 'offline_pos',
            'order_mode' => 'catalog',
            'product_id' => $frame->id,
            'lens_type_id' => $lenses[1]->id,   // lensa LAIN -> tidak dapat diskon
            'bundle_applied' => '1',
            'customer_name' => 'Uji Salah Lensa',
            'customer_phone' => '081234567891',
            'payment_type' => 'full',
            'payment_method' => 'Tunai',
            'amount_paid' => 99999999,
            'shipping_cost' => 0,
        ]);
        $resp->assertRedirect();
        $order = Order::latest('id')->first();
        $this->assertSame(0.0, (float) $order->discount_amount);
        $order->delete();
        $frame->update(['bundle_lens_type_id' => null]);
        print("NEGATIVE CASE OK: lensa non-paket tanpa diskon\n");
    }

    public function test_hpp_correction_route(): void
    {
        $u = User::where('role', 'super_admin')->first();
        $order = Order::latest('id')->first();
        if (!$order) { $this->markTestSkipped('belum ada order'); }
        $resp = $this->actingAs($u)->put('/admin/pengeluaran/modal/' . $order->id . '/hpp', [
            'hpp_modal' => 100000,
        ]);
        $resp->assertRedirect();
        $order->refresh();
        $this->assertEqualsWithDelta(100000, (float) $order->hpp_modal, 1);
        print("HPP CORRECTION OK\n");
    }
}
