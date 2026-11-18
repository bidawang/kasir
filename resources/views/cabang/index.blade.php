@extends('layouts.app')

@section('title', 'Kelola Cabang')

@section('content')
<div class="ak-page">

    {{-- HEADER --}}
    <div class="ak-head">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="ak-head-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6M3 9.75L12 3l9 6.75"/></svg>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <h1 class="ak-title">Kelola Cabang</h1>
                    <span class="ak-badge-primary">{{ $warungs->count() }}</span>
                </div>
                <p class="ak-desc sub-explanation">Daftar outlet agen, bank utama operasional, dan rekening penampung.</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <a href="{{ route('owner.monitoring') }}" class="ak-btn-secondary">
                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                <span>Monitoring</span>
            </a>
            <a href="{{ route('cabang.create') }}" class="ak-btn-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah Cabang</span>
            </a>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="ak-alert-success">
            <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- DAFTAR CABANG --}}
    @if($warungs->count())
        <div class="ak-card divide-y divide-slate-100 overflow-hidden">
            @foreach($warungs as $warung)
                @php $isActive = session('id_warung') == $warung->id; @endphp

                <div class="p-3 sm:p-3.5 flex items-center gap-3 {{ $isActive ? 'bg-primary-50/50' : 'hover:bg-slate-50/70' }} transition-colors">
                    {{-- Ikon --}}
                    <div class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center font-black text-xs
                        {{ $isActive ? 'bg-primary-600 text-white shadow-sm' : 'bg-slate-100 text-slate-500' }}">
                        {{ strtoupper(mb_substr($warung->nama_warung, 0, 2)) }}
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <h3 class="font-bold text-slate-900 text-sm truncate">{{ $warung->nama_warung }}</h3>
                            @if($isActive)
                                <span class="ak-badge-success">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                            <span class="ak-badge-primary">{{ $warung->bank_utama ?? 'BRI' }}</span>
                            @if($warung->nomor_rekening_utama)
                                <span class="text-[11px] font-mono text-slate-500">{{ $warung->nomor_rekening_utama }}</span>
                            @endif
                        </div>
                        @if($warung->alamat || $warung->no_telepon)
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate sub-explanation">
                                {{ $warung->alamat }}{{ $warung->alamat && $warung->no_telepon ? ' • ' : '' }}{{ $warung->no_telepon }}
                            </p>
                        @endif
                    </div>

                    {{-- Aksi --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if(!$isActive)
                            <form action="{{ route('cabang.pilih', $warung->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="ak-btn-soft !px-2.5 !py-1.5" title="Gunakan cabang ini">
                                    <span class="hidden sm:inline">Gunakan</span>
                                    <svg class="w-3.5 h-3.5 sm:hidden" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('cabang.edit', $warung->id) }}" title="Ubah Cabang"
                           class="ak-icon-btn bg-white hover:bg-slate-50 text-slate-500 border-slate-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 013.182 3.182L8.25 18.463 4.5 19.5l1.037-3.75L16.862 3.487z"/></svg>
                        </a>

                        @if($warungs->count() > 1)
                            <form action="{{ route('cabang.destroy', $warung->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus cabang {{ addslashes($warung->nama_warung) }}? Semua data kas di cabang ini ikut terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Cabang"
                                        class="ak-icon-btn bg-rose-50 hover:bg-rose-100 text-rose-600 border-rose-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-[11px] text-slate-400 px-1 sub-explanation">
            Cabang aktif menentukan data kas, transaksi, dan laporan yang tampil di seluruh menu.
        </p>
    @else
        <div class="ak-empty space-y-2">
            <div class="w-10 h-10 mx-auto rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25m16.5 0h2.25m-15 0v-7.5A2.25 2.25 0 016 11.25h3a2.25 2.25 0 012.25 2.25V21m-6 0h6"/></svg>
            </div>
            <p class="text-sm font-bold text-slate-700">Belum ada cabang</p>
            <p class="sub-explanation">Tambahkan cabang pertama untuk mulai mencatat transaksi.</p>
            <a href="{{ route('cabang.create') }}" class="ak-btn-primary mt-1">Tambah Cabang</a>
        </div>
    @endif

</div>
@endsection
