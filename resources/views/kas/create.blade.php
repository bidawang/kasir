@extends('layouts.app')

@section('title', 'Tambah Akun Kas / Rekening')

@section('content')
<div class="max-w-2xl mx-auto space-y-4 sm:space-y-5" x-data="{ saldo: Number(@js(old('saldo', 0))), formatIDR(v) { return new Intl.NumberFormat('id-ID').format(v || 0); } }">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Tambah Akun Kas / Rekening
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">
                Daftarkan rekening bank, akun e-wallet, atau kas tambahan untuk cabang aktif
            </p>
        </div>

        <a href="{{ route('kas.index') }}"
           class="bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <form action="{{ route('kas.store') }}" method="POST" class="space-y-4 sm:space-y-5">
            @csrf

            <!-- Tipe Akun -->
            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Tipe Akun Kas <span class="text-rose-500">*</span>
                </label>
                <select id="status" name="status" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                    <option value="bank" {{ old('status') === 'bank' ? 'selected' : '' }}>Rekening Bank (BRI, BCA, Mandiri, dll)</option>
                    <option value="ewallet" {{ old('status', 'ewallet') === 'ewallet' ? 'selected' : '' }}>E-Wallet (DANA, Gopay, OVO, ShopeePay, dll)</option>
                    <option value="cash" {{ old('status') === 'cash' ? 'selected' : '' }}>Kas Tunai / Laci Fisik Tambahan</option>
                </select>
            </div>

            <!-- Nama Akun -->
            <div>
                <label for="jenis_kas" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Nama Akun / Kas <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="jenis_kas"
                    id="jenis_kas"
                    value="{{ old('jenis_kas') }}"
                    required
                    placeholder="Contoh: Rekening BCA Operasional / Akun DANA Agen"
                    class="w-full px-4 py-3 bg-slate-50 border @error('jenis_kas') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                @error('jenis_kas')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Rekening & Atas Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nomor_rekening" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nomor Rekening / No. HP
                    </label>
                    <input
                        type="text"
                        name="nomor_rekening"
                        id="nomor_rekening"
                        value="{{ old('nomor_rekening') }}"
                        placeholder="Contoh: 1234567890 / 08123456789"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                </div>
                <div>
                    <label for="atas_nama" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Atas Nama Pemilik
                    </label>
                    <input
                        type="text"
                        name="atas_nama"
                        id="atas_nama"
                        value="{{ old('atas_nama') }}"
                        placeholder="Contoh: Toko Berkah Sentosa"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                </div>
            </div>

            <!-- Saldo Awal (Format Rupiah Otomatis) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="saldo_display" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Saldo Awal <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] font-semibold text-primary-600 bg-primary-50 px-2 py-0.5 rounded">Format Rupiah</span>
                </div>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-bold text-base">
                        Rp
                    </span>
                    <input
                        type="text"
                        inputmode="numeric"
                        id="saldo_display"
                        required
                        :value="saldo ? formatIDR(saldo) : ''"
                        @focus="$event.target.select()"
                        @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); saldo = raw ? parseInt(raw, 10) : 0; $event.target.value = saldo ? formatIDR(saldo) : (raw === '0' ? '0' : '');"
                        placeholder="0"
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border @error('saldo') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xl font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                    <input type="hidden" name="saldo" :value="saldo">
                </div>

                @error('saldo')
                    <p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-1.5">Ketik nominal tanpa titik, otomatis diformat (contoh: 50.000 atau 1.000.000).</p>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('kas.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-bold hover:bg-slate-50 transition">
                    Batal
                </a>

                <button type="submit"
                        class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-sm shadow-primary-500/25 transition cursor-pointer">
                    Simpan Akun Kas
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
