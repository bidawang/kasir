@extends('layouts.app')

@section('title', 'Manajemen Kasir')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 sm:space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">Manajemen Kasir</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-primary-50 text-primary-700 text-xs font-black border border-primary-100">
                    {{ $kasir->count() }} Karyawan
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5 sub-explanation">
                Kelola akun kasir dan penugasan cabang tempat mereka bekerja secara terisolasi.
            </p>
        </div>
        <a href="{{ route('manajemen-kasir.create') }}" class="rounded-xl bg-primary-600 px-4 py-2.5 sm:px-5 sm:py-3 text-xs sm:text-sm font-bold text-white hover:bg-primary-700 active:scale-95 transition shadow-sm flex items-center justify-center gap-1.5 shrink-0 min-h-11">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span>Tambah Kasir</span>
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-3.5 text-xs sm:text-sm text-emerald-800 flex items-center gap-2 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-xs sm:text-sm text-rose-800 flex items-center gap-2 shadow-2xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($kasir->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 sm:p-12 text-center space-y-3 shadow-2xs">
            <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <div>
                <h3 class="font-black text-slate-800 text-sm sm:text-base">Belum Ada Akun Kasir</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mt-1 sub-explanation">
                    Tambahkan akun kasir untuk karyawan cabang agar dapat mencatat transaksi harian.
                </p>
            </div>
            <a href="{{ route('manajemen-kasir.create') }}" class="inline-flex items-center gap-1.5 bg-primary-600 text-white font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl hover:bg-primary-700 transition">
                Tambah Kasir Pertama
            </a>
        </div>
    @else
        <!-- TAMPILAN MOBILE: KARTU KASIR (RESPONSIF HP) -->
        <div class="grid grid-cols-1 gap-3 lg:hidden">
            @foreach($kasir as $akun)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Inisial Avatar -->
                            <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-800 flex items-center justify-center font-black text-sm shrink-0 border border-primary-200">
                                {{ strtoupper(substr($akun->nama ?? 'K', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-black text-slate-900 text-sm truncate">{{ $akun->nama }}</h3>
                                <p class="text-xs text-slate-500 truncate">{{ $akun->email }}</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-extrabold {{ $akun->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $akun->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <!-- Cabang & Info -->
                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Cabang Penugasan:</span>
                            <span class="font-extrabold text-slate-800 truncate max-w-[180px]">
                                {{ $akun->warungs->pluck('nama_warung')->join(', ') ?: '-' }}
                            </span>
                        </div>
                        @if($akun->nomor_hp)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-bold uppercase text-[10px]">No. Telepon:</span>
                                <span class="font-medium text-slate-700">{{ $akun->nomor_hp }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi Touch-Friendly -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
                        <a href="{{ route('manajemen-kasir.edit', $akun) }}" 
                           class="flex-1 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 active:scale-95 text-amber-700 font-bold text-xs text-center border border-amber-200 transition">
                            Ubah
                        </a>
                        <form method="POST" action="{{ route('manajemen-kasir.destroy', $akun) }}" onsubmit="return confirm('Hapus akun kasir {{ addslashes($akun->nama) }}?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- TAMPILAN DESKTOP: TABEL STANDAR -->
        <div class="hidden lg:block overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 text-[11px] font-black uppercase text-slate-500 tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Nama Kasir</th>
                            <th class="px-5 py-3.5">Email</th>
                            <th class="px-5 py-3.5">Cabang Bertugas</th>
                            <th class="px-5 py-3.5">Status Akun</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kasir as $akun)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-black text-slate-900">{{ $akun->nama }}</div>
                                    @if($akun->nomor_hp)
                                        <div class="text-[11px] text-slate-400 font-normal">{{ $akun->nomor_hp }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 font-medium">{{ $akun->email }}</td>
                                <td class="px-5 py-3.5 font-bold text-primary-700">
                                    <span class="inline-flex items-center gap-1 bg-primary-50 px-2 py-0.5 rounded border border-primary-100">
                                        {{ $akun->warungs->pluck('nama_warung')->join(', ') ?: '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $akun->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $akun->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('manajemen-kasir.edit', $akun) }}" 
                                           class="rounded-lg bg-amber-50 px-3 py-1.5 font-bold text-amber-700 hover:bg-amber-100 border border-amber-200 text-xs transition">
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('manajemen-kasir.destroy', $akun) }}" onsubmit="return confirm('Hapus akun kasir {{ addslashes($akun->nama) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg bg-rose-50 px-3 py-1.5 font-bold text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
