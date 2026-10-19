<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\Kasir;
use App\Models\DetailKas;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class KasController extends Controller
{
public function index()
{
    $idWarung = session('id_warung');

    if (!$idWarung) {
        return redirect()->route('cabang.index')
            ->with('error', 'Silakan pilih cabang terlebih dahulu.');
    }

    // 1. Ambil Kas Utama (Cash) & E-Wallet
    $kasTunai = Kas::where('id_warung', $idWarung)
        ->where('status', 'cash')
        ->first();

    $kasEwallet = Kas::where('id_warung', $idWarung)
        ->where('status', 'ewallet')
        ->latest()
        ->get();

    // 2. Rekap Mutasi BRILink Hari Ini (Hanya Masuk & Keluar)
    $summaryBrilink = Transaksi::whereHas('kas', function ($q) use ($idWarung) {
            $q->where('id_warung', $idWarung);
        })
        ->where('kategori', 'transaksi_brilink')
        ->whereDate('created_at', now()->toDateString())
        ->selectRaw("
            SUM(CASE WHEN jenis = 'masuk' THEN total ELSE 0 END) as total_masuk,
            SUM(CASE WHEN jenis = 'keluar' THEN total ELSE 0 END) as total_keluar
        ")
        ->first();

    return view('kas.index', compact('kasTunai', 'kasEwallet', 'summaryBrilink'));
}

	public function getBrilinkHistory(Request $request)
{
    $idWarung = session('id_warung');

    $query = Transaksi::with('kas')
        ->whereHas('kas', function ($q) use ($idWarung) {
            $q->where('id_warung', $idWarung);
        })
        ->where('kategori', 'transaksi_brilink');

    // Filter Berdasarkan Periode
    $periode = $request->get('periode', 'today');

    if ($periode === 'today') {
        $query->whereDate('created_at', now()->toDateString());
    } elseif ($periode === 'this_month') {
        $query->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
    } elseif ($periode === 'custom' && $request->start_date && $request->end_date) {
        $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    }

    // Ambil Data Transaksi
    $transaksi = $query->latest()->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'jenis' => $item->jenis,
            'total' => $item->total,
            'keterangan' => $item->keterangan,
            'formatted_date' => $item->created_at->format('d M Y, H:i'),
            'kas' => $item->kas ? [
                'nama_kas' => $item->kas->nama_kas,
                'jenis_kas' => $item->kas->jenis_kas,
            ] : null,
        ];
    });

    // Hitung Ulang Summary Sesuai Filter
    $summary = [
        'total_masuk' => $transaksi->where('jenis', 'masuk')->sum('total'),
        'total_keluar' => $transaksi->where('jenis', 'keluar')->sum('total'),
    ];

    return response()->json([
        'summary' => $summary,
        'transaksi' => $transaksi
    ]);
}

    public function create()
    {
        if (!session('id_warung')) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        return view('kas.create');
    }

    public function store(Request $request)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $request->validate([
            'jenis_kas' => 'required|string|max:100',
            'saldo' => 'required|numeric|min:0',
        ]);

        Kas::create([
            'id_warung' => $idWarung,
            'jenis_kas' => $request->jenis_kas,
            'saldo' => $request->saldo,
        ]);

        return redirect()
            ->route('kas.index')
            ->with('success', 'Kas berhasil ditambahkan.');
    }

    public function show($id)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $kas = Kas::where('id', $id)
            ->where('id_warung', $idWarung)
            ->with('warung')
            ->firstOrFail();

        $detailKas = DetailKas::where('id_kas_warung', $kas->id)
            ->orderByDesc('pecahan')
            ->get();

        return view('kas.show', compact('kas', 'detailKas'));
    }

    public function edit($id)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $kas = Kas::where('id', $id)
            ->where('id_warung', $idWarung)
            ->firstOrFail();

        return view('kas.edit', compact('kas'));
    }

    public function update(Request $request, $id)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $request->validate([
            'jenis_kas' => 'required|string|max:100',
            'saldo' => 'required|numeric|min:0',
        ]);

        $kas = Kas::where('id', $id)
            ->where('id_warung', $idWarung)
            ->firstOrFail();

        $kas->update([
            'jenis_kas' => $request->jenis_kas,
            'saldo' => $request->saldo,
        ]);

        return redirect()
            ->route('kas.index')
            ->with('success', 'Kas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $kas = Kas::where('id', $id)
            ->where('id_warung', $idWarung)
            ->firstOrFail();

        $kas->delete();

        return redirect()
            ->route('kas.index')
            ->with('success', 'Kas berhasil dihapus.');
    }

    public function updateDetailKas(Request $request, $id)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $kas = Kas::where('id', $id)
            ->where('id_warung', $idWarung)
            ->firstOrFail();

        $request->validate([
            'pecahan' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        foreach ($request->pecahan as $index => $pecahan) {
            $jumlah = (int) ($request->jumlah[$index] ?? 0);

            $detail = DetailKas::where('id_kas_warung', $kas->id)
                ->where('pecahan', $pecahan)
                ->first();

            if ($detail) {
                $detail->jumlah = $jumlah;
                $detail->save();
            }
        }

        $totalSaldo = DetailKas::where('id_kas_warung', $kas->id)
            ->selectRaw('SUM(pecahan * jumlah) as total')
            ->value('total');

        $kas->saldo = $totalSaldo ?? 0;
        $kas->save();

        return redirect()
            ->route('kas.show', $kas->id)
            ->with('success', 'Detail kas berhasil diperbarui!');
    }
}