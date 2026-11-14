<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;

use Tests\TestCase;

class BundleSmokeTest extends TestCase
{
    public function test_pos_page_renders_with_bundle(): void
    {
        $u = User::where('role', 'super_admin')->first();
        $resp = $this->actingAs($u)->get('/pos');
        $resp->assertOk();
        $resp->assertSee('BUNDLE_PERCENT');
        $resp->assertSee('autoSelectBundleLens');
        $resp->assertSee('lens-card');
    }

    public function test_catalog_edit_has_bundle_field(): void
    {
        $u = User::where('role', 'super_admin')->first();
        $p = Product::first();
        $resp = $this->actingAs($u)->get('/admin/katalog/' . $p->id . '/edit');
        $resp->assertOk();
        $resp->assertSee('bundle_lens_type_id');
    }

    public function test_expenses_page_renders_analysis(): void
    {
        $u = User::where('role', 'super_admin')->first();
        $resp = $this->actingAs($u)->get('/admin/pengeluaran');
        $resp->assertOk();
        $resp->assertSee('Analisis Omset vs Belanja Modal');
    }
}
