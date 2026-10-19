<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\Kasir; // Pastikan model Warung di-import
use App\Models\DetailKas;
use App\Models\Transaksi;
use App\Models\TransferKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferKasController extends Controller
{
    public function index(Request $request)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()
                ->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $query = TransferKas::with([
            'kasAsal.warung',
            'kasTujuan.warung'
        ])
        ->where(function ($q) use ($idWarung) {
            $q->whereHas('kasAsal', function ($sub) use ($idWarung) {
                $sub->where('id_warung', $idWarung);
            })->orWhereHas('kasTujuan', function ($sub) use ($idWarung) {
                $sub->where('id_warung', $idWarung);
            });
        })
        ->latest();

        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $transferKas = $query->paginate(10)->withQueryString();

        return view('transfer_kas.index', compact('transferKas'));
    }

    public function create()
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()
                ->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        // Ambil kas milik cabang aktif beserta rincian pecahannya
        $kasListAsal = Kas::where('id_warung', $idWarung)
            ->with(['detailKas' => function($q) {
                $q->where('jumlah', '>', 0)->orderBy('pecahan', 'desc');
            }])
            ->orderBy('status')
            ->orderBy('jenis_kas')
            ->get();

        // Daftar cabang lain
        $warungList = Kasir::where('id', '!=', $idWarung)
            ->orderBy('nama_warung')
            ->get();

        return view('transfer_kas.create', compact('kasListAsal', 'warungList', 'idWarung'));
    }

    public function getKasByWarung($idWarung)
    {
        $kas = Kas::where('id_warung', $idWarung)
            ->with(['detailKas' => function($q) {
                $q->where('jumlah', '>', 0)->orderBy('pecahan', 'desc');
            }])
            ->orderBy('status')
            ->orderBy('jenis_kas')
            ->get();

        return response()->json($kas);
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
            'tipe_tujuan'      => 'required|in:sesama,lain',
            'id_warung_tujuan' => 'required_if:tipe_tujuan,lain|nullable|integer|exists:kasir,id',
            'id_kas_asal'      => 'required|integer|exists:kas,id',
            'id_kas_tujuan'    => 'required|integer|exists:kas,id',
            'jumlah'           => 'required|numeric|min:1',
            'biaya_transfer'   => 'nullable|numeric|min:0',
            'keterangan'       => 'nullable|string|max:255',
            'pecahan'          => 'nullable|array',
            'pecahan.*'        => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $kasAsal = Kas::where('id', $request->id_kas_asal)
                ->where('id_warung', $idWarung)
                ->lockForUpdate()
                ->firstOrFail();

            $kasTujuan = Kas::where('id', $request->id_kas_tujuan)
                ->lockForUpdate()
                ->firstOrFail();

            if ($kasAsal->id === $kasTujuan->id) {
                throw new \Exception('Kas asal dan kas tujuan tidak boleh sama.');
            }

            if ($kasAsal->status !== $kasTujuan->status) {
                throw new \Exception('Jenis tipe kas asal dan tujuan harus sama (' . strtoupper($kasAsal->status) . ').');
            }

            // Validasi transfer internal hanya ewallet
            if ($request->tipe_tujuan === 'sesama' && $kasAsal->status === 'cash') {
                throw new \Exception('Pemindahan kas tunai (Cash) hanya diperbolehkan antar cabang.');
            }

            $jumlah = (int) $request->jumlah;
            $biayaTransfer = (int) ($request->biaya_transfer ?? 0);
            $totalKeluar = $jumlah + $biayaTransfer;

            // Proses pemindahan pecahan jika tipe Cash
            if ($kasAsal->status === 'cash') {
                $pecahanInput = $request->input('pecahan', []);
                $totalHitungPecahan = 0;

                foreach ($pecahanInput as $nilaiPecahan => $lembar) {
                    $lembar = (int) $lembar;
                    if ($lembar > 0) {
                        $totalHitungPecahan += ($nilaiPecahan * $lembar);

                        $detailAsal = DetailKas::where('id_kas_warung', $kasAsal->id)
                            ->where('pecahan', $nilaiPecahan)
                            ->first();

                        if (!$detailAsal || $detailAsal->jumlah < $lembar) {
                            throw new \Exception("Stok pecahan Rp " . number_format($nilaiPecahan, 0, ',', '.') . " di kas asal tidak mencukupi.");
                        }

                        $detailAsal->jumlah -= $lembar;
                        $detailAsal->save();

                        $detailTujuan = DetailKas::firstOrCreate([
                            'id_kas_warung' => $kasTujuan->id,
                            'pecahan'       => $nilaiPecahan,
                        ], ['jumlah' => 0]);

                        $detailTujuan->jumlah += $lembar;
                        $detailTujuan->save();
                    }
                }

                if ($totalHitungPecahan !== $jumlah) {
                    throw new \Exception('Total nominal dari rincian pecahan (Rp ' . number_format($totalHitungPecahan, 0, ',', '.') . ') tidak sesuai nominal transfer (Rp ' . number_format($jumlah, 0, ',', '.') . ').');
                }
            }

            if ($kasAsal->saldo < $totalKeluar) {
                throw new \Exception('Saldo kas ' . $kasAsal->jenis_kas . ' tidak mencukupi.');
            }

            TransferKas::create([
                'id_kas_asal'    => $kasAsal->id,
                'id_kas_tujuan'  => $kasTujuan->id,
                'jumlah'         => $jumlah,
                'biaya_transfer' => $biayaTransfer,
                'keterangan'     => $request->keterangan,
            ]);

            $kasAsal->saldo -= $totalKeluar;
            $kasAsal->save();

            $kasTujuan->saldo += $jumlah;
            $kasTujuan->save();

            Transaksi::create([
                'id_kas_warung' => $kasAsal->id,
                'total'         => $totalKeluar,
                'biaya_admin'   => $biayaTransfer,
                'keterangan'    => 'Transfer ke ' . $kasTujuan->jenis_kas . ($request->keterangan ? ' - ' . $request->keterangan : ''),
                'jenis'         => 'keluar',
				'kategori' => 'transaksi_brilink'
            ]);

            Transaksi::create([
                'id_kas_warung' => $kasTujuan->id,
                'total'         => $jumlah,
                'biaya_admin'   => 0,
                'keterangan'    => 'Transfer dari ' . $kasAsal->jenis_kas . ($request->keterangan ? ' - ' . $request->keterangan : ''),
                'jenis'         => 'masuk',
				'kategori' => 'transaksi_brilink'
            ]);

            DB::commit();

            return redirect()
                ->route('transfer-kas.index')
                ->with('success', 'Pemindahan dana berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['msg' => $e->getMessage()]);
        }
    }
}