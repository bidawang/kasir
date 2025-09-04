<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data dari database.
     */
    public function index()
    {
        // Ambil data kasir pertama (asumsi hanya ada satu warung)
        $kasir = Kasir::with('kas')->first();

        // Ambil transaksi hari ini
        $pemasukanHariIni = Transaksi::where('jenis', 'masuk')
            ->whereDate('created_at', today())
            ->sum('total');

        $pengeluaranHariIni = Transaksi::where('jenis', 'keluar')
            ->whereDate('created_at', today())
            ->sum('total');

        // Ambil 5 transaksi terbaru
        $recentTransactions = Transaksi::with('kas')->latest()->take(5)->get();

        return view('dashboard', compact('kasir', 'pemasukanHariIni', 'pengeluaranHariIni', 'recentTransactions'));
    }
}