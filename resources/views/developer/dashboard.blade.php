@extends('developer.layouts.app', ['title' => 'Dashboard Platform', 'breadcrumb' => 'Dashboard Platform'])

@section('content')
<div class="space-y-6">

    <!-- Hero Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-900/60 via-slate-900 to-slate-900 border border-indigo-800/40 p-6 sm:p-8">
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                CORE DEVELOPER ENVIRONMENT
            </div>
            <h1 class="text-xl sm:text-3xl font-black text-white tracking-tight">
                Selamat Datang di Developer Console
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                Portal kontrol pengembang untuk memanajemen akun <strong>Owner penyewa</strong>, provisioning <strong>Cabang Agen</strong>, serta akun <strong>Kasir</strong> lintas cabang secara terisolasi tanpa intervensi data transaksi.
            </p>
        </div>
        <div class="absolute right-0 bottom-0 translate-x-8 translate-y-8 opacity-10 pointer-events-none hidden sm:block">
            <svg class="w-72 h-72 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Owner -->
        <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 hover:border-indigo-500/40 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono font-bold text-slate-400 uppercase tracking-wider">Owner Penyewa</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white font-mono">{{ $totalOwners }}</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1.5">
                <span class="text-emerald-400 font-bold font-mono">{{ $activeOwners }} Aktif</span>
                <span>&bull;</span>
                <span>{{ $totalOwners - $activeOwners }} Nonaktif</span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between">
                <a href="{{ route('developer.owner.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                    <span>Kelola Owner</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>
        </div>

        <!-- Card 2: Total Cabang -->
        <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 hover:border-indigo-500/40 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono font-bold text-slate-400 uppercase tracking-wider">Total Cabang</span>
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009 9.35c.692-.802 1.73-1.35 2.89-1.35 1.16 0 2.198.548 2.89 1.35a2.993 2.993 0 002.36-.615 3.001 3.001 0 003.75.615m-18 0l2.25-5.25a1.5 1.5 0 011.38-.909h10.74a1.5 1.5 0 011.38.909l2.25 5.25"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white font-mono">{{ $totalCabangs }}</div>
            <div class="text-[11px] text-slate-400 mt-1">
                Terdaftar di platform
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between">
                <a href="{{ route('developer.cabang.index') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300 flex items-center gap-1">
                    <span>Semua Cabang</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>
        </div>

        <!-- Card 3: Total Kasir -->
        <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 hover:border-indigo-500/40 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono font-bold text-slate-400 uppercase tracking-wider">Total Kasir</span>
                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white font-mono">{{ $totalKasirs }}</div>
            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1.5">
                <span class="text-emerald-400 font-bold font-mono">{{ $activeKasirs }} Aktif</span>
                <span>&bull;</span>
                <span>{{ $totalKasirs - $activeKasirs }} Nonaktif</span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between">
                <a href="{{ route('developer.kasir.index') }}" class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1">
                    <span>Kelola Kasir</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>
        </div>

        <!-- Card 4: Status Platform -->
        <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 hover:border-indigo-500/40 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono font-bold text-slate-400 uppercase tracking-wider">Status Platform</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-lg font-black text-emerald-400 font-mono">ONLINE &bull; READY</div>
            <div class="text-[11px] text-slate-400 mt-1">
                Laravel {{ app()->version() }} &bull; PHP {{ PHP_VERSION }}
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between">
                <span class="text-[11px] font-mono text-slate-400">Database Connected</span>
            </div>
        </div>
    </div>

    <!-- Quick Provisioning Actions -->
    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider font-mono mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
            <span>Aksi Cepat Developer (Quick Provisioning)</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <a href="{{ route('developer.owner.create') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 hover:bg-slate-850 transition flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.375 12.375 0 0110.375 21c-2.332 0-4.508-.65-6.375-1.765z"/></svg>
                </div>
                <div>
                    <div class="text-xs sm:text-sm font-bold text-white group-hover:text-indigo-300 transition">+ Tambah Owner Baru</div>
                    <div class="text-[11px] text-slate-400">Daftarkan akun pemilik baru</div>
                </div>
            </a>

            <a href="{{ route('developer.cabang.create') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-purple-500/50 hover:bg-slate-850 transition flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </div>
                <div>
                    <div class="text-xs sm:text-sm font-bold text-white group-hover:text-purple-300 transition">+ Tambah Cabang Baru</div>
                    <div class="text-[11px] text-slate-400">Buat cabang & kas otomatis</div>
                </div>
            </a>

            <a href="{{ route('developer.kasir.create') }}" class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-cyan-500/50 hover:bg-slate-850 transition flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.375 12.375 0 0110.375 21c-2.332 0-4.508-.65-6.375-1.765z"/></svg>
                </div>
                <div>
                    <div class="text-xs sm:text-sm font-bold text-white group-hover:text-cyan-300 transition">+ Tambah Kasir Baru</div>
                    <div class="text-[11px] text-slate-400">Tugaskan kasir ke cabang</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Owners & Recent Cabang Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Table 1: Recent Owners -->
        <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-xs sm:text-sm font-bold text-white uppercase font-mono tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>Owner Terdaftar Terbaru</span>
                </h3>
                <a href="{{ route('developer.owner.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-bold">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-mono border-b border-slate-800/80 text-[11px]">
                            <th class="py-2.5 px-3">Owner</th>
                            <th class="py-2.5 px-3">Kontak</th>
                            <th class="py-2.5 px-3 text-center">Cabang</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($recentOwners as $owner)
                            <tr class="hover:bg-slate-900/60 transition">
                                <td class="py-3 px-3">
                                    <div class="font-bold text-white">{{ $owner->nama }}</div>
                                    <div class="text-[10px] font-mono text-indigo-400 uppercase">{{ $owner->role }}</div>
                                </td>
                                <td class="py-3 px-3 text-slate-300">
                                    <div>{{ $owner->email }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $owner->nomor_hp ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-indigo-300">
                                    {{ $owner->owned_warungs_count }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono {{ $owner->status === 'aktif' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        {{ strtoupper($owner->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('developer.owner.show', $owner->id) }}" class="text-indigo-400 hover:text-indigo-300 font-bold text-[11px] underline">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-500 text-xs">Belum ada owner terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table 2: Recent Cabangs -->
        <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-xs sm:text-sm font-bold text-white uppercase font-mono tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span>Cabang Terdaftar Terbaru</span>
                </h3>
                <a href="{{ route('developer.cabang.index') }}" class="text-xs text-purple-400 hover:text-purple-300 font-bold">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-mono border-b border-slate-800/80 text-[11px]">
                            <th class="py-2.5 px-3">Nama Cabang</th>
                            <th class="py-2.5 px-3">Owner</th>
                            <th class="py-2.5 px-3">Bank Utama</th>
                            <th class="py-2.5 px-3 text-center">Kasir</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($recentCabangs as $cabang)
                            <tr class="hover:bg-slate-900/60 transition">
                                <td class="py-3 px-3">
                                    <div class="font-bold text-white">{{ $cabang->nama_warung }}</div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-[150px]">{{ $cabang->alamat ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3 text-slate-300">
                                    {{ $cabang->user->nama ?? '-' }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ $cabang->bank_utama }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-cyan-300">
                                    {{ $cabang->users_count }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('developer.cabang.show', $cabang->id) }}" class="text-purple-400 hover:text-purple-300 font-bold text-[11px] underline">
                                        Detail Cabang
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-500 text-xs">Belum ada cabang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
