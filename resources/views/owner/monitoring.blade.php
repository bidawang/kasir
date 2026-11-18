@extends('layouts.app')

@section('title', 'Pusat Kontrol & Monitoring Semua Cabang')

@section('content')
<div class="ak-page-wide space-y-3.5 pb-20" x-data="{
    filterOpen: false,
    showChart: true
}">

    <!-- 1. HEADER SECTION -->
    <div class="ak-head">
        <div>
            <div class="flex items-center gap-1.5 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-primary-600 text-white">
                    Control Center
                </span>
                <span class="text-xs text-slate-400 font-bold hidden sm:inline">•</span>
                <span class="text-xs text-slate-500 font-bold hidden sm:inline">Owner Multi-Cabang</span>
            </div>
            <h1 class="ak-title">Monitoring Konsolidasi Cabang</h1>
            <p class="ak-desc sub-explanation">
                Pantau likuiditas, omset, laba bersih, dan status tutup kas seluruh cabang secara real-time.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Tombol Cetak PDF Konsolidasi -->
            <a href="{{ route('owner.monitoring.pdf', request()->query()) }}" target="_blank" class="ak-btn-secondary">
                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.641-2.13-1.2-3.14m0 0C4.38 8.653 3.75 6.643 3.75 4.5 3.75 2.567 5.317 1 7.25 1h9.5C18.683 1 20.25 2.567 20.25 4.5c0 2.143-.63 4.153-1.77 6.189m-13.06 0c.559 1.01.96 2.064 1.2 3.14m0 0c.24 1.077.38 2.19.38 3.311 0 1.933-1.567 3.5-3.5 3.5H3.75m14.73-10c-.559 1.01-.96 2.064-1.2 3.14m0 0c-.24 1.077-.38 2.19-.38 3.311 0 1.933 1.567 3.5 3.5 3.5h-.25"/></svg>
                <span>Unduh PDF</span>
            </a>

            <!-- Tombol Transfer Kas Antar Cabang -->
            <a href="{{ route('transfer-kas.create') }}" class="ak-btn-secondary">
                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                <span>Transfer Cabang</span>
            </a>

            <!-- Tombol Tambah Cabang Baru -->
            <a href="{{ route('cabang.create') }}" class="ak-btn-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah Cabang</span>
            </a>
        </div>
    </div>

    <!-- 2. FILTER PERIODE INTERAKTIF -->
    <div class="ak-card p-3 space-y-2.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Periode:</span>
                
                @php
                    $periods = [
                        'today' => 'Hari Ini',
                        'yesterday' => 'Kemarin',
                        '7days' => '7 Hari',
                        'this_month' => 'Bulan Ini',
                        'last_month' => 'Bulan Lalu',
                    ];
                @endphp

                @foreach($periods as $key => $lbl)
                    <a href="{{ route('owner.monitoring', ['period' => $key]) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ $period === $key ? 'bg-primary-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        {{ $lbl }}
                    </a>
                @endforeach

                <button type="button" @click="filterOpen = !filterOpen"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 {{ $period === 'custom' ? 'bg-primary-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
                    <span>Custom</span>
                </button>
            </div>

            <div class="text-xs font-bold text-primary-700 bg-primary-50 px-2.5 py-1 rounded-lg border border-primary-200 inline-flex items-center gap-1.5 self-start sm:self-auto">
                <span class="w-1.5 h-1.5 rounded-full bg-primary-500 animate-pulse"></span>
                <span>{{ $periodeLabel }}</span>
            </div>
        </div>

        <!-- Custom Date Range Form (Accordion) -->
        <div x-show="filterOpen || '{{ $period }}' === 'custom'" x-collapse class="pt-2.5 border-t border-slate-100">
            <form action="{{ route('owner.monitoring') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 items-end">
                <input type="hidden" name="period" value="custom">
                <div>
                    <label class="ak-label text-[10px]">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $startDate->toDateString()) }}" class="ak-input text-xs py-1.5">
                </div>
                <div>
                    <label class="ak-label text-[10px]">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $endDate->toDateString()) }}" class="ak-input text-xs py-1.5">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="ak-btn-primary flex-1 py-1.5 text-xs">
                        Terapkan
                    </button>
                    <a href="{{ route('owner.monitoring') }}" class="ak-btn-secondary py-1.5 text-xs">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. OPERATIONAL ALERTS BAR -->
    @if(!empty($alerts))
        <div class="ak-alert-warning space-y-2">
            <div class="flex items-center gap-1.5 text-amber-900 font-extrabold text-xs">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span>Peringatan Operasional Cabang ({{ count($alerts) }} Catatan)</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-0.5">
                @foreach($alerts as $alert)
                    <div class="bg-white/90 rounded-lg p-2 border border-amber-200 flex items-start justify-between gap-2 text-xs">
                        <div class="min-w-0">
                            <span class="font-bold text-slate-900 block truncate">{{ $alert['warung_nama'] }}</span>
                            <span class="text-slate-600 text-[11px]">{{ $alert['pesan'] }}</span>
                        </div>
                        <form action="{{ route('owner.monitoring.switch', $alert['warung_id']) }}" method="POST" class="shrink-0">
                            @csrf
                            <input type="hidden" name="to" value="dashboard">
                            <button type="submit" class="px-2 py-1 rounded bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-[10px] transition cursor-pointer">
                                Kelola
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="ak-alert-success">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Semua cabang dalam kondisi operasional likuiditas normal dan terpantau dengan baik.</span>
        </div>
    @endif

    <!-- 4. 4 KARTU METRIK UTAMA KONSOLIDASI (EXECUTIVE KPI) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- 1. Total Likuiditas Konsolidasi -->
        <div class="bg-gradient-to-br from-primary-700 via-primary-800 to-primary-900 text-white rounded-2xl p-4 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-primary-200 block">Total Likuiditas Gabungan</span>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight mt-0.5">
                    Rp {{ number_format($totalLikuiditas, 0, ',', '.') }}
                </h2>
            </div>
            <div class="pt-2.5 mt-2.5 border-t border-primary-600/60 text-[11px] text-primary-100 space-y-0.5">
                <div class="flex justify-between">
                    <span class="text-primary-200">Kas Tunai (Laci):</span>
                    <span class="font-bold text-white">Rp {{ number_format($totalKasTunai, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-primary-200">Kas Digital / Bank:</span>
                    <span class="font-bold text-white">Rp {{ number_format($totalKasBank, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Laba Bersih Konsolidasi -->
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 text-white rounded-2xl p-4 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-100 block">Laba Bersih Konsolidasi</span>
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase bg-white/20 text-white">Profit</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight mt-0.5">
                    +Rp {{ number_format($totalKeuntunganKonsolidasi, 0, ',', '.') }}
                </h2>
            </div>
            <div class="pt-2.5 mt-2.5 border-t border-emerald-500/60 text-[11px] text-emerald-100 flex items-center justify-between">
                <span>Seluruh Cabang</span>
                <span class="font-bold text-white">{{ $periodeLabel }}</span>
            </div>
        </div>

        <!-- 3. Perputaran Uang / Omset Konsolidasi -->
        <div class="ak-card p-4 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Volume Transaksi Gabungan</span>
                <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight mt-0.5">
                    Rp {{ number_format($totalMasukKonsolidasi + $totalKeluarKonsolidasi, 0, ',', '.') }}
                </h2>
            </div>
            <div class="pt-2.5 mt-2.5 border-t border-slate-100 text-[11px] space-y-0.5">
                <div class="flex justify-between">
                    <span class="text-emerald-600 font-bold">Uang Masuk (+):</span>
                    <span class="font-black text-slate-800">Rp {{ number_format($totalMasukKonsolidasi, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-rose-600 font-bold">Uang Keluar (-):</span>
                    <span class="font-black text-slate-800">Rp {{ number_format($totalKeluarKonsolidasi, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Jumlah Cabang & Total Transaksi -->
        <div class="ak-card p-4 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Aktivitas Operasional</span>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        {{ $totalTransaksiKonsolidasi }}
                    </h2>
                    <span class="text-[11px] font-bold text-slate-400 uppercase">Transaksi</span>
                </div>
            </div>
            <div class="pt-2.5 mt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-500">Cabang Terdaftar:</span>
                <span class="font-black text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-100">{{ count($warungs) }} Cabang</span>
            </div>
        </div>
    </div>

    <!-- 5. GRAFIK KOMPARASI ANTAR CABANG -->
    @if(count($branchStats) > 1)
        <div class="ak-card p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-black text-slate-900">Perbandingan Kinerja Antar Cabang</h3>
                    <p class="text-[11px] text-slate-400 sub-explanation">Omset transaksi dan laba bersih tiap cabang pada periode {{ $periodeLabel }}</p>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Grafik Batang</span>
            </div>

            <div class="relative h-60 w-full">
                <canvas id="branchComparisonChart"></canvas>
            </div>
        </div>
    @endif

    <!-- 6. DAFTAR KARTU & TABEL DETAIL SELURUH CABANG -->
    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                <span>Daftar Performa Cabang</span>
                <span class="text-[10px] font-black bg-primary-100 text-primary-700 px-1.5 py-0.2 rounded-full">{{ count($branchStats) }}</span>
            </h3>
            <span class="text-[11px] text-slate-400 font-medium hidden sm:inline sub-explanation">Klik 'Beralih' untuk masuk ke dashboard cabang</span>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block ak-table-wrap">
            <table class="ak-table w-full">
                <thead>
                    <tr>
                        <th>Cabang & Bank</th>
                        <th class="text-right">Kas Tunai (Laci)</th>
                        <th class="text-right">Saldo Bank/Digital</th>
                        <th class="text-right">Total Likuiditas</th>
                        <th class="text-center">Transaksi</th>
                        <th class="text-right">Laba Bersih</th>
                        <th class="text-center">Tutup Kas Fisik</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branchStats as $bs)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $bs['is_active_session'] ? 'bg-primary-50/30' : '' }}">
                            <!-- 1. Cabang & Bank -->
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs shrink-0 {{ $bs['is_active_session'] ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                                        {{ substr($bs['nama'], 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-extrabold text-slate-900 truncate">{{ $bs['nama'] }}</span>
                                            @if($bs['is_active_session'])
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-primary-100 text-primary-800 border border-primary-200">AKTIF</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            Bank: <strong class="text-slate-600">{{ $bs['bank_utama'] }}</strong>
                                            @if(!empty($bs['karyawan_names']))
                                                • {{ count($bs['karyawan_names']) }} Kasir
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Kas Tunai -->
                            <td class="text-right whitespace-nowrap">
                                <div class="font-black {{ $bs['is_low_cash'] ? 'text-rose-600' : 'text-slate-900' }}">
                                    Rp {{ number_format($bs['kas_tunai'], 0, ',', '.') }}
                                </div>
                                @if($bs['is_low_cash'])
                                    <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-black bg-rose-50 text-rose-700 border border-rose-200 mt-0.5">
                                        Menipis
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400">Aman</span>
                                @endif
                            </td>

                            <!-- 3. Saldo Bank/Digital -->
                            <td class="text-right whitespace-nowrap">
                                <div class="font-black text-slate-900">
                                    Rp {{ number_format($bs['kas_bank'], 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-slate-400">Rekening/Wallet</span>
                            </td>

                            <!-- 4. Total Likuiditas -->
                            <td class="text-right whitespace-nowrap">
                                <div class="font-black text-primary-950 text-xs">
                                    Rp {{ number_format($bs['total_saldo'], 0, ',', '.') }}
                                </div>
                            </td>

                            <!-- 5. Transaksi -->
                            <td class="text-center whitespace-nowrap">
                                <span class="font-extrabold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs">
                                    {{ $bs['transaksi_count'] }} trx
                                </span>
                            </td>

                            <!-- 6. Laba Bersih -->
                            <td class="text-right whitespace-nowrap">
                                <div class="font-black text-emerald-600 text-xs">
                                    +Rp {{ number_format($bs['keuntungan'], 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-slate-400">Omset: Rp {{ number_format($bs['omset'], 0, ',', '.') }}</span>
                            </td>

                            <!-- 7. Status Tutup Kas Fisik -->
                            <td class="text-center whitespace-nowrap">
                                @if($bs['laporan_hari_ini'])
                                    @if($bs['laporan_hari_ini']->selisih == 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Tutup (Pas)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200" title="{{ $bs['laporan_hari_ini']->catatan }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Selisih: {{ $bs['laporan_hari_ini']->selisih > 0 ? '+' : '' }}Rp {{ number_format($bs['laporan_hari_ini']->selisih, 0, ',', '.') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Belum Tutup
                                    </span>
                                @endif
                            </td>

                            <!-- 8. Aksi Cepat -->
                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form action="{{ route('owner.monitoring.switch', $bs['id']) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="to" value="dashboard">
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-bold transition border border-primary-200 cursor-pointer" title="Beralih ke cabang ini">
                                            Beralih
                                        </button>
                                    </form>

                                    <a href="{{ route('cabang.edit', $bs['id']) }}" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Pengaturan Cabang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 013.182 3.182L8.25 18.463 4.5 19.5l1.037-3.75L16.862 3.487z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                                Belum ada cabang yang terdaftar. Tambahkan cabang pertama Anda!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:hidden">
            @foreach($branchStats as $bs)
                <div class="ak-card p-3.5 space-y-2.5 relative overflow-hidden {{ $bs['is_active_session'] ? 'ring-2 ring-primary-500/40' : '' }}">
                    <!-- Card Top Header -->
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs shrink-0 {{ $bs['is_active_session'] ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                                {{ substr($bs['nama'], 0, 2) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-black text-slate-900 text-xs truncate">{{ $bs['nama'] }}</h4>
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 mt-0.5">
                                    <span class="px-1.5 py-0.2 rounded font-bold bg-blue-50 text-blue-700 border border-blue-200">{{ $bs['bank_utama'] }}</span>
                                    @if($bs['is_active_session'])
                                        <span class="px-1.5 py-0.2 rounded font-black bg-primary-100 text-primary-800">AKTIF</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Status Tutup Kas Badge -->
                        <div>
                            @if($bs['laporan_hari_ini'])
                                @if($bs['laporan_hari_ini']->selisih == 0)
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Kas Pas
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                        Selisih Rp {{ number_format($bs['laporan_hari_ini']->selisih, 0, ',', '.') }}
                                    </span>
                                @endif
                            @else
                                <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Belum Tutup
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Saldo Breakdown -->
                    <div class="bg-slate-50 rounded-xl p-2.5 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[9px] text-slate-400 font-bold uppercase block">Kas Tunai (Laci)</span>
                            <span class="font-black text-xs block mt-0.5 {{ $bs['is_low_cash'] ? 'text-rose-600' : 'text-slate-900' }}">
                                Rp {{ number_format($bs['kas_tunai'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 font-bold uppercase block">Bank / E-Wallet</span>
                            <span class="font-black text-xs text-slate-900 block mt-0.5">
                                Rp {{ number_format($bs['kas_bank'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Periode Performance -->
                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                        <div>
                            <span class="text-[9px] text-slate-400 font-bold uppercase block">Transaksi</span>
                            <span class="font-extrabold text-slate-800 text-[11px]">{{ $bs['transaksi_count'] }} Trx</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] text-slate-400 font-bold uppercase block">Laba Bersih</span>
                            <span class="font-black text-emerald-600 text-xs">+Rp {{ number_format($bs['keuntungan'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-2 pt-1.5 border-t border-slate-100">
                        <form action="{{ route('owner.monitoring.switch', $bs['id']) }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="to" value="dashboard">
                            <button type="submit" class="w-full py-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold transition shadow-xs">
                                Buka Cabang
                            </button>
                        </form>
                        <a href="{{ route('cabang.edit', $bs['id']) }}" class="w-full py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition text-center">
                            Pengaturan
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<!-- CHART.JS SCRIPT INITIALIZATION -->
@if(count($branchStats) > 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('branchComparisonChart');
    if (!ctx) return;

    const labels = @json($chartLabels);
    const omsetData = @json($chartOmset);
    const labaData = @json($chartLaba);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Omset Transaksi (Rp)',
                    data: omsetData,
                    backgroundColor: 'rgba(0, 82, 156, 0.75)',
                    borderColor: '#00529C',
                    borderWidth: 1.5,
                    borderRadius: 6,
                },
                {
                    label: 'Laba Bersih (Rp)',
                    data: labaData,
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderColor: '#10B981',
                    borderWidth: 1.5,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000) + ' Jt';
                            }
                            return new Intl.NumberFormat('id-ID').format(value);
                        },
                        font: { size: 10 }
                    }
                },
                x: {
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 11 }
                    }
                }
            }
        }
    });
});
</script>
@endif

@endsection
