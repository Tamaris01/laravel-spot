<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiwayatParkirController;
use App\Http\Controllers\MonitoringParkirController;
use App\Http\Controllers\LaporanMetrikController;


// Middleware untuk autentikasi user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoint untuk memproses pemindaian QR Code
Route::post('/scan-qr', [RiwayatParkirController::class, 'scanQR']);
Route::get('/check_plate/{plat_nomor}', [MonitoringParkirController::class, 'checkPlate']);
// ESP32-A mengirim plat hasil scan ke server
Route::post('/send-plat', [RiwayatParkirController::class, 'sendPlat']);

// ESP32-B mengambil plat terbaru yang dikirim ESP32-A
Route::get('/get-plat', [RiwayatParkirController::class, 'getPlat']);
Route::get('/laporan-metrik', [LaporanMetrikController::class, 'index']);
