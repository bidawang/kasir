@extends('developer.layouts.app', ['title' => 'Kelola Kasir', 'breadcrumb' => 'Kelola Semua Kasir'])

@section('content')
@php
    $cabangOptions = $cabangs->map(fn($c) => [
        'value' => $c->id,
        'label' => $c->nama_warung,
        'sub'   => 'Owner: ' . ($c->user->nama ?? '-') . ' &bull; Bank: ' . $c->bank_utama
    ])->toArray();
    array_unshift($cabangOptions, ['value' => '', 'label' => '-- Semua Cabang --', 'sub' => '']);
@endphp

<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>Manajemen Kasir Platform</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                    {{ $kasirs->total() }} Kasir
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Monitoring seluruh akun kasir pelaksana di semua cabang agen, pengaturan penempatan, dan status akun.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <a href="{{ route('developer.owner.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-indigo-300 font-bold text-xs sm:text-sm border border-slate-700 transition">
                <span>👤 Show Owner</span>
            </a>
            <a href="{{ route('developer.kasir.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-cyan-600/30 transition active:scale-95 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah Kasir Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Box -->
    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4">
        <form action="{{ route('developer.kasir.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-12 lg:col-span-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kasir, email, atau HP..."
                        class="w-full pl-9 pr-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition">
                </div>
            </div>

            <!-- Searchable Select Cabang -->
            <div class="sm:col-span-6 lg:col-span-4">
                @include('developer.components.searchable-select', [
                    'name' => 'id_cabang',
                    'options' => $cabangOptions,
                    'selected' => request('id_cabang', ''),
                    'placeholder' => '-- Filter Cari Cabang --',
                ])
            </div>

            <div class="sm:col-span-6 lg:col-span-2">
                <select name="status" class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-slate-300 focus:outline-none focus:border-cyan-500 transition">
                    <option value="">-- Status --</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="sm:col-span-12 lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs sm:text-sm font-bold transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'id_cabang', 'status']))
                    <a href="{{ route('developer.kasir.index') }}" class="py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs sm:text-sm font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Kasir Table Container -->
    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-900/90 text-slate-400 font-mono text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3 px-4">Nama Kasir</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4">Cabang Penempatan &amp; Owner</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($kasirs as $kasir)
                        @php
                            $assignedCabang = $kasir->warungs->first();
                        @endphp
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm">{{ $kasir->nama }}</div>
                                <div class="text-[10px] font-mono text-cyan-400 uppercase">ROLE: KASIR</div>
                            </td>

                            <td class="py-3.5 px-4 text-slate-300">
                                <div class="font-mono text-xs">{{ $kasir->email }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $kasir->nomor_hp ?? '-' }}</div>
                            </td>

                            <td class="py-3.5 px-4">
                                @if($assignedCabang)
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <a href="{{ route('developer.cabang.show', $assignedCabang->id) }}" class="font-bold text-purple-300 hover:text-purple-200 text-xs sm:text-sm inline-flex items-center gap-1">
                                                <span>{{ $assignedCabang->nama_warung }}</span>
                                                <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                            </a>
                                            <div class="text-[11px] text-slate-400">
                                                Owner: 
                                                @if($assignedCabang->user)
                                                    <a href="{{ route('developer.owner.show', $assignedCabang->user->id) }}" class="text-indigo-300 hover:underline">
                                                        {{ $assignedCabang->user->nama }}
                                                    </a>
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-rose-400 text-xs italic">Belum ditempatkan di cabang</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <form action="{{ route('developer.kasir.toggle-status', $kasir->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                        title="Klik untuk ubah status aktif/nonaktif"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold transition cursor-pointer {{ $kasir->status === 'aktif' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30 hover:bg-rose-500/20' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $kasir->status === 'aktif' ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400' }}"></span>
                                        <span>{{ strtoupper($kasir->status) }}</span>
                                    </button>
                                </form>
                            </td>

                            <td class="py-3.5 px-4 text-center text-slate-400 text-xs font-mono">
                                {{ $kasir->created_at ? $kasir->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('developer.kasir.edit', $kasir->id) }}" 
                                       title="Edit Kasir"
                                       class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-amber-400 border border-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/></svg>
                                    </a>

                                    <form action="{{ route('developer.kasir.destroy', $kasir->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun kasir ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            title="Hapus Kasir"
                                            class="p-1.5 rounded-lg bg-slate-900 hover:bg-rose-950/50 text-rose-400 border border-slate-800 hover:border-rose-900 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Belum ada data kasir yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kasirs->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                {{ $kasirs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
