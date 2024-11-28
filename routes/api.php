<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PasienController;
use App\Http\Controllers\Api\VendorController;

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

});