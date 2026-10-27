@extends('layouts.app')

@section('title', 'Daftar Kas & E-Wallet')

@section('content')
<div class="max-w-2xl mx-auto pb-12 space-y-5 text-slate-800">

    {{-- Header Section --}}
    <div class="flex items-center justify-between px-1">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v1a1 1 0 01-1 1h-3a1 1 0 00-1 1v1a1 1 0 001 1h4a1 1 0 001-1m-4-8v1m0 8v1" />
                    </svg>
                </div>
                Manajemen Kas & E-Wallet
            </h1>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Kelola likuiditas saldo tunai fisik dan akun digital</p>
        </div>
@if(in_array(auth()->user()->role, ['admin', 'owner']))
        <a href="{{ route('kas.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-md shadow-indigo-600/10 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Tambah Akun</span>
        </a>
		@endif
    </div>

    {{-- Notification Alerts --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200/80 text-emerald-800 p-3 rounded-2xl text-xs font-semibold flex items-center gap-2.5 shadow-xs">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200/80 text-rose-800 p-3 rounded-2xl text-xs font-semibold flex items-center gap-2.5 shadow-xs">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @php
        $saldoTunai = $kasTunai ? $kasTunai->saldo : 0;
        $saldoEwallet = $kasEwallet ? $kasEwallet->sum('saldo') : 0;
        $totalLikuiditas = $saldoTunai + $saldoEwallet;
    @endphp

    {{-- SECTION 0: SUMMARY TOTAL LIKUIDITAS --}}
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 flex items-center justify-between gap-3">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Likuiditas Kas</span>
            <div class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                Rp {{ number_format($totalLikuiditas, 0, ',', '.') }}
            </div>
        </div>
        <div class="text-right">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Akun Aktif</span>
            <span class="text-xs font-extrabold text-indigo-900 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100 inline-block mt-1">
                {{ ($kasTunai ? 1 : 0) + $kasEwallet->count() }} Akun
            </span>
        </div>
    </div>

    {{-- SECTION: KARTU MUTASI BRILINK (ACCORDION INTERAKTIF + AJAX FILTER) --}}
    <div class="bg-slate-900 text-white rounded-2xl p-4 shadow-md border border-slate-800 space-y-3">
        
        {{-- Accordion Header --}}
        <div class="flex items-center justify-between cursor-pointer select-none" onclick="toggleBrilinkAccordion()">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                Mutasi & Riwayat BRILink
            </span>
            <div class="flex items-center gap-2">
                <span id="accordionStatusText" class="text-[11px] text-indigo-300 font-semibold">Buka Riwayat</span>
                <svg id="accordionArrow" class="w-4 h-4 text-indigo-300 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>

        {{-- Ringkasan Total --}}
        <div class="grid grid-cols-2 gap-2.5 pt-1 border-t border-slate-800">
            <div class="bg-slate-800/80 p-2.5 rounded-xl border border-slate-700/50 flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-400 block">Kas Masuk</span>
                    <span id="summaryMasuk" class="text-xs sm:text-sm font-black text-emerald-400">Rp {{ number_format($summaryBrilink->total_masuk ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                    </svg>
                </div>
            </div>
            <div class="bg-slate-800/80 p-2.5 rounded-xl border border-slate-700/50 flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-rose-400 block">Kas Keluar</span>
                    <span id="summaryKeluar" class="text-xs sm:text-sm font-black text-rose-400">Rp {{ number_format($summaryBrilink->total_keluar ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Accordion Content (Filter & Kontainer Data Riwayat) --}}
        <div id="brilinkAccordionBody" class="hidden pt-3 border-t border-slate-800/80 space-y-3">
            
            {{-- Filter Periode --}}
            <div class="bg-slate-800/50 p-2.5 rounded-xl border border-slate-700/40 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Filter Periode</label>
                    <select id="filterPeriode" onchange="handlePeriodeChange()" class="bg-slate-900 text-xs text-slate-200 border border-slate-700 rounded-lg px-2 py-1 focus:outline-none focus:border-indigo-500">
                        <option value="today" selected>Hari Ini</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="custom">Kustom Tanggal</option>
                    </select>
                </div>

                {{-- Input Kustom Tanggal (Default Hidden) --}}
                <div id="customDateContainer" class="hidden grid-cols-2 gap-2 pt-1">
                    <div>
                        <span class="text-[9px] text-slate-400 block mb-0.5">Dari</span>
                        <input type="date" id="startDate" class="w-full bg-slate-900 text-xs text-slate-200 border border-slate-700 rounded-lg p-1.5 focus:outline-none">
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 block mb-0.5">Sampai</span>
                        <input type="date" id="endDate" class="w-full bg-slate-900 text-xs text-slate-200 border border-slate-700 rounded-lg p-1.5 focus:outline-none">
                    </div>
                    <button onclick="fetchBrilinkHistory()" class="col-span-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-1.5 rounded-lg transition-colors mt-1">
                        Terapkan Filter
                    </button>
                </div>
            </div>

            {{-- Container List Riwayat Transaksi --}}
            <div id="brilinkListContainer" class="space-y-2 max-h-60 overflow-y-auto pr-1">
                <div class="text-center py-4 text-slate-400 text-xs">Memuat data...</div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS: KAS & OPERASIONAL INTERN --}}
    <div class="space-y-1.5">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">Kas & Operasional Intern</span>
        <div class="grid grid-cols-3 gap-2.5">
            <a href="{{ route('transfer-kas.index') }}"
               class="bg-white hover:bg-indigo-50/50 active:scale-98 p-3 rounded-2xl border border-slate-200/80 hover:border-indigo-300 transition-all shadow-xs group flex flex-col items-center justify-center text-center">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 group-hover:bg-indigo-600 text-indigo-600 group-hover:text-white flex items-center justify-center transition-colors mb-1.5 border border-indigo-100 group-hover:border-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-indigo-950 transition-colors">Pemindahan Dana</span>
                <span class="text-[9px] font-semibold text-slate-400 mt-0.5">Transfer Antar Kas</span>
            </a>

            <a href="{{ route('transaksi.kas.masuk') }}"
               class="bg-white hover:bg-emerald-50/50 active:scale-98 p-3 rounded-2xl border border-slate-200/80 hover:border-emerald-300 transition-all shadow-xs group flex flex-col items-center justify-center text-center">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 group-hover:bg-emerald-600 text-emerald-600 group-hover:text-white flex items-center justify-center transition-colors mb-1.5 border border-emerald-100 group-hover:border-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-950 transition-colors">Kas Masuk</span>
                <span class="text-[9px] font-semibold text-slate-400 mt-0.5">Tambah Saldo Kas</span>
            </a>

            <a href="{{ route('transaksi.kas.keluar') }}"
               class="bg-white hover:bg-rose-50/50 active:scale-98 p-3 rounded-2xl border border-slate-200/80 hover:border-rose-300 transition-all shadow-xs group flex flex-col items-center justify-center text-center">
                <div class="w-9 h-9 rounded-xl bg-rose-50 group-hover:bg-rose-600 text-rose-600 group-hover:text-white flex items-center justify-center transition-colors mb-1.5 border border-rose-100 group-hover:border-rose-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-800 group-hover:text-rose-950 transition-colors">Kas Keluar</span>
                <span class="text-[9px] font-semibold text-slate-400 mt-0.5">Pengeluaran Intern</span>
            </a>
        </div>
    </div>

    {{-- SECTION 1: KAS UTAMA / FISIK (CASH) --}}
@if($kasTunai)
    <div class="bg-slate-900 text-white rounded-2xl p-5 sm:p-6 shadow-xl border border-slate-800 relative overflow-hidden my-3">
        {{-- Efek Glow Background --}}
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-4">
            {{-- Header Card --}}
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-400 bg-emerald-950/90 px-2.5 py-1 rounded-md border border-emerald-800/60">
                        Kas Utama (Fisik)
                    </span>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0">
                    {{ $kasTunai->jenis_kas }}
                </span>
            </div>

            {{-- Body Card --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pt-1">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">
                        Saldo Tersedia
                    </span>
                    <div class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-none">
                        Rp {{ number_format($kasTunai->saldo, 0, ',', '.') }}
                    </div>
                </div>
@if(in_array(auth()->user()->role, ['admin', 'owner']))
                <a href="{{ route('kas.show', $kasTunai->id) }}"
                   class="bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 hover:text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 border border-slate-700 shadow-sm shrink-0">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.573 16.49 16.638 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Lihat Detail Cash</span>
                </a>
				@endif
            </div>
        </div>
    </div>
@endif

    {{-- SECTION 2: DAFTAR AKUN E-WALLET --}}
    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1 pt-1">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                </svg>
                Daftar E-Wallet & Bank Digital
            </h3>
            <span class="text-[10px] font-extrabold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200/60">
                {{ $kasEwallet->count() }} Akun
            </span>
        </div>

        <div class="space-y-2">
            @forelse($kasEwallet as $item)
                <div class="bg-white rounded-2xl p-3 sm:p-3.5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex items-center justify-between gap-3">
                    
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center shrink-0 border border-indigo-100 font-extrabold text-xs tracking-wider">
                            {{ strtoupper(substr($item->nama_kas ?? $item->jenis_kas, 0, 2)) }}
                        </div>
                        
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 tracking-tight truncate">
                                {{ $item->nama_kas ?? $item->jenis_kas }}
                            </h4>
                            <p class="text-sm font-black text-slate-800 tracking-tight mt-0.5">
                                Rp {{ number_format($item->saldo, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="{{ route('kas.edit', $item->id) }}" 
                           class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 active:scale-95 text-amber-700 flex items-center justify-center transition-all border border-amber-200/60"
                           title="Edit Akun">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125"/>
                            </svg>
                        </a>

                        <form action="{{ route('kas.destroy', $item->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin ingin menghapus akun {{ $item->nama_kas ?? $item->jenis_kas }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 text-rose-700 flex items-center justify-center transition-all border border-rose-200/60"
                                    title="Hapus Akun">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-2xl p-6 text-center border border-slate-200/80 shadow-xs space-y-1">
                    <div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 border border-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-slate-700">Belum Ada Akun E-Wallet</p>
                    <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Tambahkan e-wallet (OVO, Dana, BRI, dll) untuk mempermudah pencatatan transaksi BRILink Anda.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- JavaScript untuk Accordion dan AJAX Interaktif --}}
<script>
    let accordionOpen = false;

    function toggleBrilinkAccordion() {
        const body = document.getElementById('brilinkAccordionBody');
        const arrow = document.getElementById('accordionArrow');
        const statusText = document.getElementById('accordionStatusText');

        accordionOpen = !accordionOpen;

        if (accordionOpen) {
            body.classList.remove('hidden');
            arrow.classList.add('rotate-180');
            statusText.innerText = 'Tutup Riwayat';
            fetchBrilinkHistory();
        } else {
            body.classList.add('hidden');
            arrow.classList.remove('rotate-180');
            statusText.innerText = 'Buka Riwayat';
        }
    }

    function handlePeriodeChange() {
        const periode = document.getElementById('filterPeriode').value;
        const customContainer = document.getElementById('customDateContainer');

        if (periode === 'custom') {
            customContainer.classList.remove('hidden');
            customContainer.classList.add('grid');
        } else {
            customContainer.classList.add('hidden');
            customContainer.classList.remove('grid');
            fetchBrilinkHistory();
        }
    }

    function fetchBrilinkHistory() {
        const periode = document.getElementById('filterPeriode').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const container = document.getElementById('brilinkListContainer');

        container.innerHTML = '<div class="text-center py-4 text-slate-400 text-xs animate-pulse">Memuat riwayat...</div>';

        let url = `{{ route('kas.brilink-history') }}?periode=${periode}`;
        if (periode === 'custom' && startDate && endDate) {
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update Ringkasan Total Masuk / Keluar
            document.getElementById('summaryMasuk').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.summary.total_masuk || 0);
            document.getElementById('summaryKeluar').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.summary.total_keluar || 0);

            // Render Item Transaksi
            if (data.transaksi.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-slate-400 text-xs">Tidak ada riwayat transaksi pada periode ini.</div>';
                return;
            }

            let html = '';
            data.transaksi.forEach(item => {
                const isMasuk = item.jenis === 'masuk';
                const badgeColor = isMasuk ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' : 'text-rose-400 bg-rose-500/10 border-rose-500/20';
                const nominalColor = isMasuk ? 'text-emerald-400' : 'text-rose-400';
                const prefix = isMasuk ? '+' : '-';

                html += `
                    <div class="bg-slate-800/90 p-2.5 rounded-xl border border-slate-700/60 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-200">${item.kas ? (item.kas.nama_kas || item.kas.jenis_kas) : 'Kas'}</span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded border ${badgeColor} font-bold uppercase">${item.jenis}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 block">${item.formatted_date}</span>
                            ${item.keterangan ? `<span class="text-[10px] text-slate-400 italic block">${item.keterangan}</span>` : ''}
                        </div>
                        <div class="text-right">
                            <span class="font-black ${nominalColor}">${prefix} Rp ${new Intl.NumberFormat('id-ID').format(item.total)}</span>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            container.innerHTML = '<div class="text-center py-4 text-rose-400 text-xs">Gagal memuat data riwayat.</div>';
        });
    }
</script>
@endsection