@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
@php
    $isKasir = $isKasir ?? (auth()->check() && auth()->user()->role === 'kasir');
    $saldoEwallet = $saldoEwallet ?? collect([]);
    $saldoTunai = $saldoTunai ?? 0;
    $totalSaldo = $totalSaldo ?? 0;
    $pemasukanHariIni = $pemasukanHariIni ?? 0;
    $pengeluaranHariIni = $pengeluaranHariIni ?? 0;
    $recentTransactions = $recentTransactions ?? collect([]);
    $totalTransaksiHariIni = $totalTransaksiHariIni ?? 0;
    $totalItemHariIni = $totalItemHariIni ?? 0;
    $keuntunganHariIni = $keuntunganHariIni ?? 0;
    $laporanHarianHariIni = $laporanHarianHariIni ?? null;
@endphp

<div class="space-y-4 sm:space-y-5 max-w-2xl mx-auto pb-14 text-slate-800">

    @if($isKasir)
        <!-- ==================== DASHBOARD KHUSUS KASIR ==================== -->
        
        <!-- 1. GREETING & SHIFT STATUS BANNER KASIR -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-950 via-primary-900 to-indigo-950 p-5 sm:p-6 text-white shadow-xl border border-primary-800">
            <!-- Decorative Glow -->
            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-primary-400/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950 shadow-xs">
                                Kasir Jaga
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-primary-200 font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                {{ $kasir->nama_warung ?? 'Cabang Aktif' }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white mt-1">
                            Halo, {{ auth()->user()->nama ?? auth()->user()->username }}! 👋
                        </h1>
                        <p class="text-xs text-primary-200/80 mt-0.5">
                            Shift kerja: {{ now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>

                    <!-- Indikator Status Laporan Kas Fisik Hari Ini -->
                    @if($laporanHarianHariIni)
                        <div class="bg-emerald-500/20 border border-emerald-400/30 rounded-2xl p-3 flex items-center gap-2.5 shrink-0">
                            <div class="w-8 h-8 rounded-xl bg-emerald-400 text-slate-950 flex items-center justify-center shrink-0 font-black">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </div>
                            <div class="text-left leading-tight">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-300">Laporan Kas Fisik</div>
                                <div class="text-xs font-black text-white">Sudah Disetor</div>
                                <div class="text-[10px] text-emerald-200/80">Rp {{ number_format($laporanHarianHariIni->saldo_fisik, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('laporan-harian.index') }}" class="bg-amber-400 hover:bg-amber-300 active:scale-95 text-slate-950 font-black px-3.5 py-2.5 rounded-2xl text-xs flex items-center gap-2 transition shadow-md shrink-0">
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            <span>Input Fisik Uang Kas</span>
                        </a>
                    @endif
                </div>

                <!-- Ringkasan Shift Hari Ini -->
                <div class="grid grid-cols-3 gap-2 sm:gap-3 pt-3 border-t border-primary-800/80">
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                        <span class="text-[10px] sm:text-[11px] text-slate-300 font-bold block uppercase tracking-wider truncate">Transaksi Shift</span>
                        <span class="text-base sm:text-lg font-black text-white mt-1 block">
                            {{ $totalTransaksiHariIni }}
                        </span>
                    </div>

                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                        <span class="text-[10px] sm:text-[11px] text-emerald-300 font-bold block uppercase tracking-wider truncate">Uang Masuk (+)</span>
                        <span class="text-xs sm:text-sm font-black text-emerald-400 mt-1 block truncate">
                            Rp {{ number_format($pemasukanHariIni, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                        <span class="text-[10px] sm:text-[11px] text-rose-300 font-bold block uppercase tracking-wider truncate">Uang Keluar (-)</span>
                        <span class="text-xs sm:text-sm font-black text-rose-400 mt-1 block truncate">
                            Rp {{ number_format($pengeluaranHariIni, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MENU CEPAT LAYANAN KASIR -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('transaksi.menu') }}" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-primary-500 hover:shadow-md transition-all flex flex-col justify-between gap-3 shadow-2xs group">
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <span class="block text-xs sm:text-sm font-black text-slate-900 group-hover:text-primary-600 transition-colors">Transaksi Baru</span>
                    <span class="text-[11px] text-slate-400 font-medium">Setor, tarik & transfer</span>
                </div>
            </a>

            <a href="{{ route('transaksi.penjualan-item.create') }}" class="p-3.5 rounded-2xl bg-white border border-purple-200 hover:border-purple-400 hover:shadow-md transition-all flex flex-col justify-between gap-3 shadow-2xs group">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                </div>
                <div>
                    <span class="block text-xs sm:text-sm font-black text-slate-900 group-hover:text-purple-700 transition-colors">Jual Barang</span>
                    <span class="text-[11px] text-purple-600 font-bold">Pulsa, token & item</span>
                </div>
            </a>

            <a href="{{ route('laporan-harian.index') }}" class="p-3.5 rounded-2xl bg-white border border-emerald-200 hover:border-emerald-400 hover:shadow-md transition-all flex flex-col justify-between gap-3 shadow-2xs group">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="block text-xs sm:text-sm font-black text-slate-900 group-hover:text-emerald-700 transition-colors">Laporan Kas Fisik</span>
                    <span class="text-[11px] text-emerald-600 font-bold">Hitung uang laci shift</span>
                </div>
            </a>

            <a href="{{ route('transaksi.index') }}" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-primary-500 hover:shadow-md transition-all flex flex-col justify-between gap-3 shadow-2xs group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <span class="block text-xs sm:text-sm font-black text-slate-900 group-hover:text-primary-600 transition-colors">Riwayat Transaksi</span>
                    <span class="text-[11px] text-slate-400 font-medium">Struk & bukti transaksi</span>
                </div>
            </a>
        </div>

    @else
        <!-- ==================== DASHBOARD KHUSUS OWNER / ADMIN ==================== -->
        
        <!-- BANNER PROMINEN: PUSAT MONITORING SEMUA CABANG -->
        <a href="{{ route('owner.monitoring') }}" class="block p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 text-slate-950 shadow-md hover:shadow-lg transition-all active:scale-[0.99] group border border-amber-300">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-slate-950 text-amber-400 flex items-center justify-center shrink-0 font-black shadow-md group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.2 rounded text-[10px] font-black uppercase tracking-wider bg-slate-950 text-amber-300">Fitur Baru Owner</span>
                            <span class="text-xs font-black text-amber-950 uppercase hidden sm:inline">Konsolidasi Multi-Cabang</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-black text-slate-950 tracking-tight mt-0.5 truncate">
                            Pusat Monitoring Semua Cabang
                        </h3>
                        <p class="text-xs font-semibold text-amber-950/80 truncate">
                            Pantau likuiditas, omset, laba, dan tutup kas seluruh cabang dalam 1 layar
                        </p>
                    </div>
                </div>
                <div class="shrink-0 w-8 h-8 rounded-full bg-slate-950/10 flex items-center justify-center group-hover:translate-x-1 transition-transform">
                    <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </div>
            </div>
        </a>

        <!-- 1. KARTU SALDO UTAMA CABANG (OWNER / ADMIN) -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6 text-white shadow-xl border border-slate-800">
            <!-- Background Decorative Glow -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs sm:text-sm text-slate-400 font-semibold uppercase tracking-wider">Total Saldo Kas Cabang</span>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-emerald-500/20 border border-emerald-500/30 px-3 py-1 rounded-full text-emerald-300 font-bold shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ $kasir->nama_warung ?? 'Cabang Aktif' }}
                    </span>
                </div>

                <div>
                    <span class="text-xs text-slate-400 block font-medium">Akumulasi kas tunai, e-wallet, & rekening bank</span>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white mt-1">
                        Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}
                    </h1>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-800/80">
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-3 border border-white/10 flex flex-col justify-between">
                        <p class="text-[11px] text-slate-300 font-bold flex items-center gap-1.5 uppercase tracking-wider">
                            <span class="w-5 h-5 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
                            </span>
                            Masuk Hari Ini
                        </p>
                        <p class="font-black text-sm sm:text-base text-emerald-400 mt-2">
                            Rp {{ number_format($pemasukanHariIni ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-3 border border-white/10 flex flex-col justify-between">
                        <p class="text-[11px] text-slate-300 font-bold flex items-center gap-1.5 uppercase tracking-wider">
                            <span class="w-5 h-5 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l-6 6m6-6l6 6"/></svg>
                            </span>
                            Keluar Hari Ini
                        </p>
                        <p class="font-black text-sm sm:text-base text-rose-400 mt-2">
                            Rp {{ number_format($pengeluaranHariIni ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MENU CEPAT LAYANAN UTAMA OWNER -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('transaksi.menu') }}" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-primary-500 hover:shadow-md transition-all flex flex-col justify-between gap-3 shadow-2xs group">
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <span class="block text-xs sm:text-sm font-black text-slate-900 group-hover:text-primary-600 transition-colors">Transaksi Baru</span>
                    <span class="text-[11px] text-slate-400 font-medium">Setor, tarik & transfer</span>
                </div>
            </a>

            <a href="{{ route('laporan-harian.index') }}" class="p-3.5 rounded-2xl bg-white border border-emerald-200 hover:border-emerald-400 hover:shadow-md transition-all flex flex-col justify-between gap-3 shadow-2xs group">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="block text-xs sm:text-sm font-black text-slate-900 group-hover:text-emerald-700 transition-colors">Laporan Harian</span>
                    <span class="text-[11px] text-emerald-600 font-bold">Tutup buku kas fisik</span>
                </div>
            </a>

            <a href="{{ route('transaksi.index') }}" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-primary-500 hover:shadow-md transition-all flex flex-col justify-between gap-3 shadow-2xs group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <span class="block text-xs sm:text-sm font-black text-slate-900 group-hover:text-primary-600 transition-colors">Riwayat Kas</span>
                    <span class="text-[11px] text-slate-400 font-medium">Arus masuk & keluar</span>
                </div>
            </a>

            <a href="{{ route('manajemen-kasir.index') }}" class="p-3.5 rounded-2xl bg-white border border-slate-200/80 hover:border-primary-500 hover:shadow-md transition-all flex flex-col justify-between gap-3 shadow-2xs group">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <div>
                    <span class="block text-xs sm:text-sm font-black text-slate-900 group-hover:text-purple-700 transition-colors">Kelola Kasir</span>
                    <span class="text-[11px] text-slate-400 font-medium">Akun kasir & staf</span>
                </div>
            </a>
        </div>
    @endif

    <!-- 3. DETAIL KAS TUNAI FISIK (LACI KASIR / FISIK TOKO) -->
    @if($kasTunai)
        <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden transition-all duration-200">
            <button @click="open = !open" type="button" class="w-full flex items-center justify-between p-4 text-left hover:bg-slate-50/80 transition-colors group cursor-pointer">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0 border border-emerald-200/60 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v9.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 class="font-black text-slate-900 text-sm sm:text-base group-hover:text-emerald-700 transition-colors">
                                {{ $isKasir ? 'Kas Tunai Laci Kasir' : 'Kas Tunai Fisik Toko' }}
                            </h2>
                            <span class="text-[10px] font-black bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full uppercase">Fisik</span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                            Saldo: <span class="text-slate-900 font-bold">Rp {{ number_format($kasTunai->saldo ?? 0, 0, ',', '.') }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 ml-2">
                    <span class="text-xs font-bold text-slate-400 hidden sm:inline" x-text="open ? 'Sembunyikan' : 'Rincian Pecahan'"></span>
                    <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-slate-200 transition-colors">
                        <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>
            </button>

            <div x-show="open" x-collapse class="border-t border-slate-100 bg-slate-50/50 p-4 space-y-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @php $hasItems = false; @endphp
                    @forelse($kasTunai->detailKas as $detail)
                        @if($detail->jumlah > 0)
                            @php $hasItems = true; @endphp
                            <div class="flex items-center justify-between bg-white px-3.5 py-2.5 rounded-xl border border-slate-200/80 text-xs shadow-2xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-800">Rp {{ number_format($detail->pecahan, 0, ',', '.') }}</span>
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold text-[11px] border border-slate-200">
                                        {{ number_format($detail->jumlah, 0, ',', '.') }} lbr
                                    </span>
                                </div>
                                <span class="font-extrabold text-slate-900">
                                    Rp {{ number_format($detail->pecahan * $detail->jumlah, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    @empty
                    @endforelse

                    @if(!$hasItems)
                        <div class="col-span-full text-center py-4 text-slate-400 text-xs bg-white rounded-xl border border-dashed border-slate-200">
                            Belum ada rincian pecahan fisik uang tunai.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- 4. SALDO E-WALLET & REKENING BANK AGEN (Untuk Acuan Transaksi Kasir) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <div>
                <h2 class="font-black text-slate-700 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    Saldo Rekening Bank & E-Wallet Toko
                </h2>
                <p class="text-[11px] text-slate-400 font-medium">Acuan ketersediaan saldo sebelum melayani transfer nasabah</p>
            </div>
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'owner'], true))
                <a href="{{ route('kas.index') }}" class="text-xs text-primary-600 font-bold hover:underline flex items-center gap-0.5">
                    Kelola Kas <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            @endif
        </div>

        <div class="space-y-2">
            @forelse($saldoEwallet as $kas)
                <div class="bg-white rounded-2xl px-4 py-3 border border-slate-200/80 shadow-2xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shrink-0 border border-blue-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5h18M3 9.75h18m-13.5-6h9a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-9a2.25 2.25 0 01-2.25-2.25V6A2.25 2.25 0 017.5 3.75z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <h3 class="font-bold text-slate-800 text-xs sm:text-sm truncate">
                                    {{ $kas->jenis_kas }}
                                </h3>
                                @if($kas->is_primary)
                                    <span class="inline-block px-1.5 py-0.2 text-[9px] font-black bg-amber-100 text-amber-800 rounded">UTAMA</span>
                                @endif
                            </div>
                            <span class="inline-block text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.2 rounded-md border border-blue-100 mt-0.5">
                                {{ strtoupper($kas->status ?? 'digital') }}
                            </span>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                            Rp {{ number_format($kas->saldo, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-6 text-center border border-slate-200/80 text-slate-400 text-xs shadow-2xs">
                    Belum ada e-wallet atau rekening bank yang ditambahkan pada cabang ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 5. TRANSAKSI TERBARU (SHIFT KASIR / CABANG) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h2 class="font-black text-slate-700 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $isKasir ? 'Transaksi Terbaru Saya Hari Ini' : 'Transaksi Terbaru Cabang' }}
            </h2>
            <a href="{{ route('transaksi.index') }}" class="text-xs text-primary-600 font-bold hover:underline flex items-center gap-0.5">
                Lihat Semua <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
        </div>

        <div class="space-y-2.5">
            @forelse($recentTransactions as $transaksi)
                @php
                    $isMasuk = $transaksi->jenis === 'masuk';
                    $isTransfer = $transaksi->kategori === 'pemindahan_dana';
                    $nominalTampil = $transaksi->nominal > 0 ? $transaksi->nominal : $transaksi->total;
                @endphp
                <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl shrink-0 flex items-center justify-center font-bold text-xs {{ $isTransfer ? ($isMasuk ? 'bg-cyan-50 text-cyan-700 border border-cyan-200/60' : 'bg-indigo-50 text-indigo-700 border border-indigo-200/60') : ($isMasuk ? 'bg-emerald-50 text-emerald-600 border border-emerald-200/60' : 'bg-rose-50 text-rose-600 border border-rose-200/60') }}">
                            @if($isTransfer)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                            @elseif($isMasuk)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l-6 6m6-6l6 6"/></svg>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-800 text-xs sm:text-sm truncate">
                                {{ $transaksi->keterangan ?? ($isTransfer ? 'Pemindahan Dana' : ($transaksi->itemJualan ? 'Jual: ' . $transaksi->itemJualan->nama_item : 'Transaksi Kas')) }}
                            </h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span>{{ $transaksi->created_at ? $transaksi->created_at->format('H:i') . ' • ' . $transaksi->created_at->diffForHumans() : '-' }}</span>
                                @if($isTransfer)
                                    <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold {{ $isMasuk ? 'bg-cyan-100 text-cyan-800' : 'bg-indigo-100 text-indigo-800' }}">
                                        {{ $isMasuk ? 'Transfer Masuk' : 'Transfer Keluar' }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <p class="font-black text-xs sm:text-sm {{ $isTransfer ? ($isMasuk ? 'text-cyan-700' : 'text-indigo-700') : ($isMasuk ? 'text-emerald-600' : 'text-rose-600') }}">
                            {{ $isMasuk ? '+' : '-' }} Rp {{ number_format($nominalTampil, 0, ',', '.') }}
                        </p>
                        <span class="inline-block text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md border border-slate-200/60 mt-1">
                            {{ $transaksi->kas->jenis_kas ?? 'Kas Tunai' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-6 text-center border border-slate-200/80 text-slate-400 text-xs shadow-2xs">
                    {{ $isKasir ? 'Belum ada transaksi yang Anda proses pada shift hari ini.' : 'Belum ada transaksi terbaru pada cabang ini.' }}
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
