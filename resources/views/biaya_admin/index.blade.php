@extends('layouts.app')

@section('title', 'Biaya Admin')

@section('content')
<div class="max-w-3xl mx-auto pb-12 space-y-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Biaya Admin</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Atur tarif berdasarkan jenis dan rentang nominal</p>
        </div>
        <a href="{{ route('biaya-admin.create') }}" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Biaya
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-2xl text-xs font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-2xl text-xs font-semibold">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        @if($biayaAdmin->isEmpty())
            <div class="p-8 text-center">
                <div class="w-11 h-11 mx-auto rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m4-9H9.5a2.5 2.5 0 000 5h5a2.5 2.5 0 010 5H8"/><circle cx="12" cy="12" r="9"/></svg>
                </div>
                <p class="mt-3 text-sm font-bold text-slate-800">Belum ada tarif biaya admin</p>
                <p class="mt-1 text-xs text-slate-500">Tambahkan rentang harga dan biaya admin yang berlaku untuk cabang ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-[10px] uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3 font-bold">Rentang Nominal</th>
                            <th class="px-4 py-3 font-bold">Jenis / Bank</th>
                            <th class="px-4 py-3 font-bold">Biaya Admin</th>
                            <th class="px-4 py-3 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($biayaAdmin as $biaya)
                            <tr class="text-xs">
                                <td class="px-4 py-3 font-bold text-slate-800">Rp {{ number_format($biaya->range_awal, 0, ',', '.') }} – Rp {{ number_format($biaya->range_akhir, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ ucfirst($biaya->jenis) }}{{ $biaya->jenis === 'bank' ? ' / ' . ($biaya->kas?->jenis_kas ?? 'Semua bank') : '' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-700">Rp {{ number_format($biaya->biaya_admin, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('biaya-admin.edit', $biaya->id) }}" class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 font-bold">Edit</a>
                                        <form action="{{ route('biaya-admin.destroy', $biaya->id) }}" method="POST" onsubmit="return confirm('Hapus tarif biaya admin ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($biayaAdmin->hasPages())
                <div class="p-4 border-t border-slate-100">{{ $biayaAdmin->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
