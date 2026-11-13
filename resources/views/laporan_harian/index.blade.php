@extends('layouts.app')

@section('title', 'Laporan Kas Harian')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <header class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Laporan Kas Harian</h1>
            <p class="mt-1 text-sm sm:text-base text-slate-600">Bandingkan uang tunai fisik dengan saldo kas yang tercatat di sistem.</p>
        </div>
        <a href="{{ $isOwner ? route('laporan.index') : route('transaksi.menu') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50 min-h-12 flex items-center">{{ $isOwner ? 'Kembali ke laporan' : 'Kembali ke transaksi' }}</a>
    </header>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-base font-semibold text-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-base text-rose-800">
            <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="grid gap-4 md:grid-cols-[1fr_1.5fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-bold uppercase tracking-wide text-slate-500">Saldo kas sistem saat ini</p>
            <p class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900">Rp {{ number_format($saldoSistem, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-slate-500">Jumlah saldo seluruh kas tunai pada cabang aktif.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">{{ $isOwner ? 'Input atau koreksi laporan' : 'Laporan uang tunai hari ini' }}</h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $isOwner ? 'Owner dapat menyimpan ulang laporan karyawan untuk memperbaiki angka yang keliru.' : 'Laporan hanya dapat dikirim satu kali per hari. Hubungi owner bila ada kesalahan.' }}
            </p>

            @if ($isOwner || !$laporanHariIni)
                <form action="{{ route('laporan-harian.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    @if ($isOwner)
                        <div>
                            <label for="id_user" class="mb-2 block text-base font-semibold text-slate-700">Laporan untuk karyawan</label>
                            <select id="id_user" name="id_user" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base" required>
                                <option value="">Pilih karyawan</option>
                                @foreach ($karyawan as $employee)
                                    <option value="{{ $employee->id }}" data-rincian="{{ json_encode($laporanHarianKaryawan->get($employee->id)?->rincian_fisik ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) }}" @selected(old('id_user') == $employee->id)>{{ $employee->nama }}</option>
                                @endforeach
                            </select>
                            @if ($karyawan->isEmpty())
                                <p class="mt-2 text-sm text-amber-700">Belum ada karyawan kasir yang terhubung ke cabang ini.</p>
                            @endif
                        </div>
                    @endif
                    <div>
                        <p class="mb-3 text-base font-semibold text-slate-700">Rincian uang tunai fisik</p>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-base">
                                <thead class="bg-slate-50 text-left text-sm font-bold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Pecahan</th>
                                        <th class="px-4 py-3 text-center">Jumlah lembar</th>
                                        <th class="px-4 py-3 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach (\App\Models\DetailKas::DENOMINATIONS as $pecahan)
                                        <tr>
                                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700">Rp {{ number_format($pecahan, 0, ',', '.') }}</td>
                                            <td class="w-40 px-4 py-3">
                                                <input type="number" name="jumlah[{{ $pecahan }}]" min="0" step="1" required value="{{ old('jumlah.' . $pecahan, 0) }}" data-jumlah-lembar data-pecahan="{{ $pecahan }}" aria-label="Jumlah lembar pecahan Rp {{ number_format($pecahan, 0, ',', '.') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-center text-base font-bold text-slate-900">
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600 font-semibold">Rp <span data-total-pecahan="{{ $pecahan }}">0</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-slate-50">
                                    <tr>
                                        <th colspan="2" class="px-4 py-4 text-left text-base font-bold text-slate-800">Total uang tunai fisik</th>
                                        <th class="whitespace-nowrap px-4 py-4 text-right text-lg font-extrabold text-slate-900">Rp <span id="saldo-fisik-preview">0</span></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div>
                        <label for="catatan" class="mb-2 block text-base font-semibold text-slate-700">Catatan (opsional)</label>
                        <textarea id="catatan" name="catatan" rows="3" maxlength="2000" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base" placeholder="Contoh: selisih karena transaksi yang belum tercatat">{{ old('catatan') }}</textarea>
                    </div>
                    <button type="submit" @disabled($isOwner && $karyawan->isEmpty()) class="rounded-xl bg-primary-700 px-6 py-3.5 text-base font-bold text-white hover:bg-primary-800 disabled:cursor-not-allowed disabled:opacity-50 min-h-12">
                        {{ $isOwner ? 'Simpan / koreksi laporan' : 'Kirim laporan hari ini' }}
                    </button>
                </form>
            @else
                <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-base text-emerald-800">
                    Laporan hari ini sudah dikirim sebesar <strong>Rp {{ number_format($laporanHariIni->saldo_fisik, 0, ',', '.') }}</strong>.
                </div>
            @endif
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-bold text-slate-900">{{ $isOwner ? 'Riwayat laporan karyawan' : 'Riwayat laporan saya' }}</h2>
        </div>
        @if ($laporan->isEmpty())
            <div class="px-5 py-10 text-center text-slate-500">
                <svg class="mx-auto mb-3 h-12 w-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-base font-medium">Belum ada laporan kas harian.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-base">
                    <thead class="bg-slate-50 text-sm uppercase tracking-wide text-slate-500">
                        <tr>
                            @if ($isOwner)<th class="px-4 py-3.5 font-bold">Karyawan</th>@endif
                            <th class="px-4 py-3.5 font-bold">Tanggal</th>
                            <th class="px-4 py-3.5 font-bold">Uang fisik</th>
                            <th class="px-4 py-3.5 font-bold">Saldo sistem</th>
                            <th class="px-4 py-3.5 font-bold">Selisih</th>
                            <th class="px-4 py-3.5 font-bold">Dicatat oleh</th>
                            <th class="px-4 py-3.5 font-bold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($laporan as $item)
                            <tr>
                                @if ($isOwner)<td class="whitespace-nowrap px-4 py-3.5 font-bold text-slate-800">{{ $item->user->nama }}</td>@endif
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-700">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3.5 text-slate-700">
                                    <span class="whitespace-nowrap font-semibold">Rp {{ number_format($item->saldo_fisik, 0, ',', '.') }}</span>
                                    @if ($item->rincian_fisik)
                                        <p class="mt-1 min-w-48 text-sm text-slate-500">
                                            @foreach (\App\Models\DetailKas::DENOMINATIONS as $pecahan)
                                                @if (($item->rincian_fisik[$pecahan] ?? 0) > 0)
                                                    <span class="mr-1 inline-block whitespace-nowrap">Rp {{ number_format($pecahan, 0, ',', '.') }} × {{ number_format($item->rincian_fisik[$pecahan]) }}</span>
                                                @endif
                                            @endforeach
                                        </p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-700">Rp {{ number_format($item->saldo_sistem, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 font-bold {{ $item->selisih === 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $item->selisih === 0 ? 'Balance' : (($item->selisih > 0 ? '+' : '') . 'Rp ' . number_format($item->selisih, 0, ',', '.')) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-700">{{ $item->pelapor->nama }}</td>
                                <td class="max-w-xs px-4 py-3.5 text-slate-600">{{ $item->catatan ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

<script>
    const formatRupiah = (amount) => new Intl.NumberFormat('id-ID').format(amount);
    const quantityInputs = document.querySelectorAll('[data-jumlah-lembar]');

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

    quantityInputs.forEach((input) => input.addEventListener('input', updateCashTotal));
    updateCashTotal();

    const employeeSelect = document.getElementById('id_user');
    if (employeeSelect) {
        employeeSelect.addEventListener('change', () => {
            const rincian = JSON.parse(employeeSelect.selectedOptions[0]?.dataset.rincian || '{}');

            quantityInputs.forEach((input) => {
                input.value = rincian[input.dataset.pecahan] ?? 0;
            });

            updateCashTotal();
        });
    }
</script>
@endsection
