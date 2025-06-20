<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiwayatParkirController;
use App\Http\Controllers\MonitoringParkirController;


// Middleware untuk autentikasi user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoint untuk memproses pemindaian QR Code
Route::post('/scan-qr', [RiwayatParkirController::class, 'scanQR']);
Route::get('/scan-latest', [RiwayatParkirController::class, 'getLatest']);
Route::get('/check_plate/{plat_nomor}', [MonitoringParkirController::class, 'checkPlate']);
Route::get('/check-scan-qr', [RiwayatParkirController::class, 'CheckscanQR']);
Route::get('/perintah-palang', [RiwayatParkirController::class, 'perintahPalang']);
