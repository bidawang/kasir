<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\Kas;
use App\Models\Transaksi;
use App\Models\LaporanHarian;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $idWarung = session('id_warung');

        // Otomatis cari cabang jika session belum terisi
        if (!$idWarung) {
            if ($user->role === 'kasir') {
                $warungKasir = $user->warungs()->first() ?? Kasir::where('id_user', $user->id)->first();
                if ($warungKasir) {
                    session(['id_warung' => $warungKasir->id]);
                    $idWarung = $warungKasir->id;
                }
            } else {
                $warungOwner = Kasir::where('id_user', $user->id)->first();
                if ($warungOwner) {
                    session(['id_warung' => $warungOwner->id]);
                    $idWarung = $warungOwner->id;
                }
            }
        }

        // Jika tetap belum memilih cabang
        if (!$idWarung) {
            if (in_array($user->role, ['admin', 'owner'], true)) {
                return redirect()
                    ->route('cabang.index')
                    ->with('error', 'Silakan pilih cabang terlebih dahulu.');
            }

            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Cabang kasir tidak ditemukan. Hubungi pemilik untuk menautkan cabang.');
        }

        // Pastikan cabang benar-benar ada
        $kasir = Kasir::where('id', $idWarung)
            ->with('kas')
            ->first();

        if (!$kasir) {
            session()->forget('id_warung');

            if (in_array($user->role, ['admin', 'owner'], true)) {
                return redirect()
                    ->route('cabang.index')
                    ->with('error', 'Cabang tidak ditemukan.');
            }

            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Cabang kasir tidak ditemukan.');
        }

        $isKasir = ($user->role === 'kasir');

        // Ambil Kas Tunai Fisik (status = cash) beserta detail pecahan
        $kasTunai = Kas::where('id_warung', $idWarung)
            ->where('status', 'cash')
            ->with(['detailKas' => function ($query) {
                $query->orderBy('pecahan', 'desc');
            }])
            ->first();

        // Ambil kas E-Wallet & Rekening Bank (status = bank atau ewallet)
        $saldoEwallet = Kas::where('id_warung', $idWarung)
            ->whereIn('status', ['bank', 'ewallet'])
            ->orderBy('is_primary', 'desc')
            ->orderBy('jenis_kas')
            ->get();

        // Total saldo seluruh jenis kas cabang aktif (Cash + E-Wallet)
        $totalSaldo = Kas::where('id_warung', $idWarung)
            ->sum('saldo');

        // Filter metrik berdasarkan role kasir vs owner/admin
        if ($isKasir) {
            // Khusus Kasir: Data shift transaksi kasir yang sedang login hari ini
            $trxQuery = Transaksi::where('id_user', $user->id)
                ->whereDate('created_at', today());

            $totalTransaksiHariIni = (clone $trxQuery)->count();
            $pemasukanHariIni = (clone $trxQuery)->where('jenis', 'masuk')->sum('total');
            $pengeluaranHariIni = (clone $trxQuery)->where('jenis', 'keluar')->sum('total');
            $totalItemHariIni = (clone $trxQuery)->whereNotNull('id_item_jualan')->count();
            $keuntunganHariIni = (clone $trxQuery)->sum('keuntungan');

            // Cek status laporan fisik kasir hari ini
            $laporanHarianHariIni = LaporanHarian::where('id_user', $user->id)
                ->where('id_warung', $idWarung)
                ->whereDate('tanggal', today())
                ->first();

            // Riwayat transaksi yang dilakukan oleh kasir ini
            $recentTransactions = Transaksi::where('id_user', $user->id)
                ->with(['kas.warung', 'itemJualan', 'kasSumber.warung'])
                ->latest()
                ->take(7)
                ->get();
        } else {
            // Untuk Owner / Admin: Data akumulasi seluruh cabang aktif hari ini
            $trxQuery = Transaksi::whereHas('kas', function ($query) use ($idWarung) {
                $query->where('id_warung', $idWarung);
            })->whereDate('created_at', today());

            $totalTransaksiHariIni = (clone $trxQuery)->count();
            $pemasukanHariIni = (clone $trxQuery)->where('jenis', 'masuk')->sum('total');
            $pengeluaranHariIni = (clone $trxQuery)->where('jenis', 'keluar')->sum('total');
            $totalItemHariIni = (clone $trxQuery)->whereNotNull('id_item_jualan')->count();
            $keuntunganHariIni = (clone $trxQuery)->sum('keuntungan');

            // Cek laporan harian cabang hari ini
            $laporanHarianHariIni = LaporanHarian::where('id_warung', $idWarung)
                ->whereDate('tanggal', today())
                ->latest()
                ->first();

            // Riwayat transaksi terbaru cabang aktif
            $recentTransactions = Transaksi::whereHas('kas', function ($query) use ($idWarung) {
                $query->where('id_warung', $idWarung);
            })
            ->with(['kas.warung', 'itemJualan', 'kasSumber.warung'])
            ->latest()
            ->take(7)
            ->get();
        }

        return view('dashboard', compact(
            'kasir',
            'kasTunai',
            'saldoEwallet',
            'totalSaldo',
            'pemasukanHariIni',
            'pengeluaranHariIni',
            'recentTransactions',
            'isKasir',
            'totalTransaksiHariIni',
            'totalItemHariIni',
            'keuntunganHariIni',
            'laporanHarianHariIni'
        ));
    }
}
