<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Kas;
use App\Models\Kasir;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DeveloperController extends Controller
{
    // =========================================================================
    // AUTHENTICATION DEVELOPER
    // =========================================================================

    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'developer') {
                return redirect()->route('developer.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('developer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials['status'] = 'aktif';

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password developer salah, atau akun dinonaktifkan.',
            ]);
        }

        $user = Auth::user();

        // Validasi ketat: Halaman login ini hanya boleh dimasuki oleh role developer
        if ($user->role !== 'developer') {
            Auth::logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            throw ValidationException::withMessages([
                'email' => 'Akses ditolak! Halaman ini dikhususkan untuk Developer Platform. Silakan login di portal utama.',
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return redirect()->intended(route('developer.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('developer.login')->with('success', 'Anda telah berhasil logout dari Developer Console.');
    }

    // =========================================================================
    // DEVELOPER DASHBOARD
    // =========================================================================

    public function dashboard()
    {
        $totalOwners = User::whereIn('role', ['admin', 'owner'])->count();
        $activeOwners = User::whereIn('role', ['admin', 'owner'])->where('status', 'aktif')->count();

        $totalCabangs = Kasir::count();

        $totalKasirs = User::where('role', 'kasir')->count();
        $activeKasirs = User::where('role', 'kasir')->where('status', 'aktif')->count();

        // Data Ringkasan Cabang & Owner Terkini
        $recentOwners = User::whereIn('role', ['admin', 'owner'])
            ->withCount('ownedWarungs')
            ->latest()
            ->take(5)
            ->get();

        $recentCabangs = Kasir::with(['user'])
            ->withCount('users')
            ->latest()
            ->take(5)
            ->get();

        return view('developer.dashboard', compact(
            'totalOwners',
            'activeOwners',
            'totalCabangs',
            'totalKasirs',
            'activeKasirs',
            'recentOwners',
            'recentCabangs'
        ));
    }

    // =========================================================================
    // KELOLA OWNER (PENYEWA PLATFORM)
    // =========================================================================

    public function ownerIndex(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = User::whereIn('role', ['admin', 'owner'])
            ->withCount('ownedWarungs');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_hp', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $owners = $query->latest('id')->paginate(10)->withQueryString();

        return view('developer.owner.index', compact('owners'));
    }

    public function ownerCreate()
    {
        return view('developer.owner.create');
    }

    public function ownerStore(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:user,email',
            'nomor_hp' => 'nullable|string|max:30',
            'username' => 'nullable|string|max:50|unique:user,username',
            'role'     => 'required|in:owner,admin',
            'status'   => 'required|in:aktif,nonaktif',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'username' => $validated['username'] ?? null,
            'role'     => $validated['role'],
            'status'   => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('developer.owner.index')
            ->with('success', 'Akun Owner baru berhasil didaftarkan ke platform.');
    }

    public function ownerShow($id)
    {
        $owner = User::whereIn('role', ['admin', 'owner'])
            ->with(['ownedWarungs' => function ($q) {
                $q->withCount('users');
            }])
            ->findOrFail($id);

        $banks = Bank::where('tipe', 'bank')->where('is_active', true)->orderBy('nama')->get();
        $warungsList = $owner->ownedWarungs;
        $kasirList = User::where('role', 'kasir')
            ->whereHas('warungs', fn($q) => $q->whereIn('kasir.id', $warungsList->pluck('id')))
            ->with(['warungs' => fn($q) => $q->whereIn('kasir.id', $warungsList->pluck('id'))])
            ->latest('id')
            ->get();

        return view('developer.owner.show', compact('owner', 'banks', 'warungsList', 'kasirList'));
    }

    public function ownerEdit($id)
    {
        $owner = User::whereIn('role', ['admin', 'owner'])->findOrFail($id);

        return view('developer.owner.edit', compact('owner'));
    }

    public function ownerUpdate(Request $request, $id)
    {
        $owner = User::whereIn('role', ['admin', 'owner'])->findOrFail($id);

        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('user', 'email')->ignore($owner->id)],
            'nomor_hp' => 'nullable|string|max:30',
            'username' => ['nullable', 'string', 'max:50', Rule::unique('user', 'username')->ignore($owner->id)],
            'role'     => 'required|in:owner,admin',
            'status'   => 'required|in:aktif,nonaktif',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $owner->nama     = $validated['nama'];
        $owner->email    = $validated['email'];
        $owner->nomor_hp = $validated['nomor_hp'] ?? null;
        $owner->username = $validated['username'] ?? null;
        $owner->role     = $validated['role'];
        $owner->status   = $validated['status'];

        if (!empty($validated['password'])) {
            $owner->password = Hash::make($validated['password']);
        }

        $owner->save();

        return redirect()->route('developer.owner.index')
            ->with('success', "Data Owner '{$owner->nama}' berhasil diperbarui.");
    }

    public function ownerToggleStatus($id)
    {
        $owner = User::whereIn('role', ['admin', 'owner'])->findOrFail($id);
        $owner->status = ($owner->status === 'aktif') ? 'nonaktif' : 'aktif';
        $owner->save();

        return back()->with('success', "Status akun '{$owner->nama}' diubah menjadi " . strtoupper($owner->status) . '.');
    }

    public function ownerDestroy($id)
    {
        $owner = User::whereIn('role', ['admin', 'owner'])->findOrFail($id);

        // Cek cabang yang terhubung
        $cabangCount = Kasir::where('id_user', $owner->id)->count();
        if ($cabangCount > 0) {
            return back()->with('error', "Gagal menghapus! Owner '{$owner->nama}' masih memiliki {$cabangCount} cabang aktif. Hapus atau pindahkan cabang terlebih dahulu.");
        }

        $owner->delete();

        return redirect()->route('developer.owner.index')
            ->with('success', "Akun Owner '{$owner->nama}' berhasil dihapus dari sistem.");
    }

    // =========================================================================
    // KELOLA CABANG (WARUNG / TOKO AGEN)
    // =========================================================================

    public function cabangIndex(Request $request)
    {
        $search = $request->input('search');
        $idOwner = $request->input('id_owner');

        $query = Kasir::with(['user'])->withCount('users');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_warung', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('bank_utama', 'like', "%{$search}%")
                  ->orWhere('nomor_rekening_utama', 'like', "%{$search}%");
            });
        }

        if (!empty($idOwner)) {
            $query->where('id_user', $idOwner);
        }

        $cabangs = $query->latest('id')->paginate(10)->withQueryString();
        $owners = User::whereIn('role', ['admin', 'owner'])->orderBy('nama')->get();

        return view('developer.cabang.index', compact('cabangs', 'owners'));
    }

    public function cabangCreate()
    {
        $owners = User::whereIn('role', ['admin', 'owner'])->where('status', 'aktif')->orderBy('nama')->get();
        $banks = Bank::where('tipe', 'bank')->where('is_active', true)->orderBy('nama')->get();

        return view('developer.cabang.create', compact('owners', 'banks'));
    }

    public function cabangStore(Request $request)
    {
        $validated = $request->validate([
            'id_user'              => 'required|exists:user,id',
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
                'id_user'              => $validated['id_user'],
                'nama_warung'          => $validated['nama_warung'],
                'alamat'               => $validated['alamat'] ?? null,
                'no_telepon'           => $validated['no_telepon'] ?? null,
                'bank_utama'           => $bankUtama,
                'nomor_rekening_utama' => $validated['nomor_rekening_utama'] ?? null,
                'atas_nama_utama'      => $validated['atas_nama_utama'] ?? null,
                'modal'                => 0,
            ]);

            // 1. Kas Fisik Tunai default
            $kas = $warung->kas()->create([
                'jenis_kas'  => 'Kas Tunai',
                'status'     => 'cash',
                'saldo'      => 0,
                'is_primary' => true,
            ]);

            $kas->detailKas()->createMany(
                array_map(
                    fn (int $pecahan): array => ['pecahan' => (string) $pecahan, 'jumlah' => 0],
                    \App\Models\DetailKas::DENOMINATIONS
                )
            );

            // 2. Rekening Utama Bank default
            $warung->kas()->create([
                'jenis_kas'      => 'Rekening ' . $bankUtama . ' Utama',
                'status'         => 'bank',
                'nomor_rekening' => $noRekening,
                'atas_nama'      => $atasNama,
                'saldo'          => 0,
                'is_primary'     => true,
            ]);
        });

        if ($request->filled('redirect_to') && $request->input('redirect_to') === 'owner.show') {
            return redirect()->route('developer.owner.show', $validated['id_user'])
                ->with('success', "Cabang '{$validated['nama_warung']}' berhasil didaftarkan untuk Owner ini.");
        }

        return redirect()->route('developer.cabang.index')
            ->with('success', "Cabang '{$validated['nama_warung']}' berhasil didaftarkan beserta akun kas default.");
    }

    public function cabangShow($id)
    {
        $cabang = Kasir::with(['user', 'users'])->findOrFail($id);
        $kasirs = $cabang->users()->latest('user.id')->get();

        return view('developer.cabang.show', compact('cabang', 'kasirs'));
    }

    public function cabangEdit($id)
    {
        $cabang = Kasir::with('user')->findOrFail($id);
        $owners = User::whereIn('role', ['admin', 'owner'])->orderBy('nama')->get();
        $banks = Bank::where('tipe', 'bank')->where('is_active', true)->orderBy('nama')->get();

        return view('developer.cabang.edit', compact('cabang', 'owners', 'banks'));
    }

    public function cabangUpdate(Request $request, $id)
    {
        $cabang = Kasir::findOrFail($id);

        $validated = $request->validate([
            'id_user'              => 'required|exists:user,id',
            'nama_warung'          => 'required|string|max:255',
            'alamat'               => 'nullable|string|max:500',
            'no_telepon'           => 'nullable|string|max:30',
            'bank_utama'           => 'required|string|max:50',
            'nomor_rekening_utama' => 'nullable|string|max:50',
            'atas_nama_utama'      => 'nullable|string|max:100',
        ]);

        $cabang->update([
            'id_user'              => $validated['id_user'],
            'nama_warung'          => $validated['nama_warung'],
            'alamat'               => $validated['alamat'] ?? null,
            'no_telepon'           => $validated['no_telepon'] ?? null,
            'bank_utama'           => strtoupper($validated['bank_utama']),
            'nomor_rekening_utama' => $validated['nomor_rekening_utama'] ?? null,
            'atas_nama_utama'      => $validated['atas_nama_utama'] ?? null,
        ]);

        return redirect()->route('developer.cabang.index')
            ->with('success', "Informasi cabang '{$cabang->nama_warung}' berhasil diperbarui.");
    }

    public function cabangDestroy($id)
    {
        $cabang = Kasir::findOrFail($id);
        $nama = $cabang->nama_warung;

        DB::transaction(function () use ($cabang) {
            // Detach kasir
            $cabang->users()->detach();
            // Hapus kas terkait (jika ada)
            foreach ($cabang->kas as $k) {
                $k->detailKas()->delete();
                $k->delete();
            }
            $cabang->delete();
        });

        return redirect()->route('developer.cabang.index')
            ->with('success', "Cabang '{$nama}' dan akun kas terkait berhasil dihapus.");
    }

    // =========================================================================
    // KELOLA KASIR (USERS DENGAN ROLE KASIR)
    // =========================================================================

    public function kasirIndex(Request $request)
    {
        $search = $request->input('search');
        $idCabang = $request->input('id_cabang');
        $status = $request->input('status');

        $query = User::where('role', 'kasir')
            ->with(['warungs.user']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_hp', 'like', "%{$search}%");
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($idCabang)) {
            $query->whereHas('warungs', fn ($w) => $w->where('kasir.id', $idCabang));
        }

        $kasirs = $query->latest('id')->paginate(10)->withQueryString();
        $cabangs = Kasir::orderBy('nama_warung')->get();

        return view('developer.kasir.index', compact('kasirs', 'cabangs'));
    }

    public function kasirCreate()
    {
        $cabangs = Kasir::with('user')->orderBy('nama_warung')->get();

        return view('developer.kasir.create', compact('cabangs'));
    }

    public function kasirStore(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:user,email',
            'nomor_hp' => 'nullable|string|max:30',
            'id_kasir' => 'required|exists:kasir,id',
            'status'   => 'required|in:aktif,nonaktif',
            'password' => 'required|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'nama'     => $validated['nama'],
                'email'    => $validated['email'],
                'nomor_hp' => $validated['nomor_hp'] ?? null,
                'username' => null,
                'role'     => 'kasir',
                'status'   => $validated['status'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->warungs()->attach($validated['id_kasir']);
        });

        if ($request->filled('redirect_to')) {
            if ($request->input('redirect_to') === 'owner.show' && $request->filled('redirect_owner_id')) {
                return redirect()->route('developer.owner.show', $request->input('redirect_owner_id'))
                    ->with('success', "Kasir '{$validated['nama']}' berhasil dibuat dan ditugaskan.");
            }
            if ($request->input('redirect_to') === 'cabang.show') {
                return redirect()->route('developer.cabang.show', $validated['id_kasir'])
                    ->with('success', "Kasir '{$validated['nama']}' berhasil dibuat dan ditugaskan pada cabang ini.");
            }
        }

        return redirect()->route('developer.kasir.index')
            ->with('success', 'Akun Kasir berhasil dibuat dan ditempatkan pada cabang.');
    }

    public function kasirEdit($id)
    {
        $kasir = User::where('role', 'kasir')->with('warungs')->findOrFail($id);
        $cabangs = Kasir::with('user')->orderBy('nama_warung')->get();

        return view('developer.kasir.edit', compact('kasir', 'cabangs'));
    }

    public function kasirUpdate(Request $request, $id)
    {
        $kasir = User::where('role', 'kasir')->findOrFail($id);

        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('user', 'email')->ignore($kasir->id)],
            'nomor_hp' => 'nullable|string|max:30',
            'id_kasir' => 'required|exists:kasir,id',
            'status'   => 'required|in:aktif,nonaktif',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($kasir, $validated) {
            $kasir->nama     = $validated['nama'];
            $kasir->email    = $validated['email'];
            $kasir->nomor_hp = $validated['nomor_hp'] ?? null;
            $kasir->status   = $validated['status'];

            if (!empty($validated['password'])) {
                $kasir->password = Hash::make($validated['password']);
            }

            $kasir->save();

            // Sinkronisasi cabang kasir
            $kasir->warungs()->sync([$validated['id_kasir']]);
        });

        return redirect()->route('developer.kasir.index')
            ->with('success', "Akun Kasir '{$kasir->nama}' berhasil diperbarui.");
    }

    public function kasirToggleStatus($id)
    {
        $kasir = User::where('role', 'kasir')->findOrFail($id);
        $kasir->status = ($kasir->status === 'aktif') ? 'nonaktif' : 'aktif';
        $kasir->save();

        return back()->with('success', "Status kasir '{$kasir->nama}' diubah menjadi " . strtoupper($kasir->status) . '.');
    }

    public function kasirDestroy($id)
    {
        $kasir = User::where('role', 'kasir')->findOrFail($id);
        $nama = $kasir->nama;

        DB::transaction(function () use ($kasir) {
            $kasir->warungs()->detach();
            $kasir->delete();
        });

        return redirect()->route('developer.kasir.index')
            ->with('success', "Akun kasir '{$nama}' berhasil dihapus dari sistem.");
    }

    // =========================================================================
    // PROFIL DEVELOPER
    // =========================================================================

    public function profile()
    {
        $developer = Auth::user();

        return view('developer.profile', compact('developer'));
    }

    public function profileUpdate(Request $request)
    {
        $developer = Auth::user();

        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('user', 'email')->ignore($developer->id)],
            'nomor_hp' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $developer->nama     = $validated['nama'];
        $developer->email    = $validated['email'];
        $developer->nomor_hp = $validated['nomor_hp'] ?? null;

        if (!empty($validated['password'])) {
            $developer->password = Hash::make($validated['password']);
        }

        $developer->save();

        return back()->with('success', 'Profil Developer berhasil diperbarui.');
    }
}
