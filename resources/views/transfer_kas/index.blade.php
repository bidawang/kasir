@extends('layouts.app')

@section('content')

{{-- Inisialisasi State AlpineJS untuk Modal --}}
<div x-data="{ openDetail: false, selectedData: {} }" class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800">
                Transfer Dana
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Riwayat pemindahan dana antar kas
            </p>
        </div>

        <a href="{{ route('transfer-kas.create') }}"
           class="inline-flex items-center justify-center gap-2
                  bg-primary-600 hover:bg-primary-700
                  text-white px-4 py-2.5 rounded-xl
                  text-sm font-bold shadow-sm
                  active:scale-95 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10m0 0v10m0-10L7 17"/>
            </svg>
            Transfer Dana
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('transfer-kas.index') }}" class="bg-white rounded-2xl border border-slate-200 shadow-soft p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white rounded-xl py-2.5 text-sm font-bold transition">
                    Filter
                </button>
                <a href="{{ route('transfer-kas.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-2xl border border-slate-200 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500">Tanggal</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500">Dari</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-slate-500">→</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500">Ke</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">Jumlah</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">Biaya</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-500">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transferKas as $transfer)
                        @php
                            $payload = json_encode([
                                'id' => $transfer->id,
                                'kas_asal' => $transfer->kasAsal->jenis_kas ?? '-',
                                'status_asal' => ucfirst($transfer->kasAsal->status ?? '-'),
                                'kas_tujuan' => $transfer->kasTujuan->jenis_kas ?? '-',
                                'status_tujuan' => ucfirst($transfer->kasTujuan->status ?? '-'),
                                'jumlah' => number_format($transfer->jumlah, 0, ',', '.'),
                                'biaya_transfer' => number_format($transfer->biaya_transfer ?? 0, 0, ',', '.'),
                                'total_keluar' => number_format($transfer->jumlah + ($transfer->biaya_transfer ?? 0), 0, ',', '.'),
                                'tanggal' => $transfer->created_at?->format('d M Y, H:i') ?? '-',
                                'keterangan' => $transfer->keterangan ?? '-',
                            ]);
                        @endphp
                        <tr class="hover:bg-slate-50 transition cursor-pointer"
                            @click="selectedData = {{ $payload }}; openDetail = true">

                            <td class="px-5 py-4 text-xs text-slate-500">
                                {{ $transfer->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-800">{{ $transfer->kasAsal->jenis_kas ?? '-' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ ucfirst($transfer->kasAsal->status ?? '-') }}</div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-primary-500 font-bold text-lg">→</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-800">{{ $transfer->kasTujuan->jenis_kas ?? '-' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ ucfirst($transfer->kasTujuan->status ?? '-') }}</div>
                            </td>
                            <td class="px-5 py-4 text-right font-extrabold text-slate-800">
                                Rp {{ number_format($transfer->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right text-xs text-slate-500">
                                Rp {{ number_format($transfer->biaya_transfer ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500 truncate max-w-xs">
                                {{ $transfer->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="text-slate-300 mb-2">
                                    <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10m0 0v10m0-10L7 17"/>
                                    </svg>
                                </div>
                                <p class="font-bold text-slate-500">Belum ada transfer dana</p>
                                <p class="text-xs text-slate-400 mt-1">Transfer antar kas akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($transferKas as $transfer)
            @php
                $payload = json_encode([
                    'id' => $transfer->id,
                    'kas_asal' => $transfer->kasAsal->jenis_kas ?? '-',
                    'status_asal' => ucfirst($transfer->kasAsal->status ?? '-'),
                    'kas_tujuan' => $transfer->kasTujuan->jenis_kas ?? '-',
                    'status_tujuan' => ucfirst($transfer->kasTujuan->status ?? '-'),
                    'jumlah' => number_format($transfer->jumlah, 0, ',', '.'),
                    'biaya_transfer' => number_format($transfer->biaya_transfer ?? 0, 0, ',', '.'),
                    'total_keluar' => number_format($transfer->jumlah + ($transfer->biaya_transfer ?? 0), 0, ',', '.'),
                    'tanggal' => $transfer->created_at?->format('d M Y, H:i') ?? '-',
                    'keterangan' => $transfer->keterangan ?? '-',
                ]);
            @endphp
            <div @click="selectedData = {{ $payload }}; openDetail = true"
                 class="bg-white rounded-2xl border border-slate-200 shadow-soft p-4 active:scale-[0.99] transition cursor-pointer">

                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] text-slate-400">
                        {{ $transfer->created_at?->format('d/m/Y H:i') }}
                    </span>
                    <span class="text-[11px] font-bold bg-emerald-50 text-emerald-600 px-2 py-1 rounded-lg">
                        Berhasil
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-slate-400 mb-1">Dari</div>
                        <div class="font-bold text-slate-800 truncate">{{ $transfer->kasAsal->jenis_kas ?? '-' }}</div>
                    </div>
                    <div class="shrink-0 text-primary-500">→</div>
                    <div class="flex-1 min-w-0 text-right">
                        <div class="text-xs text-slate-400 mb-1">Ke</div>
                        <div class="font-bold text-slate-800 truncate">{{ $transfer->kasTujuan->jenis_kas ?? '-' }}</div>
                    </div>
                </div>

                <div class="border-t border-slate-100 mt-3 pt-3 flex items-end justify-between">
                    <div>
                        <div class="text-[10px] text-slate-400">Jumlah Transfer</div>
                        <div class="font-extrabold text-primary-600">
                            Rp {{ number_format($transfer->jumlah, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-slate-400">Biaya</div>
                        <div class="text-xs font-semibold text-slate-600">
                            Rp {{ number_format($transfer->biaya_transfer ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
                <p class="font-bold text-slate-500">Belum ada transfer dana</p>
                <p class="text-xs text-slate-400 mt-1">Belum ada pemindahan dana pada cabang ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($transferKas->hasPages())
        <div>
            {{ $transferKas->links() }}
        </div>
    @endif

    {{-- Modal Pop-up Detail Transfer --}}
    <div x-show="openDetail" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">

        <div @click.away="openDetail = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden w-full max-w-lg">

            {{-- Header Modal --}}
            <div class="bg-primary-900 text-white p-5 flex items-center justify-between">
                <div>
                    <div class="text-xs text-primary-200">Transfer Dana</div>
                    <div class="text-lg font-extrabold mt-1" x-text="'#' + selectedData.id"></div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-300 text-xs font-bold">
                        Berhasil
                    </span>
                    <button @click="openDetail = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body Modal --}}
            <div class="p-5 space-y-5">
                {{-- Detail Kas asal & tujuan --}}
                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                    <div>
                        <div class="text-[11px] text-slate-400 mb-1">Dari</div>
                        <div class="font-extrabold text-slate-800" x-text="selectedData.kas_asal"></div>
                        <div class="text-xs text-slate-400 mt-1" x-text="selectedData.status_asal"></div>
                    </div>

                    <div class="text-primary-600 text-xl font-bold">→</div>

                    <div class="text-right">
                        <div class="text-[11px] text-slate-400 mb-1">Ke</div>
                        <div class="font-extrabold text-slate-800" x-text="selectedData.kas_tujuan"></div>
                        <div class="text-xs text-slate-400 mt-1" x-text="selectedData.status_tujuan"></div>
                    </div>
                </div>

                {{-- Nominal --}}
                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="text-xs text-slate-400">Jumlah Transfer</div>
                    <div class="text-2xl font-extrabold text-primary-600 mt-1" x-text="'Rp ' + selectedData.jumlah"></div>
                </div>

                {{-- Rincian Biaya & Tanggal --}}
                <div class="divide-y divide-slate-100">
                    <div class="flex justify-between py-3 text-sm">
                        <span class="text-slate-500">Biaya Transfer</span>
                        <span class="font-bold text-slate-800" x-text="'Rp ' + selectedData.biaya_transfer"></span>
                    </div>

                    <div class="flex justify-between py-3 text-sm">
                        <span class="text-slate-500">Total Keluar</span>
                        <span class="font-extrabold text-slate-800" x-text="'Rp ' + selectedData.total_keluar"></span>
                    </div>

                    <div class="flex justify-between py-3 text-sm">
                        <span class="text-slate-500">Tanggal</span>
                        <span class="font-semibold text-slate-800" x-text="selectedData.tanggal"></span>
                    </div>

                    <div class="py-3 text-sm">
                        <div class="text-slate-500 mb-1">Keterangan</div>
                        <div class="font-semibold text-slate-800" x-text="selectedData.keterangan"></div>
                    </div>
                </div>

                {{-- Tombol Tutup --}}
                <button @click="openDetail = false" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-bold text-sm transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>

@endsection