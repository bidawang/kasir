<?php

namespace App\Http\Controllers;

use App\Models\DetailKas;
use App\Models\Kas;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{

	public function index(Request $request)
{
    $idWarung = session('id_warung');

    if (!$idWarung) {
        return redirect()
            ->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    // Base Query - Khusus Kategori transaksi_customer
    $baseQuery = Transaksi::whereHas('kas', function ($q) use ($idWarung) {
        $q->where('id_warung', $idWarung);
    })->where('kategori', 'transaksi_customer');

    // Filter Tanggal
    if ($request->filled(['start_date', 'end_date'])) {
        $baseQuery->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ]);
    }

    // 1. Summary Metrics
    $summary = (clone $baseQuery)->selectRaw("
        COUNT(*) as total_transaksi,
        SUM(CASE WHEN jenis = 'masuk' THEN total ELSE 0 END) as total_setor,
        SUM(CASE WHEN jenis = 'keluar' THEN total ELSE 0 END) as total_tarik,
        SUM(biaya_admin) as total_keuntungan
    ")->first();

    // 2. Data Grafik
    $chartData = (clone $baseQuery)
        ->selectRaw("
            DATE(created_at) as date,
            COUNT(*) as total_trx,
            SUM(CASE WHEN jenis = 'masuk' THEN total ELSE 0 END) as setor,
            SUM(CASE WHEN jenis = 'keluar' THEN total ELSE 0 END) as tarik,
            SUM(biaya_admin) as profit
        ")
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

    // Format ISO (Y-m-d) untuk interaksi JavaScript/Form
    $chartRawDates = $chartData->pluck('date');
    // Format Display (d M) untuk Tampilan Sumbu X Grafik
    $chartLabels   = $chartData->pluck('date')->map(fn($d) => date('d M', strtotime($d)));
    $chartSetor    = $chartData->pluck('setor');
    $chartTarik    = $chartData->pluck('tarik');
    $chartProfit   = $chartData->pluck('profit');

    // 3. Data Transaksi Paginated
    $transaksi = (clone $baseQuery)
        ->with(['kas.warung', 'detailTransaksi'])
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('transaksi.index', compact(
        'transaksi',
        'summary',
        'chartRawDates',
        'chartLabels',
        'chartSetor',
        'chartTarik',
        'chartProfit'
    ));
}
// Method untuk Halaman Setor Tunai
public function setor()
{
    $idWarung = session('id_warung');

    if (!$idWarung) {
        return redirect()
            ->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    // Ambil Kas Asal (Kas Tunai/Fisik) beserta detail pecahan yang terurut
    $kasAsal = Kas::where('id_warung', $idWarung)
        ->where('status', 'cash')
        ->with(['detailKas' => function ($query) {
            $query->orderBy('pecahan', 'desc');
        }])
        ->first();

    // Pastikan relasi detailKas / detail_kas ter-load dengan rapi untuk JavaScript (AlpineJS)
    if ($kasAsal) {
        // Menyediakan fallback nama properti agar AlpineJS dapat membaca detail_kas maupun detailKas tanpa error
        $kasAsal->setRelation('detail_kas', $kasAsal->detailKas);
    }

    // Ambil daftar Kas Tujuan (khusus e-wallet)
    $kasTujuanList = Kas::where('id_warung', $idWarung)
        ->where('status', 'ewallet')
        ->orderBy('jenis_kas', 'asc')
        ->get();

    return view('transaksi.setor', compact('kasAsal', 'kasTujuanList'));
}

// Method untuk Halaman Tarik Tunai
public function tarik()
{
    $idWarung = session('id_warung');

    if (!$idWarung) {
        return redirect()
            ->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    // Ambil Kas Asal (Kas Tunai/Fisik tempat uang fisik ditarik) beserta detail pecahan terurut
    $kasAsal = Kas::where('id_warung', $idWarung)
        ->where('status', 'cash')
        ->with(['detailKas' => function ($query) {
            $query->orderBy('pecahan', 'desc');
        }])
        ->first();

    // Pastikan relasi detailKas / detail_kas ter-load dengan rapi untuk JavaScript (AlpineJS)
    if ($kasAsal) {
        $kasAsal->setRelation('detail_kas', $kasAsal->detailKas);
    }

    // Ambil daftar Kas Sumber / E-Wallet yang bisa dipilih oleh pelanggan
    $kasSumberList = Kas::where('id_warung', $idWarung)
        ->where('status', 'ewallet')
        ->orderBy('jenis_kas', 'asc')
        ->get();

    // Juga disertakan $kasTujuanList sebagai alias agar compatible jika Blade membutuhkan nama variabel tersebut
    $kasTujuanList = $kasSumberList;

    return view('transaksi.tarik', compact('kasAsal', 'kasSumberList', 'kasTujuanList'));
}
    public function store(Request $request)
{
    $idWarung = session('id_warung');

    if (!$idWarung) {
        return redirect()
            ->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    $request->validate([
        'jenis'                     => 'required|in:masuk,keluar',
        'id_kas'                    => 'required|integer|exists:kas,id',
        'id_kas_tujuan'             => 'required|integer|exists:kas,id',
        'total'                     => 'required|numeric|min:0',
        'biaya_admin'               => 'nullable|numeric|min:0',
        'denominations_json'        => 'required|string',
        'change_denominations_json' => 'nullable|string',
        'keterangan'                => 'nullable|string',
    ]);

    $denominations       = json_decode($request->denominations_json, true) ?? [];
    $changeDenominations = json_decode($request->change_denominations_json, true) ?? [];
    $biayaAdmin          = (float) ($request->biaya_admin ?? 0);

    // Hitung total nilai nominal dari pecahan fisik utama & kembalian
    $totalUangFisik   = collect($denominations)->sum(fn ($d) => ($d['pecahan'] ?? 0) * ($d['jumlah'] ?? 0));
    $totalChangeFisik = collect($changeDenominations)->sum(fn ($d) => ($d['pecahan'] ?? 0) * ($d['jumlah'] ?? 0));

    DB::beginTransaction();

    try {
        // 1. Validasi Kas Asal & Tujuan milik cabang aktif
        $kasAsal = Kas::where('id', $request->id_kas)
            ->where('id_warung', $idWarung)
            ->firstOrFail();

        $kasTujuan = Kas::where('id', $request->id_kas_tujuan)
            ->where('id_warung', $idWarung)
            ->firstOrFail();

        if ($kasAsal->id === $kasTujuan->id) {
            throw new \Exception('Kas asal dan kas tujuan tidak boleh sama.');
        }

        // 2. Simpan Header Transaksi Utama
        $transaksi = Transaksi::create([
            'jenis'         => $request->jenis,
            'id_kas_warung' => $kasAsal->id,
            'total'         => $request->total,
            'biaya_admin'   => $biayaAdmin,
            'keterangan'    => $request->keterangan,
        ]);

        // ==========================================
        // SKENARIO A: SETOR TUNAI (JENIS = MASUK)
        // ==========================================
        if ($request->jenis === 'masuk') {
            
            // A1. Uang Fisik Utama Diserahkan Customer -> TAMBAH STOK FISIK
            foreach ($denominations as $denom) {
                $pecahan = (int) ($denom['pecahan'] ?? 0);
                $jumlah  = (int) ($denom['jumlah'] ?? 0);
                if ($pecahan <= 0 || $jumlah <= 0) continue;

                DetailTransaksi::create([
                    'id_transaksi_kas' => $transaksi->id,
                    'pecahan'          => $pecahan,
                    'jumlah'           => $jumlah,
                ]);

                $detailKas = DetailKas::firstOrCreate(
                    ['id_kas_warung' => $kasAsal->id, 'pecahan' => $pecahan],
                    ['jumlah' => 0]
                );
                $detailKas->jumlah += $jumlah;
                $detailKas->save();
            }

            // A2. Uang Kembalian Diberikan ke Customer -> KURANGI STOK FISIK
            foreach ($changeDenominations as $cDenom) {
                $pecahan = (int) ($cDenom['pecahan'] ?? 0);
                $jumlah  = (int) ($cDenom['jumlah'] ?? 0);
                if ($pecahan <= 0 || $jumlah <= 0) continue;

                $detailKas = DetailKas::where('id_kas_warung', $kasAsal->id)
                    ->where('pecahan', $pecahan)
                    ->first();

                if (!$detailKas || $detailKas->jumlah < $jumlah) {
                    throw new \Exception('Stok kembalian pecahan Rp ' . number_format($pecahan, 0, ',', '.') . ' tidak cukup!');
                }

                $detailKas->jumlah -= $jumlah;
                $detailKas->save();
            }

            // A3. Update Saldo Kas Fisik & E-Wallet
            // Kas Fisik bertambah sebesar Net Fisik (Diterima - Kembalian)
            $kasAsal->saldo += ($totalUangFisik - $totalChangeFisik);
            
            // E-Wallet Agen berkurang sebesar Nominal Netto Transfer ke Customer
            $kasTujuan->saldo -= $request->total;
        }

        // ==========================================
        // SKENARIO B: TARIK TUNAI (JENIS = KELUAR)
        // ==========================================
        else {
            // Validasi Kecukupan Saldo Kas Fisik Keseluruhan
            if ($kasAsal->saldo < $totalUangFisik) {
                throw new \Exception("Total uang tunai fisik di {$kasAsal->jenis_kas} tidak cukup!");
            }

            // B1. Uang Fisik Utama Diberikan ke Customer -> KURANGI STOK FISIK
            foreach ($denominations as $denom) {
                $pecahan = (int) ($denom['pecahan'] ?? 0);
                $jumlah  = (int) ($denom['jumlah'] ?? 0);
                if ($pecahan <= 0 || $jumlah <= 0) continue;

                DetailTransaksi::create([
                    'id_transaksi_kas' => $transaksi->id,
                    'pecahan'          => $pecahan,
                    'jumlah'           => $jumlah,
                ]);

                $detailKas = DetailKas::firstOrCreate(
                    ['id_kas_warung' => $kasAsal->id, 'pecahan' => $pecahan],
                    ['jumlah' => 0]
                );

                if ($detailKas->jumlah < $jumlah) {
                    throw new \Exception('Stok uang fisik pecahan Rp ' . number_format($pecahan, 0, ',', '.') . ' tidak cukup!');
                }

                $detailKas->jumlah -= $jumlah;
                $detailKas->save();
            }

            // B2. Uang Kembalian/Tunai Diterima dari Customer -> TAMBAH STOK FISIK
            foreach ($changeDenominations as $cDenom) {
                $pecahan = (int) ($cDenom['pecahan'] ?? 0);
                $jumlah  = (int) ($cDenom['jumlah'] ?? 0);
                if ($pecahan <= 0 || $jumlah <= 0) continue;

                $detailKas = DetailKas::firstOrCreate(
                    ['id_kas_warung' => $kasAsal->id, 'pecahan' => $pecahan],
                    ['jumlah' => 0]
                );

                $detailKas->jumlah += $jumlah; // Tambah stok fisik
                $detailKas->save();
            }

            // B3. Update Saldo Kas Fisik & E-Wallet
            // Kas Fisik berkurang sebesar Net Fisik Keluar (Uang Keluar - Uang Diterima Kembali)
            $kasAsal->saldo -= ($totalUangFisik - $totalChangeFisik);

            // E-Wallet Agen bertambah sebesar Total Transfer Digital yang masuk dari Customer
            $kasTujuan->saldo += $request->total;
        }

        $kasAsal->save();
        $kasTujuan->save();

        DB::commit();

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan!');

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'msg' => $e->getMessage()
            ]);
    }
}
	// Method untuk menampilkan Halaman Form Pengeluaran Kas Operasional / Biaya
public function pengeluaran()
{
    $idWarung = session('id_warung');

    if (!$idWarung) {
        return redirect()
            ->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    // Ambil daftar Kas (Cash maupun E-Wallet) yang ada di cabang aktif
    $kasList = Kas::where('id_warung', $idWarung)
        ->with(['detailKas' => function ($query) {
            $query->orderBy('pecahan', 'desc');
        }])
        ->get();

    // Fallback relation untuk AlpineJS pada kas jenis cash
    foreach ($kasList as $kas) {
        if ($kas->status === 'cash' && $kas->relationLoaded('detailKas')) {
            $kas->setRelation('detail_kas', $kas->detailKas);
        }
    }

    // Ambil daftar kategori khusus pengeluaran (jika menggunakan tabel kategori_transaksi)
    $kategoriList = \App\Models\KategoriTransaksi::where('jenis', 'keluar')->get();

    return view('transaksi.pengeluaran', compact('kasList', 'kategoriList'));
}

// Method untuk memproses simpan Pengeluaran Kas
public function storePengeluaran(Request $request)
{
    $idWarung = session('id_warung');

    if (!$idWarung) {
        return redirect()
            ->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    $request->validate([
        'id_kas'             => 'required|integer|exists:kas,id',
        'id_kategori'        => 'nullable|integer|exists:kategori_transaksi,id',
        'total'              => 'required|numeric|min:1',
        'keterangan'         => 'required|string|max:255',
        'denominations_json' => 'nullable|string',
    ]);

    $denominations = json_decode($request->denominations_json, true) ?? [];

    DB::beginTransaction();

    try {
        $kas = Kas::where('id', $request->id_kas)
            ->where('id_warung', $idWarung)
            ->firstOrFail();

        // Validasi Kecukupan Saldo Kas
        if ($kas->saldo < $request->total) {
            throw new \Exception("Saldo kas {$kas->jenis_kas} tidak mencukupi untuk pengeluaran ini.");
        }

        // 1. Simpan Transaksi Pengeluaran
        $transaksi = Transaksi::create([
            'jenis'         => 'keluar',
            'id_kas_warung' => $kas->id,
            'id_kategori'   => $request->id_kategori ?? null,
            'total'         => $request->total,
            'biaya_admin'   => 0,
            'keterangan'    => $request->keterangan,
        ]);

        // 2. Jika Kas bertipe 'cash' dan pecahan uang fisik diinputkan
        if ($kas->status === 'cash' && !empty($denominations)) {
            foreach ($denominations as $denom) {
                $pecahan = (int) ($denom['pecahan'] ?? 0);
                $jumlah  = (int) ($denom['jumlah'] ?? 0);
                if ($pecahan <= 0 || $jumlah <= 0) continue;

                DetailTransaksi::create([
                    'id_transaksi_kas' => $transaksi->id,
                    'pecahan'          => $pecahan,
                    'jumlah'           => $jumlah,
                ]);

                $detailKas = DetailKas::where('id_kas_warung', $kas->id)
                    ->where('pecahan', $pecahan)
                    ->first();

                if (!$detailKas || $detailKas->jumlah < $jumlah) {
                    throw new \Exception('Stok uang fisik pecahan Rp ' . number_format($pecahan, 0, ',', '.') . ' tidak cukup!');
                }

                $detailKas->jumlah -= $jumlah;
                $detailKas->save();
            }
        }

        // 3. Potong Saldo Kas
        $kas->saldo -= $request->total;
        $kas->save();

        DB::commit();

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Pengeluaran kas berhasil dicatat!');

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->withErrors([
                'msg' => $e->getMessage()
            ]);
    }
}
	/**
     * Tampilkan form Kas Masuk (Mengarah ke 1 blade transaksi.create)
     */
    /**
     * Tampilkan form Kas Masuk
     */
    public function kasMasuk()
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()
                ->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $kasList = Kas::where('id_warung', $idWarung)
            ->with(['detailKas' => function ($query) {
                $query->orderBy('pecahan', 'desc');
            }])
            ->get();

        foreach ($kasList as $kas) {
            if ($kas->status === 'cash' && $kas->relationLoaded('detailKas')) {
                $kas->setRelation('detail_kas', $kas->detailKas);
            }
        }

        $jenisTransaksi = 'masuk';

        return view('transaksi.createtransaksibrilink', compact('kasList', 'jenisTransaksi'));
    }

    /**
     * Tampilkan form Kas Keluar
     */
    public function kasKeluar()
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()
                ->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $kasList = Kas::where('id_warung', $idWarung)
            ->with(['detailKas' => function ($query) {
                $query->orderBy('pecahan', 'desc');
            }])
            ->get();

        foreach ($kasList as $kas) {
            if ($kas->status === 'cash' && $kas->relationLoaded('detailKas')) {
                $kas->setRelation('detail_kas', $kas->detailKas);
            }
        }

        $jenisTransaksi = 'keluar';

        return view('transaksi.createtransaksibrilink', compact('kasList', 'jenisTransaksi'));
    }

    /**
     * Memproses simpan transaksi Kas Masuk / Kas Keluar
     */
    public function storeKas(Request $request)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()
                ->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $request->validate([
            'jenis'              => 'required|in:masuk,keluar',
            'id_kas'             => 'required|integer|exists:kas,id',
            'total'              => 'required|numeric|min:1',
            'keterangan'         => 'required|string|max:255',
            'denominations_json' => 'nullable|string',
        ]);

        $denominations = json_decode($request->denominations_json, true) ?? [];

        DB::beginTransaction();

        try {
            $kas = Kas::where('id', $request->id_kas)
                ->where('id_warung', $idWarung)
                ->firstOrFail();

            // Jika Kas Keluar, pastikan saldo cukup
            if ($request->jenis === 'keluar' && $kas->saldo < $request->total) {
                throw new \Exception("Saldo kas {$kas->jenis_kas} tidak mencukupi untuk pengeluaran sebesar Rp " . number_format($request->total, 0, ',', '.'));
            }

            // 1. Buat Header Transaksi (menyimpan enum kategori dan jenis)
            $transaksi = Transaksi::create([
                'jenis'         => $request->jenis,
                'kategori'      => $request->kategori,
                'id_kas_warung' => $kas->id,
                'total'         => $request->total,
                'biaya_admin'   => 0,
				'kategori' => 'transaksi_brilink',
                'keterangan'    => $request->keterangan,
            ]);

            // 2. Jika Kas bertipe 'cash' dan ada pecahan uang fisik yang diisi
            if ($kas->status === 'cash' && !empty($denominations)) {
                foreach ($denominations as $denom) {
                    $pecahan = (int) ($denom['pecahan'] ?? 0);
                    $jumlah  = (int) ($denom['jumlah'] ?? 0);
                    if ($pecahan <= 0 || $jumlah <= 0) continue;

                    // Simpan Detail Transaksi
                    DetailTransaksi::create([
                        'id_transaksi_kas' => $transaksi->id,
                        'pecahan'          => $pecahan,
                        'jumlah'           => $jumlah,
                    ]);

                    $detailKas = DetailKas::firstOrCreate(
                        ['id_kas_warung' => $kas->id, 'pecahan' => $pecahan],
                        ['jumlah' => 0]
                    );

                    if ($request->jenis === 'masuk') {
                        // Kas Masuk -> Tambah stok pecahan fisik
                        $detailKas->jumlah += $jumlah;
                    } else {
                        // Kas Keluar -> Kurangi stok pecahan fisik
                        if ($detailKas->jumlah < $jumlah) {
                            throw new \Exception('Stok fisik pecahan Rp ' . number_format($pecahan, 0, ',', '.') . ' tidak mencukupi!');
                        }
                        $detailKas->jumlah -= $jumlah;
                    }

                    $detailKas->save();
                }
            }

            // 3. Mutasi Saldo Utama Kas
            if ($request->jenis === 'masuk') {
                $kas->saldo += $request->total;
            } else {
                $kas->saldo -= $request->total;
            }
            $kas->save();

            DB::commit();

            $pesan = $request->jenis === 'masuk' ? 'Kas Masuk berhasil dicatat!' : 'Kas Keluar berhasil dicatat!';

            return redirect()
                ->route('kas.index')
                ->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'msg' => $e->getMessage()
                ]);
        }
    }
}
