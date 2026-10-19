<?php

namespace App\Http\Controllers;

use App\Models\ItemJualan;
use App\Models\Kasir;
use Illuminate\Http\Request;

class ItemJualanController extends Controller
{
    public function index()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $itemJualan = ItemJualan::where('id_warung', $idWarung)
            ->orderBy('nama_item')
            ->paginate(10);

        return view('item_jualan.index', compact('itemJualan'));
    }

    public function create()
    {
        if (!$this->activeWarungId()) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        return view('item_jualan.create');
    }

    public function store(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'harga_jual' => 'required|integer|min:0',
        ]);

        ItemJualan::create($validated + ['id_warung' => $idWarung]);

        return redirect()->route('item-jualan.index')
            ->with('success', 'Item jualan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $itemJualan = ItemJualan::where('id_warung', $idWarung)->findOrFail($id);

        return view('item_jualan.edit', compact('itemJualan'));
    }

    public function update(Request $request, string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $itemJualan = ItemJualan::where('id_warung', $idWarung)->findOrFail($id);
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'harga_jual' => 'required|integer|min:0',
        ]);

        $itemJualan->update($validated);

        return redirect()->route('item-jualan.index')
            ->with('success', 'Item jualan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        ItemJualan::where('id_warung', $idWarung)->findOrFail($id)->delete();

        return redirect()->route('item-jualan.index')
            ->with('success', 'Item jualan berhasil dihapus.');
    }

    private function activeWarungId(): ?int
    {
        $warung = Kasir::whereKey(session('id_warung'))
            ->when(!in_array(auth()->user()->role, ['admin', 'owner']), fn ($query) => $query->where('id_user', auth()->id()))
            ->first();

        return $warung?->id;
    }
}
