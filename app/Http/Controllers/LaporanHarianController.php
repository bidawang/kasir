<?php

namespace App\Http\Controllers;

use App\Models\DetailKas;
use App\Models\Kas;
use App\Models\Kasir;
use App\Models\LaporanHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LaporanHarianController extends Controller
{
    public function index()
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $isOwner = in_array(auth()->user()->role, ['admin', 'owner'], true);
        $laporanHariIni = LaporanHarian::where('id_warung', $idWarung)
            ->where('id_user', auth()->id())
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        $laporan = LaporanHarian::with(['user', 'pelapor'])
            ->where('id_warung', $idWarung)
            ->when(!$isOwner, fn ($query) => $query->where('id_user', auth()->id()))
            ->orderByDesc('tanggal')
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get();

        $karyawan = collect();
        $laporanHarianKaryawan = collect();
        if ($isOwner) {
            $karyawan = $this->employeesForWarung($idWarung);
            if ($karyawan->isNotEmpty()) {
                $laporanHarianKaryawan = LaporanHarian::where('id_warung', $idWarung)
                    ->whereDate('tanggal', now()->toDateString())
                    ->whereIn('id_user', $karyawan->modelKeys())
                    ->get()
                    ->keyBy('id_user');
            }
        }

        $saldoSistem = (int) Kas::where('id_warung', $idWarung)
            ->where('status', 'cash')
            ->sum('saldo');

        return view('laporan_harian.index', compact(
            'isOwner',
            'laporanHariIni',
            'laporan',
            'karyawan',
            'laporanHarianKaryawan',
            'saldoSistem'
        ));
    }

    public function store(Request $request)
    {
        $idWarung = $this->activeWarungId();
        if (!$idWarung) {
            return redirect()->route('cabang.index')
                ->with('error', 'Silakan pilih cabang terlebih dahulu.');
        }

        $isOwner = in_array(auth()->user()->role, ['admin', 'owner'], true);
        $jumlahRules = [
            'jumlah' => 'required|array:' . implode(',', DetailKas::DENOMINATIONS),
            'catatan' => 'nullable|string|max:2000',
            'id_user' => $isOwner ? 'nullable|integer|exists:user,id' : 'prohibited',
        ];
        foreach (DetailKas::DENOMINATIONS as $pecahan) {
            $jumlahRules['jumlah.' . $pecahan] = 'required|integer|min:0';
        }
        $validated = $request->validate($jumlahRules);

        $rincianFisik = [];
        $saldoFisik = 0;
        foreach (DetailKas::DENOMINATIONS as $pecahan) {
            $jumlah = (int) $validated['jumlah'][$pecahan];
            
            if ($jumlah > intdiv(PHP_INT_MAX - $saldoFisik, $pecahan)) {
                throw ValidationException::withMessages([
                    'jumlah.' . $pecahan => 'Jumlah lembar terlalu besar untuk dihitung.',
                ]);
            }

            $rincianFisik[$pecahan] = $jumlah;
            $saldoFisik += $pecahan * $jumlah;
        }

        $idUser = auth()->id();
        if ($isOwner && !empty($validated['id_user'])) {
            if (!$this->employeesForWarung($idWarung)->contains('id', (int) $validated['id_user'])) {
                throw ValidationException::withMessages([
                    'id_user' => 'Karyawan tidak terdaftar pada cabang aktif.',
                ]);
            }
            $idUser = (int) $validated['id_user'];
        }

        $tanggal = now()->toDateString();
        $saldoSistem = (int) Kas::where('id_warung', $idWarung)
            ->where('status', 'cash')
            ->sum('saldo');

        // Pastikan variabel $rincianFisik dan $saldoFisik dimasukkan ke dalam use(...)
        DB::transaction(function () use ($idWarung, $idUser, $tanggal, $validated, $saldoSistem, $isOwner, $rincianFisik, $saldoFisik) {
            $laporan = LaporanHarian::where('id_warung', $idWarung)
                ->where('id_user', $idUser)
                ->whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->first();

            if ($laporan && !$isOwner) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Laporan harian hanya dapat dikirim satu kali. Hubungi owner jika perlu koreksi.',
                ]);
            }

            LaporanHarian::updateOrCreate(
                [
                    'id_warung' => $idWarung,
                    'id_user' => $idUser,
                    'tanggal' => $tanggal,
                ],
                [
                    'id_pelapor' => auth()->id(),
                    'rincian_fisik' => $rincianFisik,
                    'saldo_fisik' => $saldoFisik,
                    'saldo_sistem' => $saldoSistem,
                    'selisih' => $saldoFisik - $saldoSistem,
                    'catatan' => $validated['catatan'] ?? null,
                ]
            );
        });

        return redirect()->route('laporan-harian.index')
            ->with('success', $isOwner
                ? 'Laporan harian berhasil disimpan atau dikoreksi.'
                : 'Laporan harian berhasil dikirim.');
    }

    private function employeesForWarung(int $idWarung)
    {
        $warung = Kasir::with(['user', 'users'])->findOrFail($idWarung);
        $employees = $warung->users->where('role', 'kasir');
        if ($warung->user?->role === 'kasir') {
            $employees->push($warung->user);
        }

        return $employees->unique('id')->values();
    }

    private function activeWarungId(): ?int
    {
        return Kasir::whereKey(session('id_warung'))
            ->when(auth()->user()->role === 'kasir', function ($query) {
                $query->where(function ($query) {
                    $query->where('id_user', auth()->id())
                        ->orWhereHas('users', fn ($users) => $users->where('user.id', auth()->id()));
                });
            })
            ->when(!in_array(auth()->user()->role, ['admin', 'owner', 'kasir'], true), fn ($query) => $query->where('id_user', auth()->id()))
            ->value('id');
    }
}