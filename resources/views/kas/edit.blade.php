@extends('layouts.app')

@section('title', 'Ubah Akun Kas')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Ubah Akun Kas</h1>
            <p class="text-sm text-slate-500 font-medium">Perbarui nama akun dan saldo</p>
        </div>
        <a href="{{ route('kas.index') }}"
           class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Card Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <form action="{{ route('kas.update', $kas->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <input type="hidden" name="id_warung" value="2">

            <!-- Nama Akun -->
            <div>
                <label for="jenis_kas" class="block text-sm font-semibold text-slate-700 mb-2">Nama Akun</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.75 0M2.25 12h19.5m-19.5-6.75a60.07 60.07 0 0115.75 0M12 2.25v19.5"/></svg>
                    </span>
                    <input type="text" name="jenis_kas" id="jenis_kas"
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('jenis_kas') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:border-primary-500 focus:ring-primary-500 @enderror rounded-xl text-sm sm:text-base font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 transition-all duration-200"
                           value="{{ old('jenis_kas', $kas->jenis_kas) }}" required>
                </div>
                @error('jenis_kas')
                    <p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Input Saldo -->
            <div>
                <label for="saldo" class="block text-sm font-semibold text-slate-700 mb-2">Saldo</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-semibold text-sm">
                        Rp
                    </span>
                    <input type="number" name="saldo" id="saldo"
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('saldo') border-rose-300 focus:ring-rose-500 focus:border-rose-500 @else border-slate-200 focus:border-primary-500 focus:ring-primary-500 @enderror rounded-xl text-sm sm:text-base font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 transition-all duration-200"
                           value="{{ old('saldo', $kas->saldo) }}" required>
                </div>
                @error('saldo')
                    <p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 flex items-center justify-end gap-3">
                <a href="{{ route('kas.index') }}"
                   class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors min-h-12 flex items-center">
                    Batal
                </a>
                <button type="submit"
                        class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-sm shadow-primary-500/25 transition-all duration-200 min-h-12">
                    Perbarui Akun
                </button>
            </div>
        </form>
    </div>

</div>
@endsection