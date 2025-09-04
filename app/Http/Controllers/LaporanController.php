<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan dengan filter.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $totalPemasukan = Transaksi::where('jenis', 'masuk')
                                   ->whereBetween('created_at', [$startDate, $endDate])
                                   ->sum('total');
        
        $totalPengeluaran = Transaksi::where('jenis', 'keluar')
                                     ->whereBetween('created_at', [$startDate, $endDate])
                                     ->sum('total');

        // Ambil data untuk grafik per bulan
        $transaksiBulanan = Transaksi::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('SUM(CASE WHEN jenis = "masuk" THEN total ELSE 0 END) as pemasukan'),
            DB::raw('SUM(CASE WHEN jenis = "keluar" THEN total ELSE 0 END) as pengeluaran')
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();

        $labels = [];
        $pemasukanData = [];
        $pengeluaranData = [];

        foreach ($transaksiBulanan as $data) {
            $labels[] = date('F', mktime(0, 0, 0, $data->bulan, 10));
            $pemasukanData[] = $data->pemasukan;
            $pengeluaranData[] = $data->pengeluaran;
        }

        return view('laporan.index', compact('totalPemasukan', 'totalPengeluaran', 'labels', 'pemasukanData', 'pengeluaranData', 'startDate', 'endDate'));
    }
}
