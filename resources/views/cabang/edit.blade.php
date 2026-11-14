@extends('layouts.app')

@section('title', 'Ubah Cabang')

@section('content')
<div class="max-w-2xl mx-auto space-y-4 sm:space-y-5">

    <!-- NAVIGASI KEMBALI & HEADER -->
    <div class="flex items-center gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <a href="{{ route('cabang.index') }}"
           class="w-10 h-10 shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 flex items-center justify-center transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
        </a>
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-extrabold text-slate-800 leading-snug">Ubah Cabang / Agen</h2>
            <p class="text-sm text-slate-500 truncate">Perbarui profil cabang dan Bank Utama operasional</p>
        </div>
    </div>

    <!-- ALERT ERROR VALIDASI -->
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200/80 text-rose-800 p-4 rounded-2xl text-sm space-y-2">
            <div class="font-bold flex items-center gap-2 text-rose-700">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span>Periksa kembali data yang diisi:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM INPUT -->
    <form action="{{ route('cabang.update', $warung->id) }}" method="POST"
          class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <!-- NAMA CABANG -->
        <div>
            <label for="nama_warung" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                Nama Cabang / Toko <span class="text-rose-500">*</span>
            </label>
            <input
                type="text"
                id="nama_warung"
                name="nama_warung"
                value="{{ old('nama_warung', $warung->nama_warung) }}"
                placeholder="Contoh: Cabang Utama"
                required
                autofocus
                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none text-sm sm:text-base text-slate-800 transition font-medium placeholder:text-slate-400"
            >
        </div>

        <!-- BANK UTAMA OPERASIONAL -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
            <div>
                <label for="bank_utama" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Bank Utama Operasional <span class="text-rose-500">*</span>
                </label>
                <p class="text-xs text-slate-500 mb-2">Bank ini menjadi acuan transaksi "Sesama Bank" dan "Tarik Tunai Utama".</p>
                <select
                    id="bank_utama"
                    name="bank_utama"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none text-sm font-semibold text-slate-800 transition"
                >
                    @if(isset($banks) && $banks->isNotEmpty())
                        @foreach($banks as $b)
                            <option value="{{ $b->kode }}" {{ old('bank_utama', $warung->bank_utama ?? 'BRI') === $b->kode ? 'selected' : '' }}>
                                {{ $b->nama }} ({{ $b->kode }})
                            </option>
                        @endforeach
                    @else
                        <option value="BRI" {{ old('bank_utama', $warung->bank_utama) === 'BRI' ? 'selected' : '' }}>Bank Rakyat Indonesia (BRI)</option>
                        <option value="MANDIRI" {{ old('bank_utama', $warung->bank_utama) === 'MANDIRI' ? 'selected' : '' }}>Bank Mandiri (MANDIRI)</option>
                        <option value="BCA" {{ old('bank_utama', $warung->bank_utama) === 'BCA' ? 'selected' : '' }}>Bank Central Asia (BCA)</option>
                        <option value="BNI" {{ old('bank_utama', $warung->bank_utama) === 'BNI' ? 'selected' : '' }}>Bank Negara Indonesia (BNI)</option>
                        <option value="BSI" {{ old('bank_utama', $warung->bank_utama) === 'BSI' ? 'selected' : '' }}>Bank Syariah Indonesia (BSI)</option>
                    @endif
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="nomor_rekening_utama" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">
                        Nomor Rekening Bank Utama
                    </label>
                    <input
                        type="text"
                        id="nomor_rekening_utama"
                        name="nomor_rekening_utama"
                        value="{{ old('nomor_rekening_utama', $warung->nomor_rekening_utama) }}"
                        placeholder="Contoh: 123401000123530"
                        class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-sm text-slate-800 font-medium"
                    >
                </div>
                <div>
                    <label for="atas_nama_utama" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">
                        Atas Nama Rekening
                    </label>
                    <input
                        type="text"
                        id="atas_nama_utama"
                        name="atas_nama_utama"
                        value="{{ old('atas_nama_utama', $warung->atas_nama_utama) }}"
                        placeholder="Contoh: Toko Berkah"
                        class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-sm text-slate-800 font-medium"
                    >
                </div>
            </div>
        </div>

        <!-- ALAMAT & NO HP -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="no_telepon" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                    No. Telepon / WhatsApp
                </label>
                <input
                    type="text"
                    id="no_telepon"
                    name="no_telepon"
                    value="{{ old('no_telepon', $warung->no_telepon) }}"
                    placeholder="Contoh: 08123456789"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none text-sm text-slate-800 transition"
                >
            </div>
            <div>
                <label for="alamat" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Alamat Cabang
                </label>
                <input
                    type="text"
                    id="alamat"
                    name="alamat"
                    value="{{ old('alamat', $warung->alamat) }}"
                    placeholder="Contoh: Jl. Ahmad Yani No. 10"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none text-sm text-slate-800 transition"
                >
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('cabang.index') }}"
               class="flex-1 text-center px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 active:scale-95 text-sm font-bold transition">
                Batal
            </a>

            <button type="submit"
                    class="flex-1 px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 active:scale-95 text-white text-sm font-bold transition shadow-sm shadow-primary-600/20 flex items-center justify-center gap-2 min-h-12">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>

    </form>

</div>
@endsection
