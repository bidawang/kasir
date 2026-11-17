@extends('developer.layouts.app', ['title' => 'Detail Owner ' . $owner->nama, 'breadcrumb' => 'Detail Owner ' . $owner->nama])

@section('content')
@php
    $banks = $banks ?? collect();
    $kasirList = $kasirList ?? collect();
    $bankOptions = $banks->map(fn($b) => [
        'value' => $b->nama,
        'label' => $b->nama,
        'sub'   => 'Kode Bank: ' . $b->kode
    ])->toArray();

    $cabangOptions = $owner->ownedWarungs->map(fn($w) => [
        'value' => $w->id,
        'label' => $w->nama_warung,
        'sub'   => 'Bank: ' . $w->bank_utama . ' &bull; Rek: ' . ($w->nomor_rekening_utama ?? '-')
    ])->toArray();

    $totalKasirCount = $kasirList->count();
@endphp

<div class="space-y-6" x-data="{ modalTambahCabang: false, modalTambahKasir: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $owner->nama }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase {{ $owner->status === 'aktif' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                    {{ strtoupper($owner->status) }}
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    ROLE: {{ strtoupper($owner->role) }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Kelola cabang dan kasir yang terpusat di bawah kepemilikan <strong>{{ $owner->nama }}</strong>.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('developer.owner.edit', $owner->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold text-xs sm:text-sm border border-slate-700 transition">
                Edit Owner
            </a>
            <a href="{{ route('developer.owner.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-bold text-xs sm:text-sm border border-slate-800 transition">
                ← Kembali ke Daftar Owner
            </a>
        </div>
    </div>

    <!-- Owner Summary Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider block mb-1">Email Login</span>
            <div class="text-xs sm:text-sm font-bold text-white font-mono truncate">{{ $owner->email }}</div>
            <div class="text-[11px] text-slate-400 font-mono mt-0.5">Username: <span class="text-slate-300">{{ $owner->username ?? '-' }}</span></div>
        </div>

        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider block mb-1">Nomor Kontak</span>
            <div class="text-xs sm:text-sm font-bold text-white font-mono">{{ $owner->nomor_hp ?? 'Belum diisi' }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Terdaftar: {{ $owner->created_at ? $owner->created_at->format('d/m/Y') : '-' }}</div>
        </div>

        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider block mb-1">Total Cabang Aktif</span>
            <div class="text-2xl font-black text-purple-400 font-mono">{{ $owner->ownedWarungs->count() }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Cabang toko/agen</div>
        </div>

        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider block mb-1">Total Kasir Bekerja</span>
            <div class="text-2xl font-black text-cyan-400 font-mono">{{ $totalKasirCount }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Akun kasir pelaksana</div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- BAGIAN 1: KELOLA CABANG DI BAWAH OWNER INI -->
    <!-- ========================================================================= -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-800 gap-3">
            <div>
                <h2 class="text-base font-bold text-white font-mono uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    <span>1. Cabang Milik {{ $owner->nama }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-mono bg-purple-500/10 text-purple-300 border border-purple-500/20">
                        {{ $owner->ownedWarungs->count() }}
                    </span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Cabang agen yang terhubung langsung ke pembukuan dan monitoring owner ini.
                </p>
            </div>

            <button type="button" @click="modalTambahCabang = true" 
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-purple-600/25 transition active:scale-95 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>+ Tambah Cabang untuk Owner Ini</span>
            </button>
        </div>

        <!-- Tabel Cabang -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-900/90 text-slate-400 font-mono text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3 px-4">Nama Cabang &amp; Alamat</th>
                        <th class="py-3 px-4">Bank Operasional Utama</th>
                        <th class="py-3 px-4 text-center">Kasir Ditugaskan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($owner->ownedWarungs as $cabang)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm">
                                    <a href="{{ route('developer.cabang.show', $cabang->id) }}" class="hover:text-purple-300 transition">
                                        {{ $cabang->nama_warung }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $cabang->alamat ?? 'Alamat belum diisi' }}</div>
                                @if($cabang->no_telepon)
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $cabang->no_telepon }}</div>
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

                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('developer.cabang.show', $cabang->id) }}" 
                                       title="Lihat Detail Cabang & Kasir"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-purple-400 border border-slate-800 text-xs font-bold transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>Detail Cabang</span>
                                    </a>

                                    <a href="{{ route('developer.cabang.edit', $cabang->id) }}" 
                                       title="Edit Cabang"
                                       class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-amber-400 border border-slate-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500 text-xs">
                                Owner ini belum memiliki cabang agen. Klik tombol <strong>"+ Tambah Cabang untuk Owner Ini"</strong> di atas untuk membuat cabang pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- BAGIAN 2: KELOLA KASIR DI BAWAH OWNER INI -->
    <!-- ========================================================================= -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-800 gap-3">
            <div>
                <h2 class="text-base font-bold text-white font-mono uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                    <span>2. Kasir di Bawah Cabang {{ $owner->nama }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-mono bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">
                        {{ $totalKasirCount }}
                    </span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Daftar seluruh kasir pelaksana yang bertugas di cabang-cabang milik owner ini.
                </p>
            </div>

            @if($owner->ownedWarungs->count() > 0)
                <button type="button" @click="modalTambahKasir = true" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-cyan-600/25 transition active:scale-95 cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>+ Tambah Kasir untuk Owner Ini</span>
                </button>
            @endif
        </div>

        <!-- Tabel Kasir -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-900/90 text-slate-400 font-mono text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3 px-4">Nama Kasir</th>
                        <th class="py-3 px-4">Kontak (Email / HP)</th>
                        <th class="py-3 px-4">Cabang Penempatan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($kasirList as $kasir)
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
                                    <a href="{{ route('developer.cabang.show', $assignedCabang->id) }}" class="font-bold text-purple-300 hover:text-purple-200 text-xs sm:text-sm inline-flex items-center gap-1">
                                        <span>{{ $assignedCabang->nama_warung }}</span>
                                        <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    </a>
                                @else
                                    <span class="text-rose-400 text-xs italic">Belum ditempatkan</span>
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
                            <td colspan="5" class="py-8 text-center text-slate-500 text-xs">
                                @if($owner->ownedWarungs->count() === 0)
                                    Owner belum memiliki cabang. Tambahkan cabang terlebih dahulu sebelum menempatkan kasir.
                                @else
                                    Belum ada kasir yang ditugaskan di cabang owner ini. Klik tombol <strong>"+ Tambah Kasir untuk Owner Ini"</strong> di atas.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: TAMBAH CABANG UNTUK OWNER INI -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahCabang" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="modalTambahCabang = false"></div>
        <div class="relative w-full max-w-xl bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-white font-mono uppercase">Tambah Cabang Baru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pemilik: <strong class="text-indigo-400">{{ $owner->nama }}</strong></p>
                </div>
                <button type="button" @click="modalTambahCabang = false" class="text-slate-400 hover:text-white p-1 rounded-lg">✕</button>
            </div>

            <form action="{{ route('developer.cabang.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id_user" value="{{ $owner->id }}">
                <input type="hidden" name="redirect_to" value="owner.show">

                <!-- Nama Cabang -->
                <div>
                    <label for="nama_warung" class="block text-xs font-bold text-slate-300 mb-1.5">Nama Cabang / Toko Agen <span class="text-rose-400">*</span></label>
                    <input type="text" id="nama_warung" name="nama_warung" required
                        placeholder="Contoh: Cabang Simpang Lima"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 transition">
                </div>

                <!-- Alamat & Telepon -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="alamat" class="block text-xs font-bold text-slate-300 mb-1.5">Alamat Lengkap</label>
                        <input type="text" id="alamat" name="alamat"
                            placeholder="Jl. Raya No. 10"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 transition">
                    </div>
                    <div>
                        <label for="no_telepon" class="block text-xs font-bold text-slate-300 mb-1.5">Nomor Telepon Cabang</label>
                        <input type="text" id="no_telepon" name="no_telepon"
                            placeholder="08123456789"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 font-mono transition">
                    </div>
                </div>

                <!-- Bank Operasional Utama dengan Searchable Select -->
                <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-800 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Bank Utama (Cari &amp; Pilih) <span class="text-rose-400">*</span></label>
                        @include('developer.components.searchable-select', [
                            'name' => 'bank_utama',
                            'options' => $bankOptions,
                            'selected' => 'BRI',
                            'placeholder' => '-- Pilih atau Cari Bank --',
                            'required' => true,
                        ])
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="nomor_rekening_utama" class="block text-xs font-medium text-slate-300 mb-1">Nomor Rekening</label>
                            <input type="text" id="nomor_rekening_utama" name="nomor_rekening_utama"
                                placeholder="Nomor rekening"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:border-purple-500 font-mono transition">
                        </div>
                        <div>
                            <label for="atas_nama_utama" class="block text-xs font-medium text-slate-300 mb-1">Atas Nama Rekening</label>
                            <input type="text" id="atas_nama_utama" name="atas_nama_utama"
                                placeholder="Nama pemilik rekening"
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:border-purple-500 transition">
                        </div>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-800">
                    <button type="button" @click="modalTambahCabang = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30">
                        Simpan Cabang Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: TAMBAH KASIR UNTUK OWNER INI -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahKasir" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="modalTambahKasir = false"></div>
        <div class="relative w-full max-w-xl bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-white font-mono uppercase">Tambah Kasir Baru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pemilik: <strong class="text-cyan-400">{{ $owner->nama }}</strong></p>
                </div>
                <button type="button" @click="modalTambahKasir = false" class="text-slate-400 hover:text-white p-1 rounded-lg">✕</button>
            </div>

            <form action="{{ route('developer.kasir.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="redirect_to" value="owner.show">
                <input type="hidden" name="redirect_owner_id" value="{{ $owner->id }}">

                <!-- Nama Kasir -->
                <div>
                    <label for="modal_kasir_nama" class="block text-xs font-bold text-slate-300 mb-1.5">Nama Lengkap Kasir <span class="text-rose-400">*</span></label>
                    <input type="text" id="modal_kasir_nama" name="nama" required
                        placeholder="Contoh: Rina Novita"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">
                </div>

                <!-- Email & Nomor HP Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="modal_kasir_email" class="block text-xs font-bold text-slate-300 mb-1.5">Email Kasir <span class="text-rose-400">*</span></label>
                        <input type="email" id="modal_kasir_email" name="email" required
                            placeholder="kasir@gmail.com"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 font-mono transition">
                    </div>
                    <div>
                        <label for="modal_kasir_hp" class="block text-xs font-bold text-slate-300 mb-1.5">Nomor Handphone / WA</label>
                        <input type="text" id="modal_kasir_hp" name="nomor_hp"
                            placeholder="08123456789"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 font-mono transition">
                    </div>
                </div>

                <!-- Cabang Penempatan (Searchable Select dari cabang milik Owner ini!) -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Cabang Penempatan Kasir <span class="text-rose-400">*</span></label>
                    @include('developer.components.searchable-select', [
                        'name' => 'id_kasir',
                        'options' => $cabangOptions,
                        'selected' => $owner->ownedWarungs->first()?->id ?? '',
                        'placeholder' => '-- Pilih atau Cari Cabang --',
                        'required' => true,
                    ])
                </div>

                <!-- Status Akun -->
                <div>
                    <label for="modal_kasir_status" class="block text-xs font-bold text-slate-300 mb-1.5">Status Akun Kasir <span class="text-rose-400">*</span></label>
                    <select id="modal_kasir_status" name="status" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                        <option value="aktif">Aktif (Langsung Aktif)</option>
                        <option value="nonaktif">Nonaktif (Akses Ditangguhkan)</option>
                    </select>
                </div>

                <!-- Password & Konfirmasi Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label for="modal_kasir_password" class="block text-xs font-bold text-slate-300 mb-1.5">Kata Sandi <span class="text-rose-400">*</span></label>
                        <input type="password" id="modal_kasir_password" name="password" required minlength="6"
                            placeholder="Minimal 6 karakter"
                            class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500 font-mono transition">
                    </div>
                    <div>
                        <label for="modal_kasir_confirmation" class="block text-xs font-bold text-slate-300 mb-1.5">Ulangi Sandi <span class="text-rose-400">*</span></label>
                        <input type="password" id="modal_kasir_confirmation" name="password_confirmation" required minlength="6"
                            placeholder="Ketik ulang sandi"
                            class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500 font-mono transition">
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-800">
                    <button type="button" @click="modalTambahKasir = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-600/30">
                        Simpan Kasir Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
