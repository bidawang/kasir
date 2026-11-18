@extends('layouts.app')

@section('title', 'Menu Transaksi')

@section('content')
<div class="ak-page max-w-4xl space-y-3.5 pb-12"
     x-data="{ activeTab: '{{ request('tab', 'setor') }}' }">

    <!-- Header Cabang & Status Kasir -->
    <div class="ak-head">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="ak-head-icon">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <h1 class="ak-title">{{ $warung->nama_warung ?? 'Cabang Utama' }}</h1>
                    <span class="ak-badge-primary">{{ $bankUtama ?? 'BRI' }}</span>
                    <span class="ak-badge-success">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Kasir Aktif
                    </span>
                </div>
                <p class="ak-desc sub-explanation">
                    Operator: <strong class="text-slate-700 font-bold">{{ auth()->user()->nama ?? auth()->user()->username }}</strong> &bull; Pilih layanan transaksi perbankan di bawah
                </p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <a href="{{ route('transaksi.index') }}" class="ak-btn-secondary !text-xs !py-1.5">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Riwayat</span>
            </a>
        </div>
    </div>

    @php
        $kategoriList = $kategoriList ?? collect([]);
        $kategoriMasuk = $kategoriList->where('jenis_aliran', 'masuk');
        $kategoriKeluar = $kategoriList->where('jenis_aliran', 'keluar');
    @endphp

    <!-- 3 BAGIAN NAVTAB (COMPACT & RESPONSIVE UNTUK SMARTPHONE & DESKTOP) -->
    <div class="p-1 bg-slate-200/70 dark:bg-slate-800/80 rounded-2xl flex items-center gap-1 shadow-inner">
        <!-- TAB 1: SETOR & TRANSFER -->
        <button type="button" @click="activeTab = 'setor'"
                :class="activeTab === 'setor' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold'"
                class="flex-1 py-2 px-2 rounded-xl text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
            <span>Setor & Transfer</span>
            <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 font-extrabold hidden xs:inline">{{ $kategoriMasuk->count() }}</span>
        </button>

        <!-- TAB 2: TARIK TUNAI -->
        <button type="button" @click="activeTab = 'tarik'"
                :class="activeTab === 'tarik' ? 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold'"
                class="flex-1 py-2 px-2 rounded-xl text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
            <span>Tarik Tunai</span>
            <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-800 font-extrabold hidden xs:inline">{{ $kategoriKeluar->count() }}</span>
        </button>

        <!-- TAB 3: KAS OPERASIONAL & BARANG -->
        <button type="button" @click="activeTab = 'operasional'"
                :class="activeTab === 'operasional' ? 'bg-white dark:bg-slate-900 text-primary-700 dark:text-primary-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold'"
                class="flex-1 py-2 px-2 rounded-xl text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
            <span class="truncate">Toko & Barang</span>
        </button>
    </div>

    <!-- KONTEN TAB 1: SETOR & TRANSFER -->
    <div x-show="activeTab === 'setor'" x-cloak class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 sub-explanation">Pilih kategori transaksi uang masuk:</span>
            <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                Uang Fisik Diterima Kasir (+)
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 sm:gap-3">
            @forelse($kategoriMasuk as $kat)
                @php
                    $namaDinamis = $kat->getNamaDinamis($bankUtama ?? 'BRI');
                    $keteranganDb = $kat->deskripsi ?: 'Nasabah setor uang tunai untuk layanan ' . $namaDinamis;
                @endphp
                <a href="{{ route('transaksi.setor', ['kategori' => $kat->id]) }}" 
                   class="group bg-white hover:bg-emerald-50/40 p-3.5 rounded-2xl border border-slate-200/90 hover:border-emerald-400 transition-all duration-150 shadow-2xs hover:shadow-md flex flex-col justify-between gap-2.5 text-left active:scale-[0.99]">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                {{ strtoupper(str_replace('_', ' ', $kat->metode_transaksi)) }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">#{{ $kat->urutan }}</span>
                        </div>
                        <h3 class="font-black text-xs sm:text-sm text-slate-900 group-hover:text-emerald-700 transition-colors leading-snug">
                            {{ $namaDinamis }}
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 sub-explanation font-medium">
                            {{ $keteranganDb }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between text-xs font-bold text-emerald-600 pt-2 border-t border-slate-100 group-hover:border-emerald-200">
                        <span class="text-[11px]">Buka Form Setor</span>
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white p-6 rounded-2xl border border-dashed border-slate-200 text-center text-slate-400 text-xs">
                    Belum ada kategori transaksi kas masuk.
                </div>
            @endforelse
        </div>
    </div>

    <!-- KONTEN TAB 2: TARIK TUNAI -->
    <div x-show="activeTab === 'tarik'" x-cloak class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 sub-explanation">Pilih kategori penarikan tunai:</span>
            <span class="text-[10px] font-black text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200">
                Uang Fisik Dikeluarkan Kasir (-)
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 sm:gap-3">
            @forelse($kategoriKeluar as $kat)
                @php
                    $namaDinamis = $kat->getNamaDinamis($bankUtama ?? 'BRI');
                    $keteranganDb = $kat->deskripsi ?: 'Penarikan uang tunai nasabah via ' . $namaDinamis;
                @endphp
                <a href="{{ route('transaksi.tarik', ['kategori' => $kat->id]) }}" 
                   class="group bg-white hover:bg-rose-50/40 p-3.5 rounded-2xl border border-slate-200/90 hover:border-rose-400 transition-all duration-150 shadow-2xs hover:shadow-md flex flex-col justify-between gap-2.5 text-left active:scale-[0.99]">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-rose-100 text-rose-800">
                                {{ strtoupper(str_replace('_', ' ', $kat->metode_transaksi)) }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">#{{ $kat->urutan }}</span>
                        </div>
                        <h3 class="font-black text-xs sm:text-sm text-slate-900 group-hover:text-rose-700 transition-colors leading-snug">
                            {{ $namaDinamis }}
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 sub-explanation font-medium">
                            {{ $keteranganDb }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between text-xs font-bold text-rose-600 pt-2 border-t border-slate-100 group-hover:border-rose-200">
                        <span class="text-[11px]">Buka Form Tarik</span>
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white p-6 rounded-2xl border border-dashed border-slate-200 text-center text-slate-400 text-xs">
                    Belum ada kategori penarikan tunai.
                </div>
            @endforelse
        </div>
    </div>

    <!-- KONTEN TAB 3: KAS OPERASIONAL & BARANG -->
    <div x-show="activeTab === 'operasional'" x-cloak class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-slate-500 sub-explanation">Pilih jenis kas operasional atau penjualan:</span>
            <span class="text-[10px] font-black text-primary-700 bg-primary-50 px-2.5 py-0.5 rounded-full border border-primary-200">
                Operasional Internal
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            <!-- Kas Masuk Operasional -->
            <a href="{{ route('transaksi.kas.masuk') }}" 
               class="group bg-white hover:bg-sky-50/40 p-3.5 rounded-2xl border border-slate-200/90 hover:border-sky-400 transition-all duration-150 shadow-2xs hover:shadow-md flex flex-col justify-between gap-2.5 text-left active:scale-[0.99]">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-sky-100 text-sky-800">
                            KAS MASUK
                        </span>
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                        </span>
                    </div>
                    <h3 class="font-black text-xs sm:text-sm text-slate-900 group-hover:text-sky-700 transition-colors leading-snug">
                        Kas Masuk Toko
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 sub-explanation font-medium">
                        Pencatatan penambahan modal, setoran kas pemilik, atau pendapatan operasional lain.
                    </p>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-sky-600 pt-2 border-t border-slate-100 group-hover:border-sky-200">
                    <span class="text-[11px]">Input Kas Masuk</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
            </a>

            <!-- Kas Keluar Operasional -->
            <a href="{{ route('transaksi.kas.keluar') }}" 
               class="group bg-white hover:bg-amber-50/40 p-3.5 rounded-2xl border border-slate-200/90 hover:border-amber-400 transition-all duration-150 shadow-2xs hover:shadow-md flex flex-col justify-between gap-2.5 text-left active:scale-[0.99]">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-800">
                            KAS KELUAR
                        </span>
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/></svg>
                        </span>
                    </div>
                    <h3 class="font-black text-xs sm:text-sm text-slate-900 group-hover:text-amber-700 transition-colors leading-snug">
                        Kas Keluar Toko
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 sub-explanation font-medium">
                        Biaya operasional toko, listrik, air, konsumsi kasir, transportasi, atau beban usaha.
                    </p>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-amber-600 pt-2 border-t border-slate-100 group-hover:border-amber-200">
                    <span class="text-[11px]">Input Kas Keluar</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
            </a>

            <!-- Jual Barang / Jasa -->
            <a href="{{ route('transaksi.penjualan-item.create') }}" 
               class="group bg-white hover:bg-purple-50/40 p-3.5 rounded-2xl border border-slate-200/90 hover:border-purple-400 transition-all duration-150 shadow-2xs hover:shadow-md flex flex-col justify-between gap-2.5 text-left active:scale-[0.99]">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-purple-100 text-purple-800">
                            PENJUALAN
                        </span>
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-purple-100 text-purple-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                        </span>
                    </div>
                    <h3 class="font-black text-xs sm:text-sm text-slate-900 group-hover:text-purple-700 transition-colors leading-snug">
                        Jual Barang / Jasa
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 sub-explanation font-medium">
                        Penjualan inventaris fisik seperti pulsa, token, materai, amplop, atau aksesoris.
                    </p>
                </div>
                <div class="flex items-center justify-between text-xs font-bold text-purple-600 pt-2 border-t border-slate-100 group-hover:border-purple-200">
                    <span class="text-[11px]">Kasir Penjualan</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
