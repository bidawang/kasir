@extends('layouts.app')

@section('title', 'Tambah Biaya Admin')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Tambah Biaya Admin</h1>
            <p class="text-xs text-slate-500 font-medium">Tentukan tarif admin berdasarkan rentang harga item</p>
        </div>
        <a href="{{ route('biaya-admin.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-semibold">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <form action="{{ route('biaya-admin.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="range_awal" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Harga Minimum</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                        <input id="range_awal" name="range_awal" type="number" inputmode="numeric" min="0" step="1" value="{{ old('range_awal') }}" required
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('range_awal') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('range_awal')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="range_akhir" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Harga Maksimum</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                        <input id="range_akhir" name="range_akhir" type="number" inputmode="numeric" min="0" step="1" value="{{ old('range_akhir') }}" required
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('range_akhir') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('range_akhir')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label for="biaya_admin" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Biaya Admin</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                    <input id="biaya_admin" name="biaya_admin" type="number" inputmode="numeric" min="0" step="1" value="{{ old('biaya_admin') }}" required
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('biaya_admin') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                @error('biaya_admin')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div class="pt-3 flex justify-end gap-3">
                <a href="{{ route('biaya-admin.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-50">Batal</a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm">Simpan Biaya</button>
            </div>
        </form>
    </div>
</div>
@endsection
