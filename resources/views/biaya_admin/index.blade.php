@extends('layouts.app')

@section('title', 'Tarif Biaya Admin')

@section('content')
<div class="max-w-5xl mx-auto pb-12 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Tarif Biaya Admin</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Kelola tarif jasa toko, potongan bank, dan margin keuntungan per kategori transaksi.
            </p>
        </div>
        <a href="{{ route('biaya-admin.create') }}" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm shrink-0 min-h-11">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Tarif Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-2xl text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-xs sm:text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter by Kategori -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('biaya-admin.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <label for="filter_kategori" class="text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Filter Kategori:</label>
            <select id="filter_kategori" name="id_kategori" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id }}" @selected(request('id_kategori') == $kat->id)>
                        {{ $kat->getNamaDinamis($bankUtama ?? 'BRI') }}
                    </option>
                @endforeach
            </select>
            @if(request('id_kategori'))
                <a href="{{ route('biaya-admin.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 bg-slate-100 rounded-xl">Reset</a>
            @endif
        </form>
        <div class="text-xs text-slate-500 font-medium">
            Bank Utama Cabang: <strong class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200">{{ $bankUtama ?? 'BRI' }}</strong>
        </div>
    </div>

    <!-- Table Tarif -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        @if($biayaAdmin->isEmpty())
            <div class="p-10 text-center space-y-3">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m4-9H9.5a2.5 2.5 0 000 5h5a2.5 2.5 0 010 5H8"/><circle cx="12" cy="12" r="9"/></svg>
                </div>
                <p class="text-base font-bold text-slate-800">Belum ada aturan tarif biaya admin</p>
                <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto">Tambahkan tarif biaya admin toko dan potongan bank per kategori transaksi untuk cabang ini.</p>
                <div class="pt-2">
                    <a href="{{ route('biaya-admin.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 text-white text-xs font-bold rounded-xl hover:bg-primary-700 transition">
                        Tambah Tarif Pertama
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-[11px] uppercase tracking-wider text-slate-500 font-extrabold">
                            <th class="px-4 py-3.5">Kategori Transaksi</th>
                            <th class="px-4 py-3.5">Rentang Nominal</th>
                            <th class="px-4 py-3.5">Admin Toko</th>
                            <th class="px-4 py-3.5">Admin Bank/Modal</th>
                            <th class="px-4 py-3.5">Laba Bersih</th>
                            <th class="px-4 py-3.5">Akun Khusus</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($biayaAdmin as $biaya)
                            @php
                                $namaKategori = $biaya->kategori ? $biaya->kategori->getNamaDinamis($bankUtama ?? 'BRI') : ($biaya->jenis ?? 'Umum');
                                $keuntungan = (int) $biaya->biaya_admin - (int) ($biaya->biaya_admin_bank ?? 0);
                            @endphp
                            <tr class="text-xs sm:text-sm hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-800">{{ $namaKategori }}</div>
                                    @if($biaya->kategori)
                                        <span class="inline-block mt-0.5 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded {{ $biaya->kategori->jenis_aliran === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            Kas {{ $biaya->kategori->jenis_aliran }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-slate-700 whitespace-nowrap">
                                    Rp {{ number_format($biaya->range_awal, 0, ',', '.') }} – Rp {{ number_format($biaya->range_akhir, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($biaya->biaya_admin, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 font-medium text-slate-500 whitespace-nowrap">
                                    Rp {{ number_format($biaya->biaya_admin_bank ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 font-black text-emerald-600 whitespace-nowrap">
                                    Rp {{ number_format($keuntungan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">
                                    {{ $biaya->kas?->jenis_kas ?? 'Semua Akun' }}
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex justify-end items-center gap-1.5">
                                        <a href="{{ route('biaya-admin.edit', $biaya->id) }}" class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 font-bold text-xs transition">
                                            Ubah
                                        </a>
                                        <form action="{{ route('biaya-admin.destroy', $biaya->id) }}" method="POST" onsubmit="return confirm('Hapus tarif biaya admin ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition">
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
            @if($biayaAdmin->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $biayaAdmin->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
