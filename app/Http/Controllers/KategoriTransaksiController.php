<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\KategoriTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KategoriTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $idWarung = session('id_warung');
        $warung = $idWarung ? Kasir::find($idWarung) : null;
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $query = KategoriTransaksi::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_aliran')) {
            $query->where('jenis_aliran', $request->jenis_aliran);
        }

        $kategoriList = $query->orderBy('urutan')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('kategori_transaksi.index', compact('kategoriList', 'bankUtama'));
    }

    public function create()
    {
        $idWarung = session('id_warung');
        $warung = $idWarung ? Kasir::find($idWarung) : null;
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $maxUrutan = KategoriTransaksi::max('urutan') ?? 0;
        $nextUrutan = $maxUrutan + 1;

        return view('kategori_transaksi.create', compact('bankUtama', 'nextUrutan'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'kode' => Str::slug($request->kode, '_'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $validated = $request->validate([
            'kode'             => 'required|string|max:50|unique:kategori_transaksi,kode',
            'nama_kategori'    => 'required|string|max:100',
            'jenis_transaksi'  => 'required|string|max:30',
            'metode_transaksi' => 'required|string|max:30',
            'jenis_aliran'     => 'required|in:masuk,keluar',
            'deskripsi'        => 'nullable|string|max:255',
            'icon'             => 'nullable|string|max:50',
            'badge_color'      => 'required|string|max:50',
            'urutan'           => 'required|integer|min:0',
            'is_active'        => 'required|boolean',
        ]);

        KategoriTransaksi::create($validated);

        return redirect()->route('kategori-transaksi.index')
            ->with('success', 'Kategori transaksi berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $idWarung = session('id_warung');
        $warung = $idWarung ? Kasir::find($idWarung) : null;
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $kategori = KategoriTransaksi::findOrFail($id);

        return view('kategori_transaksi.edit', compact('kategori', 'bankUtama'));
    }

    public function update(Request $request, string $id)
    {
        $kategori = KategoriTransaksi::findOrFail($id);

        $request->merge([
            'kode' => Str::slug($request->kode, '_'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $validated = $request->validate([
            'kode'             => ['required', 'string', 'max:50', Rule::unique('kategori_transaksi', 'kode')->ignore($kategori->id)],
            'nama_kategori'    => 'required|string|max:100',
            'jenis_transaksi'  => 'required|string|max:30',
            'metode_transaksi' => 'required|string|max:30',
            'jenis_aliran'     => 'required|in:masuk,keluar',
            'deskripsi'        => 'nullable|string|max:255',
            'icon'             => 'nullable|string|max:50',
            'badge_color'      => 'required|string|max:50',
            'urutan'           => 'required|integer|min:0',
            'is_active'        => 'required|boolean',
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori-transaksi.index')
            ->with('success', 'Kategori transaksi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $kategori = KategoriTransaksi::findOrFail($id);

        // Cek apakah ada transaksi yang memakai kategori ini
        if ($kategori->transaksi()->exists()) {
            return redirect()->route('kategori-transaksi.index')
                ->with('error', 'Kategori ini tidak dapat dihapus karena sudah memiliki riwayat transaksi. Anda dapat menonaktifkannya saja.');
        }

        // Hapus biaya admin terkait jika ada
        $kategori->biayaAdmin()->delete();
        $kategori->delete();

        return redirect()->route('kategori-transaksi.index')
            ->with('success', 'Kategori transaksi berhasil dihapus.');
    }
}
