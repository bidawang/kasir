@extends('layouts.app')

@section('title', 'Menu Transaksi')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 pb-12"
     x-data="{ activeTab: '{{ request('tab', 'setor') }}' }">

    <!-- Header Cabang & Bank Utama -->
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Kasir Aktif</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                {{ $warung->nama_warung ?? 'Cabang Utama' }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                Bank Utama Cabang: <span class="font-black text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200">{{ $bankUtama ?? 'BRI' }}</span>
                &bull; Operator: <span class="font-semibold text-slate-700">{{ auth()->user()->nama ?? auth()->user()->username }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
            <a href="{{ route('laporan-harian.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-xs sm:text-sm font-bold text-emerald-800 hover:bg-emerald-100 transition shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Laporan Kas Harian</span>
            </a>
            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-100 transition shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Transaksi</span>
            </a>
            @if(in_array(auth()->user()->role, ['admin', 'owner']))
                <a href="{{ route('manajemen-kasir.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-purple-200 bg-purple-50 text-xs sm:text-sm font-bold text-purple-700 hover:bg-purple-100 transition shadow-2xs">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    <span>Kelola Kasir</span>
                </a>
            @endif
        </div>
    </header>

    @php
        $kategoriMasuk = $kategoriList->where('jenis_aliran', 'masuk');
        $kategoriKeluar = $kategoriList->where('jenis_aliran', 'keluar');
    @endphp

    <!-- 3 BAGIAN NAVTAB (COMPACT & RESPONSIVE) -->
    <div class="p-1.5 bg-slate-200/70 dark:bg-slate-800/80 rounded-2xl flex items-center gap-1 shadow-inner">
        <!-- TAB 1: SETOR & TRANSFER -->
        <button type="button" @click="activeTab = 'setor'"
                :class="activeTab === 'setor' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold'"
                class="flex-1 py-2.5 px-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
            <span>Setor & Transfer</span>
            <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 font-extrabold hidden sm:inline">{{ $kategoriMasuk->count() }}</span>
        </button>

        <!-- TAB 2: TARIK TUNAI -->
        <button type="button" @click="activeTab = 'tarik'"
                :class="activeTab === 'tarik' ? 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold'"
                class="flex-1 py-2.5 px-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
            <span>Tarik Tunai</span>
            <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-800 font-extrabold hidden sm:inline">{{ $kategoriKeluar->count() }}</span>
        </button>

        <!-- TAB 3: KAS OPERASIONAL & BARANG -->
        <button type="button" @click="activeTab = 'operasional'"
                :class="activeTab === 'operasional' ? 'bg-white dark:bg-slate-900 text-primary-700 dark:text-primary-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold'"
                class="flex-1 py-2.5 px-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
            <span class="truncate">Kas Toko & Barang</span>
        </button>
    </div>

    <!-- KONTEN TAB 1: SETOR & TRANSFER -->
    <div x-show="activeTab === 'setor'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500">Pilih kategori transaksi uang masuk:</span>
            <span class="text-[11px] font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                Uang Fisik Diterima Kasir
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse($kategoriMasuk as $kat)
                @php
                    $namaDinamis = $kat->getNamaDinamis($bankUtama ?? 'BRI');
                @endphp
                <a href="{{ route('transaksi.setor', ['kategori' => $kat->id]) }}" 
                   class="group bg-white hover:bg-emerald-50/40 p-4 rounded-2xl border border-slate-200/90 hover:border-emerald-400 transition-all duration-150 shadow-2xs hover:shadow-md flex flex-col justify-between gap-3 text-left">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                {{ strtoupper(str_replace('_', ' ', $kat->metode_transaksi)) }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">#{{ $kat->urutan }}</span>
                        </div>
                        <h3 class="font-black text-sm sm:text-base text-slate-900 group-hover:text-emerald-700 transition-colors leading-snug">
                            {{ $namaDinamis }}
                        </h3>
                        @if($kat->deskripsi)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $kat->deskripsi }}</p>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-xs font-bold text-emerald-600 pt-2 border-t border-slate-100 group-hover:border-emerald-200">
                        <span>Buka Form Setor</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white p-8 rounded-2xl border border-dashed border-slate-200 text-center text-slate-400 text-xs">
                    Belum ada kategori transaksi kas masuk.
                </div>
            @endforelse
        </div>
    </div>

    <!-- KONTEN TAB 2: TARIK TUNAI -->
    <div x-show="activeTab === 'tarik'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500">Pilih kategori penarikan tunai:</span>
            <span class="text-[11px] font-black text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200">
                Uang Fisik Diserahkan ke Nasabah
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse($kategoriKeluar as $kat)
                @php
                    $namaDinamis = $kat->getNamaDinamis($bankUtama ?? 'BRI');
                @endphp
                <a href="{{ route('transaksi.tarik', ['kategori' => $kat->id]) }}" 
                   class="group bg-white hover:bg-rose-50/40 p-4 rounded-2xl border border-slate-200/90 hover:border-rose-400 transition-all duration-150 shadow-2xs hover:shadow-md flex flex-col justify-between gap-3 text-left">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-rose-100 text-rose-800">
                                {{ strtoupper(str_replace('_', ' ', $kat->metode_transaksi)) }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">#{{ $kat->urutan }}</span>
                        </div>
                        <h3 class="font-black text-sm sm:text-base text-slate-900 group-hover:text-rose-700 transition-colors leading-snug">
                            {{ $namaDinamis }}
                        </h3>
                        @if($kat->deskripsi)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $kat->deskripsi }}</p>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-xs font-bold text-rose-600 pt-2 border-t border-slate-100 group-hover:border-rose-200">
                        <span>Buka Form Tarik</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white p-8 rounded-2xl border border-dashed border-slate-200 text-center text-slate-400 text-xs">
                    Belum ada kategori penarikan tunai.
                </div>
            @endforelse
        </div>
    </div>

    <!-- KONTEN TAB 3: KAS OPERASIONAL & BARANG -->
    <div x-show="activeTab === 'operasional'" x-cloak class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500">Pilih jenis kas operasional atau penjualan:</span>
            <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                Operasional Internal
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Kas Masuk Operasional -->
            <a href="{{ route('transaksi.kas.masuk') }}" class="group flex flex-col justify-between gap-3 rounded-2xl border border-sky-200 bg-white p-4.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md hover:border-sky-400 transition text-left">
                <div>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-800 mb-3 group-hover:scale-105 transition-transform">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                    </span>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base group-hover:text-sky-700 transition-colors">Kas Masuk Toko</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pencatatan penambahan modal, setoran kas pemilik, atau pendapatan operasional lain.</p>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-sky-600 pt-2 border-t border-slate-100">
                    <span>Input Kas Masuk</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
            </a>

            <!-- Kas Keluar Operasional -->
            <a href="{{ route('transaksi.kas.keluar') }}" class="group flex flex-col justify-between gap-3 rounded-2xl border border-amber-200 bg-white p-4.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md hover:border-amber-400 transition text-left">
                <div>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800 mb-3 group-hover:scale-105 transition-transform">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/></svg>
                    </span>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base group-hover:text-amber-700 transition-colors">Kas Keluar Toko</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Biaya operasional toko, listrik, air, konsumsi, transportasi, atau beban usaha.</p>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-amber-600 pt-2 border-t border-slate-100">
                    <span>Input Kas Keluar</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
            </a>

            <!-- Jual Barang / Jasa -->
            <a href="{{ route('transaksi.penjualan-item.create') }}" class="group flex flex-col justify-between gap-3 rounded-2xl border border-purple-200 bg-white p-4.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md hover:border-purple-400 transition text-left">
                <div>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-purple-100 text-purple-800 mb-3 group-hover:scale-105 transition-transform">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                    </span>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base group-hover:text-purple-700 transition-colors">Jual Barang / Jasa</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Penjualan inventaris fisik seperti materai, kartu perdana, amplop, atau aksesoris.</p>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-purple-600 pt-2 border-t border-slate-100">
                    <span>Kasir Penjualan</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
