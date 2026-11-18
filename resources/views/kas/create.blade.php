@extends('layouts.app')

@section('title', 'Tambah Akun Kas / Rekening')

@section('content')
<div class="ak-page max-w-2xl space-y-3.5 pb-20" x-data="{ saldo: Number(@js(old('saldo', 0))), formatIDR(v) { return new Intl.NumberFormat('id-ID').format(v || 0); } }">

    <!-- Header Section -->
    <div class="ak-head !flex-row !items-center !justify-start">
        <a href="{{ route('kas.index') }}" class="ak-back" title="Kembali">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </a>
        <div class="min-w-0">
            <h1 class="ak-title">Tambah Akun Kas / Rekening</h1>
            <p class="ak-desc sub-explanation">Daftarkan rekening bank, akun e-wallet, atau kas tambahan untuk cabang aktif.</p>
        </div>
    </div>

    <!-- Form -->
    <div class="ak-card p-3.5 sm:p-4">
        <form action="{{ route('kas.store') }}" method="POST" class="space-y-3.5">
            @csrf

            <!-- Tipe Akun -->
            <div>
                <label for="status" class="ak-label">
                    Tipe Akun Kas <span class="text-rose-500">*</span>
                </label>
                <select id="status" name="status" required class="ak-input font-bold">
                    <option value="bank" {{ old('status') === 'bank' ? 'selected' : '' }}>Rekening Bank (BRI, BCA, Mandiri, dll)</option>
                    <option value="ewallet" {{ old('status', 'ewallet') === 'ewallet' ? 'selected' : '' }}>E-Wallet (DANA, Gopay, OVO, ShopeePay, dll)</option>
                    <option value="cash" {{ old('status') === 'cash' ? 'selected' : '' }}>Kas Tunai / Laci Fisik Tambahan</option>
                </select>
            </div>

            <!-- Nama Akun -->
            <div>
                <label for="jenis_kas" class="ak-label">
                    Nama Akun / Kas <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="jenis_kas"
                    id="jenis_kas"
                    value="{{ old('jenis_kas') }}"
                    required
                    placeholder="Contoh: Rekening BRI Operasional / Akun DANA Agen"
                    class="ak-input">
                @error('jenis_kas')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Rekening & Atas Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="nomor_rekening" class="ak-label">
                        Nomor Rekening / No. HP
                    </label>
                    <input
                        type="text"
                        name="nomor_rekening"
                        id="nomor_rekening"
                        value="{{ old('nomor_rekening') }}"
                        placeholder="Contoh: 1234567890 / 08123456789"
                        class="ak-input">
                </div>
                <div>
                    <label for="atas_nama" class="ak-label">
                        Atas Nama Pemilik
                    </label>
                    <input
                        type="text"
                        name="atas_nama"
                        id="atas_nama"
                        value="{{ old('atas_nama') }}"
                        placeholder="Contoh: Toko Berkah Sentosa"
                        class="ak-input">
                </div>
            </div>

            <!-- Saldo Awal -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="saldo_display" class="ak-label !mb-0">
                        Saldo Awal <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[10px] font-bold text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200">Format Rupiah</span>
                </div>

                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400">
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
                        class="ak-input pl-10 pr-3 py-2.5 text-base sm:text-lg font-black text-slate-900">
                    <input type="hidden" name="saldo" :value="saldo">
                </div>

                @error('saldo')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-1 sub-explanation">Ketik nominal angka tanpa titik, otomatis diformat (contoh: 50.000 atau 1.000.000).</p>
            </div>

            <!-- Action Buttons -->
            <div class="ak-actions">
                <a href="{{ route('kas.index') }}" class="ak-btn-secondary flex-1">
                    Batal
                </a>
                <button type="submit" class="ak-btn-primary flex-1">
                    Simpan Akun Kas
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
