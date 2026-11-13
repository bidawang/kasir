@extends('layouts.app')

@section('title', 'Ubah Barang atau Layanan')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Ubah Barang atau Layanan</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Ubah nama, jenis, harga, atau stok barang</p>
        </div>
        <a href="{{ route('item-jualan.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl text-sm font-semibold min-h-11 flex items-center">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <form action="{{ route('item-jualan.update', $itemJualan->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="nama_item" class="block text-sm sm:text-base font-semibold text-slate-700 mb-2">Nama Barang atau Layanan</label>
                <input id="nama_item" name="nama_item" type="text" value="{{ old('nama_item', $itemJualan->nama_item) }}" required maxlength="255"
                    class="w-full px-4 py-3 bg-slate-50 border @error('nama_item') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm sm:text-base font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('nama_item')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="jenis" class="block text-sm sm:text-base font-semibold text-slate-700 mb-2">Jenis Barang atau Layanan</label>
                <select id="jenis" name="jenis" required
                    class="w-full px-4 py-3 bg-slate-50 border @error('jenis') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm sm:text-base font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @foreach(['fisik' => 'Barang fisik', 'virtual' => 'Layanan virtual', 'bank' => 'Layanan bank'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis', $itemJualan->jenis) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('jenis')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="harga_jual" class="block text-sm sm:text-base font-semibold text-slate-700 mb-2">Harga Jual</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-sm font-semibold">Rp</span>
                    <input id="harga_jual" name="harga_jual" type="number" inputmode="numeric" min="0" step="1" value="{{ old('harga_jual', $itemJualan->harga_jual) }}" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('harga_jual') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                @error('harga_jual')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="jumlah" class="block text-sm sm:text-base font-semibold text-slate-700 mb-2">Jumlah Stok Barang</label>
                <input id="jumlah" name="jumlah" type="number" inputmode="numeric" min="0" step="1" value="{{ old('jumlah', $itemJualan->jumlah) }}"
                    class="w-full px-4 py-3 bg-slate-50 border @error('jumlah') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm sm:text-base font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('jumlah')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
                <p class="mt-2 text-sm text-slate-400">Isi jumlah stok untuk barang fisik. Layanan virtual dan bank tidak memerlukan stok.</p>
            </div>
            <div class="pt-3 flex justify-end gap-3">
                <a href="{{ route('item-jualan.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 text-sm sm:text-base font-semibold hover:bg-slate-50 min-h-12 flex items-center">Batal</a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-6 py-3 rounded-xl text-sm sm:text-base font-semibold shadow-sm min-h-12">Perbarui Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
