<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()
                ->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $startDate = $request->input(
            'start_date',
            now()->startOfMonth()->format('Y-m-d')
        );

        $endDate = $request->input(
            'end_date',
            now()->endOfMonth()->format('Y-m-d')
        );

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $query = Transaksi::whereHas('kas', function ($query) use ($idWarung) {
            $query->where('id_warung', $idWarung);
        })->whereBetween('created_at', [
            $startDateTime,
            $endDateTime
        ]);

        $totalPemasukan = (clone $query)
            ->where('jenis', 'masuk')
            ->sum('total');

        $totalPengeluaran = (clone $query)
            ->where('jenis', 'keluar')
            ->sum('total');

        $transaksiBulanan = (clone $query)
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('SUM(CASE WHEN jenis = "masuk" THEN total ELSE 0 END) as pemasukan'),
                DB::raw('SUM(CASE WHEN jenis = "keluar" THEN total ELSE 0 END) as pengeluaran')
            )
            ->groupBy(
                DB::raw('YEAR(created_at)'),
                DB::raw('MONTH(created_at)')
            )
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $labels = [];
        $pemasukanData = [];
        $pengeluaranData = [];

        foreach ($transaksiBulanan as $data) {
            $labels[] = Carbon::create(
                $data->tahun,
                $data->bulan,
                1
            )->translatedFormat('F Y');

            $pemasukanData[] = (float) $data->pemasukan;
            $pengeluaranData[] = (float) $data->pengeluaran;
        }

        return view('laporan.index', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'labels',
            'pemasukanData',
            'pengeluaranData',
            'startDate',
            'endDate'
        ));
    }
}
