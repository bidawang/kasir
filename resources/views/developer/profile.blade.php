@extends('developer.layouts.app', ['title' => 'Profil Developer', 'breadcrumb' => 'Pengaturan Profil Pengembang'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Profil Pengembang Platform</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Kelola informasi akun developer, email login, nomor handphone, serta kata sandi keamanan Anda.
            </p>
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/30">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            ROLE: DEVELOPER
        </div>
    </div>

    <!-- Form Box -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="{{ route('developer.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Nama Developer -->
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-300 mb-1.5">Nama Lengkap Pengembang <span class="text-rose-400">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $developer->nama) }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            </div>

            <!-- Email & Nomor HP Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-300 mb-1.5">Email Pengembang (Login) <span class="text-rose-400">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $developer->email) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono transition">
                </div>
                <div>
                    <label for="nomor_hp" class="block text-xs font-bold text-slate-300 mb-1.5">Nomor Handphone / WA</label>
                    <input type="text" id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp', $developer->nomor_hp) }}"
                        placeholder="081122334455"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 font-mono transition">
                </div>
            </div>

            <!-- Ganti Password -->
            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 space-y-3 mt-2">
                <span class="text-xs font-bold text-indigo-400 block font-mono">
                    GANTI KATA SANDI (KOSONGKAN JIKA TIDAK INGIN MENGUBAH)
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
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition active:scale-95">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
