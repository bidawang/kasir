@extends('developer.layouts.app', ['title' => 'Tambah Cabang Baru', 'breadcrumb' => 'Tambah Cabang Baru'])

@section('content')
@php
    $ownerOptions = $owners->map(fn($o) => [
        'value' => $o->id,
        'label' => $o->nama,
        'sub'   => $o->email . ' (' . strtoupper($o->role) . ')'
    ])->toArray();

    $bankOptions = $banks->map(fn($b) => [
        'value' => $b->nama,
        'label' => $b->nama,
        'sub'   => 'Kode Bank: ' . $b->kode
    ])->toArray();
@endphp

<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Provisioning Cabang Baru</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Daftarkan cabang agen baru ke salah satu Owner, kas fisik dan rekening utama dibuat otomatis.
            </p>
        </div>
        <a href="{{ route('developer.cabang.index') }}" class="text-xs sm:text-sm text-slate-400 hover:text-white font-bold flex items-center gap-1 transition">
            ← Kembali
        </a>
    </div>

    <!-- Form Box -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="{{ route('developer.cabang.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Pilih Owner Pemilik Cabang dengan Searchable Select -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Alokasikan ke Owner (Cari &amp; Pilih) <span class="text-rose-400">*</span></label>
                @include('developer.components.searchable-select', [
                    'name' => 'id_user',
                    'options' => $ownerOptions,
                    'selected' => old('id_user', request('id_user', '')),
                    'placeholder' => '-- Cari & Pilih Akun Owner --',
                    'required' => true,
                ])
                <span class="text-[11px] text-slate-400 mt-1 block">Cabang ini akan menjadi kepemilikan dan muncul di dashboard owner terpilih.</span>
            </div>

            <!-- Nama Cabang -->
            <div>
                <label for="nama_warung" class="block text-xs font-bold text-slate-300 mb-1.5">Nama Cabang / Toko Agen <span class="text-rose-400">*</span></label>
                <input type="text" id="nama_warung" name="nama_warung" value="{{ old('nama_warung') }}" required
                    placeholder="Contoh: Cabang Pasar Senen"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
            </div>

            <!-- Alamat & Telepon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="alamat" class="block text-xs font-bold text-slate-300 mb-1.5">Alamat Lengkap</label>
                    <input type="text" id="alamat" name="alamat" value="{{ old('alamat') }}"
                        placeholder="Jl. Raya No. 12"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 transition">
                </div>
                <div>
                    <label for="no_telepon" class="block text-xs font-bold text-slate-300 mb-1.5">Nomor Telepon Cabang</label>
                    <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}"
                        placeholder="08123456789"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 font-mono transition">
                </div>
            </div>

            <!-- Rekening Operasional Bank Utama -->
            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80 space-y-3">
                <span class="text-xs font-bold text-purple-400 block font-mono">
                    BANK OPERASIONAL UTAMA CABANG (AUTO-GENERATE KAS)
                </span>
                
                <!-- Searchable Select Bank -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Bank Utama (Cari &amp; Pilih) <span class="text-rose-400">*</span></label>
                    @include('developer.components.searchable-select', [
                        'name' => 'bank_utama',
                        'options' => $bankOptions,
                        'selected' => old('bank_utama', 'BRI'),
                        'placeholder' => '-- Cari & Pilih Bank --',
                        'required' => true,
                    ])
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nomor_rekening_utama" class="block text-xs font-medium text-slate-300 mb-1">Nomor Rekening</label>
                        <input type="text" id="nomor_rekening_utama" name="nomor_rekening_utama" value="{{ old('nomor_rekening_utama') }}"
                            placeholder="Contoh: 1234-01-000123-50-1"
                            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-600 focus:outline-none focus:border-purple-500 font-mono transition">
                    </div>
                    <div>
                        <label for="atas_nama_utama" class="block text-xs font-medium text-slate-300 mb-1">Atas Nama Rekening</label>
                        <input type="text" id="atas_nama_utama" name="atas_nama_utama" value="{{ old('atas_nama_utama') }}"
                            placeholder="Contoh: Toko Berkah Mandiri"
                            class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-600 focus:outline-none focus:border-purple-500 transition">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800">
                <a href="{{ route('developer.cabang.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-900 text-xs sm:text-sm font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-purple-600/30 transition active:scale-95">
                    Buat Cabang Baru
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
