@extends('developer.layouts.app', ['title' => 'Kelola Cabang', 'breadcrumb' => 'Kelola Semua Cabang'])

@section('content')
@php
    $ownerOptions = $owners->map(fn($o) => [
        'value' => $o->id,
        'label' => $o->nama,
        'sub'   => $o->email . ' (' . strtoupper($o->role) . ')'
    ])->toArray();
    array_unshift($ownerOptions, ['value' => '', 'label' => '-- Semua Owner --', 'sub' => '']);
@endphp

<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>Manajemen Cabang Agen</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                    {{ $cabangs->total() }} Cabang
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Kelola seluruh cabang agen, pantau pemilik (Owner), serta kelola kasir yang ditugaskan di masing-masing cabang.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <a href="{{ route('developer.owner.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-indigo-300 font-bold text-xs sm:text-sm border border-slate-700 transition">
                <span>👤 Daftar &amp; Show Owner</span>
            </a>
            <a href="{{ route('developer.cabang.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-purple-600/30 transition active:scale-95 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah Cabang Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Box -->
    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4">
        <form action="{{ route('developer.cabang.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6 lg:col-span-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama cabang, alamat, rekening, atau bank..."
                        class="w-full pl-9 pr-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                </div>
            </div>

            <!-- Searchable Select untuk Filter Owner -->
            <div class="sm:col-span-6 lg:col-span-5">
                @include('developer.components.searchable-select', [
                    'name' => 'id_owner',
                    'options' => $ownerOptions,
                    'selected' => request('id_owner', ''),
                    'placeholder' => '-- Filter Cari Owner --',
                ])
            </div>

            <div class="sm:col-span-12 lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs sm:text-sm font-bold transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'id_owner']))
                    <a href="{{ route('developer.cabang.index') }}" class="py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs sm:text-sm font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Cabang Table Container -->
    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-900/90 text-slate-400 font-mono text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3 px-4">Nama Cabang / Alamat</th>
                        <th class="py-3 px-4">Pemilik (Owner)</th>
                        <th class="py-3 px-4">Rekening Utama Bank</th>
                        <th class="py-3 px-4 text-center">Jml Kasir</th>
                        <th class="py-3 px-4 text-center">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($cabangs as $cabang)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm">
                                    <a href="{{ route('developer.cabang.show', $cabang->id) }}" class="hover:text-purple-300 transition">
                                        {{ $cabang->nama_warung }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $cabang->alamat ?? 'Alamat belum diatur' }}</div>
                                @if($cabang->no_telepon)
                                    <div class="text-[10px] font-mono text-slate-500">{{ $cabang->no_telepon }}</div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if($cabang->user)
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <div class="font-bold text-white text-xs sm:text-sm">{{ $cabang->user->nama }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $cabang->user->email }}</div>
                                        </div>
                                        <a href="{{ route('developer.owner.show', $cabang->user->id) }}" 
                                           title="Buka Show Owner"
                                           class="px-2 py-0.5 rounded-md bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 text-[10px] font-mono font-bold border border-indigo-500/20 transition shrink-0">
                                            Show Owner →
                                        </a>
                                    </div>
                                @else
                                    <span class="text-rose-400 text-xs italic">Tanpa Owner</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-slate-200 border border-slate-700">
                                        {{ $cabang->bank_utama }}
                                    </span>
                                    <span class="font-mono text-xs text-white font-bold">{{ $cabang->nomor_rekening_utama ?? '-' }}</span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">a.n. {{ $cabang->atas_nama_utama ?? '-' }}</div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('developer.cabang.show', $cabang->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-xs hover:bg-cyan-500/20 transition">
                                    <span>{{ $cabang->users_count }} Kasir</span>
                                </a>
                            </td>

                            <td class="py-3.5 px-4 text-center text-slate-400 text-xs font-mono">
                                {{ $cabang->created_at ? $cabang->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Show Cabang Button -->
                                    <a href="{{ route('developer.cabang.show', $cabang->id) }}" 
                                       title="Lihat Detail Cabang & Kasir"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-purple-950/40 text-purple-300 border border-slate-800 hover:border-purple-800/80 text-xs font-bold transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Show Cabang</span>
                                    </a>

                                    <!-- Edit Cabang -->
                                    <a href="{{ route('developer.cabang.edit', $cabang->id) }}" 
                                       title="Edit Cabang"
                                       class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-amber-400 border border-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/></svg>
                                    </a>

                                    <!-- Delete Cabang -->
                                    <form action="{{ route('developer.cabang.destroy', $cabang->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang ini? Seluruh data kas cabang akan dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            title="Hapus Cabang"
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
                                Belum ada data cabang yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cabangs->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                {{ $cabangs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
