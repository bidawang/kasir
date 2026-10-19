<?php

namespace App\Http\Controllers;

use App\Models\BiayaAdmin;
use App\Models\Kasir;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BiayaAdminController extends Controller
{
    public function index()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $biayaAdmin = BiayaAdmin::where('id_warung', $idWarung)
            ->orderBy('range_awal')
            ->paginate(10);

        return view('biaya_admin.index', compact('biayaAdmin'));
    }

    public function create()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        return view('biaya_admin.create');
    }

    public function store(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $validated = $this->validateBiayaAdmin($request);
        $this->ensureRangeIsAvailable($validated, $idWarung);
        BiayaAdmin::create($validated + ['id_warung' => $idWarung]);

        return redirect()->route('biaya-admin.index')
            ->with('success', 'Biaya admin berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $biayaAdmin = BiayaAdmin::where('id_warung', $idWarung)->findOrFail($id);
        return view('biaya_admin.edit', compact('biayaAdmin'));
    }

    public function update(Request $request, string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $biayaAdmin = BiayaAdmin::where('id_warung', $idWarung)->findOrFail($id);
        $validated = $this->validateBiayaAdmin($request);
        $this->ensureRangeIsAvailable($validated, $idWarung, $biayaAdmin->id);
        $biayaAdmin->update($validated);

        return redirect()->route('biaya-admin.index')
            ->with('success', 'Biaya admin berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        BiayaAdmin::where('id_warung', $idWarung)->findOrFail($id)->delete();

        return redirect()->route('biaya-admin.index')
            ->with('success', 'Biaya admin berhasil dihapus.');
    }

    private function validateBiayaAdmin(Request $request): array
    {
        return $request->validate([
            'range_awal' => 'required|integer|min:0',
            'range_akhir' => 'required|integer|gte:range_awal',
            'biaya_admin' => 'required|integer|min:0',
        ]);
    }

    private function ensureRangeIsAvailable(array $validated, int $idWarung, ?int $exceptId = null): void
    {
        $overlaps = BiayaAdmin::where('id_warung', $idWarung)
            ->where('range_awal', '<=', $validated['range_akhir'])
            ->where('range_akhir', '>=', $validated['range_awal'])
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->exists();

        if ($overlaps) {
            throw ValidationException::withMessages([
                'range_awal' => 'Rentang harga bertumpang tindih dengan tarif yang sudah ada.',
            ]);
        }
    }

    private function activeWarungId(): ?int
    {
        $warung = Kasir::whereKey(session('id_warung'))
            ->when(!in_array(auth()->user()->role, ['admin', 'owner']), fn ($query) => $query->where('id_user', auth()->id()))
            ->first();

        return $warung?->id;
    }
}
