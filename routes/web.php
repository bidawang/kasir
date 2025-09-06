<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute yang dapat diakses oleh semua orang (guest)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Rute yang memerlukan autentikasi (harus login)
Route::middleware(['auth'])->group(function () {

    // === Khusus Role Kasir ===
    Route::middleware(['role:kasir'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('transaksi', TransaksiController::class);
    });

    // === Khusus Role Admin ===
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('transaksi', TransaksiController::class);
        Route::resource('kas', KasController::class);
        Route::post('/kas/{id}/update-detail', [KasController::class, 'updateDetailKas'])->name('kas.updateDetail');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });

    // Rute untuk logout (bisa diakses semua role yang login)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
