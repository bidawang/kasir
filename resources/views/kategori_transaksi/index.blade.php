@extends('layouts.app')

@section('title', 'Kategori Transaksi')

@section('content')
<div class="max-w-5xl mx-auto pb-12 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kategori Transaksi</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                Master data jenis transaksi agen, metode pembayaran, dan arah arus kas masuk/keluar.
            </p>
        </div>
        <a href="{{ route('kategori-transaksi.create') }}" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm shrink-0 min-h-11">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Kategori Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-2xl text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('kategori-transaksi.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-60">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode..."
                       class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <select name="jenis_aliran" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Arus Kas</option>
                <option value="masuk" @selected(request('jenis_aliran') === 'masuk')>Kas Masuk</option>
                <option value="keluar" @selected(request('jenis_aliran') === 'keluar')>Kas Keluar</option>
            </select>

            <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                Cari
            </button>

            @if(request()->hasAny(['search', 'jenis_aliran']))
                <a href="{{ route('kategori-transaksi.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 bg-slate-100 rounded-xl">Reset</a>
            @endif
        </form>

        <div class="text-xs text-slate-500 font-medium">
            Bank Utama Cabang: <strong class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200">{{ $bankUtama ?? 'BRI' }}</strong>
        </div>
    </div>

    <!-- Table Kategori -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        @if($kategoriList->isEmpty())
            <div class="p-10 text-center space-y-3">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </div>
                <p class="text-base font-bold text-slate-800">Belum ada kategori transaksi</p>
                <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto">Tambahkan kategori transaksi atau jalankan seeder default untuk mengisi preset.</p>
                <div class="pt-2">
                    <a href="{{ route('kategori-transaksi.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 text-white text-xs font-bold rounded-xl hover:bg-primary-700 transition">
                        Tambah Kategori Baru
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-[11px] uppercase tracking-wider text-slate-500 font-extrabold">
                            <th class="px-4 py-3.5 w-12 text-center">Urutan</th>
                            <th class="px-4 py-3.5">Nama Kategori & Kode</th>
                            <th class="px-4 py-3.5">Arus Uang Fisik</th>
                            <th class="px-4 py-3.5">Metode</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kategoriList as $item)
                            @php
                                $namaDinamis = $item->getNamaDinamis($bankUtama ?? 'BRI');
                            @endphp
                            <tr class="text-xs sm:text-sm hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-3.5 text-center font-black text-slate-400">
                                    {{ $item->urutan }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900">{{ $namaDinamis }}</div>
                                    <div class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $item->kode }}</div>
                                    @if($item->deskripsi)
                                        <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $item->deskripsi }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-block text-[10px] font-black uppercase px-2.5 py-1 rounded-md border {{ $item->jenis_aliran === 'masuk' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200' }}">
                                        {{ $item->jenis_aliran === 'masuk' ? 'Uang Masuk (+)' : 'Uang Keluar (-)' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-600">
                                    {{ strtoupper(str_replace('_', ' ', $item->metode_transaksi)) }}
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    @if($item->is_active)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-500">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex justify-end items-center gap-1.5">
                                        <a href="{{ route('kategori-transaksi.edit', $item->id) }}" class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 font-bold text-xs transition">
                                            Ubah
                                        </a>
                                        <form action="{{ route('kategori-transaksi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus kategori transaksi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($kategoriList->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $kategoriList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
