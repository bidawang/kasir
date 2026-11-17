@extends('developer.layouts.app', ['title' => 'Kelola Owner', 'breadcrumb' => 'Kelola Owner Penyewa'])

@section('content')
<div class="space-y-6">

    <!-- Top Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <span>Manajemen Owner Platform</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    {{ $owners->total() }} Akun
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Kelola akun pemilik (Owner / Admin) penyewa platform serta status akses ke sistem.
            </p>
        </div>

        <a href="{{ route('developer.owner.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition active:scale-95 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span>Tambah Owner Baru</span>
        </a>
    </div>

    <!-- Filter & Search Box -->
    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-4">
        <form action="{{ route('developer.owner.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6 lg:col-span-7">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama owner, email, no HP, atau username..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <div class="sm:col-span-3 lg:col-span-3">
                <select name="status" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-slate-300 focus:outline-none focus:border-indigo-500 transition">
                    <option value="">-- Semua Status --</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="sm:col-span-3 lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs sm:text-sm font-bold transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('developer.owner.index') }}" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs sm:text-sm font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-900/90 text-slate-400 font-mono text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3 px-4">Owner / Akun</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4 text-center">Jml Cabang</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($owners as $owner)
                        <tr class="hover:bg-slate-900/40 transition">
                            <!-- Owner Info -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm flex items-center gap-2">
                                    <span>{{ $owner->nama }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $owner->role === 'admin' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' }}">
                                        {{ $owner->role }}
                                    </span>
                                </div>
                                @if($owner->username)
                                    <div class="text-[11px] font-mono text-slate-400">@<span>{{ $owner->username }}</span></div>
                                @endif
                            </td>

                            <!-- Contact -->
                            <td class="py-3.5 px-4 text-slate-300">
                                <div class="font-mono text-xs">{{ $owner->email }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $owner->nomor_hp ?? '-' }}</div>
                            </td>

                            <!-- Branches Count -->
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('developer.owner.show', $owner->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 font-mono font-bold text-xs hover:bg-indigo-500/20 transition">
                                    <span>{{ $owner->owned_warungs_count }} Cabang</span>
                                </a>
                            </td>

                            <!-- Status Toggle -->
                            <td class="py-3.5 px-4 text-center">
                                <form action="{{ route('developer.owner.toggle-status', $owner->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                        title="Klik untuk ubah status aktif/nonaktif"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold transition cursor-pointer {{ $owner->status === 'aktif' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30 hover:bg-rose-500/20' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $owner->status === 'aktif' ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400' }}"></span>
                                        <span>{{ strtoupper($owner->status) }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Date Registered -->
                            <td class="py-3.5 px-4 text-center text-slate-400 text-xs font-mono">
                                {{ $owner->created_at ? $owner->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Detail -->
                                    <a href="{{ route('developer.owner.show', $owner->id) }}" 
                                       title="Lihat Detail Cabang & Kasir"
                                       class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-indigo-400 border border-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('developer.owner.edit', $owner->id) }}" 
                                       title="Edit Data Owner"
                                       class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-amber-400 border border-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/></svg>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('developer.owner.destroy', $owner->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun owner ini? Pastikan owner tidak memiliki cabang aktif.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            title="Hapus Owner"
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
                                Belum ada data owner yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($owners->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                {{ $owners->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
