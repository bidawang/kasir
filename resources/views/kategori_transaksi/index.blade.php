@extends('layouts.app')

@section('title', 'Kategori Transaksi')

@section('content')
<div class="ak-page max-w-4xl space-y-3.5 pb-20">
    <!-- Header -->
    <div class="ak-head">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('dashboard') }}" class="ak-back" title="Kembali ke Beranda">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="ak-title">Kategori Transaksi</h1>
                <p class="ak-desc sub-explanation">Master data jenis transaksi agen, metode pembayaran, dan alur kas.</p>
            </div>
        </div>
        <a href="{{ route('kategori-transaksi.create') }}" class="ak-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span>Tambah Kategori</span>
        </a>
    </div>

    @if(session('success'))
        <div class="ak-alert-success">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="ak-alert-danger">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="ak-card p-3 flex flex-col sm:flex-row items-center justify-between gap-2.5">
        <form action="{{ route('kategori-transaksi.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-56">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode..."
                       class="ak-input text-xs py-1.5 pl-3 pr-8">
            </div>

            <select name="jenis_aliran" onchange="this.form.submit()" class="ak-input text-xs py-1.5 w-auto">
                <option value="">Semua Arus Kas</option>
                <option value="masuk" @selected(request('jenis_aliran') === 'masuk')>Kas Masuk</option>
                <option value="keluar" @selected(request('jenis_aliran') === 'keluar')>Kas Keluar</option>
            </select>

            <button type="submit" class="ak-btn-secondary text-xs py-1.5 px-3">
                Cari
            </button>

            @if(request()->hasAny(['search', 'jenis_aliran']))
                <a href="{{ route('kategori-transaksi.index') }}" class="px-2.5 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 bg-slate-100 rounded-lg">Reset</a>
            @endif
        </form>

        <div class="text-[11px] text-slate-500 font-medium self-end sm:self-center">
            Bank Cabang: <strong class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200 font-bold">{{ $bankUtama ?? 'BRI' }}</strong>
        </div>
    </div>

    <!-- Table Kategori -->
    <div class="ak-card overflow-hidden">
        @if($kategoriList->isEmpty())
            <div class="p-8 text-center space-y-2.5">
                <div class="w-10 h-10 mx-auto rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </div>
                <p class="text-xs sm:text-sm font-bold text-slate-800">Belum ada kategori transaksi</p>
                <p class="text-[11px] text-slate-500 max-w-xs mx-auto">Tambahkan kategori transaksi atau jalankan seeder default untuk mengisi preset.</p>
                <div class="pt-1">
                    <a href="{{ route('kategori-transaksi.create') }}" class="ak-btn-primary text-xs py-1.5 px-3.5 inline-flex">
                        Tambah Kategori Baru
                    </a>
                </div>
            </div>
        @else
            <!-- Mobile View (Card List) -->
            <div class="divide-y divide-slate-100 sm:hidden">
                @foreach($kategoriList as $item)
                    @php
                        $namaDinamis = $item->getNamaDinamis($bankUtama ?? 'BRI');
                    @endphp
                    <div class="p-3 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded bg-slate-100 text-slate-500 text-[10px] font-bold flex items-center justify-center">{{ $item->urutan }}</span>
                                    <span class="text-xs font-bold text-slate-900">{{ $namaDinamis }}</span>
                                </div>
                                <div class="text-[10px] font-mono text-slate-400 ml-5.5">{{ $item->kode }}</div>
                            </div>
                            <span class="inline-block text-[9px] font-black uppercase px-2 py-0.5 rounded border {{ $item->jenis_aliran === 'masuk' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200' }}">
                                {{ $item->jenis_aliran === 'masuk' ? 'Masuk (+)' : 'Keluar (-)' }}
                            </span>
                        </div>

                        @if($item->deskripsi)
                            <p class="text-[11px] text-slate-500 line-clamp-1 sub-explanation">{{ $item->deskripsi }}</p>
                        @endif

                        <div class="flex items-center justify-between pt-1 border-t border-slate-50 text-[10px]">
                            <div class="flex items-center gap-2 text-slate-500">
                                <span>{{ strtoupper(str_replace('_', ' ', $item->metode_transaksi)) }}</span>
                                <span>•</span>
                                @if($item->is_active)
                                    <span class="text-emerald-700 font-bold">Aktif</span>
                                @else
                                    <span class="text-slate-400">Nonaktif</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('kategori-transaksi.edit', $item->id) }}" class="px-2 py-1 rounded bg-primary-50 text-primary-700 font-bold hover:bg-primary-100 transition">
                                    Ubah
                                </a>
                                <form action="{{ route('kategori-transaksi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus kategori transaksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded bg-rose-50 text-rose-700 font-bold hover:bg-rose-100 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="ak-table w-full">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">Urutan</th>
                            <th>Nama Kategori & Kode</th>
                            <th>Arus Kas</th>
                            <th>Metode</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kategoriList as $item)
                            @php
                                $namaDinamis = $item->getNamaDinamis($bankUtama ?? 'BRI');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="text-center font-bold text-slate-400">
                                    {{ $item->urutan }}
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900">{{ $namaDinamis }}</div>
                                    <div class="text-[10px] font-mono text-slate-400">{{ $item->kode }}</div>
                                    @if($item->deskripsi)
                                        <div class="text-[10px] text-slate-500 line-clamp-1 sub-explanation">{{ $item->deskripsi }}</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="inline-block text-[10px] font-black uppercase px-2 py-0.5 rounded border {{ $item->jenis_aliran === 'masuk' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200' }}">
                                        {{ $item->jenis_aliran === 'masuk' ? 'Kas Masuk (+)' : 'Kas Keluar (-)' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap text-xs font-semibold text-slate-600">
                                    {{ strtoupper(str_replace('_', ' ', $item->metode_transaksi)) }}
                                </td>
                                <td class="text-center whitespace-nowrap">
                                    @if($item->is_active)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex justify-end items-center gap-1.5">
                                        <a href="{{ route('kategori-transaksi.edit', $item->id) }}" class="px-2.5 py-1 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 font-bold text-xs transition">
                                            Ubah
                                        </a>
                                        <form action="{{ route('kategori-transaksi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus kategori transaksi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition">
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
                <div class="p-3 border-t border-slate-100">
                    {{ $kategoriList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
