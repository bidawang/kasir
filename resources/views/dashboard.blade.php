@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="space-y-4 max-w-2xl mx-auto pb-10 text-slate-800">

    {{-- Ringkasan Saldo Utama --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6 text-white shadow-lg border border-slate-800">
        <!-- Background Decorative Blur -->
        <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-300 font-semibold uppercase tracking-wider">Total Saldo Kas</span>
                <span class="inline-flex items-center gap-1.5 text-xs bg-emerald-500/20 border border-emerald-500/30 px-2.5 py-0.5 rounded-md text-emerald-300 font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ $kasir->nama_warung ?? 'Cabang Aktif' }}
                </span>
            </div>

            <div>
                <span class="text-sm text-slate-400 block font-medium">Total saldo tunai, e-wallet, dan rekening bank</span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white mt-1">
                    Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}
                </h1>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-800">
                <div class="bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/10">
                    <p class="text-xs text-slate-300 font-semibold flex items-center gap-1 uppercase tracking-wider">
                        <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
                        Pemasukan Hari Ini
                    </p>
                    <p class="font-extrabold text-xs sm:text-sm text-emerald-400 mt-0.5">
                        Rp {{ number_format($pemasukanHariIni ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/10">
                    <p class="text-xs text-slate-300 font-semibold flex items-center gap-1 uppercase tracking-wider">
                        <svg class="w-3 h-3 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
                        Pengeluaran Hari Ini
                    </p>
                    <p class="font-extrabold text-xs sm:text-sm text-rose-400 mt-0.5">
                        Rp {{ number_format($pengeluaranHariIni ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu Cepat Layanan Utama --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        <a href="{{ route('transaksi.menu') }}" class="p-3 rounded-2xl bg-white border border-slate-200/80 hover:border-primary-400 hover:shadow-xs transition flex flex-col justify-between gap-2 shadow-2xs group">
            <div class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </div>
            <div>
                <span class="block text-xs font-black text-slate-900 group-hover:text-primary-600 transition-colors">Transaksi Baru</span>
                <span class="text-[10px] text-slate-400">Setor, tarik & transfer</span>
            </div>
        </a>

        <a href="{{ route('laporan-harian.index') }}" class="p-3 rounded-2xl bg-white border border-emerald-200 hover:border-emerald-400 hover:shadow-xs transition flex flex-col justify-between gap-2 shadow-2xs group">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="block text-xs font-black text-slate-900 group-hover:text-emerald-700 transition-colors">Laporan Harian</span>
                <span class="text-[10px] text-emerald-600 font-semibold">Tutup buku kas fisik</span>
            </div>
        </a>

        <a href="{{ route('transaksi.index') }}" class="p-3 rounded-2xl bg-white border border-slate-200/80 hover:border-primary-400 hover:shadow-xs transition flex flex-col justify-between gap-2 shadow-2xs group">
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <span class="block text-xs font-black text-slate-900 group-hover:text-primary-600 transition-colors">Riwayat Kas</span>
                <span class="text-[10px] text-slate-400">Arus masuk & keluar</span>
            </div>
        </a>

        <a href="{{ route('manajemen-kasir.index') }}" class="p-3 rounded-2xl bg-white border border-slate-200/80 hover:border-primary-400 hover:shadow-xs transition flex flex-col justify-between gap-2 shadow-2xs group">
            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <div>
                <span class="block text-xs font-black text-slate-900 group-hover:text-purple-700 transition-colors">Kelola Kasir</span>
                <span class="text-[10px] text-slate-400">Akun kasir & staf</span>
            </div>
        </a>
    </div>

    {{-- Detail Kas Tunai Fisik (status = cash) --}}
    @if($kasTunai)
        <div x-data="{ open: false }" class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden transition-all duration-200">
            <!-- Accordion Header -->
            <button @click="open = !open" type="button" class="w-full flex items-center justify-between p-3.5 text-left hover:bg-slate-50 transition-colors group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0 border border-emerald-200/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v9.75a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/>
                        </svg>
                    </div>
                    <div class="truncate">
                        <h2 class="font-bold text-slate-900 text-xs sm:text-sm group-hover:text-emerald-700 transition-colors flex items-center gap-1.5">
                            Kas Tunai Fisik
                            <span class="text-xs font-extrabold bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded uppercase">Fisik</span>
                        </h2>
                        <p class="text-sm text-slate-500 font-medium truncate">
                            Saldo: <span class="text-slate-900 font-bold">Rp {{ number_format($kasTunai->saldo ?? 0, 0, ',', '.') }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 ml-2">
                    <span class="text-xs font-bold text-slate-400 hidden sm:inline" x-text="open ? 'Sembunyikan' : 'Rincian Pecahan'"></span>
                    <div class="w-6 h-6 rounded-md bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-slate-200 transition-colors">
                        <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </div>
                </div>
            </button>

            <!-- Accordion Content -->
            <div x-show="open" x-collapse class="border-t border-slate-100 bg-slate-50/50 p-3 space-y-2.5">
                
                <!-- Grid Rincian Pecahan Uang -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                        $hasItems = false;
                    @endphp
                    @forelse($kasTunai->detailKas as $detail)
                        @if($detail->jumlah > 0)
                            @php $hasItems = true; @endphp
                            <div class="flex items-center justify-between bg-white px-3 py-2 rounded-lg border border-slate-200/70 text-xs shadow-2xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800">Rp {{ number_format($detail->pecahan, 0, ',', '.') }}</span>
                                    <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-bold text-xs border border-slate-200">
                                        {{ number_format($detail->jumlah, 0, ',', '.') }} lembar
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
                        <div class="col-span-full text-center py-4 text-slate-400 text-xs bg-white rounded-lg border border-dashed border-slate-200">
                            Belum ada rincian pecahan fisik uang tunai.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif

    {{-- Saldo per akun digital dan rekening bank --}}
    <div class="space-y-2">
        <div class="flex items-center justify-between px-1">
            <h2 class="font-bold text-slate-700 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                </svg>
                Saldo E-Wallet dan Rekening Bank
            </h2>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('kas.index') }}" class="text-sm text-emerald-600 font-bold hover:underline flex items-center gap-0.5">
                    Kelola Kas
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            @forelse($saldoEwallet as $kas)
                <div class="bg-white rounded-xl p-3 border border-slate-200/80 shadow-2xs hover:border-slate-300 transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider truncate block">{{ $kas->jenis_kas }}</span>
                        <span class="text-xs font-bold px-1.5 py-0.2 rounded bg-blue-50 text-blue-600">
                            AKUN DIGITAL/BANK
                        </span>
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-xs sm:text-sm truncate">
                        Rp {{ number_format($kas->saldo, 0, ',', '.') }}
                    </h3>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl p-4 text-center border border-slate-200/80 text-slate-400 text-xs shadow-2xs">
                    Belum ada e-wallet atau rekening bank yang ditambahkan.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="space-y-2">
        <div class="flex items-center justify-between px-1">
            <h2 class="font-bold text-slate-700 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Transaksi Terbaru
            </h2>
            <a href="{{ route('transaksi.index') }}" class="text-sm text-emerald-600 font-bold hover:underline flex items-center gap-0.5">
                Lihat Semua Transaksi
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
        </div>

        <div class="space-y-2">
            @forelse($recentTransactions as $transaksi)
                <div class="bg-white rounded-xl p-3 border border-slate-200/80 shadow-2xs flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center font-bold text-xs {{ $transaksi->jenis == 'masuk' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200/60' : 'bg-rose-50 text-rose-600 border border-rose-200/60' }}">
                            @if($transaksi->jenis == 'masuk')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-800 text-xs truncate">
                                {{ $transaksi->keterangan ?? 'Transaksi Kas' }}
                            </h3>
                            <p class="text-xs text-slate-400 font-medium">
                                {{ $transaksi->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <p class="font-extrabold text-xs {{ $transaksi->jenis == 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $transaksi->jenis == 'masuk' ? '+' : '-' }} Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                        </p>
                        <span class="inline-block text-xs font-bold bg-slate-100 text-slate-600 px-1.5 py-0.2 rounded border border-slate-200/60 mt-0.5">
                            {{ $transaksi->kas->jenis_kas ?? '-' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-6 text-center border border-slate-200/80 text-slate-400 text-xs shadow-2xs">
                    Belum ada transaksi terbaru pada cabang ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection