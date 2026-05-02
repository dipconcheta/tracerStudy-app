<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\AlumniAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Autentikasi (Publik)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/',        [AuthController::class, 'tampilkanLogin'])->name('login');
    Route::get('/masuk',   [AuthController::class, 'tampilkanLogin'])->name('login');
    Route::post('/masuk',  [AuthController::class, 'prosesLogin'])->name('login.proses');

    Route::get('/daftar',  [AuthController::class, 'tampilkanDaftar'])->name('daftar');
    Route::post('/daftar', [AuthController::class, 'prosesDaftar'])->name('daftar.proses');
});

Route::post('/keluar', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Rute Alumni
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'cek.peran:alumni'])->prefix('alumni')->name('alumni.')->group(function () {
    Route::get('/beranda',     [AlumniController::class, 'dashboard'])->name('dashboard');
    Route::get('/form-data',   [AlumniController::class, 'tampilkanForm'])->name('form');
    Route::post('/form-data',  [AlumniController::class, 'simpanForm'])->name('form.simpan');
});

/*
|--------------------------------------------------------------------------
| Rute Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'cek.peran:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard & Visualisasi
    Route::get('/',                  [DashboardAdminController::class, 'index'])->name('dashboard');
    Route::get('/grafik-data',       [DashboardAdminController::class, 'dataGrafikJson'])->name('grafik.json');

    // Manajemen Data Alumni
    Route::prefix('alumni')->name('alumni.')->group(function () {
        Route::get('/',              [AlumniAdminController::class, 'index'])->name('index');
        Route::get('/{alumni}',      [AlumniAdminController::class, 'tampilkan'])->name('tampilkan');
        Route::post('/{alumni}/verifikasi',         [AlumniAdminController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/{alumni}/batal-verifikasi',   [AlumniAdminController::class, 'batalkanVerifikasi'])->name('batal.verifikasi');
        Route::get('/ekspor/csv',    [AlumniAdminController::class, 'ekspor'])->name('ekspor');
    });

    // Manajemen Akun
    Route::prefix('akun')->name('akun.')->group(function () {
        Route::get('/',              [AlumniAdminController::class, 'daftarAkun'])->name('index');
        Route::post('/buat',         [AlumniAdminController::class, 'buatAkun'])->name('buat');
        Route::post('/{User}/toggle-aktif', [AlumniAdminController::class, 'toggleAktif'])->name('toggle');
    });
});
