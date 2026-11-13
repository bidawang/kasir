@extends('layouts.app')

@section('title', 'Barang dan Layanan')

@section('content')
<div class="max-w-3xl mx-auto pb-12 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Barang dan Layanan</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Kelola barang atau layanan yang dijual di cabang ini</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('transaksi.penjualan-item.create') }}" class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all flex items-center gap-1.5 shadow-sm shrink-0 min-h-12">
                Jual Barang atau Layanan
            </a>
            <a href="{{ route('item-jualan.create') }}" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all flex items-center gap-1.5 shadow-sm shrink-0 min-h-12">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Barang atau Layanan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        @if($itemJualan->isEmpty())
            <div class="p-8 text-center space-y-2">
                <div class="w-12 h-12 mx-auto rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.4 12.2a2 2 0 002 1.6h8.8a2 2 0 002-1.6L22 8H6"/></svg>
                </div>
                <p class="text-base font-bold text-slate-800">Belum ada barang atau layanan</p>
                <p class="text-sm text-slate-500 max-w-xs mx-auto">Tambahkan barang atau layanan pertama untuk cabang ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-sm uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3.5 font-bold">Nama Barang atau Layanan</th>
                            <th class="px-4 py-3.5 font-bold">Jenis dan Stok</th>
                            <th class="px-4 py-3.5 font-bold">Harga Jual</th>
                            <th class="px-4 py-3.5 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($itemJualan as $item)
                            <tr class="text-sm">
                                <td class="px-4 py-3.5 font-bold text-slate-800">{{ $item->nama_item }}</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-700">{{ ['fisik' => 'Barang fisik', 'virtual' => 'Layanan virtual', 'bank' => 'Layanan bank'][$item->jenis] ?? ucfirst($item->jenis) }}{{ $item->jenis === 'fisik' ? ' · Stok: ' . $item->jumlah : '' }}</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-700">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('item-jualan.edit', $item->id) }}" class="px-3.5 py-2 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 font-bold text-sm">Ubah</a>
                                        <form action="{{ route('item-jualan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang atau layanan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3.5 py-2 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($itemJualan->hasPages())
                <div class="p-4 border-t border-slate-100">{{ $itemJualan->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
