<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\Kas;
use App\Models\Transaksi;
use App\Models\LaporanHarian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OwnerMonitoringController extends Controller
{
    /**
     * Dashboard Pusat Kontrol & Monitoring Seluruh Cabang untuk Owner
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua cabang milik owner yang sedang login
        $warungs = Kasir::where('id_user', $user->id)
            ->with(['kas.detailKas', 'users'])
            ->orderBy('id')
            ->get();

        // Tentukan rentang tanggal periode
        $period = $request->get('period', 'today');
        $now = Carbon::now();

        switch ($period) {
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                $periodeLabel = 'Kemarin (' . $startDate->translatedFormat('d F Y') . ')';
                break;
            case '7days':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $periodeLabel = '7 Hari Terakhir (' . $startDate->format('d/m') . ' - ' . $endDate->format('d/m/Y') . ')';
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth()->startOfDay();
                $endDate = $now->copy()->endOfMonth()->endOfDay();
                $periodeLabel = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonth()->startOfMonth()->startOfDay();
                $endDate = $now->copy()->subMonth()->endOfMonth()->endOfDay();
                $periodeLabel = 'Bulan Lalu (' . $startDate->translatedFormat('F Y') . ')';
                break;
            case 'custom':
                $startInput = $request->get('start_date', $now->toDateString());
                $endInput = $request->get('end_date', $now->toDateString());
                $startDate = Carbon::parse($startInput)->startOfDay();
                $endDate = Carbon::parse($endInput)->endOfDay();
                $periodeLabel = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
                break;
            case 'today':
            default:
                $period = 'today';
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $periodeLabel = 'Hari Ini (' . $startDate->translatedFormat('l, d F Y') . ')';
                break;
        }

        $branchStats = [];
        $totalLikuiditas = 0;
        $totalKasTunai = 0;
        $totalKasBank = 0;
        $totalTransaksiKonsolidasi = 0;
        $totalMasukKonsolidasi = 0;
        $totalKeluarKonsolidasi = 0;
        $totalKeuntunganKonsolidasi = 0;
        $alerts = [];

        foreach ($warungs as $w) {
            // Saldo Kas
            $saldoTunai = $w->kas->where('status', 'cash')->sum('saldo');
            $saldoBank = $w->kas->whereIn('status', ['bank', 'ewallet'])->sum('saldo');
            $saldoTotal = $saldoTunai + $saldoBank;

            $totalLikuiditas += $saldoTotal;
            $totalKasTunai += $saldoTunai;
            $totalKasBank += $saldoBank;

            // Transaksi cabang pada periode terpilih
            $trxQuery = Transaksi::whereHas('kas', fn($q) => $q->where('id_warung', $w->id))
                ->whereBetween('created_at', [$startDate, $endDate]);

            $countTrx = (clone $trxQuery)->count();
            $sumMasuk = (clone $trxQuery)->where('jenis', 'masuk')->sum('total');
            $sumKeluar = (clone $trxQuery)->where('jenis', 'keluar')->sum('total');
            $sumKeuntungan = (clone $trxQuery)->sum('keuntungan');
            $omset = $sumMasuk + $sumKeluar;

            $totalTransaksiKonsolidasi += $countTrx;
            $totalMasukKonsolidasi += $sumMasuk;
            $totalKeluarKonsolidasi += $sumKeluar;
            $totalKeuntunganKonsolidasi += $sumKeuntungan;

            // Cek Laporan Harian Fisik Hari Ini
            $laporanHariIni = LaporanHarian::where('id_warung', $w->id)
                ->whereDate('tanggal', today())
                ->latest()
                ->first();

            // Peringatan Likuiditas Rendah (< 1.000.000)
            $isLowCash = ($saldoTunai < 1000000);
            if ($isLowCash) {
                $alerts[] = [
                    'type' => 'warning',
                    'warung_id' => $w->id,
                    'warung_nama' => $w->nama_warung,
                    'pesan' => "Saldo kas tunai laci menipis (Rp " . number_format($saldoTunai, 0, ',', '.') . "). Pertimbangkan kirim transfer likuiditas tunai.",
                ];
            }

            // Peringatan Status Laporan Harian
            if (!$laporanHariIni) {
                $alerts[] = [
                    'type' => 'info',
                    'warung_id' => $w->id,
                    'warung_nama' => $w->nama_warung,
                    'pesan' => "Kasir belum melakukan tutup buku kas fisik hari ini.",
                ];
            } elseif ($laporanHariIni->selisih != 0) {
                $selisihFmt = ($laporanHariIni->selisih > 0 ? '+' : '') . 'Rp ' . number_format($laporanHariIni->selisih, 0, ',', '.');
                $alerts[] = [
                    'type' => 'danger',
                    'warung_id' => $w->id,
                    'warung_nama' => $w->nama_warung,
                    'pesan' => "Terdapat selisih kas fisik vs sistem sebesar {$selisihFmt}. Catatan: " . ($laporanHariIni->catatan ?: 'Tidak ada catatan.'),
                ];
            }

            $branchStats[] = [
                'id' => $w->id,
                'nama' => $w->nama_warung,
                'bank_utama' => $w->bank_utama ?? 'BRI',
                'alamat' => $w->alamat ?? '-',
                'telepon' => $w->no_telepon ?? '-',
                'kas_tunai' => $saldoTunai,
                'kas_bank' => $saldoBank,
                'total_saldo' => $saldoTotal,
                'is_low_cash' => $isLowCash,
                'transaksi_count' => $countTrx,
                'masuk' => $sumMasuk,
                'keluar' => $sumKeluar,
                'keuntungan' => $sumKeuntungan,
                'omset' => $omset,
                'karyawan_count' => $w->users->count(),
                'karyawan_names' => $w->users->pluck('nama')->toArray(),
                'laporan_hari_ini' => $laporanHariIni,
                'is_active_session' => session('id_warung') == $w->id,
            ];
        }

        // Data Grafik Perbandingan Antar Cabang
        $chartLabels = array_column($branchStats, 'nama');
        $chartOmset = array_column($branchStats, 'omset');
        $chartLaba = array_column($branchStats, 'keuntungan');
        $chartSaldo = array_column($branchStats, 'total_saldo');

        return view('owner.monitoring', compact(
            'warungs',
            'branchStats',
            'period',
            'periodeLabel',
            'startDate',
            'endDate',
            'totalLikuiditas',
            'totalKasTunai',
            'totalKasBank',
            'totalTransaksiKonsolidasi',
            'totalMasukKonsolidasi',
            'totalKeluarKonsolidasi',
            'totalKeuntunganKonsolidasi',
            'alerts',
            'chartLabels',
            'chartOmset',
            'chartLaba',
            'chartSaldo'
        ));
    }

    /**
     * Beralih ke cabang tertentu dari monitoring dan langsung buka menu tujuan
     */
    public function switchBranch(Request $request, $id)
    {
        $warung = Kasir::where('id', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        session(['id_warung' => $warung->id]);

        $target = $request->get('to', 'transaksi.menu');

        switch ($target) {
            case 'transaksi':
                return redirect()->route('transaksi.index')->with('success', 'Beralih ke cabang: ' . $warung->nama_warung);
            case 'dashboard':
                return redirect()->route('dashboard')->with('success', 'Beralih ke cabang: ' . $warung->nama_warung);
            case 'laporan':
                return redirect()->route('laporan-harian.index')->with('success', 'Beralih ke cabang: ' . $warung->nama_warung);
            case 'transfer':
                return redirect()->route('transfer-kas.create')->with('success', 'Beralih ke cabang: ' . $warung->nama_warung);
            default:
                return redirect()->route('transaksi.menu')->with('success', 'Beralih ke cabang: ' . $warung->nama_warung);
        }
    }

    /**
     * Export Rekapitulasi Monitoring Konsolidasi Seluruh Cabang ke PDF
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $warungs = Kasir::where('id_user', $user->id)
            ->with(['kas', 'users'])
            ->orderBy('id')
            ->get();

        $period = $request->get('period', 'today');
        $now = Carbon::now();

        switch ($period) {
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate = $now->copy()->subDay()->endOfDay();
                $periodeLabel = 'Kemarin (' . $startDate->translatedFormat('d F Y') . ')';
                break;
            case '7days':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $periodeLabel = '7 Hari Terakhir (' . $startDate->format('d/m') . ' - ' . $endDate->format('d/m/Y') . ')';
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth()->startOfDay();
                $endDate = $now->copy()->endOfMonth()->endOfDay();
                $periodeLabel = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date', $now->toDateString()))->startOfDay();
                $endDate = Carbon::parse($request->get('end_date', $now->toDateString()))->endOfDay();
                $periodeLabel = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
                break;
            case 'today':
            default:
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $periodeLabel = 'Hari Ini (' . $startDate->translatedFormat('d F Y') . ')';
                break;
        }

        $branchStats = [];
        $totalLikuiditas = 0;
        $totalKasTunai = 0;
        $totalKasBank = 0;
        $totalTransaksi = 0;
        $totalMasuk = 0;
        $totalKeluar = 0;
        $totalLaba = 0;

        foreach ($warungs as $w) {
            $saldoTunai = $w->kas->where('status', 'cash')->sum('saldo');
            $saldoBank = $w->kas->whereIn('status', ['bank', 'ewallet'])->sum('saldo');
            $saldoTotal = $saldoTunai + $saldoBank;

            $totalLikuiditas += $saldoTotal;
            $totalKasTunai += $saldoTunai;
            $totalKasBank += $saldoBank;

            $trxQuery = Transaksi::whereHas('kas', fn($q) => $q->where('id_warung', $w->id))
                ->whereBetween('created_at', [$startDate, $endDate]);

            $cTrx = (clone $trxQuery)->count();
            $sMasuk = (clone $trxQuery)->where('jenis', 'masuk')->sum('total');
            $sKeluar = (clone $trxQuery)->where('jenis', 'keluar')->sum('total');
            $sLaba = (clone $trxQuery)->sum('keuntungan');

            $totalTransaksi += $cTrx;
            $totalMasuk += $sMasuk;
            $totalKeluar += $sKeluar;
            $totalLaba += $sLaba;

            $laporan = LaporanHarian::where('id_warung', $w->id)
                ->whereDate('tanggal', today())
                ->latest()
                ->first();

            $branchStats[] = [
                'nama' => $w->nama_warung,
                'bank_utama' => $w->bank_utama ?? 'BRI',
                'kas_tunai' => $saldoTunai,
                'kas_bank' => $saldoBank,
                'total_saldo' => $saldoTotal,
                'transaksi_count' => $cTrx,
                'masuk' => $sMasuk,
                'keluar' => $sKeluar,
                'laba' => $sLaba,
                'omset' => $sMasuk + $sKeluar,
                'status_laporan' => $laporan ? ($laporan->selisih == 0 ? 'Sesuai' : 'Selisih Rp ' . number_format($laporan->selisih, 0, ',', '.')) : 'Belum Lapor',
            ];
        }

        $pdf = Pdf::loadView('owner.monitoring_pdf', compact(
            'user',
            'periodeLabel',
            'branchStats',
            'totalLikuiditas',
            'totalKasTunai',
            'totalKasBank',
            'totalTransaksi',
            'totalMasuk',
            'totalKeluar',
            'totalLaba'
        ));

        $pdf->setPaper('a4', 'landscape');

        $filename = 'rekap-konsolidasi-cabang-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }
}
