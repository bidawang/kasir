<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\DetailKas;
use Illuminate\Http\Request;

class KasController extends Controller
{
    /**
     * Tampilkan semua data kas.
     */
    public function index()
    {
        $kas = Kas::with('warung')->latest()->get();
        return view('kas.index', compact('kas'));
    }

    /**
     * Form tambah kas baru.
     */
    public function create()
    {
        return view('kas.create');
    }

    /**
     * Simpan kas baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_warung' => 'required|integer',
            'jenis_kas' => 'required|string|max:100',
            'saldo' => 'required|numeric|min:0',
        ]);

        Kas::create($request->all());

        return redirect()->route('kas.index')->with('success', 'Kas berhasil ditambahkan.');
    }

    /**
     * Form edit kas.
     */
    public function edit($id)
    {
        $kas = Kas::findOrFail($id);
        return view('kas.edit', compact('kas'));
    }

    /**
     * Update kas.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_warung' => 'required|integer',
            'jenis_kas' => 'required|string|max:100',
            'saldo' => 'required|numeric|min:0',
        ]);

        $kas = Kas::findOrFail($id);
        $kas->update($request->all());

        return redirect()->route('kas.index')->with('success', 'Kas berhasil diperbarui.');
    }

    /**
     * Hapus kas.
     */
    public function destroy($id)
    {
        $kas = Kas::findOrFail($id);
        $kas->delete();

        return redirect()->route('kas.index')->with('success', 'Kas berhasil dihapus.');
    }

    public function show($id)
    {
        // Ambil kas
        $kas = Kas::findOrFail($id);

        // Jika id=3, tampilkan detail pecahan
        if ($id == 3) {
            $detailKas = DetailKas::where('id_kas_warung', 3)->orderByDesc('pecahan')->get();
            return view('kas.show', compact('kas', 'detailKas'));
        }

        // Kas lain tampil default
        return view('kas.show', compact('kas'));
    }

    public function updateDetailKas(Request $request, $id)
    {
        if ($id != 3) {
            return back()->withErrors(['msg' => 'Hanya kas utama (id=3) yang bisa diupdate detailnya.']);
        }

        $request->validate([
            'pecahan' => 'required|array',
            'jumlah'  => 'required|array',
        ]);

        // Update jumlah pecahan
        foreach ($request->pecahan as $index => $pecahan) {
            $jumlah = (int)($request->jumlah[$index] ?? 0);

            $detail = DetailKas::where('id_kas_warung', 3)
                ->where('pecahan', $pecahan)
                ->first();

            if ($detail) {
                $detail->jumlah = $jumlah;
                $detail->save();
            }
        }

        // Hitung ulang saldo kas id=3
        $totalSaldo = DetailKas::where('id_kas_warung', 3)
            ->selectRaw('SUM(pecahan * jumlah) as total')
            ->value('total');

        $kas = Kas::findOrFail(3);
        $kas->saldo = $totalSaldo ?? 0;
        $kas->save();

        return redirect()->route('kas.show', 3)
            ->with('success', 'Detail kas berhasil diperbarui!');
    }
}
