@extends('layouts.app')

@section('title', 'Ubah Cabang')

@section('content')
<div class="max-w-2xl mx-auto space-y-4 sm:space-y-5">

    <!-- NAVIGASI KEMBALI & HEADER -->
    <div class="flex items-center gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <a href="{{ route('cabang.index') }}"
           class="w-10 h-10 shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 flex items-center justify-center transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
        </a>
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-extrabold text-slate-800 leading-snug">Ubah Cabang</h2>
            <p class="text-sm text-slate-500 truncate">Ubah nama cabang usaha Anda</p>
        </div>
    </div>

    <!-- ALERT ERROR VALIDASI -->
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200/80 text-rose-800 p-4 rounded-2xl text-sm space-y-2">
            <div class="font-bold flex items-center gap-2 text-rose-700">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span>Periksa kembali data yang diisi:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM INPUT -->
    <form action="{{ route('cabang.update', $warung->id) }}" method="POST"
          class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="nama_warung" class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">
                Nama Cabang <span class="text-rose-500">*</span>
            </label>

            <div class="relative">
                <input
                    type="text"
                    id="nama_warung"
                    name="nama_warung"
                    value="{{ old('nama_warung', $warung->nama_warung) }}"
                    placeholder="Contoh: Cabang Utama"
                    required
                    autofocus
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none text-sm sm:text-base text-slate-800 transition font-medium placeholder:text-slate-400"
                >
            </div>
            <p class="text-sm text-slate-400 mt-2">Gunakan nama yang mudah dikenali oleh Anda dan staf.</p>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('cabang.index') }}"
               class="flex-1 text-center px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 active:scale-95 text-sm font-bold transition">
                Batal
            </a>

            <button type="submit"
                    class="flex-1 px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 active:scale-95 text-white text-sm font-bold transition shadow-sm shadow-primary-600/20 flex items-center justify-center gap-2 min-h-12">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                <span>Perbarui Data</span>
            </button>
        </div>

    </form>

</div>
@endsection