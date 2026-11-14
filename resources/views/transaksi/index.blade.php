@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12" 
     x-data="{ 
         expanded: null, 
         activeView: '{{ request('view', 'transaksi') }}',
         showPdfModal: false, 
         pdfStep: 1, 
         pdfNote: '',
         downloadReport(saveToDb) {
             const params = new URLSearchParams({
                 start_date: '{{ request('start_date') }}',
                 end_date: '{{ request('end_date') }}',
                 id_kategori: '{{ request('id_kategori') }}',
                 simpan: saveToDb ? '1' : '0',
                 catatan: this.pdfNote || ''
             });
             window.location.href = '{{ route('transaksi.download-pdf') }}?' + params.toString();
             this.showPdfModal = false;
             this.pdfStep = 1;
             this.pdfNote = '';
         }
     }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Riwayat Transaksi & Arus Kas</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                Monitoring transparan seluruh uang masuk, uang keluar, biaya admin, dan laba bersih cabang.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <!-- Download PDF Button with Double Confirmation -->
            <button type="button" 
                    @click="showPdfModal = true; pdfStep = 1;"
                    class="bg-rose-600 hover:bg-rose-700 active:scale-95 text-white px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-2 shadow-sm cursor-pointer"
                    title="Unduh Laporan Periode Ini dalam Format PDF">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <span>Download PDF</span>
            </button>

            <a href="{{ route('transaksi.menu') }}" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Transaksi Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-xs sm:text-sm text-emerald-800 flex items-center gap-2 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(in_array(auth()->user()->role, ['admin', 'owner']))
        <!-- Metric Cards Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Keuntungan Bersih -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-4 sm:p-5 text-white shadow-lg shadow-emerald-600/10 flex flex-col justify-between">
                <div class="flex items-center justify-between opacity-85 mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider">Total Laba Bersih</span>
                    <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-6h6"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-black tracking-tight">
                        Rp {{ number_format($summary->total_keuntungan ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-emerald-100 mt-1 font-medium flex items-center justify-between">
                        <span>Admin Toko: Rp {{ number_format($summary->total_admin_toko ?? 0, 0, ',', '.') }}</span>
                        <span>Bank: -{{ number_format($summary->total_admin_bank ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Uang Masuk -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Uang Masuk</span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-black text-slate-900">
                        Rp {{ number_format($summary->total_setor ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-emerald-600 font-semibold mt-1">Uang fisik diterima kasir</div>
                </div>
            </div>

            <!-- Total Uang Keluar -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Uang Keluar</span>
                    <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-black text-slate-900">
                        Rp {{ number_format($summary->total_tarik ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-rose-600 font-semibold mt-1">Uang fisik diserahkan ke nasabah</div>
                </div>
            </div>

            <!-- Jumlah Transaksi -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Volume Transaksi</span>
                    <div class="w-7 h-7 rounded-lg bg-primary-50 flex items-center justify-center text-primary-600 border border-primary-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div>
                    <div class="text-xl sm:text-2xl font-black text-slate-900">
                        {{ number_format($summary->total_transaksi ?? 0, 0, ',', '.') }} <span class="text-xs text-slate-400 font-normal">transaksi</span>
                    </div>
                    <div class="text-[11px] text-slate-400 font-medium mt-1">Total catatan aktif</div>
                </div>
            </div>
        </div>

        <!-- Filter Form & Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Form Filter -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs space-y-4">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Filter Riwayat</h3>
                <form id="filterForm" action="{{ route('transaksi.index') }}" method="GET" class="space-y-3">
                    <!-- Filter Kategori -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-600">Kategori Transaksi</label>
                        <select name="id_kategori" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}" @selected(request('id_kategori') == $kat->id)>
                                    {{ $kat->getNamaDinamis($bankUtama ?? 'BRI') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-600">Dari Tanggal</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-600">Sampai Tanggal</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                    </div>
                    <div class="pt-1 flex gap-2">
                        <button class="flex-1 bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl text-xs sm:text-sm font-bold transition shadow-xs cursor-pointer" type="submit">
                            Terapkan Filter
                        </button>
                        @if(request()->hasAny(['start_date', 'end_date', 'id_kategori']))
                            <a href="{{ route('transaksi.index') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Chart Canvas -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Grafik Arus Kas & Laba</h3>
                    <span class="text-[11px] text-slate-400">Klik titik tanggal untuk filter cepat</span>
                </div>
                <div class="relative h-60 w-full">
                    <canvas id="brilinkChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- VIEW TABS (Daftar Transaksi vs Arsip Unduhan PDF) -->
    <div class="flex items-center gap-2 border-b border-slate-200">
        <button type="button" 
                @click="activeView = 'transaksi'"
                :class="activeView === 'transaksi' 
                    ? 'border-primary-600 text-primary-700 font-extrabold' 
                    : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'"
                class="px-4 py-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
            <span>Daftar Transaksi</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-bold">
                {{ $transaksi->total() }}
            </span>
        </button>

        <button type="button" 
                @click="activeView = 'arsip'"
                :class="activeView === 'arsip' 
                    ? 'border-rose-600 text-rose-700 font-extrabold' 
                    : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'"
                class="px-4 py-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            <span>Arsip Unduhan PDF</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $arsipPdfList->total() > 0 ? 'bg-rose-100 text-rose-700 font-extrabold' : 'bg-slate-100 text-slate-600 font-bold' }}">
                {{ $arsipPdfList->total() }}
            </span>
        </button>
    </div>

    <!-- TAB 1: SECTION LIST TRANSAKSI -->
    <div x-show="activeView === 'transaksi'" id="transaksi-section" class="space-y-3.5">
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2">
                <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider">Daftar Rincian Transaksi</h3>
                @if(request('id_kategori'))
                    @php
                        $selectedKat = $kategoriList->firstWhere('id', request('id_kategori'));
                    @endphp
                    @if($selectedKat)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-primary-50 text-primary-700 border border-primary-200">
                            {{ $selectedKat->getNamaDinamis($bankUtama ?? 'BRI') }}
                        </span>
                    @endif
                @endif
            </div>
            @if(request()->hasAny(['start_date', 'end_date', 'id_kategori']))
                <a href="{{ route('transaksi.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold underline">
                    Lihat Semua
                </a>
            @endif
        </div>

        <!-- List Transaksi Items -->
        <div class="space-y-2.5">
            @forelse($transaksi as $item)
                @php
                    $isMasuk = $item->jenis === 'masuk';
                    $kategoriModel = $item->kategoriTransaksi;
                    $namaKategori = $kategoriModel 
                        ? $kategoriModel->getNamaDinamis($bankUtama ?? 'BRI') 
                        : match($item->kategori) {
                            'penjualan_item' => 'Penjualan Toko',
                            'transaksi_brilink' => 'Kas Operasional',
                            default => 'Transaksi'
                        };
                    $labaItem = $item->keuntungan != 0 ? $item->keuntungan : ($item->biaya_admin - $item->biaya_admin_bank);
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden transition-all duration-150 hover:border-slate-300">
                    <div @click="expanded = expanded === {{ $item->id }} ? null : {{ $item->id }}"
                         class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/60 transition-colors select-none">
                        
                        <div class="space-y-1.5 pr-3 min-w-0">
                            <!-- Header Badges -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-black text-sm sm:text-base text-slate-900 tracking-tight truncate max-w-xs sm:max-w-md">
                                    {{ $namaKategori }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider
                                    {{ $isMasuk ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $isMasuk ? 'Masuk' : 'Keluar' }}
                                </span>
                                @if($item->kas)
                                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                        {{ $item->kas->jenis_kas }}
                                    </span>
                                @endif
                            </div>

                            <!-- Sub Info -->
                            <div class="flex items-center gap-2 sm:gap-4 text-xs text-slate-500 flex-wrap">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $item->created_at->format('d/m/Y H:i') }}
                                </span>
                                @if($item->user)
                                    <span>Kasir: <strong class="text-slate-700 font-semibold">{{ $item->user->nama }}</strong></span>
                                @endif
                                @if($item->keterangan)
                                    <span class="text-slate-400 truncate max-w-xs">&bull; {{ $item->keterangan }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Right Metric Badges -->
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-right">
                                <div class="text-sm sm:text-base font-black {{ $isMasuk ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $isMasuk ? '+' : '-' }}Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] font-extrabold text-emerald-700">
                                    Laba: Rp {{ number_format($labaItem, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 transition-transform duration-200" :class="expanded === {{ $item->id }} ? 'rotate-180 bg-slate-200 text-slate-700' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion Content -->
                    <div x-show="expanded === {{ $item->id }}" x-collapse class="border-t border-slate-100 bg-slate-50/70 p-4 sm:p-5 space-y-4">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nominal Transaksi</span>
                                <span class="text-sm font-black text-slate-900 mt-0.5 block">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Admin Toko (Jasa)</span>
                                <span class="text-sm font-black text-emerald-700 mt-0.5 block">+Rp {{ number_format($item->biaya_admin, 0, ',', '.') }}</span>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-slate-200/70 shadow-2xs">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Potongan Bank (Server)</span>
                                <span class="text-sm font-black text-rose-600 mt-0.5 block">-Rp {{ number_format($item->biaya_admin_bank, 0, ',', '.') }}</span>
                            </div>
                            <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-200 shadow-2xs">
                                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Keuntungan Bersih Toko</span>
                                <span class="text-sm font-black text-emerald-700 mt-0.5 block">Rp {{ number_format($labaItem, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Pecahan Kas Masuk Jika Ada -->
                        @if($item->detailTransaksi->isNotEmpty())
                            <div class="space-y-1.5 pt-1">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Rincian Pecahan Uang Fisik</span>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach($item->detailTransaksi as $dt)
                                        @if($dt->jumlah > 0)
                                            <div class="bg-white px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs flex justify-between items-center">
                                                <span class="font-bold text-slate-700">Rp {{ number_format($dt->pecahan, 0, ',', '.') }}</span>
                                                <span class="font-semibold text-slate-500">{{ $dt->jumlah }} lbr</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 sm:p-12 text-center border border-slate-200/80 shadow-2xs space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-800 text-sm sm:text-base">Belum Ada Riwayat Transaksi</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Tidak ditemukan transaksi pada rentang filter tanggal atau kategori yang dipilih.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($transaksi->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-2xs flex items-center justify-between gap-3 flex-wrap">
                <div class="text-xs text-slate-500">
                    Menampilkan <span class="font-bold text-slate-800">{{ $transaksi->firstItem() }}</span> s/d <span class="font-bold text-slate-800">{{ $transaksi->lastItem() }}</span> dari <span class="font-bold text-slate-800">{{ $transaksi->total() }}</span> transaksi
                </div>
                <div>
                    {{ $transaksi->links() }}
                </div>
            </div>
        @endif
    </div> <!-- END TAB 1 -->

    <!-- TAB 2: ARSIP UNDUHAN PDF -->
    <div x-show="activeView === 'arsip'" x-cloak class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-1">
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider">Arsip Laporan PDF yang Pernah Disimpan</h3>
                <p class="text-xs text-slate-500 font-medium">Hanya data parameter tersimpan di database. PDF dibuat seketika saat diunduh (hemat penyimpanan server 0 KB).</p>
            </div>
            <button type="button" 
                    @click="showPdfModal = true; pdfStep = 1;"
                    class="self-start sm:self-auto bg-rose-600 hover:bg-rose-700 active:scale-95 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Buat Unduhan PDF Baru</span>
            </button>
        </div>

        @if($arsipPdfList->isEmpty())
            <div class="bg-white rounded-2xl p-10 text-center border border-slate-200/80 shadow-2xs space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <div>
                    <h4 class="font-black text-slate-800 text-sm">Belum Ada Riwayat Unduhan PDF</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Saat mengunduh laporan PDF dengan memilih opsi "Simpan ke Database", riwayatnya akan otomatis tercatat di sini.</p>
                </div>
            </div>
        @else
            <!-- Desktop Table Arsip PDF -->
            <div class="hidden lg:block bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[10px] font-black uppercase text-slate-500 tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">No. Dokumen & Judul</th>
                            <th class="px-4 py-3">Periode Tanggal</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3 text-right">Uang Masuk</th>
                            <th class="px-4 py-3 text-right">Uang Keluar</th>
                            <th class="px-4 py-3 text-right">Laba Bersih</th>
                            <th class="px-4 py-3">Diunduh Oleh</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($arsipPdfList as $arsip)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="font-black text-slate-900">{{ $arsip->nomor_laporan }}</div>
                                    <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ $arsip->judul }}</div>
                                    @if($arsip->catatan)
                                        <div class="text-[10px] text-amber-600 italic mt-0.5 font-medium">"{{ $arsip->catatan }}"</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-bold text-slate-800">
                                        {{ $arsip->start_date ? $arsip->start_date->format('d/m/Y') : 'Awal' }}
                                    </span>
                                    <span class="text-slate-400">s/d</span>
                                    <span class="font-bold text-slate-800">
                                        {{ $arsip->end_date ? $arsip->end_date->format('d/m/Y') : 'Hari Ini' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-[11px]">
                                        {{ $arsip->kategori_label ?? 'Semua Kategori' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-emerald-700 whitespace-nowrap">
                                    Rp {{ number_format($arsip->total_masuk, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-rose-700 whitespace-nowrap">
                                    Rp {{ number_format($arsip->total_keluar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-black text-emerald-800 whitespace-nowrap">
                                    Rp {{ number_format($arsip->total_keuntungan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">{{ $arsip->user->nama ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $arsip->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('transaksi.arsip.download', $arsip->id) }}"
                                           class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-bold text-xs flex items-center gap-1 transition shadow-2xs"
                                           title="Unduh Ulang PDF">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.25V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                            <span>Unduh PDF</span>
                                        </a>

                                        <form method="POST" action="{{ route('transaksi.arsip.destroy', $arsip->id) }}" onsubmit="return confirm('Hapus riwayat arsip laporan ini dari database?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition cursor-pointer" title="Hapus Riwayat">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards Arsip PDF -->
            <div class="lg:hidden space-y-2.5">
                @foreach($arsipPdfList as $arsip)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                    {{ $arsip->nomor_laporan }}
                                </span>
                                <h4 class="font-black text-slate-900 text-sm mt-1">{{ $arsip->judul }}</h4>
                                <div class="text-[11px] text-slate-500">
                                    Periode: <strong class="text-slate-700">{{ $arsip->start_date ? $arsip->start_date->format('d/m/Y') : 'Awal' }} - {{ $arsip->end_date ? $arsip->end_date->format('d/m/Y') : 'Hari Ini' }}</strong>
                                </div>
                            </div>
                            <span class="text-xs font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 shrink-0">
                                +Rp {{ number_format($arsip->total_keuntungan, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-[11px] bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Uang Masuk</span>
                                <span class="font-bold text-emerald-700">Rp {{ number_format($arsip->total_masuk, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Uang Keluar</span>
                                <span class="font-bold text-rose-700">Rp {{ number_format($arsip->total_keluar, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($arsip->catatan)
                            <div class="text-[11px] text-amber-700 italic bg-amber-50/70 p-2 rounded-lg border border-amber-100 font-medium">
                                "{{ $arsip->catatan }}"
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
                            <span class="text-[11px] text-slate-400">
                                {{ $arsip->user->nama ?? '-' }} &bull; {{ $arsip->created_at->format('d/m/Y H:i') }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('transaksi.arsip.download', $arsip->id) }}" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center gap-1 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.25V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                    <span>Unduh</span>
                                </a>
                                <form method="POST" action="{{ route('transaksi.arsip.destroy', $arsip->id) }}" onsubmit="return confirm('Hapus riwayat arsip ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Arsip -->
            <div class="pt-2">
                {{ $arsipPdfList->appends(['view' => 'arsip'])->links() }}
            </div>
        @endif
    </div>

    <!-- DOUBLE CONFIRMATION MODAL DOWNLOAD PDF (2 LANGKAH) -->
    <div x-show="showPdfModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="showPdfModal = false" class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200 space-y-4">
            
            <!-- STEP 1: KONFIRMASI PERIODE & DATA LAPORAN -->
            <template x-if="pdfStep === 1">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded">Konfirmasi 1 dari 2</span>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">Unduh Laporan Transaksi (PDF)</h3>
                            <p class="text-xs text-slate-500 font-medium">Periksa ringkasan dan periode data sebelum mengunduh.</p>
                        </div>
                    </div>

                    <!-- Summary Preview Box -->
                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200 space-y-2 text-xs">
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Periode Tanggal:</span>
                            <span class="font-bold text-slate-800">
                                {{ request('start_date') ? date('d/m/Y', strtotime(request('start_date'))) : 'Semua (Awal)' }} 
                                s/d 
                                {{ request('end_date') ? date('d/m/Y', strtotime(request('end_date'))) : 'Hari Ini' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Kategori:</span>
                            <span class="font-bold text-slate-800">
                                {{ request('id_kategori') && $kategoriList->firstWhere('id', request('id_kategori')) ? $kategoriList->firstWhere('id', request('id_kategori'))->getNamaDinamis($bankUtama ?? 'BRI') : 'Semua Kategori' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Total Uang Masuk:</span>
                            <span class="font-bold text-emerald-700">Rp {{ number_format($summary->total_setor ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium">Total Uang Keluar:</span>
                            <span class="font-bold text-rose-700">Rp {{ number_format($summary->total_tarik ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-slate-500 font-medium">Laba Bersih Terhitung:</span>
                            <span class="font-black text-emerald-700 text-sm">Rp {{ number_format($summary->total_keuntungan ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <button type="button" @click="showPdfModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-bold hover:bg-slate-100 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="button" @click="pdfStep = 2" class="px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-bold transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                            <span>Lanjut ke Langkah 2</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>
                </div>
            </template>

            <!-- STEP 2: KONFIRMASI SIMPAN KE DATABASE ATAU TIDAK -->
            <template x-if="pdfStep === 2">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Konfirmasi 2 dari 2</span>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 mt-1">Simpan Riwayat Laporan ke Database?</h3>
                            <p class="text-xs text-slate-500 font-medium">Tentukan apakah riwayat unduhan ini ingin dicatat di arsip web.</p>
                        </div>
                    </div>

                    <!-- Hemat Storage Callout -->
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-800 space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Hemat Penyimpanan Server (0 KB File)</span>
                        </div>
                        <p class="text-[11px] text-emerald-700 leading-relaxed">
                            Sistem hanya mencatat parameter & angka ringkasan di database (tidak menyimpan file fisik .pdf di disk server). Anda dapat mengunduh ulang laporan ini kapan saja di tab <strong>Arsip Unduhan PDF</strong>.
                        </p>
                    </div>

                    <!-- Input Catatan Opsional -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Catatan Laporan <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <input type="text" x-model="pdfNote" placeholder="Contoh: Laporan tutup shift minggu ke-2 cabang utama" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                    </div>

                    <!-- Pilihan Aksi (Simpan & Unduh vs Unduh Saja) -->
                    <div class="space-y-2 pt-2">
                        <button type="button" @click="downloadReport(true)" class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
                            <span>Ya, Simpan ke Database & Unduh PDF</span>
                        </button>

                        <button type="button" @click="downloadReport(false)" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.25V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            <span>Unduh Saja Tanpa Simpan</span>
                        </button>

                        <button type="button" @click="pdfStep = 1" class="w-full text-slate-400 hover:text-slate-600 text-xs py-1 font-semibold transition cursor-pointer">
                            ← Kembali ke Langkah 1
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

</div>

@if(in_array(auth()->user()->role, ['admin', 'owner']))
    <!-- Chart Script (untuk admin & owner) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('brilinkChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const rawDates = @json($chartRawDates ?? []);
            const displayLabels = @json($chartLabels ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: displayLabels,
                    datasets: [
                        {
                            label: 'Laba Bersih',
                            data: @json($chartProfit ?? []),
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Kas Masuk',
                            data: @json($chartSetor ?? []),
                            borderColor: '#2563eb',
                            borderWidth: 2,
                            tension: 0.35,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Kas Keluar',
                            data: @json($chartTarik ?? []),
                            borderColor: '#e11d48',
                            borderWidth: 2,
                            tension: 0.35,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    },
                    onClick: (event, activeElements) => {
                        if (activeElements.length > 0) {
                            const index = activeElements[0].index;
                            const selectedDate = rawDates[index];

                            document.getElementById('start_date').value = selectedDate;
                            document.getElementById('end_date').value = selectedDate;
                            document.getElementById('filterForm').submit();
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            ticks: { callback: v => 'Rp ' + (v / 1000).toLocaleString('id-ID') + 'k' }
                        },
                        y1: {
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') }
                        }
                    }
                }
            });
        });
    </script>
@endif
@endsection
