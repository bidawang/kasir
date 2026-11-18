@extends('layouts.app')

@section('title', 'Tarif Biaya Admin')

@section('content')
<div class="ak-page-wide space-y-3.5 pb-20">
    <div class="ak-head">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-primary-600"></span>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pengaturan Biaya</span>
            </div>
            <div class="flex items-center gap-2">
                <h1 class="ak-title">Tarif Biaya Admin</h1>
                <span class="px-2 py-0.2 rounded-full bg-primary-50 text-primary-700 text-[10px] font-black border border-primary-100">
                    {{ $biayaAdmin->count() }} Aturan
                </span>
            </div>
            <p class="ak-desc sub-explanation">
                Kelola tarif jasa toko, potongan bank/server, dan margin keuntungan per kategori transaksi.
            </p>
        </div>
        <a href="{{ route('biaya-admin.create') }}" class="ak-btn-primary !text-xs !py-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span>Tambah Tarif</span>
        </a>
    </div>

    @if(session('success'))
        <div class="ak-alert-success">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="ak-alert-error">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter by Kategori -->
    <div class="ak-card p-3 sm:p-3.5 flex flex-col sm:flex-row items-center justify-between gap-2.5">
        <form action="{{ route('biaya-admin.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <label for="filter_kategori" class="text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Filter Kategori:</label>
            <select id="filter_kategori" name="id_kategori" onchange="this.form.submit()" class="ak-input font-bold !py-1.5 !w-auto">
                <option value="">Semua Kategori</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat->id }}" @selected(request('id_kategori') == $kat->id)>
                        {{ $kat->getNamaDinamis($bankUtama ?? 'BRI') }}
                    </option>
                @endforeach
            </select>
            @if(request('id_kategori'))
                <a href="{{ route('biaya-admin.index') }}" class="ak-btn-secondary !text-xs !py-1.5">Reset</a>
            @endif
        </form>
        <div class="text-[11px] text-slate-500 font-medium">
            Bank Utama: <strong class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200">{{ $bankUtama ?? 'BRI' }}</strong>
        </div>
    </div>

    <!-- Table Tarif -->
    @if($biayaAdmin->isEmpty())
        <div class="ak-card p-8 text-center space-y-2">
            <div class="w-10 h-10 mx-auto rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m4-9H9.5a2.5 2.5 0 000 5h5a2.5 2.5 0 010 5H8"/><circle cx="12" cy="12" r="9"/></svg>
            </div>
            <p class="text-xs sm:text-sm font-bold text-slate-800">Belum ada aturan tarif biaya admin</p>
            <p class="text-[11px] text-slate-500 max-w-sm mx-auto sub-explanation">Tambahkan tarif biaya admin toko dan potongan bank per kategori transaksi untuk cabang ini.</p>
            <a href="{{ route('biaya-admin.create') }}" class="ak-btn-primary !text-xs !py-1.5 inline-flex">
                Tambah Tarif Pertama
            </a>
        </div>
    @else
        <!-- Mobile Cards -->
        <div class="grid grid-cols-1 gap-2.5 sm:hidden">
            @foreach($biayaAdmin as $biaya)
                @php
                    $namaKategori = $biaya->kategori ? $biaya->kategori->getNamaDinamis($bankUtama ?? 'BRI') : ($biaya->jenis ?? 'Umum');
                    $keuntungan = (int) $biaya->biaya_admin - (int) ($biaya->biaya_admin_bank ?? 0);
                @endphp
                <div class="ak-card p-3 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded {{ ($biaya->kategori?->jenis_aliran ?? '') === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                Kas {{ $biaya->kategori?->jenis_aliran ?? 'Umum' }}
                            </span>
                            <h3 class="font-black text-xs sm:text-sm text-slate-900 mt-1">{{ $namaKategori }}</h3>
                            <p class="text-[11px] text-slate-500 font-semibold">
                                Rp {{ number_format($biaya->range_awal, 0, ',', '.') }} - Rp {{ number_format($biaya->range_akhir, 0, ',', '.') }}
                            </p>
                        </div>
                        <span class="text-xs font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                            +Rp {{ number_format($keuntungan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] bg-slate-50 p-2 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-slate-400 block font-bold uppercase">Admin Toko</span>
                            <span class="font-black text-slate-800">Rp {{ number_format($biaya->biaya_admin, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-bold uppercase">Potongan Bank</span>
                            <span class="font-bold text-rose-700">Rp {{ number_format($biaya->biaya_admin_bank ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-slate-100">
                        <a href="{{ route('biaya-admin.edit', $biaya->id) }}" class="flex-1 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs text-center border border-amber-200 transition">
                            Ubah
                        </a>
                        <form action="{{ route('biaya-admin.destroy', $biaya->id) }}" method="POST" onsubmit="return confirm('Hapus tarif ini?');" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block ak-table-wrap">
            <table class="ak-table text-xs">
                <thead>
                    <tr>
                        <th>Kategori Transaksi</th>
                        <th>Rentang Nominal</th>
                        <th class="text-right">Admin Toko</th>
                        <th class="text-right">Admin Bank</th>
                        <th class="text-right">Laba Bersih</th>
                        <th>Akun Khusus</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($biayaAdmin as $biaya)
                        @php
                            $namaKategori = $biaya->kategori ? $biaya->kategori->getNamaDinamis($bankUtama ?? 'BRI') : ($biaya->jenis ?? 'Umum');
                            $keuntungan = (int) $biaya->biaya_admin - (int) ($biaya->biaya_admin_bank ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-3.5 py-3">
                                <div class="font-bold text-slate-900">{{ $namaKategori }}</div>
                                @if($biaya->kategori)
                                    <span class="inline-block mt-0.5 text-[9px] font-black uppercase px-1.5 py-0.2 rounded {{ $biaya->kategori->jenis_aliran === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        Kas {{ $biaya->kategori->jenis_aliran }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 font-semibold text-slate-700 whitespace-nowrap">
                                Rp {{ number_format($biaya->range_awal, 0, ',', '.') }} - Rp {{ number_format($biaya->range_akhir, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-3 text-right font-black text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($biaya->biaya_admin, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-3 text-right font-bold text-rose-700 whitespace-nowrap">
                                Rp {{ number_format($biaya->biaya_admin_bank ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-3 text-right font-black text-emerald-700 whitespace-nowrap">
                                +Rp {{ number_format($keuntungan, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-3 text-slate-500 whitespace-nowrap">
                                {{ $biaya->kas ? $biaya->kas->jenis_kas : 'Semua Akun' }}
                            </td>
                            <td class="px-3.5 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('biaya-admin.edit', $biaya->id) }}" class="rounded-lg bg-amber-50 px-2.5 py-1 font-bold text-amber-700 hover:bg-amber-100 border border-amber-200 text-xs transition">Ubah</a>
                                    <form action="{{ route('biaya-admin.destroy', $biaya->id) }}" method="POST" onsubmit="return confirm('Hapus tarif ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-rose-50 px-2.5 py-1 font-bold text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs transition cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
