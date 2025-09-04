<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rute yang dapat diakses oleh semua orang (guest)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Rute yang memerlukan autentikasi (harus login)
Route::middleware(['auth'])->group(function () {
    // Rute untuk halaman utama (dashboard)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rute untuk Transaksi menggunakan Route::resource
    Route::resource('transaksi', TransaksiController::class);

    // Rute untuk halaman laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Rute untuk logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
