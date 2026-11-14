<?php

namespace App\Services;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorSVG;

/**
 * Barcode frame toko — kode unik Code128 per produk (format AG-#####).
 */
class BarcodeService
{
    /** Ambil (atau buat) kode barcode untuk sebuah produk. */
    public static function ensureFor(Product $product): string
    {
        if (!empty($product->barcode)) {
            return $product->barcode;
        }

        $code = sprintf('AG-%05d', $product->id);
        $product->forceFill(['barcode' => $code])->save();

        return $code;
    }

    /** Render SVG Code128 untuk dicetak sebagai tag label. */
    public static function svg(string $code): string
    {
        $generator = new BarcodeGeneratorSVG();

        return $generator->getBarcode($code, $generator::TYPE_CODE_128, 1.6, 40);
    }
}
