<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Kasir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Jika user adalah admin, ambil semua data cabang
        if ($user->role === 'admin') {
            $warungs = Kasir::orderBy('id')->get();
        } else {
            // Jika bukan admin, hanya ambil cabang milik user yang login
            $warungs = Kasir::where('id_user', $user->id)
                ->orderBy('id')
                ->get();
        }

        return view('cabang.index', compact('warungs'));
    }

    public function create()
    {
        $banks = Bank::where('tipe', 'bank')->where('is_active', true)->orderBy('nama')->get();
        return view('cabang.create', compact('banks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_warung'          => 'required|string|max:255',
            'alamat'               => 'nullable|string|max:500',
            'no_telepon'           => 'nullable|string|max:30',
            'bank_utama'           => 'required|string|max:50',
            'nomor_rekening_utama' => 'nullable|string|max:50',
            'atas_nama_utama'      => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            $bankUtama = strtoupper($validated['bank_utama']);
            $noRekening = !empty($validated['nomor_rekening_utama']) ? trim($validated['nomor_rekening_utama']) : '-';
            $atasNama = !empty($validated['atas_nama_utama']) ? trim($validated['atas_nama_utama']) : $validated['nama_warung'];

            $warung = Kasir::create([
                'id_user'              => auth()->id(),
                'nama_warung'          => $validated['nama_warung'],
                'alamat'               => $validated['alamat'] ?? null,
                'no_telepon'           => $validated['no_telepon'] ?? null,
                'bank_utama'           => $bankUtama,
                'nomor_rekening_utama' => $validated['nomor_rekening_utama'] ?? null,
                'atas_nama_utama'      => $validated['atas_nama_utama'] ?? null,
                'modal'                => 0,
            ]);

            // 1. OTOMATIS Buat Kas Fisik Tunai default
            $kas = $warung->kas()->create([
                'jenis_kas'      => 'Kas Tunai',
                'status'         => 'cash',
                'saldo'          => 0,
                'is_primary'     => true,
            ]);

            $kas->detailKas()->createMany(
                array_map(
                    fn (int $pecahan): array => ['pecahan' => (string) $pecahan, 'jumlah' => 0],
                    \App\Models\DetailKas::DENOMINATIONS
                )
            );

            // 2. OTOMATIS Buat Rekening Utama Bank
            $warung->kas()->create([
                'jenis_kas'      => 'Rekening ' . $bankUtama . ' Utama',
                'status'         => 'bank',
                'nomor_rekening' => $noRekening,
                'atas_nama'      => $atasNama,
                'saldo'          => 0,
                'is_primary'     => true,
            ]);
        });

        return redirect()
            ->route('cabang.index')
            ->with('success', 'Cabang baru dan Rekening Utama berhasil dibuat otomatis.');
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

        $banks = Bank::where('tipe', 'bank')->where('is_active', true)->orderBy('nama')->get();

        return view('cabang.edit', compact('warung', 'banks'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_warung'          => 'required|string|max:255',
            'alamat'               => 'nullable|string|max:500',
            'no_telepon'           => 'nullable|string|max:30',
            'bank_utama'           => 'required|string|max:50',
            'nomor_rekening_utama' => 'nullable|string|max:50',
            'atas_nama_utama'      => 'nullable|string|max:100',
        ]);

        $warung = Kasir::where('id', $id)
            ->where('id_user', auth()->id())
            ->firstOrFail();

        $bankUtama = strtoupper($validated['bank_utama']);
        $noRekening = !empty($validated['nomor_rekening_utama']) ? trim($validated['nomor_rekening_utama']) : '-';
        $atasNama = !empty($validated['atas_nama_utama']) ? trim($validated['atas_nama_utama']) : $validated['nama_warung'];

        DB::transaction(function () use ($warung, $validated, $bankUtama, $noRekening, $atasNama) {
            $warung->update([
                'nama_warung'          => $validated['nama_warung'],
                'alamat'               => $validated['alamat'] ?? null,
                'no_telepon'           => $validated['no_telepon'] ?? null,
                'bank_utama'           => $bankUtama,
                'nomor_rekening_utama' => $validated['nomor_rekening_utama'] ?? null,
                'atas_nama_utama'      => $validated['atas_nama_utama'] ?? null,
            ]);

            // Sinkronkan atau buat Rekening Utama di kas
            $primaryBankKas = $warung->kas()->where('status', 'bank')->where('is_primary', true)->first();
            if ($primaryBankKas) {
                $primaryBankKas->update([
                    'jenis_kas'      => 'Rekening ' . $bankUtama . ' Utama',
                    'nomor_rekening' => $noRekening,
                    'atas_nama'      => $atasNama,
                ]);
            } else {
                $warung->kas()->create([
                    'jenis_kas'      => 'Rekening ' . $bankUtama . ' Utama',
                    'status'         => 'bank',
                    'nomor_rekening' => $noRekening,
                    'atas_nama'      => $atasNama,
                    'saldo'          => 0,
                    'is_primary'     => true,
                ]);
            }
        });

        return redirect()
            ->route('cabang.index')
            ->with('success', 'Cabang dan Rekening Utama berhasil diperbarui.');
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
