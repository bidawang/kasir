@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Laporan Keuangan</h1>
            <p class="text-xs text-slate-500 font-medium">Analisis transaksi dan rekapitulasi saldo</p>
        </div>
    </div>

    <!-- Filter Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/85 p-4 sm:p-5">
        <form action="{{ route('laporan.index') }}" method="GET" class="space-y-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Start Date -->
                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" 
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                           name="start_date" 
                           value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                </div>
                <!-- End Date -->
                <div>
                    <label class="block text-[11px] sm:text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" 
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                           name="end_date" 
                           value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-1">
                <button type="submit" 
                        class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold shadow-sm shadow-primary-500/25 transition-all duration-200 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.658 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                    <span>Filter Laporan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Chart Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/85 p-4 sm:p-5 space-y-3">
        <h3 class="font-bold text-slate-800 text-xs sm:text-sm tracking-tight flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary-600"></span>
            Laporan Keuangan Bulanan
        </h3>
        <div class="relative w-full h-64 sm:h-72">
            <canvas id="myChart"></canvas>
        </div>
    </div>

    <!-- Summary Total Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/85 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-bold text-slate-800 text-xs sm:text-sm tracking-tight">Ringkasan Total</h3>
        </div>
        <div class="divide-y divide-slate-100 text-xs sm:text-sm">
            <!-- Total Pemasukan -->
            <div class="px-4 sm:px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                <div class="flex items-center gap-2 text-slate-600 font-medium">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </div>
                    <span>Total Pemasukan</span>
                </div>
                <div class="font-bold text-emerald-600">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="px-4 sm:px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                <div class="flex items-center gap-2 text-slate-600 font-medium">
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/></svg>
                    </div>
                    <span>Total Pengeluaran</span>
                </div>
                <div class="font-bold text-rose-600">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </div>
            </div>

            <!-- Saldo Akhir -->
            <div class="px-4 sm:px-5 py-4 flex items-center justify-between bg-slate-50/40">
                <div class="flex items-center gap-2 text-slate-800 font-bold">
                    <div class="w-7 h-7 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center font-bold">
                        💰
                    </div>
                    <span>Saldo Akhir</span>
                </div>
                <div class="font-extrabold text-slate-900 text-sm sm:text-base">
                    Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart');
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
                backgroundColor: 'rgba(11, 99, 206, 0.75)',
                borderColor: '#0b63ce',
                borderWidth: 1,
                borderRadius: 6
            }, {
                label: 'Pengeluaran',
                data: pengeluaranData,
                backgroundColor: 'rgba(244, 63, 94, 0.75)',
                borderColor: '#f43f5e',
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
                            weight: 500
                        },
                        color: '#64748b'
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 10
                        },
                        color: '#64748b'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(226, 232, 240, 0.6)'
                    },
                    ticks: {
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 10
                        },
                        color: '#64748b'
                    }
                }
            }
        }
    });
</script>
@endsection