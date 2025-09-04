<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi dengan pagination dan filter.
     */
    public function index(Request $request)
    {
        $query = Transaksi::latest();

        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        $transaksi = $query->paginate(10);

        return view('transaksi.index', compact('transaksi'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        return view('transaksi.create');
    }

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'total' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);
        Transaksi::create(array_merge($request->all(), [
            'id_kas_warung' => 1
        ]));


        // Logika untuk memperbarui saldo kasir
        $kasir = Kasir::first();
        if ($kasir) {
            $saldo = $kasir->kas->saldo ?? 0;
            if ($request->jenis === 'masuk') {
                $saldo += $request->total;
            } else {
                $saldo -= $request->total;
            }
            $kasir->kas->saldo = $saldo;
            $kasir->kas->save();
        }

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }
}
