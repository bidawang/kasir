<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\Kas;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $idWarung = session('id_warung');

        // Belum memilih cabang
        if (!$idWarung) {
            if ($user->role === 'admin') {
                return redirect()
                    ->route('cabang.index')
                    ->with('error', 'Silakan pilih cabang terlebih dahulu.');
            }

            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Cabang kasir tidak ditemukan.');
        }

        // Pastikan cabang benar-benar ada
        $kasir = Kasir::where('id', $idWarung)
            ->with('kas')
            ->first();

        if (!$kasir) {
            session()->forget('id_warung');

            if ($user->role === 'admin') {
                return redirect()
                    ->route('cabang.index')
                    ->with('error', 'Cabang tidak ditemukan.');
            }

            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Cabang kasir tidak ditemukan.');
        }

        // Ambil Kas Tunai Fisik (status = cash) beserta detail pecahan
        $kasTunai = Kas::where('id_warung', $idWarung)
            ->where('status', 'cash')
            ->with(['detailKas' => function ($query) {
                $query->orderBy('pecahan', 'desc');
            }])
            ->first();

        // Hanya ambil kas E-Wallet (status = ewallet) untuk bagian Saldo Per Jenis Kas
        $saldoEwallet = Kas::where('id_warung', $idWarung)
            ->where('status', 'ewallet')
            ->get();

        // Pemasukan hari ini
        $pemasukanHariIni = Transaksi::where('jenis', 'masuk')
            ->whereHas('kas', function ($query) use ($idWarung) {
                $query->where('id_warung', $idWarung);
            })
            ->whereDate('created_at', today())
            ->sum('total');

        // Pengeluaran hari ini
        $pengeluaranHariIni = Transaksi::where('jenis', 'keluar')
            ->whereHas('kas', function ($query) use ($idWarung) {
                $query->where('id_warung', $idWarung);
            })
            ->whereDate('created_at', today())
            ->sum('total');

        // Transaksi terbaru cabang aktif
        $recentTransactions = Transaksi::whereHas('kas', function ($query) use ($idWarung) {
                $query->where('id_warung', $idWarung);
            })
            ->with('kas')
            ->latest()
            ->take(5)
            ->get();

        // Total saldo seluruh jenis kas cabang aktif (Cash + E-Wallet)
        $totalSaldo = Kas::where('id_warung', $idWarung)
            ->sum('saldo');

        return view('dashboard', compact(
            'kasir',
            'kasTunai',
            'saldoEwallet',
            'totalSaldo',
            'pemasukanHariIni',
            'pengeluaranHariIni',
            'recentTransactions'
        ));
    }
}