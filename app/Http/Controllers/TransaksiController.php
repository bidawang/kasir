<?php

namespace App\Http\Controllers;

use App\Models\BiayaAdmin;
use App\Models\DetailKas;
use App\Models\DetailTransaksi;
use App\Models\ItemJualan;
use App\Models\Kas;
use App\Models\Kasir;
use App\Models\KategoriTransaksi;
use App\Models\RiwayatLaporanPdf;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function menu()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $warung = Kasir::find($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';
        $kategoriList = KategoriTransaksi::where('is_active', true)->orderBy('urutan')->get();

        return view('transaksi.menu', compact('warung', 'bankUtama', 'kategoriList'));
    }

    public function index(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $warung = Kasir::find($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $branchQuery = Transaksi::whereHas('kas', function ($q) use ($idWarung) {
            $q->where('id_warung', $idWarung);
        });

        if (auth()->user()->role === 'kasir') {
            $branchQuery->where('id_user', auth()->id());
        }

        if ($request->filled('id_kategori')) {
            $branchQuery->where('id_kategori', $request->id_kategori);
        }

        if ($request->filled(['start_date', 'end_date'])) {
            $dateRange = [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ];
            $branchQuery->whereBetween('created_at', $dateRange);
        }

        $summary = null;
        $chartRawDates = $chartLabels = $chartSetor = $chartTarik = $chartProfit = collect();

        if (in_array(auth()->user()->role, ['admin', 'owner'], true)) {
            $stats = (clone $branchQuery)->selectRaw("
                COUNT(*) as total_transaksi,
                SUM(CASE WHEN jenis = 'masuk' THEN total ELSE 0 END) as total_setor,
                SUM(CASE WHEN jenis = 'keluar' THEN total ELSE 0 END) as total_tarik,
                SUM(CASE 
                    WHEN keuntungan != 0 THEN keuntungan 
                    WHEN is_profit = 1 THEN total 
                    ELSE (biaya_admin - biaya_admin_bank) 
                END) as total_keuntungan,
                SUM(biaya_admin) as total_admin_toko,
                SUM(biaya_admin_bank) as total_admin_bank
            ")->first();

            $summary = (object) [
                'total_transaksi' => (int) ($stats->total_transaksi ?? 0),
                'total_setor' => (int) ($stats->total_setor ?? 0),
                'total_tarik' => (int) ($stats->total_tarik ?? 0),
                'total_keuntungan' => (int) ($stats->total_keuntungan ?? 0),
                'total_admin_toko' => (int) ($stats->total_admin_toko ?? 0),
                'total_admin_bank' => (int) ($stats->total_admin_bank ?? 0),
            ];

            $chartData = (clone $branchQuery)
                ->selectRaw("
                    DATE(created_at) as transaction_date,
                    SUM(CASE WHEN jenis = 'masuk' THEN total ELSE 0 END) as setor,
                    SUM(CASE WHEN jenis = 'keluar' THEN total ELSE 0 END) as tarik,
                    SUM(CASE 
                        WHEN keuntungan != 0 THEN keuntungan 
                        WHEN is_profit = 1 THEN total 
                        ELSE (biaya_admin - biaya_admin_bank) 
                    END) as profit
                ")
                ->groupBy('transaction_date')
                ->orderBy('transaction_date', 'ASC')
                ->get();

            $chartRawDates = $chartData->pluck('transaction_date');
            $chartLabels = $chartData->pluck('transaction_date')->map(fn ($date) => date('d M', strtotime($date)));
            $chartSetor = $chartData->pluck('setor');
            $chartTarik = $chartData->pluck('tarik');
            $chartProfit = $chartData->pluck('profit');
        }

        $transaksi = (clone $branchQuery)
            ->with(['kas.warung', 'kasSumber', 'detailTransaksi', 'itemJualan', 'kategoriTransaksi', 'user'])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $kategoriList = KategoriTransaksi::where('is_active', true)->orderBy('urutan')->get();

        $arsipPdfList = RiwayatLaporanPdf::where('id_warung', $idWarung)
            ->with(['user', 'kategoriTransaksi'])
            ->latest('id')
            ->paginate(10, ['*'], 'arsip_page');

        return view('transaksi.index', compact(
            'transaksi',
            'summary',
            'kategoriList',
            'bankUtama',
            'chartRawDates',
            'chartLabels',
            'chartSetor',
            'chartTarik',
            'chartProfit',
            'arsipPdfList'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $warung = Kasir::with('user')->findOrFail($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $idKategori = $request->input('id_kategori');
        $simpan = $request->boolean('simpan');
        $catatan = $request->input('catatan');

        $query = Transaksi::whereHas('kas', function ($q) use ($idWarung) {
            $q->where('id_warung', $idWarung);
        });

        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if (!empty($idKategori)) {
            $query->where('id_kategori', $idKategori);
        }

        $summary = (clone $query)->selectRaw("
            COALESCE(SUM(nominal), 0) as total_volume,
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN total ELSE 0 END), 0) as total_setor,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN total ELSE 0 END), 0) as total_tarik,
            COALESCE(SUM(biaya_admin), 0) as total_admin_toko,
            COALESCE(SUM(biaya_admin_bank), 0) as total_admin_bank,
            COALESCE(SUM(CASE 
                WHEN keuntungan != 0 THEN keuntungan 
                WHEN is_profit = 1 THEN total 
                ELSE (biaya_admin - biaya_admin_bank) 
            END), 0) as total_keuntungan,
            COUNT(id) as total_transaksi
        ")->first();

        $transaksi = (clone $query)
            ->with(['kategoriTransaksi', 'itemJualan', 'user', 'kas'])
            ->latest('id')
            ->get();

        $kategori = !empty($idKategori) ? KategoriTransaksi::find($idKategori) : null;
        $kategoriLabel = $kategori ? $kategori->getNamaDinamis($bankUtama) : 'Semua Kategori';

        $nomorLaporan = 'RPT-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Simpan data/parameter laporan ke database jika dipilih user (tanpa simpan file fisik di disk)
        if ($simpan) {
            RiwayatLaporanPdf::create([
                'id_warung'        => $idWarung,
                'id_user'          => auth()->id(),
                'nomor_laporan'    => $nomorLaporan,
                'judul'            => 'Laporan Transaksi ' . ($warung->nama_warung ?? 'Cabang'),
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'id_kategori'      => $idKategori ?: null,
                'kategori_label'   => $kategoriLabel,
                'total_masuk'      => (float) $summary->total_setor,
                'total_keluar'     => (float) $summary->total_tarik,
                'total_keuntungan' => (float) $summary->total_keuntungan,
                'total_admin_toko' => (float) $summary->total_admin_toko,
                'total_admin_bank' => (float) $summary->total_admin_bank,
                'total_transaksi'  => (int) $summary->total_transaksi,
                'catatan'          => $catatan,
                'filter_snapshot'  => [
                    'start_date'  => $startDate,
                    'end_date'    => $endDate,
                    'id_kategori' => $idKategori,
                ],
            ]);
        }

        $pdf = Pdf::loadView('transaksi.pdf', compact(
            'warung',
            'bankUtama',
            'startDate',
            'endDate',
            'kategoriLabel',
            'nomorLaporan',
            'summary',
            'transaksi'
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan_Transaksi_' . Str::slug($warung->nama_warung ?? 'Cabang') . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadPdfArsip($id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $arsip = RiwayatLaporanPdf::where('id_warung', $idWarung)->findOrFail($id);
        $warung = Kasir::with('user')->findOrFail($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $startDate = $arsip->start_date ? $arsip->start_date->toDateString() : null;
        $endDate = $arsip->end_date ? $arsip->end_date->toDateString() : null;
        $idKategori = $arsip->id_kategori;
        $kategoriLabel = $arsip->kategori_label ?? 'Semua Kategori';
        $nomorLaporan = $arsip->nomor_laporan;

        $query = Transaksi::whereHas('kas', function ($q) use ($idWarung) {
            $q->where('id_warung', $idWarung);
        });

        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if (!empty($idKategori)) {
            $query->where('id_kategori', $idKategori);
        }

        $summary = (clone $query)->selectRaw("
            COALESCE(SUM(nominal), 0) as total_volume,
            COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN total ELSE 0 END), 0) as total_setor,
            COALESCE(SUM(CASE WHEN jenis = 'keluar' THEN total ELSE 0 END), 0) as total_tarik,
            COALESCE(SUM(biaya_admin), 0) as total_admin_toko,
            COALESCE(SUM(biaya_admin_bank), 0) as total_admin_bank,
            COALESCE(SUM(CASE 
                WHEN keuntungan != 0 THEN keuntungan 
                WHEN is_profit = 1 THEN total 
                ELSE (biaya_admin - biaya_admin_bank) 
            END), 0) as total_keuntungan,
            COUNT(id) as total_transaksi
        ")->first();

        $transaksi = (clone $query)
            ->with(['kategoriTransaksi', 'itemJualan', 'user', 'kas'])
            ->latest('id')
            ->get();

        $pdf = Pdf::loadView('transaksi.pdf', compact(
            'warung',
            'bankUtama',
            'startDate',
            'endDate',
            'kategoriLabel',
            'nomorLaporan',
            'summary',
            'transaksi'
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan_Transaksi_' . Str::slug($nomorLaporan) . '.pdf';

        return $pdf->download($filename);
    }

    public function destroyPdfArsip($id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $arsip = RiwayatLaporanPdf::where('id_warung', $idWarung)->findOrFail($id);
        $arsip->delete();

        return redirect()->route('transaksi.index', ['view' => 'arsip'])
            ->with('success', 'Riwayat laporan PDF berhasil dihapus.');
    }

    public function setor(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $warung = Kasir::find($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $kasAsal = Kas::where('id_warung', $idWarung)
            ->where('status', 'cash')
            ->first();

        $kasTujuanList = Kas::where('id_warung', $idWarung)
            ->whereIn('status', ['bank', 'ewallet'])
            ->orderBy('is_primary', 'desc')
            ->orderBy('jenis_kas', 'asc')
            ->get();

        $kategoriList = KategoriTransaksi::where('jenis_aliran', 'masuk')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        $selected = $request->query('kategori');
        $kategori = null;
        if ($selected) {
            $kategori = is_numeric($selected)
                ? $kategoriList->firstWhere('id', (int) $selected)
                : $kategoriList->firstWhere('kode', $selected);
        }
        if (!$kategori) {
            $kategori = $kategoriList->first();
        }

        $biayaAdminList = BiayaAdmin::where('id_warung', $idWarung)
            ->get(['id_kategori', 'id_kas', 'range_awal', 'range_akhir', 'biaya_admin', 'biaya_admin_bank']);

        return view('transaksi.setor', compact(
            'warung',
            'bankUtama',
            'kasAsal',
            'kasTujuanList',
            'kategoriList',
            'kategori',
            'biayaAdminList'
        ));
    }

    public function tarik(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $warung = Kasir::find($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $kasAsal = Kas::where('id_warung', $idWarung)
            ->where('status', 'cash')
            ->first();

        $kasSumberList = Kas::where('id_warung', $idWarung)
            ->whereIn('status', ['bank', 'ewallet'])
            ->orderBy('is_primary', 'desc')
            ->orderBy('jenis_kas', 'asc')
            ->get();

        $kategoriList = KategoriTransaksi::where('jenis_aliran', 'keluar')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        $selected = $request->query('kategori');
        $kategori = null;
        if ($selected) {
            $kategori = is_numeric($selected)
                ? $kategoriList->firstWhere('id', (int) $selected)
                : $kategoriList->firstWhere('kode', $selected);
        }
        if (!$kategori) {
            $kategori = $kategoriList->first();
        }

        $biayaAdminList = BiayaAdmin::where('id_warung', $idWarung)
            ->get(['id_kategori', 'id_kas', 'range_awal', 'range_akhir', 'biaya_admin', 'biaya_admin_bank']);

        return view('transaksi.tarik', compact(
            'warung',
            'bankUtama',
            'kasAsal',
            'kasSumberList',
            'kategoriList',
            'kategori',
            'biayaAdminList'
        ));
    }

    public function store(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        // Sanitize numeric inputs
        foreach (['nominal', 'total', 'biaya_admin_manual', 'biaya_admin_toko', 'biaya_admin_bank'] as $field) {
            if ($request->filled($field)) {
                $cleaned = preg_replace('/[^0-9]/', '', (string) $request->$field);
                $request->merge([$field => $cleaned !== '' ? (int) $cleaned : 0]);
            }
        }

        $request->validate([
            'id_kategori'   => 'required|integer|exists:kategori_transaksi,id',
            'id_kas'        => 'required|integer|exists:kas,id',
            'id_kas_tujuan' => 'required|integer|exists:kas,id',
            'nominal'       => 'required|integer|min:1',
            'biaya_admin_toko' => 'nullable|integer|min:0',
            'biaya_admin_bank' => 'nullable|integer|min:0',
            'is_manual_fee' => 'nullable|boolean',
            'keterangan'    => 'nullable|string|max:255',
        ]);

        $kategori = KategoriTransaksi::findOrFail($request->id_kategori);
        $nominalTransaksi = (int) $request->nominal;
        $jenis = $kategori->jenis_aliran; // 'masuk' atau 'keluar'

        return DB::transaction(function () use ($request, $idWarung, $kategori, $nominalTransaksi, $jenis) {
            $kasAsal = Kas::where('id', $request->id_kas)
                ->where('id_warung', $idWarung)
                ->where('status', 'cash')
                ->lockForUpdate()
                ->firstOrFail();

            $kasTujuan = Kas::where('id', $request->id_kas_tujuan)
                ->where('id_warung', $idWarung)
                ->whereIn('status', ['bank', 'ewallet'])
                ->lockForUpdate()
                ->firstOrFail();

            // Cari biaya admin yang cocok dengan kategori & nominal
            $feeRange = BiayaAdmin::where('id_warung', $idWarung)
                ->where('id_kategori', $kategori->id)
                ->where(function ($query) use ($kasTujuan) {
                    $query->where('id_kas', $kasTujuan->id)->orWhereNull('id_kas');
                })
                ->where('range_awal', '<=', $nominalTransaksi)
                ->where('range_akhir', '>=', $nominalTransaksi)
                ->orderByRaw('CASE WHEN id_kas IS NULL THEN 1 ELSE 0 END')
                ->orderBy('range_awal')
                ->first();

            // Prioritaskan input manual jika dipilih kasir atau jika belum ada aturan di tabel
            if ($request->boolean('is_manual_fee')) {
                $biayaAdminToko = (int) ($request->biaya_admin_toko ?? 0);
                $biayaAdminBank = (int) ($request->biaya_admin_bank ?? 0);
            } elseif ($feeRange) {
                $biayaAdminToko = (int) $feeRange->biaya_admin;
                $biayaAdminBank = (int) $feeRange->biaya_admin_bank;
            } else {
                $biayaAdminToko = (int) ($request->biaya_admin_toko ?? 0);
                $biayaAdminBank = (int) ($request->biaya_admin_bank ?? 0);
            }
            $keuntungan = $biayaAdminToko - $biayaAdminBank;

            // ==========================================
            // SKENARIO A: KAS MASUK (Setor / Transfer / Top Up / VA / PPOB)
            // ==========================================
            if ($jenis === 'masuk') {
                $totalFisikDiterima = $nominalTransaksi + $biayaAdminToko;

                // Verifikasi saldo rekening/ewallet agen cukup untuk transfer ke nasabah
                $totalPotongDigital = $nominalTransaksi + $biayaAdminBank;
                if ($kasTujuan->saldo < $totalPotongDigital) {
                    throw ValidationException::withMessages([
                        'id_kas_tujuan' => "Saldo {$kasTujuan->jenis_kas} tidak mencukupi untuk transfer Rp " . number_format($totalPotongDigital, 0, ',', '.') . " (termasuk biaya bank).",
                    ]);
                }

                // Mutasi Kas
                $kasAsal->saldo += $totalFisikDiterima;
                $kasTujuan->saldo -= $totalPotongDigital;

                $totalTransaksi = $totalFisikDiterima;
                $kasMasuk = $totalFisikDiterima;
                $kasKeluar = 0;
            }
            // ==========================================
            // SKENARIO B: KAS KELUAR (Tarik Tunai Bank / E-Wallet)
            // ==========================================
            else {
                $totalFisikDiserahkan = $nominalTransaksi;

                // Verifikasi kas tunai kasir cukup untuk diserahkan ke nasabah
                if ($kasAsal->saldo < $totalFisikDiserahkan) {
                    throw ValidationException::withMessages([
                        'nominal' => "Uang fisik di kas tunai ({$kasAsal->jenis_kas}) tidak mencukupi penarikan Rp " . number_format($totalFisikDiserahkan, 0, ',', '.') . ".",
                    ]);
                }

                // Mutasi Kas: Fisik keluar, Digital agen bertambah dari debit nasabah
                $kasAsal->saldo -= $totalFisikDiserahkan;
                // Akun digital agen menerima debet penarikan + admin toko dikurangi admin bank
                $kasTujuan->saldo += ($nominalTransaksi + $biayaAdminToko - $biayaAdminBank);

                $totalTransaksi = $totalFisikDiserahkan;
                $kasMasuk = 0;
                $kasKeluar = $totalFisikDiserahkan;
            }

            $kasAsal->save();
            $kasTujuan->save();

            // Simpan Transaksi
            Transaksi::create([
                'id_user'           => auth()->id(),
                'id_kas_warung'     => $kasAsal->id,
                'id_kas_sumber'     => $kasTujuan->id,
                'id_kategori'       => $kategori->id,
                'jenis'             => $jenis,
                'kategori'          => 'transaksi_customer',
                'metode_transaksi'  => $kategori->metode_transaksi,
                'metode_pembayaran' => $kasTujuan->status,
                'nominal'           => $nominalTransaksi,
                'biaya_admin'       => $biayaAdminToko,
                'biaya_admin_bank'  => $biayaAdminBank,
                'keuntungan'        => $keuntungan,
                'kas_masuk'         => $kasMasuk,
                'kas_keluar'        => $kasKeluar,
                'total'             => $totalTransaksi,
                'tanggal'           => now()->toDateString(),
                'keterangan'        => $request->keterangan ?: ($kategori->nama_kategori . ' - Rp ' . number_format($nominalTransaksi, 0, ',', '.')),
            ]);

            $pesan = $jenis === 'masuk'
                ? "Transaksi {$kategori->nama_kategori} berhasil dicatat! Kas tunai masuk Rp " . number_format($totalTransaksi, 0, ',', '.') . " (Laba: Rp " . number_format($keuntungan, 0, ',', '.') . ")"
                : "Penarikan {$kategori->nama_kategori} berhasil dicatat! Kas fisik keluar Rp " . number_format($totalTransaksi, 0, ',', '.') . " (Laba: Rp " . number_format($keuntungan, 0, ',', '.') . ")";

            return redirect()->route('transaksi.index')->with('success', $pesan);
        });
    }

    public function createPenjualanItem()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $items = ItemJualan::where('id_warung', $idWarung)->orderBy('nama_item')->get();
        $kasList = Kas::where('id_warung', $idWarung)->where('status', 'cash')->orderBy('jenis_kas')->get();
        $walletList = Kas::where('id_warung', $idWarung)->where('status', 'ewallet')->orderBy('jenis_kas')->get();
        $biayaAdminList = BiayaAdmin::where('id_warung', $idWarung)
            ->get(['id_kategori', 'id_kas', 'range_awal', 'range_akhir', 'biaya_admin', 'biaya_admin_bank']);

        return view('transaksi.penjualan_item', compact('items', 'kasList', 'walletList', 'biayaAdminList'));
    }

    public function storePenjualanItem(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $validated = $request->validate([
            'id_item_jualan' => 'required|integer|exists:item_jualan,id',
            'id_kas_sumber' => 'nullable|integer|exists:kas,id',
            'harga_item' => 'nullable|integer|min:1',
            'biaya_admin_manual' => 'nullable|integer|min:0',
            'denominations_json' => 'required|json',
            'change_denominations_json' => 'nullable|json',
            'change_total' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $idWarung) {
            $item = ItemJualan::where('id_warung', $idWarung)
                ->lockForUpdate()
                ->findOrFail($validated['id_item_jualan']);
            $kas = Kas::where('id_warung', $idWarung)
                ->where('status', 'cash')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();
            if (!$kas) {
                throw ValidationException::withMessages([
                    'kas_tunai' => 'Cabang ini belum memiliki kas tunai untuk menerima pembayaran.',
                ]);
            }

            $isPhysical = $item->jenis === 'fisik';
            $hargaItem = $isPhysical ? (int) $item->harga_jual : (int) ($validated['harga_item'] ?? 0);
            if ($hargaItem <= 0) {
                throw ValidationException::withMessages([
                    'harga_item' => $isPhysical ? 'Harga item fisik harus lebih dari 0.' : 'Nominal item virtual harus lebih dari 0.',
                ]);
            }

            $wallet = null;
            if (!$isPhysical) {
                if (empty($validated['id_kas_sumber'])) {
                    throw ValidationException::withMessages([
                        'id_kas_sumber' => 'Pilih wallet sumber untuk penjualan virtual.',
                    ]);
                }

                $wallet = Kas::where('id_warung', $idWarung)
                    ->where('status', 'ewallet')
                    ->lockForUpdate()
                    ->findOrFail($validated['id_kas_sumber']);

                if ($wallet->saldo < $hargaItem) {
                    throw ValidationException::withMessages([
                        'id_kas_sumber' => 'Saldo wallet tidak mencukupi untuk nominal item ini.',
                    ]);
                }
            } elseif ((int) ($item->jumlah ?? 0) < 1) {
                throw ValidationException::withMessages([
                    'id_item_jualan' => 'Stok item fisik habis.',
                ]);
            }

            $biaya = (int) ($validated['biaya_admin_manual'] ?? 0);
            $total = $hargaItem + $biaya;
            $received = json_decode($validated['denominations_json'], true);
            $change = json_decode($validated['change_denominations_json'] ?? '[]', true);
            $allowedDenominations = DetailKas::DENOMINATIONS;
            $totalReceived = $this->denominationTotal($received, $allowedDenominations, 'denominations_json');
            $totalChange = $this->denominationTotal($change, $allowedDenominations, 'change_denominations_json');

            if ((int) $validated['change_total'] !== $totalChange || $totalReceived - $totalChange !== $total) {
                throw ValidationException::withMessages([
                    'denominations_json' => 'Rincian total uang dan kembalian tidak sesuai dengan total transaksi Rp ' . number_format($total, 0, ',', '.') . '.',
                ]);
            }

            $transaksi = Transaksi::create([
                'id_user' => auth()->id(),
                'id_kas_warung' => $kas->id,
                'id_kas_sumber' => $wallet?->id,
                'id_item_jualan' => $item->id,
                'jenis' => 'masuk',
                'kategori' => 'penjualan_item',
                'nominal' => $hargaItem,
                'total' => $total,
                'kas_masuk' => $total,
                'kas_keluar' => 0,
                'biaya_admin' => $biaya,
                'biaya_admin_bank' => 0,
                'keuntungan' => $biaya + ($isPhysical ? ($hargaItem - ($item->harga_beli ?? 0)) : 0),
                'nama_item' => $item->nama_item,
                'harga_item' => $hargaItem,
                'metode_pembayaran' => 'cash',
                'tanggal' => now()->toDateString(),
                'keterangan' => ($validated['keterangan'] ?? null) ?: $item->id . '-Penjualan item: ' . $item->nama_item,
            ]);

            foreach ($received as $denomination) {
                $pecahan = (int) $denomination['pecahan'];
                $jumlah = (int) $denomination['jumlah'];
                if ($jumlah < 1) continue;

                DetailTransaksi::create([
                    'id_transaksi_kas' => $transaksi->id,
                    'pecahan' => (string) $pecahan,
                    'jumlah' => $jumlah,
                ]);
                DetailKas::firstOrCreate(
                    ['id_kas_warung' => $kas->id, 'pecahan' => (string) $pecahan],
                    ['jumlah' => 0]
                )->increment('jumlah', $jumlah);
            }

            foreach ($change as $denomination) {
                $pecahan = (int) $denomination['pecahan'];
                $jumlah = (int) $denomination['jumlah'];
                if ($jumlah < 1) continue;

                $detailKas = DetailKas::where('id_kas_warung', $kas->id)
                    ->where('pecahan', (string) $pecahan)
                    ->lockForUpdate()
                    ->first();

                if (!$detailKas || $detailKas->jumlah < $jumlah) {
                    throw ValidationException::withMessages([
                        'change_denominations_json' => 'Stok kembalian pecahan Rp ' . number_format($pecahan, 0, ',', '.') . ' tidak mencukupi.',
                    ]);
                }

                $detailKas->decrement('jumlah', $jumlah);
            }

            if ($isPhysical) {
                $item->decrement('jumlah');
            } else {
                $wallet->decrement('saldo', $hargaItem);
            }

            $kas->saldo += $total;
            $kas->save();

            return redirect()->route('transaksi.index')
                ->with('success', 'Penjualan item berhasil dicatat sebesar Rp ' . number_format($total, 0, ',', '.') . '.');
        });
    }

    public function kasMasuk()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $kasList = Kas::where('id_warung', $idWarung)
            ->with(['detailKas' => fn ($query) => $query->orderBy('pecahan', 'desc')])
            ->get();

        foreach ($kasList as $kas) {
            if ($kas->status === 'cash' && $kas->relationLoaded('detailKas')) {
                $kas->setRelation('detail_kas', $kas->detailKas);
            }
        }

        $jenisTransaksi = 'masuk';
        return view('transaksi.createtransaksibrilink', compact('kasList', 'jenisTransaksi'));
    }

    public function kasKeluar()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $kasList = Kas::where('id_warung', $idWarung)
            ->with(['detailKas' => fn ($query) => $query->orderBy('pecahan', 'desc')])
            ->get();

        foreach ($kasList as $kas) {
            if ($kas->status === 'cash' && $kas->relationLoaded('detailKas')) {
                $kas->setRelation('detail_kas', $kas->detailKas);
            }
        }

        $jenisTransaksi = 'keluar';
        return view('transaksi.createtransaksibrilink', compact('kasList', 'jenisTransaksi'));
    }

    public function storeKas(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        // Sanitize total
        if ($request->filled('total')) {
            $cleaned = preg_replace('/[^0-9]/', '', (string) $request->total);
            $request->merge(['total' => $cleaned !== '' ? (int) $cleaned : 0]);
        }

        $validated = $request->validate([
            'jenis'              => 'required|in:masuk,keluar',
            'is_profit'          => 'sometimes|boolean',
            'id_kas'             => 'required|integer|exists:kas,id',
            'total'              => 'required|integer|min:1',
            'keterangan'         => 'required|string|max:255',
            'denominations_json' => 'nullable|json',
        ]);

        $denominations = json_decode($validated['denominations_json'] ?? '[]', true);
        if (!empty($denominations)) {
            Validator::make(
                ['denominations' => $denominations],
                [
                    'denominations' => 'required|array',
                    'denominations.*.pecahan' => ['required', 'integer', Rule::in(DetailKas::DENOMINATIONS)],
                    'denominations.*.jumlah' => 'required|integer|min:0',
                ]
            )->validate();
        }

        return DB::transaction(function () use ($request, $validated, $idWarung, $denominations) {
            $kas = Kas::where('id', $request->id_kas)
                ->where('id_warung', $idWarung)
                ->lockForUpdate()
                ->firstOrFail();

            if ($kas->status === 'cash' && !empty($denominations)) {
                $totalPecahan = collect($denominations)->sum(
                    fn ($denom) => (int) $denom['pecahan'] * (int) $denom['jumlah']
                );

                if ($totalPecahan !== (int) $validated['total']) {
                    throw ValidationException::withMessages([
                        'total' => 'Nominal harus sama dengan total rincian pecahan uang fisik.',
                    ]);
                }
            }

            if ($validated['jenis'] === 'keluar' && $kas->saldo < $validated['total']) {
                throw new \Exception("Saldo kas {$kas->jenis_kas} tidak mencukupi untuk pengeluaran sebesar Rp " . number_format($validated['total'], 0, ',', '.'));
            }

            $isProfit = $validated['jenis'] === 'masuk' && $request->boolean('is_profit');

            $transaksi = Transaksi::create([
                'id_user'           => auth()->id(),
                'jenis'             => $validated['jenis'],
                'is_profit'         => $isProfit,
                'kategori'          => 'transaksi_brilink',
                'id_kas_warung'     => $kas->id,
                'nominal'           => $validated['total'],
                'total'             => $validated['total'],
                'kas_masuk'         => $validated['jenis'] === 'masuk' ? $validated['total'] : 0,
                'kas_keluar'        => $validated['jenis'] === 'keluar' ? $validated['total'] : 0,
                'biaya_admin'       => 0,
                'biaya_admin_bank'  => 0,
                'keuntungan'        => $isProfit ? $validated['total'] : 0,
                'metode_pembayaran' => $kas->status === 'cash' ? 'cash' : 'ewallet',
                'tanggal'           => now()->toDateString(),
                'keterangan'        => $validated['keterangan'],
            ]);

            if ($kas->status === 'cash' && !empty($denominations)) {
                foreach ($denominations as $denom) {
                    $pecahan = (int) ($denom['pecahan'] ?? 0);
                    $jumlah  = (int) ($denom['jumlah'] ?? 0);
                    if ($pecahan <= 0 || $jumlah <= 0) continue;

                    DetailTransaksi::create([
                        'id_transaksi_kas' => $transaksi->id,
                        'pecahan'          => (string) $pecahan,
                        'jumlah'           => $jumlah,
                    ]);

                    $detailKas = DetailKas::firstOrCreate(
                        ['id_kas_warung' => $kas->id, 'pecahan' => (string) $pecahan],
                        ['jumlah' => 0]
                    );

                    if ($validated['jenis'] === 'masuk') {
                        $detailKas->increment('jumlah', $jumlah);
                    } else {
                        if ($detailKas->jumlah < $jumlah) {
                            throw new \Exception('Stok uang fisik pecahan Rp ' . number_format($pecahan, 0, ',', '.') . ' tidak cukup!');
                        }
                        $detailKas->decrement('jumlah', $jumlah);
                    }
                }
            }

            if ($validated['jenis'] === 'masuk') {
                $kas->saldo += $validated['total'];
            } else {
                $kas->saldo -= $validated['total'];
            }
            $kas->save();

            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi kas berhasil dicatat.');
        });
    }

    private function denominationTotal(mixed $denominations, array $allowed, string $field): int
    {
        if (!is_array($denominations) || !array_is_list($denominations)) {
            throw ValidationException::withMessages([$field => 'Rincian pecahan uang tidak valid.']);
        }

        $total = 0;
        foreach ($denominations as $denomination) {
            if (!is_array($denomination)
                || !isset($denomination['pecahan'], $denomination['jumlah'])
                || !is_numeric($denomination['pecahan'])
                || !is_numeric($denomination['jumlah'])) {
                throw ValidationException::withMessages([$field => 'Rincian pecahan uang tidak valid.']);
            }

            $pecahan = (int) $denomination['pecahan'];
            $jumlah = (int) $denomination['jumlah'];
            if (!in_array($pecahan, $allowed, true) || $jumlah < 0 || (string) $jumlah !== (string) $denomination['jumlah']) {
                throw ValidationException::withMessages([$field => 'Rincian pecahan uang tidak valid.']);
            }

            $total += $pecahan * $jumlah;
        }

        return $total;
    }

    private function activeWarungId(): ?int
    {
        return Kasir::whereKey(session('id_warung'))
            ->when(auth()->user()->role === 'kasir', function ($query) {
                $query->where(function ($query) {
                    $query->where('id_user', auth()->id())
                        ->orWhereHas('users', fn ($users) => $users->where('user.id', auth()->id()));
                });
            })
            ->when(!in_array(auth()->user()->role, ['admin', 'owner', 'kasir'], true), fn ($query) => $query->where('id_user', auth()->id()))
            ->value('id');
    }
}
