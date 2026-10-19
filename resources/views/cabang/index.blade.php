@extends('layouts.app')

@section('title', 'Kelola Cabang')

@section('content')
<div class="space-y-3 sm:space-y-4">

    <!-- HEADER PAGE CARD -->
    <div class="flex items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <h2 class="text-base sm:text-lg font-extrabold text-slate-800">Daftar Cabang</h2>
                <span class="px-2 py-0.5 rounded-full bg-primary-50 text-primary-700 text-[11px] font-extrabold border border-primary-100">
                    {{ $warungs->count() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 truncate mt-0.5">Kelola seluruh lokasi agen BRILINK Anda</p>
        </div>

        <a href="{{ route('cabang.create') }}"
           class="shrink-0 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm shadow-primary-600/20 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>Tambah</span>
        </a>
    </div>

    <!-- ALERT SUCCESS NOTIFICATION -->
    @if(session('success'))
        <div class="flex items-center gap-2.5 bg-emerald-50 border border-emerald-200/80 text-emerald-800 px-3.5 py-2.5 rounded-xl text-xs font-medium">
            <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="flex-1">{{ session('success') }}</span>
        </div>
    @endif

    <!-- LIST DAFTAR CABANG -->
    @if($warungs->count())
        <div class="grid gap-2.5">
            @foreach($warungs as $warung)
                @php $isActive = session('id_warung') == $warung->id; @endphp

                <div class="relative bg-white border transition-all rounded-2xl p-3.5 shadow-sm flex items-center justify-between gap-3
                    {{ $isActive ? 'border-primary-500/50 ring-1 ring-primary-500/20 bg-gradient-to-r from-primary-50/40 via-white to-white' : 'border-slate-200/80 hover:border-slate-300' }}">

                    <!-- KIRI: IKON & INFO CABANG -->
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Box Ikon -->
                        <div class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center font-bold text-sm transition-colors
                            {{ $isActive ? 'bg-primary-600 text-white shadow-sm shadow-primary-600/30' : 'bg-slate-100 text-slate-500' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6"/>
                            </svg>
                        </div>

                        <!-- Informasi Nama & Badge -->
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-800 text-sm truncate leading-snug">
                                {{ $warung->nama_warung }}
                            </h3>

                            @if($isActive)
                                <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full bg-emerald-50 text-[10px] font-bold text-emerald-600 border border-emerald-200/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Aktif Digunakan
                                </span>
                            @else
                                <span class="text-[11px] text-slate-400 font-medium block mt-0.5">
                                    Cabang Agen
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- KANAN: TOMBOL AKSI -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if(!$isActive)
                            <form action="{{ route('cabang.pilih', $warung->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-xl bg-primary-50 hover:bg-primary-100 active:scale-95 text-primary-700 text-xs font-bold transition border border-primary-100">
                                    Pilih
                                </button>
                            </form>
                        @endif

                        <!-- Tombol Edit -->
                        <a href="{{ route('cabang.edit', $warung->id) }}"
                           title="Edit Cabang"
                           class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 active:scale-95 text-slate-500 flex items-center justify-center border border-slate-200/80 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 013.182 3.182L8.25 18.463 4.5 19.5l1.037-3.75L16.862 3.487z"/>
                            </svg>
                        </a>

                        <!-- Tombol Hapus (Hanya tampil jika cabang > 1) -->
                        @if($warungs->count() > 1)
                            <form action="{{ route('cabang.destroy', $warung->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang {{ addslashes($warung->nama_warung) }}?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        title="Hapus Cabang"
                                        class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-95 text-rose-500 flex items-center justify-center border border-rose-200/60 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <!-- EMPTY STATE (JIKA BELUM ADA CABANG) -->
        <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-6 text-center shadow-sm">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6"/>
                </svg>
            </div>

            <h3 class="font-bold text-slate-800 text-sm">Belum Ada Cabang</h3>
            <p class="text-xs text-slate-500 mt-1 mb-4 max-w-[220px] mx-auto leading-relaxed">
                Tambahkan lokasi cabang BRILINK pertama Anda untuk mulai mengelola transaksi.
            </p>

            <a href="{{ route('cabang.create') }}"
               class="inline-flex items-center gap-1.5 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-sm shadow-primary-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>Tambah Cabang</span>
            </a>
        </div>
    @endif

</div>
@endsection