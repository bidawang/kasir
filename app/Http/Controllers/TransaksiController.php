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
    /**
     * Menampilkan daftar transaksi dengan pagination dan filter.
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['kas.warung', 'detailTransaksi'])->latest();

        // Filter tanggal (opsional)
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date . ' 23:59:59'
            ]);
        }

        $transaksi = $query->paginate(10);

        return view('transaksi.index', compact('transaksi'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        $kasList = Kas::with('warung')->get();

        // ambil kas id = 3 beserta detailKas
        $kasId3 = Kas::with('detailKas')->find(3);

        return view('transaksi.create', compact('kasList', 'kasId3'));
    }



    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis'                  => 'required|in:masuk,keluar',
            'id_kas'                 => 'required|exists:kas,id',
            'total'                  => 'required|numeric|min:0',
            'biaya_admin'            => 'nullable|numeric|min:0',
            'keterangan'             => 'nullable|string|max:255',
            'denominations_json'     => 'required|string',
            'change_denominations_json' => 'nullable|string', // Validasi untuk data kembalian
        ]);

        $denominations = json_decode($request->denominations_json, true) ?? [];
        $changeDenominations = json_decode($request->change_denominations_json, true) ?? [];
        $biayaAdmin = $request->biaya_admin ?? 0;

        // Hitung total kembalian
        $changeTotal = 0;
        foreach ($changeDenominations as $changeDenom) {
            $changeTotal += ($changeDenom['pecahan'] * $changeDenom['jumlah']);
        }

        // Hitung total transaksi bersih (total dikurangi biaya admin)
        $totalBersih = $request->total - $biayaAdmin;
        if ($totalBersih < 0) {
            return back()->withErrors(['msg' => 'Total tidak boleh lebih kecil dari biaya admin!']);
        }

        DB::beginTransaction();
        try {
            $kas = Kas::findOrFail($request->id_kas);
            $saldoUtama = Kas::findOrFail(3);

            // Validasi saldo jika jenis = keluar
            if ($request->jenis === 'keluar' && $kas->saldo < $totalBersih) {
                return back()->withErrors(['msg' => 'Saldo kas tidak mencukupi untuk transaksi ini!']);
            }

            // Validasi stok pecahan di kas utama untuk kembalian
            if ($request->jenis === 'masuk' && !empty($changeDenominations)) {
                foreach ($changeDenominations as $changeDenom) {
                    if (!empty($changeDenom['pecahan']) && $changeDenom['jumlah'] > 0) {
                        $changeDetailKas = DetailKas::where('id_kas_warung', 3)
                            ->where('pecahan', $changeDenom['pecahan'])
                            ->first();

                        if (!$changeDetailKas || $changeDetailKas->jumlah < $changeDenom['jumlah']) {
                            DB::rollBack();
                            return back()->withErrors(['msg' => "Jumlah pecahan untuk kembalian Rp {$changeDenom['pecahan']} tidak mencukupi di kas utama!"]);
                        }
                    }
                }
            }

            // Simpan transaksi utama
            $transaksi = Transaksi::create([
                'jenis'         => $request->jenis,
                'id_kas_warung' => $request->id_kas,
                'total'         => $totalBersih,
                'biaya_admin'   => $biayaAdmin,
                'keterangan'    => $request->keterangan,
            ]);

            // Simpan detail pecahan & update detail_kas.
            // Tidak perlu update detail_kas di kas utama jika pengeluaran bukan dari kas utama.
            foreach ($denominations as $denom) {
                if (!empty($denom['pecahan']) && $denom['jumlah'] > 0) {
                    // Simpan detail transaksi
                    DetailTransaksi::create([
                        'id_transaksi_kas' => $transaksi->id,
                        'pecahan'          => $denom['pecahan'],
                        'jumlah'           => $denom['jumlah'],
                    ]);

                    // Hanya update detail kas utama (ID 3) jika ini transaksi masuk,
                    // atau jika ini transaksi keluar dari kas utama itu sendiri.
                    if ($request->id_kas == 3) {
                        $detailKas = DetailKas::where('id_kas_warung', 3)
                            ->where('pecahan', $denom['pecahan'])
                            ->first();

                        if ($detailKas) {
                            if ($request->jenis === 'masuk') {
                                $detailKas->jumlah += $denom['jumlah'];
                            } else {
                                if ($detailKas->jumlah < $denom['jumlah']) {
                                    DB::rollBack();
                                    return back()->withErrors(['msg' => "Jumlah pecahan Rp {$denom['pecahan']} tidak mencukupi di kas utama!"]);
                                }
                                $detailKas->jumlah -= $denom['jumlah'];
                            }
                            $detailKas->save();
                        }
                    }
                }
            }

            // Buat transaksi pengeluaran baru untuk uang kembalian
            if ($request->jenis === 'masuk' && !empty($changeDenominations)) {
                $changeTransaksi = Transaksi::create([
                    'jenis'         => 'keluar',
                    'id_kas_warung' => 3, // Kas utama
                    'total'         => $changeTotal,
                    'biaya_admin'   => 0,
                    'keterangan'    => "Uang kembalian dari transaksi masuk ID {$transaksi->id}",
                ]);

                // Simpan detail pecahan kembalian & update detail_kas kas id=3
                foreach ($changeDenominations as $changeDenom) {
                    if (!empty($changeDenom['pecahan']) && $changeDenom['jumlah'] > 0) {
                        DetailTransaksi::create([
                            'id_transaksi_kas' => $changeTransaksi->id,
                            'pecahan'          => $changeDenom['pecahan'],
                            'jumlah'           => $changeDenom['jumlah'],
                        ]);

                        // Update detail_kas kas id=3 (pengurangan)
                        $changeDetailKas = DetailKas::where('id_kas_warung', 3)
                            ->where('pecahan', $changeDenom['pecahan'])
                            ->first();

                        if ($changeDetailKas) {
                            $changeDetailKas->jumlah -= $changeDenom['jumlah'];
                            $changeDetailKas->save();
                        }
                    }
                }
            }

            // Update saldo kas utama dan kas yang dipilih untuk transaksi utama
            if ($request->jenis === 'masuk') {
                $kas->saldo += $totalBersih; // Saldo kas bertambah dari transaksi bersih
                $saldoUtama->saldo += $totalBersih; // Saldo kas utama bertambah dari transaksi bersih
            } else { // Transaksi keluar
                $kas->saldo -= $totalBersih; // Saldo kas berkurang

                // Saldo kas utama hanya berkurang jika transaksi berasal dari kas utama itu sendiri
                if ($request->id_kas == 3) {
                    $saldoUtama->saldo -= $totalBersih;
                }
            }

            // Kurangi saldo kas utama dengan total kembalian (jika ada)
            if ($request->jenis === 'masuk' && !empty($changeDenominations)) {
                $saldoUtama->saldo -= $changeTotal;
            }

            $kas->save();
            $saldoUtama->save();

            DB::commit();
            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
        }
    }
}
