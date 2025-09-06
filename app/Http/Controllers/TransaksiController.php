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

        return view('transaksi.create', compact('kasList'));
    }

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis'              => 'required|in:masuk,keluar',
            'id_kas'             => 'required|exists:kas,id',
            'total'              => 'required|numeric|min:0',
            'biaya_admin'        => 'nullable|numeric|min:0',
            'keterangan'         => 'nullable|string|max:255',
            'denominations_json' => 'required|string',
        ]);

        $denominations = json_decode($request->denominations_json, true) ?? [];
        $biayaAdmin = $request->biaya_admin ?? 0;

        // Hitung total transaksi bersih
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

            // Simpan transaksi utama
            $transaksi = Transaksi::create([
                'jenis'        => $request->jenis,
                'id_kas_warung' => $request->id_kas,
                'total'        => $totalBersih,
                'biaya_admin'  => $biayaAdmin,
                'keterangan'   => $request->keterangan,
            ]);

            // Simpan detail pecahan & update detail_kas untuk kas id=3
            foreach ($denominations as $denom) {
                if (!empty($denom['pecahan']) && $denom['jumlah'] > 0) {
                    // Simpan detail transaksi
                    DetailTransaksi::create([
                        'id_transaksi_kas' => $transaksi->id,
                        'pecahan'          => $denom['pecahan'],
                        'jumlah'           => $denom['jumlah'],
                    ]);

                    // Update detail_kas kas id=3
                    $detailKas = DetailKas::where('id_kas_warung', 3)
                        ->where('pecahan', $denom['pecahan'])
                        ->first();

                    if ($detailKas) {
                        if ($request->jenis === 'masuk') {
                            $detailKas->jumlah += $denom['jumlah'];
                        } else {
                            // Validasi jangan minus
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

            // Update saldo kas
            if ($request->jenis === 'masuk') {
                $saldoUtama->saldo += $request->total;
                $kas->saldo -= $request->total;
            } else {
                $kas->saldo += $request->total;
                $saldoUtama->saldo -= $request->total;
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
