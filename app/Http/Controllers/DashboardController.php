<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\Kas;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data dari database.
     */
    public function index()
    {
        $kasir = Kasir::with('kas')->first();

        // Kas dengan id = 3 + detailKas
        $kasId3 = Kas::with('detailKas')->find(3);

        $saldoPerJenis = Kas::where('id_warung', $kasir->id ?? null)->get();

        $pemasukanHariIni = Transaksi::where('jenis', 'masuk')
            ->whereDate('created_at', today())
            ->sum('total');

        $pengeluaranHariIni = Transaksi::where('jenis', 'keluar')
            ->whereDate('created_at', today())
            ->sum('total');

        $recentTransactions = Transaksi::with('kas')->latest()->take(5)->get();

        $totalSaldo = Kas::sum('saldo');

        return view('dashboard', compact(
            'kasir',
            'totalSaldo',
            'saldoPerJenis',
            'pemasukanHariIni',
            'pengeluaranHariIni',
            'recentTransactions',
            'kasId3'
        ));
    }
}
