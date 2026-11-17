@extends('developer.layouts.app', ['title' => 'Detail Cabang ' . $cabang->nama_warung, 'breadcrumb' => 'Detail Cabang ' . $cabang->nama_warung])

@section('content')
<div class="space-y-6" x-data="{ modalTambahKasir: false }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $cabang->nama_warung }}</h1>
                <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-purple-500/10 text-purple-300 border border-purple-500/20">
                    BANK {{ $cabang->bank_utama }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Pemilik Cabang: 
                @if($cabang->user)
                    <a href="{{ route('developer.owner.show', $cabang->user->id) }}" class="text-indigo-400 hover:text-indigo-300 font-bold underline">
                        {{ $cabang->user->nama }} (Lihat Profil Owner →)
                    </a>
                @else
                    <span class="text-rose-400 italic">Tanpa Owner</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('developer.cabang.edit', $cabang->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold text-xs sm:text-sm border border-slate-700 transition">
                Edit Cabang
            </a>
            @if($cabang->user)
                <a href="{{ route('developer.owner.show', $cabang->user->id) }}" class="px-3.5 py-2 rounded-xl bg-indigo-950/60 hover:bg-indigo-900/60 text-indigo-300 font-bold text-xs sm:text-sm border border-indigo-800/80 transition">
                    👤 Show Owner
                </a>
            @endif
            <a href="{{ route('developer.cabang.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-bold text-xs sm:text-sm border border-slate-800 transition">
                ← Kembali ke Cabang
            </a>
        </div>
    </div>

    <!-- Cabang Info Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider block mb-1">Pemilik (Owner)</span>
            <div class="text-xs sm:text-sm font-bold text-white truncate">{{ $cabang->user->nama ?? '-' }}</div>
            <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $cabang->user->email ?? '-' }}</div>
        </div>

        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider block mb-1">Rekening Operasional</span>
            <div class="text-xs sm:text-sm font-bold text-white font-mono">{{ $cabang->nomor_rekening_utama ?? '-' }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5 truncate">a.n. {{ $cabang->atas_nama_utama ?? '-' }}</div>
        </div>

        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider block mb-1">Alamat Cabang</span>
            <div class="text-xs font-semibold text-slate-200 line-clamp-2">{{ $cabang->alamat ?? 'Alamat belum diatur' }}</div>
            <div class="text-[11px] text-slate-400 font-mono mt-0.5">Telp: {{ $cabang->no_telepon ?? '-' }}</div>
        </div>

        <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider block mb-1">Kasir Ditugaskan</span>
            <div class="text-2xl font-black text-cyan-400 font-mono">{{ $kasirs->count() }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Petugas kasir aktif</div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- DAFTAR KASIR DI CABANG INI & TOMBOL TAMBAH KASIR -->
    <!-- ========================================================================= -->
    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-800 gap-3">
            <div>
                <h2 class="text-base font-bold text-white font-mono uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                    <span>Daftar Kasir di Cabang {{ $cabang->nama_warung }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-mono bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">
                        {{ $kasirs->count() }} Kasir
                    </span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Tambahkan kasir baru yang langsung teralokasi khusus untuk cabang ini.
                </p>
            </div>

            <!-- Tombol Tambah Kasir di Cabang Ini -->
            <button type="button" @click="modalTambahKasir = true" 
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-cyan-600/25 transition active:scale-95 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>+ Tambah Kasir di Cabang Ini</span>
            </button>
        </div>

        <!-- Tabel Kasir Cabang -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-900/90 text-slate-400 font-mono text-[11px] uppercase tracking-wider border-b border-slate-800">
                        <th class="py-3 px-4">Nama Kasir</th>
                        <th class="py-3 px-4">Kontak (Email / HP)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($kasirs as $kasir)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm">{{ $kasir->nama }}</div>
                                <div class="text-[10px] font-mono text-cyan-400 uppercase">ROLE: KASIR</div>
                            </td>

                            <td class="py-3.5 px-4 text-slate-300">
                                <div class="font-mono text-xs">{{ $kasir->email }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $kasir->nomor_hp ?? '-' }}</div>
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
                            <td colspan="5" class="py-8 text-center text-slate-500 text-xs">
                                Belum ada kasir yang ditugaskan pada cabang ini. Klik tombol <strong>"+ Tambah Kasir di Cabang Ini"</strong> di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: TAMBAH KASIR LANGSUNG DI CABANG INI -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahKasir" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="modalTambahKasir = false"></div>
        <div class="relative w-full max-w-xl bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-white font-mono uppercase">Tambah Kasir Baru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Penempatan: <strong class="text-purple-400">{{ $cabang->nama_warung }}</strong></p>
                </div>
                <button type="button" @click="modalTambahKasir = false" class="text-slate-400 hover:text-white p-1 rounded-lg">✕</button>
            </div>

            <form action="{{ route('developer.kasir.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id_kasir" value="{{ $cabang->id }}">
                <input type="hidden" name="redirect_to" value="cabang.show">

                <!-- Nama Kasir -->
                <div>
                    <label for="modal_branch_kasir_nama" class="block text-xs font-bold text-slate-300 mb-1.5">Nama Lengkap Kasir <span class="text-rose-400">*</span></label>
                    <input type="text" id="modal_branch_kasir_nama" name="nama" required
                        placeholder="Contoh: Rina Novita"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition">
                </div>

                <!-- Email & Nomor HP Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="modal_branch_kasir_email" class="block text-xs font-bold text-slate-300 mb-1.5">Email Kasir <span class="text-rose-400">*</span></label>
                        <input type="email" id="modal_branch_kasir_email" name="email" required
                            placeholder="kasir@gmail.com"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 font-mono transition">
                    </div>
                    <div>
                        <label for="modal_branch_kasir_hp" class="block text-xs font-bold text-slate-300 mb-1.5">Nomor Handphone / WA</label>
                        <input type="text" id="modal_branch_kasir_hp" name="nomor_hp"
                            placeholder="08123456789"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 font-mono transition">
                    </div>
                </div>

                <!-- Status Akun -->
                <div>
                    <label for="modal_branch_kasir_status" class="block text-xs font-bold text-slate-300 mb-1.5">Status Akun Kasir <span class="text-rose-400">*</span></label>
                    <select id="modal_branch_kasir_status" name="status" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-white focus:outline-none focus:border-cyan-500 transition">
                        <option value="aktif">Aktif (Langsung Aktif)</option>
                        <option value="nonaktif">Nonaktif (Akses Ditangguhkan)</option>
                    </select>
                </div>

                <!-- Password & Konfirmasi Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label for="modal_branch_kasir_password" class="block text-xs font-bold text-slate-300 mb-1.5">Kata Sandi <span class="text-rose-400">*</span></label>
                        <input type="password" id="modal_branch_kasir_password" name="password" required minlength="6"
                            placeholder="Minimal 6 karakter"
                            class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500 font-mono transition">
                    </div>
                    <div>
                        <label for="modal_branch_kasir_confirmation" class="block text-xs font-bold text-slate-300 mb-1.5">Ulangi Sandi <span class="text-rose-400">*</span></label>
                        <input type="password" id="modal_branch_kasir_confirmation" name="password_confirmation" required minlength="6"
                            placeholder="Ketik ulang sandi"
                            class="w-full px-3.5 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500 font-mono transition">
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-800">
                    <button type="button" @click="modalTambahKasir = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-lg shadow-cyan-600/30">
                        Simpan Kasir di Cabang Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
