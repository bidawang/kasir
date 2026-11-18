@extends('layouts.app')

@section('title', 'Detail Akun Kas')

@section('content')
<div class="ak-page max-w-2xl space-y-3.5 pb-20">

    <!-- Header Section -->
    <div class="ak-head !flex-row !items-center !justify-between">
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('kas.index') }}" class="ak-back" title="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="min-w-0">
                <h1 class="ak-title">Detail Pecahan Kas Fisik</h1>
                <p class="ak-desc sub-explanation">{{ $kas->jenis_kas ?? 'Kas ID ' . $kas->id }}</p>
            </div>
        </div>
        <a href="{{ route('kas.index') }}" class="ak-btn-secondary !text-xs !py-1.5 shrink-0">
            Kembali
        </a>
    </div>

    <!-- Alert Notification -->
    @if(session('success'))
        <div class="ak-alert-success">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($detailKas))
        <div class="ak-card p-0 overflow-hidden">
            <form action="{{ route('kas.updateDetail', $kas->id) }}" method="POST">
                @csrf

                <div class="overflow-x-auto no-scrollbar">
                    <table class="ak-table text-xs">
                        <thead>
                            <tr>
                                <th>Pecahan</th>
                                <th class="text-center">Jumlah Lembar</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($detailKas as $detail)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-3.5 py-2.5 font-bold text-slate-800">
                                    Rp {{ number_format($detail->pecahan, 0, ',', '.') }}
                                    <input type="hidden" name="pecahan[]" value="{{ $detail->pecahan }}">
                                </td>
                                <td class="px-3.5 py-2.5 text-center w-36 sm:w-44">
                                    <input type="number" 
                                           class="ak-input text-center font-bold !py-1" 
                                           name="jumlah[]" 
                                           value="{{ $detail->jumlah }}" 
                                           min="0">
                                </td>
                                <td class="px-3.5 py-2.5 text-right font-black text-primary-700">
                                    Rp {{ number_format($detail->pecahan * $detail->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Action Footer -->
                <div class="p-3 sm:p-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="ak-btn-primary !text-xs !py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span>Simpan Rincian Kas</span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="ak-card p-8 text-center space-y-1.5">
            <p class="text-xs sm:text-sm font-semibold text-slate-500">Tidak ada detail kas fisik untuk akun ini.</p>
        </div>
    @endif

</div>
@endsection
