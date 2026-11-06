<?php

namespace App\Http\Controllers;

use App\Models\DetailKas;
use App\Models\Kas;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\ItemJualan;
use App\Models\BiayaAdmin;
use App\Models\Kasir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{

    public function menu()
    {
        if (!$this->activeWarungId()) {
            return redirect()->route('cabang.index')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        return view('transaksi.menu');
    }

	public function index(Request $request)
{
    $idWarung = $this->activeWarungId();

    if (!$idWarung) {
        return redirect()
            ->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    $branchQuery = Transaksi::whereHas('kas', function ($q) use ($idWarung) {
        $q->where('id_warung', $idWarung);
    });
    if (auth()->user()->role === 'kasir') {
        $branchQuery->where('id_user', auth()->id());
    }

    $customerQuery = (clone $branchQuery)->where('kategori', 'transaksi_customer');
    $transaksiQuery = clone $branchQuery;
    $profitQuery = (clone $branchQuery)->where(function ($query) {
        $query->whereIn('kategori', ['transaksi_customer', 'penjualan_item'])
            ->orWhere('is_profit', true);
    });

    if ($request->filled(['start_date', 'end_date'])) {
        $dateRange = [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ];
        $customerQuery->whereBetween('created_at', $dateRange);
        $transaksiQuery->whereBetween('created_at', $dateRange);
        $profitQuery->whereBetween('created_at', $dateRange);
    }

    $summary = null;
    $chartRawDates = $chartLabels = $chartSetor = $chartTarik = $chartProfit = collect();
    if (in_array(auth()->user()->role, ['admin', 'owner'], true)) {
        $summary = (clone $customerQuery)->selectRaw("
            COUNT(*) as total_transaksi,
            SUM(CASE WHEN jenis = 'masuk' THEN total ELSE 0 END) as total_setor,
            SUM(CASE WHEN jenis = 'keluar' THEN total ELSE 0 END) as total_tarik
        ")->first();
        $summary->total_transaksi = $transaksiQuery->count();
        $summary->total_keuntungan = (clone $profitQuery)->sum('biaya_admin')
            + (clone $profitQuery)->where('is_profit', true)->sum('total');

        $chartData = $profitQuery
            ->selectRaw("
                DATE(created_at) as transaction_date,
                SUM(CASE WHEN kategori = 'transaksi_customer' AND jenis = 'masuk' THEN total ELSE 0 END) as setor,
                SUM(CASE WHEN kategori = 'transaksi_customer' AND jenis = 'keluar' THEN total ELSE 0 END) as tarik,
                SUM(biaya_admin) + SUM(CASE WHEN is_profit = 1 THEN total ELSE 0 END) as profit
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

    // 3. Data Transaksi Paginated
    $transaksi = $transaksiQuery
        ->with(['kas.warung', 'kasSumber', 'detailTransaksi', 'itemJualan', 'kategoriTransaksi'])
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
        ->get(['jenis', 'id_kas', 'range_awal', 'range_akhir', 'biaya_admin']);

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

        $feeQuery = BiayaAdmin::where('id_warung', $idWarung)
            ->where('jenis', $item->jenis)
            ->where('range_awal', '<=', $hargaItem)
            ->where('range_akhir', '>=', $hargaItem)
            ->where(function ($query) use ($item, $wallet) {
                if ($item->jenis !== 'bank' || !$wallet) {
                    $query->whereNull('id_kas');
                    return;
                }
                $query->where('id_kas', $wallet->id)->orWhereNull('id_kas');
            });
        $biayaAdmin = $feeQuery
            ->orderByRaw('CASE WHEN id_kas IS NULL THEN 1 ELSE 0 END')
            ->orderBy('range_awal')
            ->first();
        $biaya = $biayaAdmin
            ? (int) $biayaAdmin->biaya_admin
            : (int) ($validated['biaya_admin_manual'] ?? 0);
        $total = $hargaItem + $biaya;
        $received = json_decode($validated['denominations_json'], true);
        $change = json_decode($validated['change_denominations_json'] ?? '[]', true);
        $allowedDenominations = [100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000];
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
            'total' => $total,
            'biaya_admin' => $biaya,
            'nama_item' => $item->nama_item,
            'harga_item' => $hargaItem,
            'metode_pembayaran' => 'cash',
            'tanggal' => now()->toDateString(),
            'keterangan' => ($validated['keterangan'] ?? null) ?: $item->id . '-Penjualan item: ' . $item->nama_item,
        ]);

        foreach ($received as $denomination) {
            $pecahan = (int) $denomination['pecahan'];
            $jumlah = (int) $denomination['jumlah'];
            if ($jumlah < 1) {
                continue;
            }

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
            if ($jumlah < 1) {
                continue;
            }

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

// Method untuk Halaman Setor Tunai
public function setor()
{
    $idWarung = $this->activeWarungId();

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
    $biayaAdminList = BiayaAdmin::where('id_warung', $idWarung)
        ->where('jenis', 'bank')
        ->get(['id_kas', 'range_awal', 'range_akhir', 'biaya_admin']);

    return view('transaksi.setor', compact('kasAsal', 'kasTujuanList', 'biayaAdminList'));
}

// Method untuk Halaman Tarik Tunai
public function tarik()
{
    $idWarung = $this->activeWarungId();

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
    $biayaAdminList = BiayaAdmin::where('id_warung', $idWarung)
        ->where('jenis', 'bank')
        ->get(['id_kas', 'range_awal', 'range_akhir', 'biaya_admin']);

    return view('transaksi.tarik', compact('kasAsal', 'kasSumberList', 'kasTujuanList', 'biayaAdminList'));
}
    public function store(Request $request)
{
        $idWarung = $this->activeWarungId();

    if (!$idWarung) {
        return redirect()
            ->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    $request->validate([
        'jenis'                     => 'required|in:masuk,keluar',
        'id_kas'                    => 'required|integer|exists:kas,id',
        'id_kas_tujuan'             => 'required|integer|exists:kas,id',
        'total'                     => 'required|integer|min:0',
        'biaya_admin_manual'        => 'nullable|integer|min:0',
        'biaya_admin_toko'          => 'nullable|integer|min:0',
        'change_total'              => 'required|integer|min:0',
        'denominations_json'        => 'required|json',
        'change_denominations_json' => 'nullable|json',
        'keterangan'                => 'nullable|string',
    ]);

    $denominations       = json_decode($request->denominations_json, true) ?? [];
    $changeDenominations = json_decode($request->change_denominations_json, true) ?? [];

    // Hitung total nilai nominal dari pecahan fisik utama & kembalian
    $allowedDenominations = [100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000];
    $totalUangFisik = $this->denominationTotal($denominations, $allowedDenominations, 'denominations_json');
    $totalChangeFisik = $this->denominationTotal($changeDenominations, $allowedDenominations, 'change_denominations_json');
    if ((int) $request->change_total !== $totalChangeFisik) {
        throw ValidationException::withMessages([
            'change_total' => 'Total kembalian tidak sesuai dengan rincian pecahan uang.',
        ]);
    }
    $nominalTransaksi = $totalUangFisik - $totalChangeFisik;
    if ($nominalTransaksi <= 0) {
        throw ValidationException::withMessages([
            'denominations_json' => 'Nominal bersih transaksi harus lebih dari 0.',
        ]);
    }

    DB::beginTransaction();

    try {
        // 1. Validasi Kas Asal & Tujuan milik cabang aktif
        $kasAsal = Kas::where('id', $request->id_kas)
            ->where('id_warung', $idWarung)
            ->where('status', 'cash')
            ->lockForUpdate()
            ->firstOrFail();

        $kasTujuan = Kas::where('id', $request->id_kas_tujuan)
            ->where('id_warung', $idWarung)
            ->where('status', 'ewallet')
            ->lockForUpdate()
            ->firstOrFail();

        if ($kasAsal->id === $kasTujuan->id) {
            throw new \Exception('Kas asal dan kas tujuan tidak boleh sama.');
        }

        $feeRange = BiayaAdmin::where('id_warung', $idWarung)
            ->where('jenis', 'bank')
            ->where(function ($query) use ($kasTujuan) {
                $query->where('id_kas', $kasTujuan->id)->orWhereNull('id_kas');
            })
            ->where('range_awal', '<=', $nominalTransaksi)
            ->where('range_akhir', '>=', $nominalTransaksi)
            ->orderByRaw('CASE WHEN id_kas IS NULL THEN 1 ELSE 0 END')
            ->orderBy('range_awal')
            ->first();
        $biayaAdminBank = $feeRange
            ? (int) $feeRange->biaya_admin
            : (int) ($request->biaya_admin_manual ?? 0);
        $biayaAdminToko = (int) ($request->biaya_admin_toko ?? 0);
        $totalTransaksi = $request->jenis === 'masuk'
            ? $nominalTransaksi - $biayaAdminBank - $biayaAdminToko
            : $nominalTransaksi + $biayaAdminBank + $biayaAdminToko;

        if ($totalTransaksi <= 0 || (int) $request->total !== $totalTransaksi) {
            throw new \Exception('Total transaksi tidak sesuai dengan nominal dan biaya admin bank/toko.');
        }

        if ($request->jenis === 'masuk' && $kasTujuan->saldo < $totalTransaksi) {
            throw new \Exception('Saldo wallet tidak mencukupi untuk nominal setoran ini.');
        }
        // 2. Simpan Header Transaksi Utama
        $transaksi = Transaksi::create([
            'id_user'         => auth()->id(),
            'jenis'         => $request->jenis,
            'id_kas_warung' => $kasAsal->id,
            'id_kas_sumber' => $kasTujuan->id,
            'kategori'      => 'transaksi_customer',
            'total'         => $totalTransaksi,
            'biaya_admin'   => $biayaAdminToko,
            'biaya_admin_bank' => $biayaAdminBank,
            'metode_pembayaran' => 'ewallet',
            'tanggal'       => now()->toDateString(),
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
            $kasTujuan->saldo -= $totalTransaksi;
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
            $kasTujuan->saldo += $totalTransaksi;
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
    $idWarung = $this->activeWarungId();

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
    $idWarung = $this->activeWarungId();

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
            'id_user'         => auth()->id(),
            'jenis'         => 'keluar',
            'id_kas_warung' => $kas->id,
            'id_kategori'   => $request->id_kategori ?? null,
            'total'         => $request->total,
            'biaya_admin'   => 0,
            'metode_pembayaran' => $kas->status === 'cash' ? 'cash' : 'ewallet',
            'tanggal'       => now()->toDateString(),
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
        $idWarung = $this->activeWarungId();

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
        $idWarung = $this->activeWarungId();

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
        $idWarung = $this->activeWarungId();

        if (!$idWarung) {
            return redirect()
                ->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
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
        Validator::make(
            ['denominations' => $denominations],
            [
                'denominations' => 'required|array',
                'denominations.*.pecahan' => 'required|integer|min:1',
                'denominations.*.jumlah' => 'required|integer|min:0',
            ]
        )->validate();

        DB::beginTransaction();

        try {
            $kas = Kas::where('id', $request->id_kas)
                ->where('id_warung', $idWarung)
                ->lockForUpdate()
                ->firstOrFail();

            if ($kas->status === 'cash') {
                $totalPecahan = collect($denominations)->sum(
                    fn ($denom) => (int) $denom['pecahan'] * (int) $denom['jumlah']
                );

                if ($totalPecahan !== (int) $validated['total']) {
                    throw ValidationException::withMessages([
                        'total' => 'Nominal harus sama dengan total rincian pecahan uang fisik.',
                    ]);
                }
            }

            // Jika Kas Keluar, pastikan saldo cukup
            if ($validated['jenis'] === 'keluar' && $kas->saldo < $validated['total']) {
                throw new \Exception("Saldo kas {$kas->jenis_kas} tidak mencukupi untuk pengeluaran sebesar Rp " . number_format($validated['total'], 0, ',', '.'));
            }

            // 1. Buat Header Transaksi (menyimpan enum kategori dan jenis)
            $transaksi = Transaksi::create([
                'id_user'         => auth()->id(),
                'jenis'         => $validated['jenis'],
                'is_profit'     => $validated['jenis'] === 'masuk' && $request->boolean('is_profit'),
                'kategori'      => 'transaksi_brilink',
                'id_kas_warung' => $kas->id,
                'total'         => $validated['total'],
                'biaya_admin'   => 0,
                'metode_pembayaran' => $kas->status === 'cash' ? 'cash' : 'ewallet',
                'tanggal'       => now()->toDateString(),
                'keterangan'    => $validated['keterangan'],
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

                    if ($validated['jenis'] === 'masuk') {
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
            if ($validated['jenis'] === 'masuk') {
                $kas->saldo += $validated['total'];
            } else {
                $kas->saldo -= $validated['total'];
            }
            $kas->save();

            DB::commit();

            $pesan = $validated['jenis'] === 'masuk' ? 'Kas Masuk berhasil dicatat!' : 'Kas Keluar berhasil dicatat!';
            $routeSukses = auth()->user()->role === 'kasir'
                ? 'transaksi.index'
                : 'kas.index';

            return redirect()
                ->route($routeSukses)
                ->with('success', $pesan);

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
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
