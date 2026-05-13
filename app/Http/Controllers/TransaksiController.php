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
            'jenis'                     => 'required|in:masuk,keluar',
            'id_kas'                    => 'required|exists:kas,id', // Laci Fisik
            'id_kas_tujuan'             => 'required|exists:kas,id', // Akun Digital (Bank)
            'total'                     => 'required|numeric|min:0',
            'biaya_admin'               => 'nullable|numeric|min:0',
            'denominations_json'        => 'required|string',
            'change_denominations_json' => 'nullable|string',
            'admin_denominations_json'  => 'nullable|string',
        ]);

        // Decode data JSON pecahan
        $denominations = json_decode($request->denominations_json, true) ?? [];
        $changeDenominations = json_decode($request->change_denominations_json, true) ?? [];
        $adminDenominations = json_decode($request->admin_denominations_json, true) ?? [];

        $biayaAdmin = $request->biaya_admin ?? 0;
        $changeTotal = collect($changeDenominations)->sum(fn($d) => $d['pecahan'] * $d['jumlah']);
        $adminFisikTotal = collect($adminDenominations)->sum(fn($d) => $d['pecahan'] * $d['jumlah']);

        DB::beginTransaction();
        try {
            $kasAsal = Kas::findOrFail($request->id_kas);     // Laci Fisik
            $kasTujuan = Kas::findOrFail($request->id_kas_tujuan); // Saldo Digital (Bank)

            // 1. Validasi Saldo Fisik di Laci jika TARIK TUNAI
            if ($request->jenis === 'keluar' && $kasAsal->saldo < $request->total) {
                throw new \Exception("Uang tunai di {$kasAsal->jenis_kas} tidak cukup untuk ditarik!");
            }

            // 2. Simpan Transaksi Utama
            $transaksi = Transaksi::create([
                'jenis'         => $request->jenis,
                'id_kas_warung' => $request->id_kas,
                'total'         => $request->total,
                'biaya_admin'   => $biayaAdmin,
                'keterangan'    => $request->keterangan,
            ]);

            // 3. Update Detail Pecahan & Stok Fisik di Laci (Kas Asal)
            foreach ($denominations as $denom) {
                if ($denom['jumlah'] <= 0) continue;

                DetailTransaksi::create([
                    'id_transaksi_kas' => $transaksi->id,
                    'pecahan'          => $denom['pecahan'],
                    'jumlah'           => $denom['jumlah'],
                ]);

                $dk = DetailKas::firstOrCreate(
                    ['id_kas_warung' => $kasAsal->id, 'pecahan' => $denom['pecahan']],
                    ['jumlah' => 0]
                );

                if ($request->jenis === 'masuk') {
                    $dk->jumlah += $denom['jumlah']; // Setor: Fisik nambah
                } else {
                    if ($dk->jumlah < $denom['jumlah']) throw new \Exception("Stok pecahan Rp " . number_format($denom['pecahan']) . " tidak cukup!");
                    $dk->jumlah -= $denom['jumlah']; // Tarik: Fisik kurang
                }
                $dk->save();
            }

            // 4. Proses Admin Fisik (Jika nasabah bayar admin cash terpisah)
            foreach ($adminDenominations as $aDenom) {
                if ($aDenom['jumlah'] <= 0) continue;

                $dkA = DetailKas::firstOrCreate(
                    ['id_kas_warung' => $kasAsal->id, 'pecahan' => $aDenom['pecahan']],
                    ['jumlah' => 0]
                );
                $dkA->jumlah += $aDenom['jumlah'];
                $dkA->save();
            }

            // 5. Proses Kembalian (Mengurangi stok fisik di laci asal)
            if ($changeTotal > 0) {
                foreach ($changeDenominations as $cDenom) {
                    $dkC = DetailKas::where('id_kas_warung', $kasAsal->id)
                        ->where('pecahan', $cDenom['pecahan'])
                        ->first();

                    if (!$dkC || $dkC->jumlah < $cDenom['jumlah']) {
                        throw new \Exception("Uang fisik untuk kembalian pecahan " . number_format($cDenom['pecahan']) . " tidak cukup!");
                    }
                    $dkC->jumlah -= $cDenom['jumlah'];
                    $dkC->save();
                }
            }

            // 6. UPDATE SALDO FINAL (Logika Positif/Negatif)
            if ($request->jenis === 'masuk') {
                /** * SETOR TUNAI:
                 * Kas Asal (Laci) BERTAMBAH: Uang utama + Admin fisik (jika ada) - Kembalian
                 */
                // dd($request->total, $adminFisikTotal, $changeTotal, $biayaAdmin);
                $kasAsal->saldo += ($request->total + $biayaAdmin);

                /** * Kas Tujuan (Bank Digital) BERKURANG:
                 * Kita transfer saldo ke nasabah senilai 'total' dan bank potong 'biaya admin'
                 */
                $kasTujuan->saldo -= ($request->total);
            } else {
                /** * TARIK TUNAI:
                 * Kas Asal (Laci) BERKURANG: Uang diserahkan ke nasabah. 
                 * Jika nasabah bayar admin cash, laci bertambah sedikit.
                 */
                $kasAsal->saldo -= ($request->total + $adminFisikTotal - $biayaAdmin);

                /** * Kas Tujuan (Bank Digital) BERTAMBAH:
                 * Nasabah transfer ke kita senilai 'total', jatah kita berkurang biaya admin bank
                 */
                $kasTujuan->saldo += ($request->total - $biayaAdmin);
            }

            // Simpan perubahan saldo
            $kasAsal->save();
            $kasTujuan->save();

            DB::commit();
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['msg' => $e->getMessage()]);
        }
    }
}
