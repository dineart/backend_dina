<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriUktController;
use App\Http\Controllers\KeuanganMahasiswaController;
use App\Http\Controllers\TagihanController;

Route::middleware(['jwt.auth'])->group(function () {
    Route::apiResource('kategori-ukt', KategoriUktController::class);
    Route::apiResource('keuangan-mahasiswa', KeuanganMahasiswaController::class);
    Route::apiResource('tagihan', TagihanController::class);
    
    // Endpoint untuk kelompok lain
    Route::get('status-aktif/{id_mahasiswa}', [KeuanganMahasiswaController::class, 'statusAktif']);

    // Endpoint untuk pengujian golongan
    Route::post(
    '/keuangan-mahasiswa/test-golongan',
    [KeuanganMahasiswaController::class, 'testGolongan']
    );

    // Endpoint untuk generate data dummy
    Route::post(
        'keuangan-mahasiswa/generate-dummy',
        [KeuanganMahasiswaController::class, 'generateDummy']
    );
});