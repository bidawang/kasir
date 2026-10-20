@extends('layouts.app')

@section('title', 'Pemantauan Transaksi Customer')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ expanded: null }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Riwayat Transaksi</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">Transaksi customer dan penjualan item di cabang aktif</p>
        </div>
        <a href="{{ route('transaksi.penjualan-item.create') }}" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Jual Item
        </a>
    </div>

    @if(auth()->user()->role === 'owner')
        <!-- Metric Cards Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-4 text-white shadow-lg shadow-emerald-500/10">
                <div class="flex items-center justify-between opacity-80 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Total Keuntungan</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-6h6"/></svg>
                </div>
                <div class="text-2xl font-black tracking-tight">
                    Rp {{ number_format($summary->total_keuntungan ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-emerald-100 mt-1 font-medium">Hasil Biaya Admin</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200/85 shadow-sm">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Setor</span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold text-slate-900">
                    Rp {{ number_format($summary->total_setor ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Uang Masuk</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200/85 shadow-sm">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Tarik</span>
                    <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold text-slate-900">
                    Rp {{ number_format($summary->total_tarik ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Uang Keluar</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200/85 shadow-sm">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Transaksi</span>
                    <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold text-slate-900">
                    {{ number_format($summary->total_transaksi ?? 0, 0, ',', '.') }} <span class="text-xs text-slate-400 font-normal">Trx</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Total Aktivitas</div>
            </div>
        </div>

        <!-- Filter Form & Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Filter Tanggal -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/85 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Filter Periode</h3>
                <form id="filterForm" action="{{ route('transaksi.index') }}" method="GET" class="space-y-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-600">Dari Tanggal</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-600">Sampai Tanggal</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                    </div>
                    <div class="pt-2 flex gap-2">
                        <button class="flex-1 bg-slate-900 hover:bg-slate-800 text-white py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm" type="submit">
                            Terapkan Filter
                        </button>
                        @if(request()->hasAny(['start_date', 'end_date']))
                            <a href="{{ route('transaksi.index') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Chart Canvas -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-slate-200/85 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Grafik Perputaran Kas & Profit</h3>
                    <span class="text-xs text-slate-400">Klik titik grafik untuk memfilter data harian</span>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="brilinkChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- Section List Transaksi -->
    <div id="transaksi-section" class="space-y-4">
        <!-- Header Info Filter Tanggal -->
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Rincian Transaksi</h3>
                @if(request('start_date') && request('start_date') == request('end_date'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                        <span>Tanggal: <strong>{{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y') }}</strong></span>
                        <a href="{{ route('transaksi.index') }}" class="hover:bg-purple-200 text-purple-800 rounded-full w-4 h-4 inline-flex items-center justify-center text-xs ml-1 transition-colors">&times;</a>
                    </span>
                @endif
            </div>
            @if(request()->hasAny(['start_date', 'end_date']))
                <a href="{{ route('transaksi.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline transition-colors">
                    Tampilkan Semua Transaksi
                </a>
            @endif
        </div>

        <!-- List Data Transaksi -->
        <div class="space-y-3">
            @forelse($transaksi as $item)
                <div class="bg-white rounded-2xl border border-slate-200/85 shadow-sm overflow-hidden transition-all duration-200 hover:border-slate-300">
                    <div @click="expanded = expanded === {{ $item->id }} ? null : {{ $item->id }}"
                         class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition-colors select-none">
                        <div class="space-y-1 pr-3">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs sm:text-sm text-slate-900 tracking-tight">
                                    {{ $item->keterangan ?: 'Tanpa Keterangan' }}
                                </span>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $item->kategori === 'penjualan_item' ? 'Penjualan Item' : 'Customer' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-[11px] sm:text-xs text-slate-500 font-medium">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold">
                                    {{ $item->kas->jenis_kas ?? 'Tanpa Kas' }}
                                </span>
                                <span>•</span>
                                <span>{{ $item->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <div class="text-right">
                                <span class="block text-xs sm:text-sm font-extrabold {{ $item->jenis == 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $item->jenis == 'masuk' ? '+' : '-' }} Rp {{ number_format($item->total, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    {{ $item->kategori === 'penjualan_item' ? 'Penjualan' : ($item->jenis == 'masuk' ? 'Setor Tunai' : 'Tarik Tunai') }}
                                </span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="expanded === {{ $item->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                    </div>

                    <!-- Accordion Body -->
                    <div x-show="expanded === {{ $item->id }}" x-transition x-cloak class="px-4 pb-4 sm:px-5 sm:pb-5 pt-0 border-t border-slate-100 space-y-4">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 text-xs">
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Jenis Aktivitas</span>
                                <span class="font-bold text-slate-700">{{ $item->kategori === 'penjualan_item' ? 'Penjualan Item' : ($item->jenis == 'masuk' ? 'Setor Tunai' : 'Tarik Tunai') }}</span>
                            </div>
                            <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-700">Keuntungan (Admin)</span>
                                <span class="font-extrabold text-emerald-700">Rp {{ number_format($item->biaya_admin ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 col-span-2">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Warung & Kas</span>
                                <span class="font-bold text-slate-700 truncate block">{{ $item->kas->jenis_kas ?? '-' }} <span class="font-normal text-slate-500">({{ $item->kas->warung->nama ?? '-' }})</span></span>
                            </div>
                        </div>

                        @if($item->kategori === 'penjualan_item')
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Harga Item</span>
                                    <span class="font-bold text-slate-700">Rp {{ number_format($item->harga_item ?? 0, 0, ',', '.') }}</span>
                                </div>
                                @if($item->kasSumber)
                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Wallet Sumber</span>
                                        <span class="font-bold text-slate-700">{{ $item->kasSumber->jenis_kas }}</span>
                                    </div>
                                @endif
                                <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-700">Total Dibayar</span>
                                    <span class="font-extrabold text-emerald-700">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @else
                            <!-- Detail Pecahan -->
                            <div class="space-y-2">
                                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-500">Detail Pecahan Uang:</h6>
                                @if($item->detailTransaksi && $item->detailTransaksi->count())
                                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/80 space-y-2">
                                        @foreach($item->detailTransaksi as $detail)
                                            <div class="flex items-center justify-between text-xs font-medium text-slate-700">
                                                <span>Rp {{ number_format($detail->pecahan, 0, ',', '.') }} <span class="text-slate-400">×</span> {{ $detail->jumlah }} lembar</span>
                                                <span class="font-bold text-slate-900">= Rp {{ number_format($detail->pecahan * $detail->jumlah, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">Tidak ada detail pecahan.</p>
                                @endif
                            </div>
                        @endif {{-- ← @endif yang sebelumnya hilang --}}
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center border border-slate-200/85 space-y-2">
                    <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">Belum ada data transaksi customer.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination View -->
    @if ($transaksi->hasPages())
        <div class="bg-white rounded-2xl p-4 border border-slate-200/85 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600">
            <div>
                Menampilkan <span class="font-bold text-slate-900">{{ $transaksi->firstItem() }}</span> - <span class="font-bold text-slate-900">{{ $transaksi->lastItem() }}</span> dari <span class="font-bold text-slate-900">{{ $transaksi->total() }}</span> data
            </div>
            <div class="flex items-center gap-1">
                {{-- Previous Link --}}
                @if ($transaksi->onFirstPage())
                    <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed font-medium">« Prev</span>
                @else
                    <a href="{{ $transaksi->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-all">« Prev</a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($transaksi->getUrlRange(1, $transaksi->lastPage()) as $page => $url)
                    @if ($page == $transaksi->currentPage())
                        <span class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-all">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Link --}}
                @if ($transaksi->hasMorePages())
                    <a href="{{ $transaksi->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-all">Next »</a>
                @else
                    <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed font-medium">Next »</span>
                @endif
            </div>
        </div>
    @endif

</div>

@if(auth()->user()->role === 'owner')
    <!-- Chart Script (hanya dimuat untuk owner) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('brilinkChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const rawDates = @json($chartRawDates ?? []);
            const displayLabels = @json($chartLabels ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: displayLabels,
                    datasets: [
                        {
                            label: 'Keuntungan',
                            data: @json($chartProfit ?? []),
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Setor',
                            data: @json($chartSetor ?? []),
                            borderColor: '#2563eb',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Tarik',
                            data: @json($chartTarik ?? []),
                            borderColor: '#e11d48',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onHover: (event, chartElement) => {
                        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                    },
                    onClick: (event, activeElements) => {
                        if (activeElements.length > 0) {
                            const index = activeElements[0].index;
                            const selectedDate = rawDates[index];

                            document.getElementById('start_date').value = selectedDate;
                            document.getElementById('end_date').value = selectedDate;
                            document.getElementById('filterForm').submit();
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            ticks: { callback: v => 'Rp ' + (v / 1000).toLocaleString('id-ID') + 'k' }
                        },
                        y1: {
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') }
                        }
                    }
                }
            });
        });
    </script>
@endif
@endsection