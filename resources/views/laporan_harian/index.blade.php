@extends('layouts.app')

@section('title', 'Laporan Kas Harian')

@section('content')
@php
    $denominationsDesc = collect(\App\Models\DetailKas::DENOMINATIONS)->sortDesc()->values();
@endphp

<div class="ak-page-wide space-y-3.5 pb-20" x-data="{ expandedHistory: null }">

    <!-- Header Section -->
    <div class="ak-head">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-primary-600"></span>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Verifikasi Fisik</span>
            </div>
            <h1 class="ak-title">Laporan Kas Harian</h1>
            <p class="ak-desc sub-explanation">Bandingkan hitungan uang tunai fisik di laci kas dengan saldo kas sistem.</p>
        </div>
        <a href="{{ $isOwner ? route('laporan.index') : route('transaksi.menu') }}" class="ak-btn-secondary !text-xs !py-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            <span>{{ $isOwner ? 'Ke Laporan' : 'Ke Transaksi' }}</span>
        </a>
    </div>

    @if (session('success'))
        <div class="ak-alert-success">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (isset($errors) && $errors->any())
        <div class="ak-alert-error">
            <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Saldo Kas Sistem Card (Gradien BRI Blue) -->
        <div class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 rounded-2xl p-4 sm:p-5 text-white shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between opacity-85 mb-2">
                    <span class="text-[10px] font-black uppercase tracking-wider">Saldo Kas Sistem</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                </div>
                <div class="text-2xl sm:text-3xl font-black tracking-tight">
                    Rp {{ number_format($saldoSistem, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-[11px] text-primary-100 mt-3 font-medium sub-explanation">
                Total saldo kas tunai pada cabang aktif menurut pembukuan sistem.
            </div>
        </div>

        <!-- Input / Koreksi Form Container -->
        <div class="lg:col-span-2 ak-card p-3.5 sm:p-4 space-y-3">
            <div>
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ $isOwner ? 'Input atau koreksi laporan' : 'Laporan uang tunai fisik hari ini' }}</h3>
                <p class="mt-0.5 text-xs text-slate-500 sub-explanation">
                    {{ $isOwner ? 'Owner dapat menyimpan ulang laporan kasir untuk memperbaiki selisih angka.' : 'Laporan dikirim satu kali per shift/hari. Laporkan ke owner jika ada selisih.' }}
                </p>
            </div>

            @if ($isOwner || !$laporanHariIni)
                <form id="form-laporan-harian" action="{{ route('laporan-harian.store') }}" method="POST" class="space-y-3" onsubmit="return false;">
                    @csrf
                    @if ($isOwner)
                        <div>
                            <label for="id_user" class="ak-label">Laporan Untuk Karyawan</label>
                            <select id="id_user" name="id_user" class="ak-input font-bold">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawan as $employee)
                                    <option value="{{ $employee->id }}" data-rincian="{{ json_encode($laporanHarianKaryawan->get($employee->id)?->rincian_fisik ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) }}" @selected(old('id_user') == $employee->id)>{{ $employee->nama }}</option>
                                @endforeach
                            </select>
                            @if ($karyawan->isEmpty())
                                <p class="mt-1 text-[11px] text-amber-700 font-medium">Belum ada kasir terhubung ke cabang ini.</p>
                            @endif
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <label class="ak-label !mb-0">Rincian Pecahan Uang Fisik</label>
                            <span class="text-[10px] text-slate-400 sub-explanation">Klik centang atau tekan Enter untuk baris berikutnya</span>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-xs">
                                <thead class="bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-3.5 py-2 text-left">Pecahan</th>
                                        <th class="px-3.5 py-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($denominationsDesc as $index => $pecahan)
                                        <tr data-denomination-row data-index="{{ $index }}" class="{{ $index === 0 ? '' : 'hidden' }} transition-colors">
                                            <td colspan="2" class="px-3.5 py-2.5 space-y-1.5">
                                                <!-- Baris Atas: Pecahan & Total -->
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-xs sm:text-sm text-slate-800">Rp {{ number_format($pecahan, 0, ',', '.') }}</span>
                                                    <span class="font-black text-primary-700 text-xs">Rp <span data-total-pecahan="{{ $pecahan }}">0</span></span>
                                                </div>
                                                <!-- Baris Bawah: Input Lembar & Button Centang -->
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1">
                                                        <input
                                                            type="number"
                                                            name="jumlah[{{ $pecahan }}]"
                                                            min="0"
                                                            step="1"
                                                            required
                                                            value="{{ old('jumlah.' . $pecahan, 0) }}"
                                                            data-jumlah-lembar
                                                            data-pecahan="{{ $pecahan }}"
                                                            aria-label="Jumlah lembar pecahan Rp {{ number_format($pecahan, 0, ',', '.') }}"
                                                            placeholder="0 lembar"
                                                            onkeydown="if(event.key==='Enter'||event.keyCode===13){event.preventDefault();event.stopPropagation();confirmDenominationRow({{ $index }});return false;}"
                                                            class="ak-input text-center font-black [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                        >
                                                    </div>
                                                    <button
                                                        type="button"
                                                        data-confirm-row
                                                        onclick="confirmDenominationRow({{ $index }})"
                                                        title="Konfirmasi pecahan"
                                                        aria-label="Konfirmasi pecahan"
                                                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 transition hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 shadow-2xs cursor-pointer"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-slate-50 font-bold text-slate-900 border-t border-slate-200">
                                    <tr>
                                        <th class="px-3.5 py-2.5 text-left text-xs uppercase tracking-wider text-slate-600">Total Fisik:</th>
                                        <th class="px-3.5 py-2.5 text-right text-sm sm:text-base font-black text-emerald-700">Rp <span id="saldo-fisik-preview">0</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div>
                        <label for="catatan" class="ak-label">Catatan <span class="font-normal text-slate-400 lowercase">(opsional)</span></label>
                        <textarea id="catatan" name="catatan" rows="2" maxlength="2000" class="ak-input" placeholder="Contoh: selisih karena transaksi yang belum tercatat">{{ old('catatan') }}</textarea>
                    </div>

                    <button type="button" id="btn-submit-laporan" onclick="submitLaporanForm()" @disabled($isOwner && $karyawan->isEmpty()) class="ak-btn-primary w-full !py-2.5">
                        {{ $isOwner ? 'Simpan / Koreksi Laporan' : 'Kirim Laporan Kas Harian' }}
                    </button>
                </form>
            @else
                <div class="ak-alert-success">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Laporan hari ini sudah dikirim sebesar <strong>Rp {{ number_format($laporanHariIni->saldo_fisik, 0, ',', '.') }}</strong>.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Section -->
    <div class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">{{ $isOwner ? 'Riwayat Laporan Karyawan' : 'Riwayat Laporan Saya' }}</h3>
        </div>

        @if ($laporan->isEmpty())
            <div class="ak-card p-8 text-center space-y-2">
                <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <p class="text-xs sm:text-sm font-semibold text-slate-500">Belum ada riwayat laporan kas harian.</p>
            </div>
        @else
            <!-- Tampilan Mobile: Accordion Cards -->
            <div class="space-y-2 lg:hidden">
                @foreach ($laporan as $item)
                    <div class="ak-card p-0 overflow-hidden">
                        <div @click="expandedHistory = expandedHistory === {{ $item->id }} ? null : {{ $item->id }}"
                             class="p-3 sm:p-3.5 flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition-colors select-none">
                            <div class="space-y-0.5 pr-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if ($isOwner)
                                        <span class="font-extrabold text-slate-900 text-xs">{{ $item->user->nama }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                    @endif
                                    <span class="text-[11px] font-bold text-slate-500">{{ $item->tanggal->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-xs font-black text-slate-800">
                                    Fisik: Rp {{ number_format($item->saldo_fisik, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md {{ $item->selisih === 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $item->selisih === 0 ? 'Balance' : (($item->selisih > 0 ? '+' : '') . 'Rp ' . number_format($item->selisih, 0, ',', '.')) }}
                                </span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="expandedHistory === {{ $item->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </div>
                        </div>

                        <!-- Accordion Body (Mobile) -->
                        <div x-show="expandedHistory === {{ $item->id }}" x-transition x-cloak class="px-3 pb-3 pt-0 border-t border-slate-100 space-y-2.5 text-xs">
                            <div class="grid grid-cols-2 gap-2 pt-2.5">
                                <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                                    <span class="block font-bold text-slate-400 uppercase text-[9px]">Saldo Sistem</span>
                                    <span class="font-semibold text-slate-700 text-xs">Rp {{ number_format($item->saldo_sistem, 0, ',', '.') }}</span>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                                    <span class="block font-bold text-slate-400 uppercase text-[9px]">Dicatat Oleh</span>
                                    <span class="font-semibold text-slate-700 text-xs">{{ $item->pelapor->nama }}</span>
                                </div>
                            </div>

                            <!-- Rincian Pecahan Uang (Accordion) -->
                            <div x-data="{ openPecahan: false }" class="rounded-xl border border-slate-200/90 overflow-hidden bg-slate-50/60">
                                <button type="button" 
                                        @click="openPecahan = !openPecahan" 
                                        class="w-full flex items-center justify-between p-2 text-left hover:bg-slate-100/70 transition-colors select-none cursor-pointer">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="block font-bold text-slate-700 uppercase text-[10px] tracking-wider truncate">Rincian Pecahan Uang</span>
                                        @php
                                            $rincian = is_array($item->rincian_fisik) ? $item->rincian_fisik : [];
                                            $totalLembar = 0;
                                            $hasPecahan = false;
                                            foreach ($denominationsDesc as $p) {
                                                $qty = (int) ($rincian[$p] ?? $rincian[(string)$p] ?? 0);
                                                if ($qty > 0) {
                                                    $totalLembar += $qty;
                                                    $hasPecahan = true;
                                                }
                                            }
                                        @endphp
                                        <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded {{ $hasPecahan ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $totalLembar }} lbr
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0 ml-2">
                                        <span class="text-[10px] font-semibold text-slate-500" x-text="openPecahan ? 'Tutup' : 'Lihat'"></span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openPecahan ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                        </svg>
                                    </div>
                                </button>
                                <div x-show="openPecahan" x-collapse class="p-2.5 border-t border-slate-200/80 bg-white space-y-1.5">
                                    @if ($hasPecahan)
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                                            @foreach ($denominationsDesc as $pecahan)
                                                @php $qty = (int) ($rincian[$pecahan] ?? $rincian[(string)$pecahan] ?? 0); @endphp
                                                @if ($qty > 0)
                                                    <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50 border border-slate-100 text-[11px]">
                                                        <span class="font-bold text-slate-700">Rp {{ number_format($pecahan, 0, ',', '.') }}</span>
                                                        <div class="text-right">
                                                            <span class="font-semibold text-slate-500">{{ $qty }} lbr</span>
                                                            <span class="font-bold text-emerald-700 ml-1">= Rp {{ number_format($pecahan * $qty, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-100 flex justify-between items-center text-xs font-black text-slate-800">
                                            <span>Total Fisik Terhitung:</span>
                                            <span class="text-emerald-700">Rp {{ number_format($item->saldo_fisik, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-center py-2 text-xs text-slate-400 font-medium">
                                            <p>Tidak ada lembar pecahan fisik yang tercatat</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                                <span class="block font-bold text-slate-400 uppercase text-[9px]">Catatan</span>
                                <span class="text-slate-600 italic text-[11px]">{{ $item->catatan ?: '—' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tampilan Desktop: Tabel Standar -->
            <div class="hidden lg:block ak-table-wrap">
                <table class="ak-table text-xs">
                    <thead>
                        <tr>
                            @if ($isOwner)<th>Karyawan</th>@endif
                            <th>Tanggal</th>
                            <th>Uang Fisik</th>
                            <th>Saldo Sistem</th>
                            <th>Selisih</th>
                            <th>Dicatat Oleh</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach ($laporan as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                @if ($isOwner)<td class="whitespace-nowrap px-3.5 py-3 font-bold text-slate-900">{{ $item->user->nama }}</td>@endif
                                <td class="whitespace-nowrap px-3.5 py-3">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-3.5 py-3">
                                    <div x-data="{ openPecahanDesk: false }" class="relative">
                                        @php
                                            $rincian = is_array($item->rincian_fisik) ? $item->rincian_fisik : [];
                                            $totalLembar = 0;
                                            $hasPecahan = false;
                                            foreach ($denominationsDesc as $p) {
                                                $qty = (int) ($rincian[$p] ?? $rincian[(string)$p] ?? 0);
                                                if ($qty > 0) {
                                                    $totalLembar += $qty;
                                                    $hasPecahan = true;
                                                }
                                            }
                                        @endphp
                                        <div class="flex items-center gap-1.5">
                                            <span class="whitespace-nowrap font-bold text-slate-900">Rp {{ number_format($item->saldo_fisik, 0, ',', '.') }}</span>
                                            <button type="button" @click="openPecahanDesk = !openPecahanDesk"
                                                    class="text-[9px] font-extrabold px-1.5 py-0.2 rounded border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition flex items-center gap-1 cursor-pointer">
                                                <span>{{ $totalLembar }} lbr</span>
                                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openPecahanDesk ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                            </button>
                                        </div>
                                        <div x-show="openPecahanDesk" x-collapse class="mt-2 p-2 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1 min-w-56 shadow-sm">
                                            @if($hasPecahan)
                                                <div class="space-y-0.5">
                                                    @foreach ($denominationsDesc as $pecahan)
                                                        @php $qty = (int) ($rincian[$pecahan] ?? $rincian[(string)$pecahan] ?? 0); @endphp
                                                        @if ($qty > 0)
                                                            <div class="flex items-center justify-between text-[10px] py-0.5 border-b border-slate-200/60 last:border-0">
                                                                <span class="font-bold text-slate-700">Rp {{ number_format($pecahan, 0, ',', '.') }}</span>
                                                                <div>
                                                                    <span class="text-slate-500 font-medium">{{ $qty }} lbr</span>
                                                                    <span class="font-bold text-emerald-700 ml-1">= {{ number_format($pecahan * $qty, 0, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-[10px] text-slate-400 italic text-center">Tidak ada lembar fisik</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3.5 py-3">Rp {{ number_format($item->saldo_sistem, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-3.5 py-3 font-bold {{ $item->selisih === 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $item->selisih === 0 ? 'Balance' : (($item->selisih > 0 ? '+' : '') . 'Rp ' . number_format($item->selisih, 0, ',', '.')) }}
                                </td>
                                <td class="whitespace-nowrap px-3.5 py-3">{{ $item->pelapor->nama }}</td>
                                <td class="max-w-xs px-3.5 py-3 text-slate-500 text-[11px] truncate">{{ $item->catatan ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
    const formatRupiah = (amount) => new Intl.NumberFormat('id-ID').format(amount);

    function submitLaporanForm() {
        const form = document.getElementById('form-laporan-harian');
        if (!form) return;
        const empSelect = document.getElementById('id_user');
        if (empSelect && !empSelect.value) {
            empSelect.focus();
            empSelect.setCustomValidity('Pilih karyawan terlebih dahulu.');
            empSelect.reportValidity();
            return;
        }
        form.submit();
    }

    function updateCashTotal() {
        const quantityInputs = document.querySelectorAll('[data-jumlah-lembar]');
        let total = 0n;

        quantityInputs.forEach((input) => {
            if (/^0\d+/.test(input.value)) {
                input.value = input.value.replace(/^0+(?=\d)/, '');
            }

            const count = BigInt(input.value || '0');
            const denomination = BigInt(input.dataset.pecahan || '0');
            const subtotal = count * denomination;

            total += subtotal;
            const previewEl = document.querySelector(`[data-total-pecahan="${input.dataset.pecahan}"]`);
            if (previewEl) previewEl.textContent = formatRupiah(subtotal);
        });

        const fisikPreviewEl = document.getElementById('saldo-fisik-preview');
        if (fisikPreviewEl) fisikPreviewEl.textContent = formatRupiah(total);
    }

    function confirmDenominationRow(index) {
        const denominationRows = Array.from(document.querySelectorAll('[data-denomination-row]'));
        const currentRow = denominationRows[index];
        if (!currentRow) return;

        const currentInput = currentRow.querySelector('[data-jumlah-lembar]');
        if (currentInput && currentInput.value === '') {
            currentInput.value = '0';
        }

        const button = currentRow.querySelector('[data-confirm-row]');
        if (button) {
            currentRow.classList.add('bg-emerald-50/40');
            button.classList.add('bg-emerald-600', 'border-emerald-600', 'text-white');
            button.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-600');
        }

        const nextRow = denominationRows[index + 1];
        if (nextRow) {
            nextRow.classList.remove('hidden');
            const nextInput = nextRow.querySelector('[data-jumlah-lembar]');
            if (nextInput) {
                setTimeout(() => {
                    nextInput.focus();
                    nextInput.select();
                }, 10);
            }
        } else {
            setTimeout(() => {
                document.getElementById('catatan')?.focus();
            }, 10);
        }

        updateCashTotal();
    }

    function syncProgressiveRowsFromValues() {
        const denominationRows = Array.from(document.querySelectorAll('[data-denomination-row]'));
        let lastNonZeroIndex = -1;

        denominationRows.forEach((row, index) => {
            const val = Number(row.querySelector('[data-jumlah-lembar]')?.value || 0);
            if (val > 0) {
                lastNonZeroIndex = index;
            }
        });

        denominationRows.forEach((row, index) => {
            const shouldShow = index === 0 || index <= lastNonZeroIndex + 1;
            const isConfirmed = index <= lastNonZeroIndex;

            row.classList.toggle('hidden', !shouldShow);
            const button = row.querySelector('[data-confirm-row]');
            if (button) {
                row.classList.toggle('bg-emerald-50/40', isConfirmed);
                button.classList.toggle('bg-emerald-600', isConfirmed);
                button.classList.toggle('border-emerald-600', isConfirmed);
                button.classList.toggle('text-white', isConfirmed);
                button.classList.toggle('bg-slate-50', !isConfirmed);
                button.classList.toggle('border-slate-200', !isConfirmed);
                button.classList.toggle('text-slate-600', !isConfirmed);
            }
        });
    }

    document.querySelectorAll('[data-jumlah-lembar]').forEach((input) => {
        input.addEventListener('input', () => {
            if (/^0\d+/.test(input.value)) {
                input.value = input.value.replace(/^0+(?=\d)/, '');
            }
            updateCashTotal();
        });
        input.addEventListener('focus', () => {
            input.select();
        });
    });

    const employeeSelect = document.getElementById('id_user');
    if (employeeSelect) {
        employeeSelect.addEventListener('change', () => {
            employeeSelect.setCustomValidity('');
            try {
                const rincian = JSON.parse(employeeSelect.selectedOptions[0]?.dataset.rincian || '{}');
                document.querySelectorAll('[data-jumlah-lembar]').forEach((input) => {
                    input.value = rincian[input.dataset.pecahan] ?? 0;
                });
                updateCashTotal();
                syncProgressiveRowsFromValues();
            } catch (err) {
                console.error('Gagal membaca rincian kasir:', err);
            }
        });
    }

    updateCashTotal();
    syncProgressiveRowsFromValues();
</script>
@endsection
