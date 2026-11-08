<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Voucher;

Route::post('/vouchers/check', function (Request $request) {
    $code = strtoupper(trim($request->input('code')));
    $totalAmount = floatval($request->input('total_amount', 0));

    if (empty($code)) {
        return response()->json([
            'success' => false,
            'message' => 'Silakan masukkan kode voucher terlebih dahulu.'
        ], 400);
    }

    $voucher = Voucher::where('code', $code)->first();

    if (!$voucher) {
        return response()->json([
            'success' => false,
            'message' => 'Kode voucher "' . $code . '" tidak ditemukan atau tidak valid.'
        ], 404);
    }

    $result = $voucher->isValidFor($totalAmount);

    if (!$result['valid']) {
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 422);
    }

    return response()->json([
        'success' => true,
        'data' => $result
    ]);
});
