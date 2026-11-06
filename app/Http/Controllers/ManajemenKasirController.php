<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManajemenKasirController extends Controller
{
    public function index()
    {
        $warungIds = $this->warungIds();
        $kasir = User::where('role', 'kasir')
            ->whereHas('warungs', fn ($query) => $query->whereIn('kasir.id', $warungIds))
            ->with('warungs')
            ->orderBy('nama')
            ->get();

        return view('manajemen_kasir.index', compact('kasir'));
    }

    public function create()
    {
        $warungs = $this->warungs();

        return view('manajemen_kasir.create', compact('warungs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:user,email',
            'nomor_hp' => 'nullable|string|max:30',
            'id_kasir' => [
                'required',
                'integer',
                Rule::exists('kasir', 'id')->where('id_user', auth()->id()),
            ],
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'nomor_hp' => $validated['nomor_hp'] ?? null,
                'username' => null,
                'role' => 'kasir',
                'status' => $validated['status'],
                'password' => Hash::make($validated['password']),
            ]);
            $user->warungs()->attach($validated['id_kasir']);
        });

        return redirect()->route('manajemen-kasir.index')
            ->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    public function edit(User $manajemenKasir)
    {
        $manajemenKasir = $this->ownedCashier($manajemenKasir);
        $warungs = $this->warungs();

        return view('manajemen_kasir.edit', compact('manajemenKasir', 'warungs'));
    }

    public function update(Request $request, User $manajemenKasir)
    {
        $manajemenKasir = $this->ownedCashier($manajemenKasir);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('user', 'email')->ignore($manajemenKasir->id)],
            'nomor_hp' => 'nullable|string|max:30',
            'id_kasir' => [
                'required',
                'integer',
                Rule::exists('kasir', 'id')->where('id_user', auth()->id()),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $updates = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'status' => $validated['status'],
        ];
        if (! empty($validated['password'])) {
            $updates['password'] = Hash::make($validated['password']);
        }

        DB::transaction(function () use ($manajemenKasir, $updates, $validated) {
            $manajemenKasir->update($updates);
            $manajemenKasir->warungs()->sync([$validated['id_kasir']]);
        });

        return redirect()->route('manajemen-kasir.index')
            ->with('success', 'Akun kasir berhasil diperbarui.');
    }

    public function destroy(User $manajemenKasir)
    {
        $this->ownedCashier($manajemenKasir)->delete();

        return redirect()->route('manajemen-kasir.index')
            ->with('success', 'Akun kasir berhasil dihapus.');
    }

    private function warungs()
    {
        return Kasir::where('id_user', auth()->id())->orderBy('nama_warung')->get();
    }

    private function warungIds()
    {
        return $this->warungs()->modelKeys();
    }

    private function ownedCashier(User $user): User
    {
        abort_unless(
            $user->role === 'kasir'
                && $user->warungs()->whereIn('kasir.id', $this->warungIds())->exists(),
            404
        );

        return $user;
    }
}
