@extends('layouts.app')

@section('title', 'Detail Kas')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Detail Kas</h1>
            <p class="text-xs text-slate-500 font-medium">{{ $kas->jenis_kas ?? 'Kas ID ' . $kas->id }}</p>
        </div>
        <a href="{{ route('kas.index') }}"
           class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Alert Notification -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($detailKas))
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <form action="{{ route('kas.updateDetail', $kas->id) }}" method="POST">
                @csrf

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">Pecahan</th>
                                <th class="py-3.5 px-4 text-center">Jumlah Lembar</th>
                                <th class="py-3.5 px-4 text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                            @foreach($detailKas as $detail)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-semibold text-slate-700">
                                    Rp {{ number_format($detail->pecahan, 0, ',', '.') }}
                                    <input type="hidden" name="pecahan[]" value="{{ $detail->pecahan }}">
                                </td>
                                <td class="py-3.5 px-4 text-center w-36 sm:w-44">
                                    <input type="number" 
                                           class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-center text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200" 
                                           name="jumlah[]" 
                                           value="{{ $detail->jumlah }}" 
                                           min="0">
                                </td>
                                <td class="py-3.5 px-4 text-end font-bold text-slate-900">
                                    Rp {{ number_format($detail->pecahan * $detail->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Action Footer -->
                <div class="p-4 sm:p-5 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" 
                            class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm shadow-primary-500/25 transition-all duration-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span>Update Detail Kas</span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-10 text-center">
            <div class="flex flex-col items-center justify-center space-y-2">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                <p class="text-xs sm:text-sm font-medium text-slate-500">Tidak ada detail kas untuk kas ini.</p>
            </div>
        </div>
    @endif

</div>
@endsection