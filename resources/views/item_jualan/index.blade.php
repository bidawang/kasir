@extends('layouts.app')

@section('title', 'Barang dan Layanan')

@section('content')
<div class="ak-page max-w-4xl space-y-3.5 pb-20">
    <!-- Header -->
    <div class="ak-head">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-primary-600"></span>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Inventaris & Jasa</span>
            </div>
            <div class="flex items-center gap-2">
                <h1 class="ak-title">Barang dan Layanan</h1>
                <span class="px-2 py-0.2 rounded-full bg-primary-50 text-primary-700 text-[10px] font-black border border-primary-100">
                    {{ $itemJualan->count() }} Item
                </span>
            </div>
            <p class="ak-desc sub-explanation">Kelola daftar produk fisik (stok toko) dan produk virtual (pulsa, token, top-up).</p>
        </div>
        <div class="flex items-center gap-1.5 shrink-0 flex-wrap sm:flex-nowrap">
            <a href="{{ route('transaksi.penjualan-item.create') }}" class="ak-btn-success !text-xs !py-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                <span>Kasir Jual</span>
            </a>
            <a href="{{ route('item-jualan.create') }}" class="ak-btn-primary !text-xs !py-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah Item</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="ak-alert-success">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="ak-alert-error">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($itemJualan->isEmpty())
        <div class="ak-card p-8 text-center space-y-2">
            <div class="w-10 h-10 mx-auto rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 002 1.6h8.8a2 2 0 002-1.6L22 8H6"/></svg>
            </div>
            <p class="text-xs sm:text-sm font-bold text-slate-800">Belum ada barang atau layanan</p>
            <p class="text-[11px] text-slate-500 max-w-xs mx-auto sub-explanation">Tambahkan barang atau layanan pertama untuk cabang ini.</p>
            <a href="{{ route('item-jualan.create') }}" class="ak-btn-primary !text-xs !py-1.5 inline-flex">Tambah Item Pertama</a>
        </div>
    @else
        <!-- Mobile Cards -->
        <div class="grid grid-cols-1 gap-2.5 sm:hidden">
            @foreach($itemJualan as $item)
                @php
                    $modal = (int) ($item->harga_beli ?? 0);
                    $jual = (int) ($item->harga_jual ?? 0);
                    $margin = $jual - $modal;
                @endphp
                <div class="ak-card p-3 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded {{ $item->jenis === 'fisik' ? 'bg-primary-100 text-primary-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ['fisik' => 'Fisik', 'virtual' => 'Virtual', 'bank' => 'Bank'][$item->jenis] ?? ucfirst($item->jenis) }}
                            </span>
                            <h3 class="font-black text-xs sm:text-sm text-slate-900 mt-1">{{ $item->nama_item }}</h3>
                            @if($item->jenis === 'fisik')
                                <span class="text-[10px] text-slate-500 font-bold">Stok: {{ $item->jumlah }} pcs</span>
                            @endif
                        </div>
                        <span class="text-xs font-black {{ $margin >= 0 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-rose-700 bg-rose-50 border border-rose-200' }} px-2 py-0.5 rounded-lg">
                            {{ $margin >= 0 ? '+' : '-' }}Rp {{ number_format(abs($margin), 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] bg-slate-50 p-2 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-slate-400 block font-bold uppercase">Harga Modal</span>
                            <span class="font-semibold text-slate-700">Rp {{ number_format($modal, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-bold uppercase">Harga Jual</span>
                            <span class="font-black text-primary-700">Rp {{ number_format($jual, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-slate-100">
                        <a href="{{ route('item-jualan.edit', $item->id) }}" class="flex-1 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs text-center border border-amber-200 transition">
                            Ubah
                        </a>
                        <form action="{{ route('item-jualan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang atau layanan ini?');" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block ak-table-wrap">
            <table class="ak-table text-xs">
                <thead>
                    <tr>
                        <th>Barang / Layanan</th>
                        <th>Jenis & Stok</th>
                        <th>Harga Modal</th>
                        <th>Harga Jual</th>
                        <th>Laba Bersih</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($itemJualan as $item)
                        @php
                            $modal = (int) ($item->harga_beli ?? 0);
                            $jual = (int) ($item->harga_jual ?? 0);
                            $margin = $jual - $modal;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-3.5 py-3 font-bold text-slate-800">{{ $item->nama_item }}</td>
                            <td class="px-3.5 py-3 font-semibold text-slate-600">
                                <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-black {{ $item->jenis === 'fisik' ? 'bg-primary-50 text-primary-700' : 'bg-purple-50 text-purple-700' }}">
                                    {{ ['fisik' => 'Fisik', 'virtual' => 'Virtual', 'bank' => 'Bank'][$item->jenis] ?? ucfirst($item->jenis) }}
                                </span>
                                @if($item->jenis === 'fisik')
                                    <span class="block text-[10px] text-slate-400 font-medium mt-0.5">Stok: {{ $item->jumlah }} pcs</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 font-semibold text-slate-500">
                                Rp {{ number_format($modal, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-3 font-black text-slate-900">
                                Rp {{ number_format($jual, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-3 font-black">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] {{ $margin >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $margin >= 0 ? '+' : '-' }}Rp {{ number_format(abs($margin), 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3 text-right whitespace-nowrap">
                                <div class="flex justify-end items-center gap-1.5">
                                    <a href="{{ route('item-jualan.edit', $item->id) }}" class="rounded-lg bg-amber-50 px-2.5 py-1 font-bold text-amber-700 hover:bg-amber-100 border border-amber-200 text-xs transition">Ubah</a>
                                    <form action="{{ route('item-jualan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang atau layanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-rose-50 px-2.5 py-1 font-bold text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs transition cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
