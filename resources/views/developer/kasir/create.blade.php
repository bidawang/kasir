@extends('developer.layouts.app', ['title' => 'Tambah Kasir Baru', 'breadcrumb' => 'Tambah Kasir Baru'])

@section('content')
@php
    $cabangOptions = $cabangs->map(fn($c) => [
        'value' => $c->id,
        'label' => $c->nama_warung,
        'sub'   => 'Owner: ' . ($c->user->nama ?? '-') . ' &bull; Bank: ' . $c->bank_utama
    ])->toArray();
@endphp

<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Daftarkan Kasir Baru</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Buat akun kasir pelaksana dan tempatkan langsung ke cabang yang diinginkan.
            </p>
        </div>
        <a href="{{ route('developer.kasir.index') }}" class="text-xs sm:text-sm text-slate-400 hover:text-white font-bold flex items-center gap-1 transition">
            ← Kembali
        </a>
    </div>

    <!-- Form Box -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="{{ route('developer.kasir.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Kasir -->
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-300 mb-1.5">Nama Lengkap Kasir <span class="text-rose-400">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                    placeholder="Contoh: Rina Novita"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition">
            </div>

            <!-- Email & Nomor HP Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-300 mb-1.5">Email Kasir <span class="text-rose-400">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="kasir.rina@gmail.com"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 font-mono transition">
                </div>
                <div>
                    <label for="nomor_hp" class="block text-xs font-bold text-slate-300 mb-1.5">Nomor Handphone / WA</label>
                    <input type="text" id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp') }}"
                        placeholder="08123456789"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 font-mono transition">
                </div>
            </div>

            <!-- Cabang Penempatan dengan Searchable Select -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Cabang Penempatan (Cari &amp; Pilih) <span class="text-rose-400">*</span></label>
                @include('developer.components.searchable-select', [
                    'name' => 'id_kasir',
                    'options' => $cabangOptions,
                    'selected' => old('id_kasir', request('id_cabang', '')),
                    'placeholder' => '-- Cari & Pilih Cabang Penempatan --',
                    'required' => true,
                ])
            </div>

            <!-- Status Akun -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-300 mb-1.5">Status Akun Kasir <span class="text-rose-400">*</span></label>
                <select id="status" name="status" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                    <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif (Bisa Langsung Melayani Transaksi)</option>
                    <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Akses Dinonaktifkan Sementara)</option>
                </select>
            </div>

            <!-- Password & Konfirmasi Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-300 mb-1.5">Kata Sandi <span class="text-rose-400">*</span></label>
                    <input type="password" id="password" name="password" required minlength="6"
                        placeholder="Minimal 6 karakter"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 font-mono transition">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-300 mb-1.5">Ulangi Kata Sandi <span class="text-rose-400">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6"
                        placeholder="Ketik ulang sandi"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 font-mono transition">
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <a href="{{ route('developer.kasir.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-900 text-xs sm:text-sm font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-cyan-600/30 transition active:scale-95">
                    Simpan Akun Kasir
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
