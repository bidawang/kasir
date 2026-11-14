<?php

namespace App\Http\Controllers;

use App\Models\BiayaAdmin;
use App\Models\Kas;
use App\Models\Kasir;
use App\Models\KategoriTransaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BiayaAdminController extends Controller
{
    public function index(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $warung = Kasir::find($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $query = BiayaAdmin::where('id_warung', $idWarung)
            ->with(['kategori', 'kas']);

        if ($request->filled('id_kategori')) {
            $query->where('id_kategori', $request->id_kategori);
        }

        $biayaAdmin = $query
            ->orderBy('id_kategori')
            ->orderBy('range_awal')
            ->paginate(15)
            ->withQueryString();

        $kategoriList = KategoriTransaksi::where('is_active', true)->orderBy('urutan')->get();

        return view('biaya_admin.index', compact('biayaAdmin', 'kategoriList', 'bankUtama'));
    }

    public function create()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $warung = Kasir::find($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $kategoriList = KategoriTransaksi::where('is_active', true)->orderBy('urutan')->get();

        $bankList = Kas::where('id_warung', $idWarung)
            ->whereIn('status', ['bank', 'ewallet'])
            ->orderBy('is_primary', 'desc')
            ->orderBy('jenis_kas')
            ->get(['id', 'jenis_kas']);

        return view('biaya_admin.create', compact('kategoriList', 'bankList', 'bankUtama'));
    }

    public function store(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $validated = $this->validateBiayaAdmin($request, $idWarung);
        $this->ensureRangeIsAvailable($validated, $idWarung);

        BiayaAdmin::create($validated + ['id_warung' => $idWarung]);

        return redirect()->route('biaya-admin.index')
            ->with('success', 'Tarif biaya admin berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $warung = Kasir::find($idWarung);
        $bankUtama = $warung->bank_utama ?? 'BRI';

        $biayaAdmin = BiayaAdmin::where('id_warung', $idWarung)->findOrFail($id);
        $kategoriList = KategoriTransaksi::where('is_active', true)->orderBy('urutan')->get();

        $bankList = Kas::where('id_warung', $idWarung)
            ->whereIn('status', ['bank', 'ewallet'])
            ->orderBy('is_primary', 'desc')
            ->orderBy('jenis_kas')
            ->get(['id', 'jenis_kas']);

        return view('biaya_admin.edit', compact('biayaAdmin', 'kategoriList', 'bankList', 'bankUtama'));
    }

    public function update(Request $request, string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $biayaAdmin = BiayaAdmin::where('id_warung', $idWarung)->findOrFail($id);
        $validated = $this->validateBiayaAdmin($request, $idWarung);
        $this->ensureRangeIsAvailable($validated, $idWarung, $biayaAdmin->id);

        $biayaAdmin->update($validated);

        return redirect()->route('biaya-admin.index')
            ->with('success', 'Tarif biaya admin berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        BiayaAdmin::where('id_warung', $idWarung)->findOrFail($id)->delete();

        return redirect()->route('biaya-admin.index')
            ->with('success', 'Tarif biaya admin berhasil dihapus.');
    }

    private function validateBiayaAdmin(Request $request, int $idWarung): array
    {
        foreach (['range_awal', 'range_akhir', 'biaya_admin', 'biaya_admin_bank'] as $field) {
            if ($request->filled($field)) {
                $cleaned = preg_replace('/[^0-9]/', '', (string) $request->$field);
                $request->merge([$field => $cleaned !== '' ? (int) $cleaned : 0]);
            }
        }

        $validated = $request->validate([
            'id_kategori' => 'required|integer|exists:kategori_transaksi,id',
            'id_kas' => 'nullable|integer|exists:kas,id',
            'range_awal' => 'required|integer|min:0',
            'range_akhir' => 'required|integer|gte:range_awal',
            'biaya_admin' => 'required|integer|min:0',
            'biaya_admin_bank' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['id_kas'])) {
            Kas::where('id_warung', $idWarung)
                ->whereIn('status', ['bank', 'ewallet'])
                ->findOrFail($validated['id_kas']);
        }

        $validated['biaya_admin_bank'] = (int) ($validated['biaya_admin_bank'] ?? 0);

        return $validated;
    }

    private function ensureRangeIsAvailable(array $validated, int $idWarung, ?int $exceptId = null): void
    {
        $overlaps = BiayaAdmin::where('id_warung', $idWarung)
            ->where('id_kategori', $validated['id_kategori'])
            ->when(
                $validated['id_kas'] ?? null,
                fn ($query, $idKas) => $query->where('id_kas', $idKas),
                fn ($query) => $query->whereNull('id_kas')
            )
            ->where('range_awal', '<=', $validated['range_akhir'])
            ->where('range_akhir', '>=', $validated['range_awal'])
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->exists();

        if ($overlaps) {
            throw ValidationException::withMessages([
                'range_awal' => 'Rentang nominal bertumpang tindih dengan tarif yang sudah ada untuk kategori ini.',
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
