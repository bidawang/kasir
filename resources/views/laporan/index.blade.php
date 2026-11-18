@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
@php
    $labels = $labels ?? [];
    $pemasukanData = $pemasukanData ?? [];
    $pengeluaranData = $pengeluaranData ?? [];
    $totalPemasukan = $totalPemasukan ?? 0;
    $totalPengeluaran = $totalPengeluaran ?? 0;
    $saldoAkhir = $saldoAkhir ?? 0;
@endphp
<div class="ak-page max-w-2xl space-y-3.5 pb-20">

    <!-- Header Section -->
    <div class="ak-head">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-primary-600"></span>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Ringkasan Finansial</span>
            </div>
            <h1 class="ak-title">Laporan Keuangan</h1>
            <p class="ak-desc sub-explanation">Ringkasan pemasukan, pengeluaran kas operasional, dan saldo akhir usaha.</p>
        </div>
        <a href="{{ route('laporan-harian.index') }}" class="ak-btn-secondary !text-xs !py-1.5 shrink-0">
            Kas Harian
        </a>
    </div>

    <!-- Filter Form Card -->
    <div class="ak-card p-3 sm:p-4">
        <form action="{{ route('laporan.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <!-- Start Date -->
                <div>
                    <label class="ak-label">Dari Tanggal</label>
                    <input type="date"
                           class="ak-input font-medium"
                           name="start_date"
                           value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                </div>
                <!-- End Date -->
                <div>
                    <label class="ak-label">Sampai Tanggal</label>
                    <input type="date"
                           class="ak-input font-medium"
                           name="end_date"
                           value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-0.5">
                <button type="submit" class="ak-btn-primary w-full sm:w-auto !text-xs !py-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.658 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                    <span>Tampilkan Laporan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Chart Card -->
    <div class="ak-card p-3 sm:p-4 space-y-2.5">
        <h3 class="font-black text-slate-800 text-xs uppercase tracking-wider flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary-600"></span>
            Grafik Keuangan Bulanan
        </h3>
        <div class="relative w-full h-56 sm:h-64">
            <canvas id="myChart"></canvas>
        </div>
    </div>

    <!-- Summary Total Card -->
    <div class="ak-card p-0 overflow-hidden">
        <div class="p-3 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-black text-slate-800 text-xs uppercase tracking-wider">Ringkasan Total Periode Ini</h3>
        </div>
        <div class="divide-y divide-slate-100 text-xs">
            <!-- Total Pemasukan -->
            <div class="px-3.5 py-3 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                <div class="flex items-center gap-2.5 text-slate-600 font-semibold">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </div>
                    <span>Total Pemasukan</span>
                </div>
                <div class="font-black text-emerald-600 text-sm sm:text-base">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="px-3.5 py-3 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                <div class="flex items-center gap-2.5 text-slate-600 font-semibold">
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                    </div>
                    <span>Total Pengeluaran</span>
                </div>
                <div class="font-black text-rose-600 text-sm sm:text-base">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </div>
            </div>

            <!-- Saldo Akhir -->
            <div class="px-3.5 py-3.5 flex items-center justify-between bg-primary-50/40">
                <div class="flex items-center gap-2.5 text-primary-950 font-bold">
                    <div class="w-7 h-7 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/></svg>
                    </div>
                    <span>Saldo Akhir</span>
                </div>
                <div class="font-black text-primary-800 text-base sm:text-lg">
                    Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const ctx = document.getElementById('myChart');
    if (ctx) {
        const labels = @json($labels);
        const pemasukanData = @json($pemasukanData);
        const pengeluaranData = @json($pengeluaranData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pemasukan',
                    data: pemasukanData,
                    backgroundColor: 'rgba(0, 82, 156, 0.85)',
                    borderColor: '#00529C',
                    borderWidth: 1,
                    borderRadius: 6
                }, {
                    label: 'Pengeluaran',
                    data: pengeluaranData,
                    backgroundColor: 'rgba(225, 29, 72, 0.85)',
                    borderColor: '#e11d48',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            borderRadius: 4,
                            font: {
                                family: 'Plus Jakarta Sans',
                                size: 11,
                                weight: 600
                            },
                            color: '#64748b',
                            padding: 12
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226, 232, 240, 0.6)' },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            color: '#64748b',
                            callback: v => 'Rp ' + (v / 1000).toLocaleString('id-ID') + 'k'
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
