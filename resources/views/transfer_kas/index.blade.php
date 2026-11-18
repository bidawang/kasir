@extends('layouts.app')

@section('title', 'Transfer Dana')

@section('content')
{{-- Inisialisasi State AlpineJS untuk Modal --}}
<div x-data="{ openDetail: false, selectedData: {} }" class="ak-page-wide space-y-3.5 pb-20">

    {{-- Header --}}
    <div class="ak-head">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-primary-600"></span>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Mutasi Antar Kas</span>
            </div>
            <h1 class="ak-title">Transfer Dana Antar Akun</h1>
            <p class="ak-desc sub-explanation">Riwayat pemindahan saldo antar kas fisik, e-wallet, dan rekening bank cabang.</p>
        </div>

        <a href="{{ route('transfer-kas.create') }}" class="ak-btn-primary !text-xs !py-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>Buat Transfer</span>
        </a>
    </div>

    {{-- Flash Messages --}}
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

    @if($errors->any())
        <div class="ak-alert-error">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('transfer-kas.index') }}" class="ak-card p-3 sm:p-3.5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div>
                <label class="ak-label">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="ak-input font-medium">
            </div>

            <div>
                <label class="ak-label">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="ak-input font-medium">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="ak-btn-primary flex-1 !text-xs !py-2">
                    Tampilkan
                </button>
                <a href="{{ route('transfer-kas.index') }}" class="ak-btn-secondary !text-xs !py-2">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Desktop Table --}}
    <div class="hidden md:block ak-table-wrap">
        <table class="ak-table text-xs">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Akun Pengirim</th>
                    <th class="text-center">&rarr;</th>
                    <th>Akun Penerima</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Biaya</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transferKas as $transfer)
                    @php
                        $payload = json_encode([
                            'id' => $transfer->id,
                            'kas_asal' => $transfer->kasAsal->jenis_kas ?? '-',
                            'status_asal' => ($transfer->kasAsal->status ?? null) === 'cash' ? 'Tunai' : (($transfer->kasAsal->status ?? null) === 'ewallet' ? 'E-wallet / Bank' : '-'),
                            'kas_tujuan' => $transfer->kasTujuan->jenis_kas ?? '-',
                            'status_tujuan' => ($transfer->kasTujuan->status ?? null) === 'cash' ? 'Tunai' : (($transfer->kasTujuan->status ?? null) === 'ewallet' ? 'E-wallet / Bank' : '-'),
                            'jumlah' => number_format($transfer->jumlah, 0, ',', '.'),
                            'biaya_transfer' => number_format($transfer->biaya_transfer ?? 0, 0, ',', '.'),
                            'total_keluar' => number_format($transfer->jumlah + ($transfer->biaya_transfer ?? 0), 0, ',', '.'),
                            'tanggal' => $transfer->created_at?->format('d M Y, H:i') ?? '-',
                            'keterangan' => $transfer->keterangan ?? '-',
                        ]);
                    @endphp
                    <tr class="hover:bg-slate-50 transition cursor-pointer"
                        @click="selectedData = {{ $payload }}; openDetail = true">

                        <td class="px-3.5 py-3 text-slate-500 whitespace-nowrap">
                            {{ $transfer->created_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-3.5 py-3">
                            <div class="font-bold text-slate-900">{{ $transfer->kasAsal->jenis_kas ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400 sub-explanation">{{ ($transfer->kasAsal->status ?? null) === 'cash' ? 'Tunai' : 'Digital/Bank' }}</div>
                        </td>
                        <td class="px-3.5 py-3 text-center">
                            <span class="text-primary-600 font-black text-sm">&rarr;</span>
                        </td>
                        <td class="px-3.5 py-3">
                            <div class="font-bold text-slate-900">{{ $transfer->kasTujuan->jenis_kas ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400 sub-explanation">{{ ($transfer->kasTujuan->status ?? null) === 'cash' ? 'Tunai' : 'Digital/Bank' }}</div>
                        </td>
                        <td class="px-3.5 py-3 text-right font-black text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($transfer->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-3.5 py-3 text-right text-slate-500 whitespace-nowrap">
                            Rp {{ number_format($transfer->biaya_transfer ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-3.5 py-3 text-slate-500 truncate max-w-xs sub-explanation">
                            {{ $transfer->keterangan ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs">
                            Belum ada riwayat transfer dana pada cabang ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-2">
        @forelse($transferKas as $transfer)
            @php
                $payload = json_encode([
                    'id' => $transfer->id,
                    'kas_asal' => $transfer->kasAsal->jenis_kas ?? '-',
                    'status_asal' => ($transfer->kasAsal->status ?? null) === 'cash' ? 'Tunai' : (($transfer->kasAsal->status ?? null) === 'ewallet' ? 'E-wallet / Bank' : '-'),
                    'kas_tujuan' => $transfer->kasTujuan->jenis_kas ?? '-',
                    'status_tujuan' => ($transfer->kasTujuan->status ?? null) === 'cash' ? 'Tunai' : (($transfer->kasTujuan->status ?? null) === 'ewallet' ? 'E-wallet / Bank' : '-'),
                    'jumlah' => number_format($transfer->jumlah, 0, ',', '.'),
                    'biaya_transfer' => number_format($transfer->biaya_transfer ?? 0, 0, ',', '.'),
                    'total_keluar' => number_format($transfer->jumlah + ($transfer->biaya_transfer ?? 0), 0, ',', '.'),
                    'tanggal' => $transfer->created_at?->format('d M Y, H:i') ?? '-',
                    'keterangan' => $transfer->keterangan ?? '-',
                ]);
            @endphp
            <div @click="selectedData = {{ $payload }}; openDetail = true"
                 class="ak-card p-3 active:scale-[0.99] transition cursor-pointer space-y-2">

                <div class="flex items-center justify-between text-xs">
                    <span class="text-[10px] text-slate-400">
                        {{ $transfer->created_at?->format('d/m/Y H:i') }}
                    </span>
                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                        Berhasil
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="text-[10px] text-slate-400">Dari</div>
                        <div class="font-bold text-slate-800 text-xs truncate">{{ $transfer->kasAsal->jenis_kas ?? '-' }}</div>
                    </div>
                    <div class="shrink-0 text-primary-600 font-black text-sm">&rarr;</div>
                    <div class="flex-1 min-w-0 text-right">
                        <div class="text-[10px] text-slate-400">Ke</div>
                        <div class="font-bold text-slate-800 text-xs truncate">{{ $transfer->kasTujuan->jenis_kas ?? '-' }}</div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-2 flex items-end justify-between">
                    <div>
                        <div class="text-[10px] text-slate-400">Jumlah Transfer</div>
                        <div class="font-black text-primary-700 text-xs sm:text-sm">
                            Rp {{ number_format($transfer->jumlah, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-slate-400">Biaya</div>
                        <div class="text-[11px] font-bold text-slate-600">
                            Rp {{ number_format($transfer->biaya_transfer ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="ak-card p-6 text-center space-y-1">
                <p class="font-bold text-slate-600 text-xs">Belum ada transfer dana</p>
                <p class="text-[11px] text-slate-400 sub-explanation">Belum ada pemindahan dana pada cabang ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($transferKas->hasPages())
        <div class="pt-2">
            {{ $transferKas->links() }}
        </div>
    @endif

    {{-- Modal Pop-up Detail Transfer --}}
    <div x-show="openDetail" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs">

        <div @click.away="openDetail = false"
             class="bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden w-full max-w-md my-auto">

            {{-- Header Modal --}}
            <div class="bg-primary-900 text-white p-3.5 sm:p-4 flex items-center justify-between">
                <div>
                    <div class="text-[10px] text-primary-200 font-bold uppercase">Detail Transfer Dana</div>
                    <div class="text-base font-black mt-0.5" x-text="'ID #' + selectedData.id"></div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-500/20 text-emerald-300">
                        Berhasil
                    </span>
                    <button @click="openDetail = false" class="text-white/70 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body Modal --}}
            <div class="p-3.5 sm:p-4 space-y-3">
                {{-- Detail Kas asal & tujuan --}}
                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Dari</div>
                        <div class="font-bold text-slate-900 text-xs" x-text="selectedData.kas_asal"></div>
                        <div class="text-[10px] text-slate-400" x-text="selectedData.status_asal"></div>
                    </div>

                    <div class="text-primary-600 font-black text-sm">&rarr;</div>

                    <div class="text-right">
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Ke</div>
                        <div class="font-bold text-slate-900 text-xs" x-text="selectedData.kas_tujuan"></div>
                        <div class="text-[10px] text-slate-400" x-text="selectedData.status_tujuan"></div>
                    </div>
                </div>

                {{-- Nominal --}}
                <div class="bg-primary-50 rounded-xl p-3 border border-primary-100">
                    <div class="text-[10px] font-black text-primary-800 uppercase tracking-wider">Jumlah Transfer</div>
                    <div class="text-xl font-black text-primary-700 mt-0.5" x-text="'Rp ' + selectedData.jumlah"></div>
                </div>

                {{-- Rincian Biaya & Tanggal --}}
                <div class="divide-y divide-slate-100 text-xs">
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Biaya Transfer</span>
                        <span class="font-bold text-slate-800" x-text="'Rp ' + selectedData.biaya_transfer"></span>
                    </div>

                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Total Keluar</span>
                        <span class="font-black text-slate-900" x-text="'Rp ' + selectedData.total_keluar"></span>
                    </div>

                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Tanggal Transaksi</span>
                        <span class="font-semibold text-slate-800" x-text="selectedData.tanggal"></span>
                    </div>

                    <div class="py-2">
                        <div class="text-slate-500 mb-0.5">Keterangan</div>
                        <div class="font-medium text-slate-700 sub-explanation" x-text="selectedData.keterangan"></div>
                    </div>
                </div>

                {{-- Tombol Tutup --}}
                <button @click="openDetail = false" class="ak-btn-secondary w-full !py-2 text-xs">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
