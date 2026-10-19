<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\TransferKasController;
use App\Http\Controllers\ItemJualanController;
use App\Http\Controllers\BiayaAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==============================
// GUEST
// ==============================

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegistrationForm'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register']);


// ==============================
// AUTHENTICATED
// ==============================

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');


    // ==============================
    // TRANSAKSI
    // Admin & Kasir
    // ==============================

    Route::resource('transaksi', TransaksiController::class)->except('show');
    Route::get('/transaksi/penjualan-item/create', [TransaksiController::class, 'createPenjualanItem'])
        ->name('transaksi.penjualan-item.create');
    Route::post('/transaksi/penjualan-item', [TransaksiController::class, 'storePenjualanItem'])
        ->name('transaksi.penjualan-item.store');
    Route::get('/transaksi/setor', [TransaksiController::class, 'setor'])->name('transaksi.setor');
Route::get('/transaksi/tarik', [TransaksiController::class, 'tarik'])->name('transaksi.tarik');

Route::get('/transfer-kas', [TransferKasController::class, 'index'])
    ->name('transfer-kas.index');

Route::get('/transfer-kas/create', [TransferKasController::class, 'create'])
    ->name('transfer-kas.create');

Route::post('/transfer-kas', [TransferKasController::class, 'store'])
    ->name('transfer-kas.store');

Route::get('/transfer-kas/{id}', [TransferKasController::class, 'show'])
    ->name('transfer-kas.show');
	Route::get('/api/warung/{idWarung}/kas', [TransferKasController::class, 'getKasByWarung']);
Route::get('/kas/brilink-history', [KasController::class, 'getBrilinkHistory'])->name('kas.brilink-history');

	Route::get('/transaksi/kas-masuk', [TransaksiController::class, 'kasMasuk'])->name('transaksi.kas.masuk');
    Route::get('/transaksi/kas-keluar', [TransaksiController::class, 'kasKeluar'])->name('transaksi.kas.keluar');
    Route::post('/transaksi/kas-store', [TransaksiController::class, 'storeKas'])->name('transaksi.kas.store');


	// --------------------------
        // Kas
        // --------------------------

        Route::resource('kas', KasController::class);

        Route::post(
            '/kas/{id}/update-detail',
            [KasController::class, 'updateDetailKas']
        )->name('kas.updateDetail');

        Route::resource('item-jualan', ItemJualanController::class)->except('show');
        Route::resource('biaya-admin', BiayaAdminController::class)->except('show');


        // --------------------------
        // Laporan
        // --------------------------

        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');
    // ==============================
    // KHUSUS ADMIN
    // ==============================

    Route::middleware(['role:admin'])->group(function () {

        // --------------------------
        // Cabang / Warung
        // --------------------------

        // Daftar cabang
        Route::get('/cabang', [CabangController::class, 'index'])
            ->name('cabang.index');

        // Form tambah cabang
        Route::get('/cabang/create', [CabangController::class, 'create'])
            ->name('cabang.create');

        // Simpan cabang
        Route::post('/cabang', [CabangController::class, 'store'])
            ->name('cabang.store');

        // Pilih cabang aktif
        Route::post('/cabang/pilih/{id}', [CabangController::class, 'pilih'])
            ->name('cabang.pilih');

        // Form edit cabang
        Route::get('/cabang/{id}/edit', [CabangController::class, 'edit'])
            ->name('cabang.edit');

        // Update cabang
        Route::put('/cabang/{id}', [CabangController::class, 'update'])
            ->name('cabang.update');

        // Hapus cabang
        Route::delete('/cabang/{id}', [CabangController::class, 'destroy'])
            ->name('cabang.destroy');


        
    });


    // ==============================
    // LOGOUT
    // ==============================

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});