@extends('developer.layouts.app', ['title' => 'Edit Owner', 'breadcrumb' => 'Edit Owner ' . $owner->nama])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Edit Akun Owner</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Perbarui identitas, status akses, atau kata sandi untuk pemilik <strong>{{ $owner->nama }}</strong>.
            </p>
        </div>
        <a href="{{ route('developer.owner.index') }}" class="text-xs sm:text-sm text-slate-400 hover:text-white font-bold flex items-center gap-1 transition">
            ← Kembali
        </a>
    </div>

    <!-- Form Box -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="{{ route('developer.owner.update', $owner->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-300 mb-1.5">Nama Lengkap Owner <span class="text-rose-400">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $owner->nama) }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <!-- Email & Nomor HP Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-300 mb-1.5">Email Pengguna <span class="text-rose-400">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $owner->email) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono transition">
                </div>
                <div>
                    <label for="nomor_hp" class="block text-xs font-bold text-slate-300 mb-1.5">Nomor Handphone / WhatsApp</label>
                    <input type="text" id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp', $owner->nomor_hp) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono transition">
                </div>
            </div>

            <!-- Username & Role Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-300 mb-1.5">Username <span class="text-slate-500 font-normal">(opsional)</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username', $owner->username) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono transition">
                </div>
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-300 mb-1.5">Tipe Hak Akses <span class="text-rose-400">*</span></label>
                    <select id="role" name="role" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                        <option value="owner" {{ old('role', $owner->role) === 'owner' ? 'selected' : '' }}>Owner (Pemilik Toko/Agen)</option>
                        <option value="admin" {{ old('role', $owner->role) === 'admin' ? 'selected' : '' }}>Admin (Pengelola Utama Cabang)</option>
                    </select>
                </div>
            </div>

            <!-- Status Akun -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-300 mb-1.5">Status Akun <span class="text-rose-400">*</span></label>
                <select id="status" name="status" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-indigo-500 transition">
                    <option value="aktif" {{ old('status', $owner->status) === 'aktif' ? 'selected' : '' }}>Aktif (Diberikan Akses Login)</option>
                    <option value="nonaktif" {{ old('status', $owner->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Akses Dinonaktifkan)</option>
                </select>
            </div>

            <!-- Ubah Password (Opsional) -->
            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 space-y-3">
                <span class="text-xs font-bold text-indigo-400 block font-mono">
                    UBAH KATA SANDI (KOSONGKAN JIKA TIDAK INGIN DIUBAH)
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-300 mb-1">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" minlength="6"
                            placeholder="Minimal 6 karakter"
                            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 font-mono transition">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-slate-300 mb-1">Ulangi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" minlength="6"
                            placeholder="Ketik ulang sandi baru"
                            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 font-mono transition">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <a href="{{ route('developer.owner.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-900 text-xs sm:text-sm font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition active:scale-95">
                    Perbarui Akun Owner
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
