@extends('layouts.app')

@section('title', 'Tambah Cabang')

@section('content')
<div class="max-w-lg mx-auto space-y-3 sm:space-y-4">

    <!-- NAVIGASI KEMBALI & HEADER -->
    <div class="flex items-center gap-3 bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <a href="{{ route('cabang.index') }}"
           class="w-9 h-9 shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 flex items-center justify-center transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
        </a>
        <div class="min-w-0">
            <h2 class="text-base sm:text-lg font-extrabold text-slate-800 leading-snug">Tambah Cabang</h2>
            <p class="text-xs text-slate-500 truncate">Tambahkan lokasi agen BRILINK baru</p>
        </div>
    </div>

    <!-- ALERT ERROR VALIDASI -->
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200/80 text-rose-800 p-3.5 rounded-2xl text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-rose-700">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span>Terjadi kesalahan input:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-slate-600 pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM INPUT -->
    <form action="{{ route('cabang.store') }}" method="POST"
          class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-sm space-y-4">
        @csrf

        <div>
            <label for="nama_warung" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Nama Cabang / Warung <span class="text-rose-500">*</span>
            </label>

            <div class="relative">
                <input
                    type="text"
                    id="nama_warung"
                    name="nama_warung"
                    value="{{ old('nama_warung') }}"
                    placeholder="Contoh: BRILINK Cabang Utama"
                    required
                    autofocus
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none text-xs text-slate-800 transition font-medium placeholder:text-slate-400"
                >
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Gunakan nama yang mudah dikenali oleh kasir/admin.</p>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            <a href="{{ route('cabang.index') }}"
               class="flex-1 text-center px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 active:scale-95 text-xs font-bold transition">
                Batal
            </a>

            <button type="submit"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 active:scale-95 text-white text-xs font-bold transition shadow-sm shadow-primary-600/20 flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <span>Simpan Cabang</span>
            </button>
        </div>

    </form>

</div>
@endsection