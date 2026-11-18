@extends('layouts.app')

@section('title', 'Tambah Barang atau Layanan')

@section('content')
<div class="ak-page max-w-2xl space-y-3.5 pb-20"
     x-data="{
         hargaJual: Number(@js(old('harga_jual', 0))),
         hargaBeli: Number(@js(old('harga_beli', 0))),
         formatIDR(v) { return new Intl.NumberFormat('id-ID').format(v || 0); },
         get laba() { return (this.hargaJual || 0) - (this.hargaBeli || 0); }
     }">
    
    <div class="ak-head !flex-row !items-center !justify-start">
        <a href="{{ route('item-jualan.index') }}" class="ak-back" title="Kembali">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </a>
        <div class="min-w-0">
            <h1 class="ak-title">Tambah Barang atau Layanan</h1>
            <p class="ak-desc sub-explanation">Isi data barang, modal beli, harga jual, dan stok untuk penghitungan laba otomatis.</p>
        </div>
    </div>

    <div class="ak-card p-3.5 sm:p-4">
        <form action="{{ route('item-jualan.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label for="nama_item" class="ak-label">Nama Barang atau Layanan <span class="text-rose-500">*</span></label>
                <input id="nama_item" name="nama_item" type="text" value="{{ old('nama_item') }}" required maxlength="255" placeholder="Contoh: Pulsa Telkomsel / Kertas Struk / Kartu Perdana"
                    class="ak-input font-bold">
                @error('nama_item')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="jenis" class="ak-label">Jenis Barang atau Layanan <span class="text-rose-500">*</span></label>
                <select id="jenis" name="jenis" required class="ak-input font-bold">
                    <option value="">-- Pilih Jenis --</option>
                    @foreach(['fisik' => 'Barang fisik (memiliki stok fisik)', 'virtual' => 'Layanan virtual (pulsa, token, top-up e-wallet)', 'bank' => 'Layanan transfer bank'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('jenis')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- Grid Harga Modal dan Harga Jual -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Harga Beli / Modal -->
                <div>
                    <label for="harga_beli" class="ak-label">
                        Harga Modal / Beli
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input id="harga_beli" name="harga_beli" type="text" inputmode="numeric"
                            :value="hargaBeli ? formatIDR(hargaBeli) : ''"
                            @focus="$event.target.select()"
                            @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); hargaBeli = raw ? parseInt(raw, 10) : 0; $event.target.value = hargaBeli ? formatIDR(hargaBeli) : (raw === '0' ? '0' : '');"
                            placeholder="0"
                            class="ak-input pl-9 font-bold">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block sub-explanation">Modal dasar pembelian per unit</span>
                    @error('harga_beli')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>

                <!-- Harga Jual -->
                <div>
                    <label for="harga_jual" class="ak-label">
                        Harga Jual <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input id="harga_jual" name="harga_jual" type="text" inputmode="numeric" required
                            :value="hargaJual ? formatIDR(hargaJual) : ''"
                            @focus="$event.target.select()"
                            @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); hargaJual = raw ? parseInt(raw, 10) : 0; $event.target.value = hargaJual ? formatIDR(hargaJual) : (raw === '0' ? '0' : '');"
                            placeholder="10.000"
                            class="ak-input pl-9 font-bold">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block sub-explanation">Harga jual ke pembeli</span>
                    @error('harga_jual')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Perhitungan Laba Card -->
            <div class="p-3 rounded-xl border flex items-center justify-between"
                 :class="laba >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200'">
                <div>
                    <span class="text-xs font-black uppercase tracking-wider" :class="laba >= 0 ? 'text-emerald-800' : 'text-rose-800'">
                        Estimasi Laba per Item
                    </span>
                    <span class="block text-[10px] sub-explanation" :class="laba >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                        Harga Jual dikurangi Modal Beli
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-sm sm:text-base font-black" :class="laba >= 0 ? 'text-emerald-700' : 'text-rose-700'"
                          x-text="(laba < 0 ? '- Rp ' : 'Rp ') + formatIDR(Math.abs(laba))"></span>
                </div>
            </div>

            <div>
                <label for="jumlah" class="ak-label">Jumlah Stok Barang (khusus fisik)</label>
                <input id="jumlah" name="jumlah" type="number" inputmode="numeric" min="0" step="1" value="{{ old('jumlah') }}"
                    class="ak-input">
                @error('jumlah')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                <p class="mt-1 text-[11px] text-slate-400 sub-explanation">Isi jumlah stok untuk barang fisik. Layanan virtual tidak memerlukan stok fisik.</p>
            </div>

            <div class="ak-actions">
                <a href="{{ route('item-jualan.index') }}" class="ak-btn-secondary flex-1">Batal</a>
                <button type="submit" class="ak-btn-primary flex-1">Simpan Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
