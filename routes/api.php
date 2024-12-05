<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PasienController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\TransaksiMasukController;
use App\Http\Controllers\Api\TransaksiPenjualanController;
use App\Http\Controllers\Api\GeneralActionTransaksiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

Route::middleware('jwtMiddleware')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('user', [AuthController::class, 'getUserProfile']);
    Route::apiResource('kategori',KategoriController::class);
    Route::apiResource('pasien',PasienController::class);
    Route::apiResource('vendor',VendorController::class);
    Route::apiResource('obat',ObatController::class);
    Route::get('cek-restok-obat', [ObatController::class, 'cekRestokObat']);
    Route::get('detail-obat/{id}', [ObatController::class, 'detailObat']);

    // Transaksi Pembelian Barang
    Route::apiResource('transaksi-masuk',TransaksiMasukController::class);
    Route::post('buat-transaksi-masuk', [TransaksiMasukController::class, 'buatTransaksiMasuk']);
    Route::post('tambah-transaksi-masuk-obat', [TransaksiMasukController::class, 'tambahTransaksiMasukObat']);
    Route::put('update-transaksi-masuk-obat/{id}', [TransaksiMasukController::class, 'updateTransaksiMasukObat']);

    // Transaksi Penjualan Barang
    Route::apiResource('transaksi-penjualan',TransaksiMasukController::class);


    // General
    Route::delete('hapus-transaksi/{id}', [GeneralActionTransaksiController::class, 'hapusTransaksi']);
    Route::delete('hapus-item-transaksi/{id}', [GeneralActionTransaksiController::class, 'hapusTransaksiItem']);
    Route::put('update-status-transaksi/{id}', [GeneralActionTransaksiController::class, 'updateStatusTransaksi']);
    Route::post('generate-invoic-by-id', [GeneralActionTransaksiController::class, 'generateInvoiceById']);
    Route::post('generate-all-invoice', [GeneralActionTransaksiController::class, 'generateInvoiceAll']);

});