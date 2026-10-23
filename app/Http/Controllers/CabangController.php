<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\Kas;
use App\Models\DetailKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // Jika user adalah owner, ambil semua data cabang
    if ($user->role === 'admin') {
        $warungs = Kasir::orderBy('id')->get();
    } else {
        // Jika bukan owner, hanya ambil cabang milik user yang login
        $warungs = Kasir::where('id_user', $user->id)
            ->orderBy('id')
            ->get();
    }

    return view('cabang.index', compact('warungs'));
}

    public function create()
    {
        return view('cabang.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_warung' => 'required|string|max:255',
    ]);

    DB::transaction(function () use ($request) {
        // 1. Buat Cabang / Warung Baru
        $warung = Kasir::create([
            'id_user'     => auth()->id(),
            'nama_warung' => $request->nama_warung,
            'modal'       => 0,
        ]);

        // 2. Buat Kas Utama (Cash Tunai) Otomatis
        $kas = Kas::create([
            'id_warung' => $warung->id,
            'jenis_kas' => 'Tunai', // Sesuaikan jika ada default nama kas lain (e.g., 'Kas Utama')
            'status'    => 'cash',  // Kolom status berdasarkan controller sebelumnya
            'saldo'     => 0,
        ]);

        // 3. Buat Detail Pecahan Uang Otomatis (Default Jumlah = 0)
        $pecahanList = [100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000];

        $detailKasData = [];
        $now = now();

        foreach ($pecahanList as $pecahan) {
            $detailKasData[] = [
                'id_kas_warung' => $kas->id,
                'pecahan'       => $pecahan,
                'jumlah'        => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // Mass Insert ke tabel detail_kas
        DetailKas::insert($detailKasData);
    });

    return redirect()
        ->route('cabang.index')
        ->with('success', 'Cabang dan Kas Utama berhasil ditambahkan.');
}

public function pilih($id)
{
    $warung = Kasir::findOrFail($id);

    session([
        'id_warung' => $warung->id,
    ]);

    return redirect()
        ->back()
        ->with('success', 'Cabang aktif: ' . $warung->nama_warung);
}

    public function edit($id)
    {
        $warung = Kasir::where('id', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        return view('cabang.edit', compact('warung'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_warung' => 'required|string|max:255',
        ]);

        $warung = Kasir::where('id', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        $warung->update([
            'nama_warung' => $request->nama_warung,
        ]);

        return redirect()
            ->route('cabang.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $warung = Kasir::where('id', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        $warung->delete();

        if (session('id_warung') == $id) {
            session()->forget('id_warung');
        }

        return redirect()
            ->route('cabang.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }
}