<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriUktController;
use App\Http\Controllers\BeasiswaController;
use App\Http\Controllers\KeuanganMahasiswaController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\MetodeBayarController;
use App\Http\Controllers\TransaksiPembayaranController;

Route::middleware(['jwt.auth'])->group(function () {
    Route::apiResource('kategori-ukt', KategoriUktController::class);
    Route::apiResource('beasiswa', BeasiswaController::class);
    Route::apiResource('keuangan-mahasiswa', KeuanganMahasiswaController::class);
    Route::apiResource('tagihan', TagihanController::class);
    Route::apiResource('metode-bayar', MetodeBayarController::class);
    Route::apiResource('transaksi-pembayaran', TransaksiPembayaranController::class);
});