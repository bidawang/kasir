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
use App\Http\Controllers\KategoriTransaksiController;
use App\Http\Controllers\ManajemenKasirController;
use App\Http\Controllers\LaporanHarianController;
use App\Http\Controllers\OwnerMonitoringController;
use App\Http\Controllers\Developer\DeveloperController;

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

    // Dashboard and reporting are reserved for administrators and owners.
// ==============================
// ROOT / HOME
// ==============================
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        // Cek status keaktifan user (opsional, menyesuaikan middleware Anda)
        if ($user->status !== 'aktif') {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda tidak aktif.']);
        }

        // Khusus akun developer, arahkan ke developer console
        if ($user->role === 'developer') {
            return redirect()->route('developer.dashboard');
        }

        // Izinkan admin, owner, dan kasir mengakses dashboard
        if (in_array($user->role, ['admin', 'owner', 'kasir'], true)) {
            return app(DashboardController::class)->index();
        }
        
        // Handle jika role lain yang akses root
        abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
    }
    
    // Jika BELUM login, lempar ke halaman /login
    return redirect()->route('login');
})->name('dashboard'); // <-- Berikan name dashboard di sini

    // Cashiers can create transactions and review their own transaction history.
    Route::middleware('role:admin,owner,kasir')->group(function () {
        Route::get('/transaksi/menu', [TransaksiController::class, 'menu'])->name('transaksi.menu');
        Route::get('/transaksi/download-pdf', [TransaksiController::class, 'downloadPdf'])->name('transaksi.download-pdf');
        Route::get('/transaksi/arsip/{id}/download', [TransaksiController::class, 'downloadPdfArsip'])->name('transaksi.arsip.download');
        Route::delete('/transaksi/arsip/{id}', [TransaksiController::class, 'destroyPdfArsip'])->name('transaksi.arsip.destroy');
        Route::resource('transaksi', TransaksiController::class)->only(['index', 'store']);
        Route::get('/transaksi/penjualan-item/create', [TransaksiController::class, 'createPenjualanItem'])
            ->name('transaksi.penjualan-item.create');
        Route::post('/transaksi/penjualan-item', [TransaksiController::class, 'storePenjualanItem'])
            ->name('transaksi.penjualan-item.store');
        Route::get('/transaksi/setor', [TransaksiController::class, 'setor'])->name('transaksi.setor');
        Route::get('/transaksi/tarik', [TransaksiController::class, 'tarik'])->name('transaksi.tarik');
        Route::get('/transaksi/kas-masuk', [TransaksiController::class, 'kasMasuk'])->name('transaksi.kas.masuk');
        Route::get('/transaksi/kas-keluar', [TransaksiController::class, 'kasKeluar'])->name('transaksi.kas.keluar');
        Route::post('/transaksi/kas-store', [TransaksiController::class, 'storeKas'])->name('transaksi.kas.store');
    });

    Route::middleware('role:admin,owner,kasir')->group(function () {
        Route::get('/laporan-harian', [LaporanHarianController::class, 'index'])
            ->name('laporan-harian.index');
        Route::post('/laporan-harian', [LaporanHarianController::class, 'store'])
            ->name('laporan-harian.store');
    });

    // Cash, inventory setup, transfers, and reports are not cashier pages.
    Route::middleware('role:admin,owner')->group(function () {
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

        Route::resource('kas', KasController::class);
        Route::post('/kas/{id}/update-detail', [KasController::class, 'updateDetailKas'])
            ->name('kas.updateDetail');
        Route::resource('item-jualan', ItemJualanController::class)->except('show');
        Route::resource('biaya-admin', BiayaAdminController::class)->except('show');
        Route::resource('kategori-transaksi', KategoriTransaksiController::class)->except('show');
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');
    });
    // ==============================
    // KHUSUS ADMIN & OWNER
    // ==============================

    Route::middleware(['role:admin,owner'])->group(function () {

        Route::resource('manajemen-kasir', ManajemenKasirController::class)
            ->except(['show']);

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

        // --------------------------
        // Monitoring Konsolidasi Semua Cabang (Owner / Admin)
        // --------------------------
        Route::get('/owner/monitoring', [OwnerMonitoringController::class, 'index'])
            ->name('owner.monitoring');
        Route::post('/owner/monitoring/switch/{id}', [OwnerMonitoringController::class, 'switchBranch'])
            ->name('owner.monitoring.switch');
        Route::get('/owner/monitoring/export-pdf', [OwnerMonitoringController::class, 'exportPdf'])
            ->name('owner.monitoring.pdf');


        
    });


    // ==============================
    // LOGOUT
    // ==============================

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// =========================================================================
// DEVELOPER PORTAL (Kelola Owner, Cabang, Kasir & Platform Provisioning)
// =========================================================================
Route::prefix('developer')->name('developer.')->group(function () {
    // Guest Developer Login
    Route::get('/login', [DeveloperController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DeveloperController::class, 'login']);

    // Authenticated Developer
    Route::middleware(['auth', 'role:developer'])->group(function () {
        Route::post('/logout', [DeveloperController::class, 'logout'])->name('logout');
        Route::get('/', [DeveloperController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [DeveloperController::class, 'dashboard']);

        // Kelola Owner
        Route::get('/owner', [DeveloperController::class, 'ownerIndex'])->name('owner.index');
        Route::get('/owner/create', [DeveloperController::class, 'ownerCreate'])->name('owner.create');
        Route::post('/owner', [DeveloperController::class, 'ownerStore'])->name('owner.store');
        Route::get('/owner/{id}', [DeveloperController::class, 'ownerShow'])->name('owner.show');
        Route::get('/owner/{id}/edit', [DeveloperController::class, 'ownerEdit'])->name('owner.edit');
        Route::put('/owner/{id}', [DeveloperController::class, 'ownerUpdate'])->name('owner.update');
        Route::delete('/owner/{id}', [DeveloperController::class, 'ownerDestroy'])->name('owner.destroy');
        Route::patch('/owner/{id}/toggle-status', [DeveloperController::class, 'ownerToggleStatus'])->name('owner.toggle-status');

        // Kelola Cabang
        Route::get('/cabang', [DeveloperController::class, 'cabangIndex'])->name('cabang.index');
        Route::get('/cabang/create', [DeveloperController::class, 'cabangCreate'])->name('cabang.create');
        Route::post('/cabang', [DeveloperController::class, 'cabangStore'])->name('cabang.store');
        Route::get('/cabang/{id}', [DeveloperController::class, 'cabangShow'])->name('cabang.show');
        Route::get('/cabang/{id}/edit', [DeveloperController::class, 'cabangEdit'])->name('cabang.edit');
        Route::put('/cabang/{id}', [DeveloperController::class, 'cabangUpdate'])->name('cabang.update');
        Route::delete('/cabang/{id}', [DeveloperController::class, 'cabangDestroy'])->name('cabang.destroy');

        // Kelola Kasir
        Route::get('/kasir', [DeveloperController::class, 'kasirIndex'])->name('kasir.index');
        Route::get('/kasir/create', [DeveloperController::class, 'kasirCreate'])->name('kasir.create');
        Route::post('/kasir', [DeveloperController::class, 'kasirStore'])->name('kasir.store');
        Route::get('/kasir/{id}/edit', [DeveloperController::class, 'kasirEdit'])->name('kasir.edit');
        Route::put('/kasir/{id}', [DeveloperController::class, 'kasirUpdate'])->name('kasir.update');
        Route::delete('/kasir/{id}', [DeveloperController::class, 'kasirDestroy'])->name('kasir.destroy');
        Route::patch('/kasir/{id}/toggle-status', [DeveloperController::class, 'kasirToggleStatus'])->name('kasir.toggle-status');

        // Developer Profile
        Route::get('/profile', [DeveloperController::class, 'profile'])->name('profile');
        Route::put('/profile', [DeveloperController::class, 'profileUpdate'])->name('profile.update');
    });
});
