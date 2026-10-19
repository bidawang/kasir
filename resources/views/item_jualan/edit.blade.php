@extends('layouts.app')

@section('title', 'Edit Item Jualan')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Edit Item Jualan</h1>
            <p class="text-xs text-slate-500 font-medium">Perbarui nama dan harga item</p>
        </div>
        <a href="{{ route('item-jualan.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-semibold">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <form action="{{ route('item-jualan.update', $itemJualan->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="nama_item" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Nama Item</label>
                <input id="nama_item" name="nama_item" type="text" value="{{ old('nama_item', $itemJualan->nama_item) }}" required maxlength="255"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border @error('nama_item') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('nama_item')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="harga_jual" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Harga Jual</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                    <input id="harga_jual" name="harga_jual" type="number" inputmode="numeric" min="0" step="1" value="{{ old('harga_jual', $itemJualan->harga_jual) }}" required
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('harga_jual') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                @error('harga_jual')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div class="pt-3 flex justify-end gap-3">
                <a href="{{ route('item-jualan.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-50">Batal</a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm">Perbarui Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
