@extends('layouts.app')

@section('title', 'Laporan Kas Harian')

@section('content')
@php
    $denominationsDesc = collect(\App\Models\DetailKas::DENOMINATIONS)->sortDesc()->values();
@endphp

<div class="max-w-6xl mx-auto space-y-6" x-data="{ expandedHistory: null }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Laporan Kas Harian</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">Bandingkan uang tunai fisik dengan saldo kas yang tercatat di sistem.</p>
        </div>
        <a href="{{ $isOwner ? route('laporan.index') : route('transaksi.menu') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-all">
            {{ $isOwner ? 'Kembali ke laporan' : 'Kembali ke transaksi' }}
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm sm:text-base font-semibold text-emerald-800 flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm sm:text-base text-rose-800 shadow-sm">
            <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Saldo Kas Sistem Card (Gradien Hijau) -->
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/10 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between opacity-80 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Saldo Kas Sistem</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                </div>
                <div class="text-2xl sm:text-3xl font-black tracking-tight">
                    Rp {{ number_format($saldoSistem, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-xs sm:text-sm text-emerald-100 mt-4 font-medium opacity-90">
                Jumlah saldo seluruh kas tunai pada cabang aktif.
            </div>
        </div>

        <!-- Input / Koreksi Form Container -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/85 shadow-sm space-y-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ $isOwner ? 'Input atau koreksi laporan' : 'Laporan uang tunai hari ini' }}</h3>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    {{ $isOwner ? 'Owner dapat menyimpan ulang laporan karyawan untuk memperbaiki angka yang keliru.' : 'Laporan hanya dapat dikirim satu kali per hari. Hubungi owner bila ada kesalahan.' }}
                </p>
            </div>

            @if ($isOwner || !$laporanHariIni)
                <form action="{{ route('laporan-harian.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @if ($isOwner)
                        <div class="space-y-1">
                            <label for="id_user" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Laporan untuk karyawan</label>
                            <select id="id_user" name="id_user" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" required>
                                <option value="">Pilih karyawan</option>
                                @foreach ($karyawan as $employee)
                                    <option value="{{ $employee->id }}" data-rincian="{{ json_encode($laporanHarianKaryawan->get($employee->id)?->rincian_fisik ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) }}" @selected(old('id_user') == $employee->id)>{{ $employee->nama }}</option>
                                @endforeach
                            </select>
                            @if ($karyawan->isEmpty())
                                <p class="mt-1 text-xs text-amber-700 font-medium">Belum ada karyawan kasir yang terhubung ke cabang ini.</p>
                            @endif
                        </div>
                    @endif

                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Rincian uang tunai fisik</label>
                            <span class="text-xs text-slate-400">Klik centang atau tekan Enter untuk lanjut</span>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-slate-200/80">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Pecahan</th>
                                        <th class="px-4 py-3 text-right">Total Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($denominationsDesc as $index => $pecahan)
                                        <tr data-denomination-row data-index="{{ $index }}" class="{{ $index === 0 ? '' : 'hidden' }} transition-colors">
                                            <td colspan="2" class="px-4 py-3.5 space-y-2">
                                                <!-- Baris Atas: Pecahan & Total -->
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-base text-slate-800">Rp {{ number_format($pecahan, 0, ',', '.') }}</span>
                                                    <span class="font-bold text-slate-700 text-sm">Rp <span data-total-pecahan="{{ $pecahan }}">0</span></span>
                                                </div>
                                                <!-- Baris Bawah: Input Jumlah Lembar & Button Centang -->
                                                <div class="flex items-center gap-2 pt-1">
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
                                                            placeholder="Jumlah lembar / keping"
                                                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-900 text-center [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                                        >
                                                    </div>
                                                    <button
                                                        type="button"
                                                        data-confirm-row
                                                        title="Konfirmasi pecahan Rp {{ number_format($pecahan, 0, ',', '.') }}"
                                                        aria-label="Konfirmasi pecahan Rp {{ number_format($pecahan, 0, ',', '.') }}"
                                                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 transition hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 shadow-sm"
                                                    >
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-slate-50 font-bold text-slate-900">
                                    <tr>
                                        <th class="px-4 py-3.5 text-left text-xs uppercase tracking-wider text-slate-600">Total Uang Tunai Fisik</th>
                                        <th class="px-4 py-3.5 text-right text-base text-emerald-700">Rp <span id="saldo-fisik-preview">0</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="catatan" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Catatan (opsional)</label>
                        <textarea id="catatan" name="catatan" rows="2" maxlength="2000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Contoh: selisih karena transaksi yang belum tercatat">{{ old('catatan') }}</textarea>
                    </div>

                    <button type="submit" @disabled($isOwner && $karyawan->isEmpty()) class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm disabled:cursor-not-allowed disabled:opacity-50">
                        {{ $isOwner ? 'Simpan / Koreksi Laporan' : 'Kirim Laporan Hari Ini' }}
                    </button>
                </form>
            @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 shadow-sm">
                    Laporan hari ini sudah dikirim sebesar <strong class="font-extrabold">Rp {{ number_format($laporanHariIni->saldo_fisik, 0, ',', '.') }}</strong>.
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ $isOwner ? 'Riwayat Laporan Karyawan' : 'Riwayat Laporan Saya' }}</h3>
        </div>

        @if ($laporan->isEmpty())
            <div class="bg-white rounded-2xl p-10 text-center border border-slate-200/85 space-y-3 shadow-sm">
                <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <p class="text-base font-semibold text-slate-500">Belum ada laporan kas harian.</p>
            </div>
        @else
            <!-- Tampilan Mobile: Accordion Cards -->
            <div class="space-y-3 lg:hidden">
                @foreach ($laporan as $item)
                    <div class="bg-white rounded-2xl border border-slate-200/85 overflow-hidden shadow-sm transition-all">
                        <div @click="expandedHistory = expandedHistory === {{ $item->id }} ? null : {{ $item->id }}"
                             class="p-4 flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition-colors select-none">
                            <div class="space-y-1 pr-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if ($isOwner)
                                        <span class="font-extrabold text-slate-900 text-sm">{{ $item->user->nama }}</span>
                                        <span class="text-slate-300">•</span>
                                    @endif
                                    <span class="text-xs font-bold text-slate-500">{{ $item->tanggal->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm font-bold text-slate-800">
                                    <span>Fisik: Rp {{ number_format($item->saldo_fisik, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-extrabold px-2.5 py-1 rounded-lg {{ $item->selisih === 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $item->selisih === 0 ? 'Balance' : (($item->selisih > 0 ? '+' : '') . 'Rp ' . number_format($item->selisih, 0, ',', '.')) }}
                                </span>
                                <svg class="w-5 h-5 text-slate-400 transition-transform duration-200" :class="expandedHistory === {{ $item->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </div>
                        </div>

                        <!-- Accordion Body (Mobile) -->
                        <div x-show="expandedHistory === {{ $item->id }}" x-transition x-cloak class="px-4 pb-4 pt-0 border-t border-slate-100 space-y-3 text-xs sm:text-sm">
                            <div class="grid grid-cols-2 gap-2 pt-3">
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="block font-bold text-slate-400 uppercase text-[10px]">Saldo Sistem</span>
                                    <span class="font-semibold text-slate-700">Rp {{ number_format($item->saldo_sistem, 0, ',', '.') }}</span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="block font-bold text-slate-400 uppercase text-[10px]">Dicatat Oleh</span>
                                    <span class="font-semibold text-slate-700">{{ $item->pelapor->nama }}</span>
                                </div>
                            </div>

                            @if ($item->rincian_fisik)
                                <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 space-y-1">
                                    <span class="block font-bold text-slate-400 uppercase text-[10px]">Rincian Pecahan Uang:</span>
                                    <p class="text-slate-600 font-medium">
                                        @foreach ($denominationsDesc as $pecahan)
                                            @if (($item->rincian_fisik[$pecahan] ?? 0) > 0)
                                                <span class="mr-1 inline-block whitespace-nowrap">Rp {{ number_format($pecahan, 0, ',', '.') }} × {{ number_format($item->rincian_fisik[$pecahan]) }}</span>
                                            @endif
                                        @endforeach
                                    </p>
                                </div>
                            @endif

                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <span class="block font-bold text-slate-400 uppercase text-[10px]">Catatan</span>
                                <span class="text-slate-600 italic">{{ $item->catatan ?: '—' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tampilan Desktop: Tabel Standar -->
            <div class="hidden lg:block bg-white rounded-2xl border border-slate-200/85 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-bold">
                            <tr>
                                @if ($isOwner)<th class="px-4 py-3.5">Karyawan</th>@endif
                                <th class="px-4 py-3.5">Tanggal</th>
                                <th class="px-4 py-3.5">Uang Fisik</th>
                                <th class="px-4 py-3.5">Saldo Sistem</th>
                                <th class="px-4 py-3.5">Selisih</th>
                                <th class="px-4 py-3.5">Dicatat Oleh</th>
                                <th class="px-4 py-3.5">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach ($laporan as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    @if ($isOwner)<td class="whitespace-nowrap px-4 py-3.5 font-bold text-slate-900">{{ $item->user->nama }}</td>@endif
                                    <td class="whitespace-nowrap px-4 py-3.5">{{ $item->tanggal->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3.5">
                                        <span class="whitespace-nowrap font-bold text-slate-900">Rp {{ number_format($item->saldo_fisik, 0, ',', '.') }}</span>
                                        @if ($item->rincian_fisik)
                                            <p class="mt-0.5 text-xs text-slate-400 min-w-48">
                                                @foreach ($denominationsDesc as $pecahan)
                                                    @if (($item->rincian_fisik[$pecahan] ?? 0) > 0)
                                                        <span class="mr-1 inline-block whitespace-nowrap">Rp {{ number_format($pecahan, 0, ',', '.') }} × {{ number_format($item->rincian_fisik[$pecahan]) }}</span>
                                                    @endif
                                                @endforeach
                                            </p>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5">Rp {{ number_format($item->saldo_sistem, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3.5 font-bold {{ $item->selisih === 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $item->selisih === 0 ? 'Balance' : (($item->selisih > 0 ? '+' : '') . 'Rp ' . number_format($item->selisih, 0, ',', '.')) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5">{{ $item->pelapor->nama }}</td>
                                    <td class="max-w-xs px-4 py-3.5 text-slate-500">{{ $item->catatan ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    const formatRupiah = (amount) => new Intl.NumberFormat('id-ID').format(amount);
    const quantityInputs = document.querySelectorAll('[data-jumlah-lembar]');
    const denominationRows = Array.from(document.querySelectorAll('[data-denomination-row]'));

    const setRowConfirmedState = (row, confirmed) => {
        const button = row.querySelector('[data-confirm-row]');
        if (!button) return;

        row.classList.toggle('bg-emerald-50/40', confirmed);
        button.classList.toggle('bg-emerald-600', confirmed);
        button.classList.toggle('border-emerald-600', confirmed);
        button.classList.toggle('text-white', confirmed);
        button.classList.toggle('bg-slate-50', !confirmed);
        button.classList.toggle('border-slate-200', !confirmed);
        button.classList.toggle('text-slate-600', !confirmed);
    };

    const confirmRow = (index, shouldFocusNext = true) => {
        const currentRow = denominationRows[index];
        if (!currentRow) return;

        const currentInput = currentRow.querySelector('[data-jumlah-lembar]');
        if (currentInput && currentInput.value === '') {
            currentInput.value = '0';
            updateCashTotal();
        }

        setRowConfirmedState(currentRow, true);

        const nextRow = denominationRows[index + 1];
        if (nextRow) {
            nextRow.classList.remove('hidden');
            if (shouldFocusNext) {
                const nextInput = nextRow.querySelector('[data-jumlah-lembar]');
                nextInput?.focus();
                nextInput?.select();
            }
        }
    };

    const syncProgressiveRowsFromValues = () => {
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
            setRowConfirmedState(row, isConfirmed);
        });
    };

    const updateCashTotal = () => {
        let total = 0n;

        quantityInputs.forEach((input) => {
            const count = BigInt(input.value || '0');
            const denomination = BigInt(input.dataset.pecahan);
            const subtotal = count * denomination;

            total += subtotal;
            document.querySelector(`[data-total-pecahan="${input.dataset.pecahan}"]`).textContent = formatRupiah(subtotal);
        });

        document.getElementById('saldo-fisik-preview').textContent = formatRupiah(total);
    };

    denominationRows.forEach((row, index) => {
        const input = row.querySelector('[data-jumlah-lembar]');
        const confirmBtn = row.querySelector('[data-confirm-row]');

        confirmBtn?.addEventListener('click', () => confirmRow(index, true));

        input?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                confirmRow(index, true);
            }
        });
    });

    quantityInputs.forEach((input) => input.addEventListener('input', updateCashTotal));
    updateCashTotal();
    syncProgressiveRowsFromValues();

    const employeeSelect = document.getElementById('id_user');
    if (employeeSelect) {
        employeeSelect.addEventListener('change', () => {
            const rincian = JSON.parse(employeeSelect.selectedOptions[0]?.dataset.rincian || '{}');

            quantityInputs.forEach((input) => {
                input.value = rincian[input.dataset.pecahan] ?? 0;
            });

            updateCashTotal();
            syncProgressiveRowsFromValues();
        });
    }
</script>
@endsection