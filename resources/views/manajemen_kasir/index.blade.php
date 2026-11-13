@extends('layouts.app')

@section('title', 'Manajemen Kasir')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Manajemen Kasir</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola akun kasir dan cabang tempat mereka bekerja.</p>
        </div>
        <a href="{{ route('manajemen-kasir.create') }}" class="rounded-xl bg-primary-600 px-5 py-3 text-sm font-bold text-white hover:bg-primary-700 min-h-12 flex items-center transition">
            Tambah Kasir
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm text-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm text-rose-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @if($kasir->isEmpty())
            <div class="p-8 text-center space-y-2">
                <p class="font-bold text-slate-800 text-base">Belum ada akun kasir</p>
                <p class="text-sm text-slate-500 max-w-xs mx-auto">Tambahkan akun kasir beserta cabang tempatnya bekerja.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-sm uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3.5 font-bold">Nama Lengkap</th>
                            <th class="px-4 py-3.5 font-bold">Email</th>
                            <th class="px-4 py-3.5 font-bold">Cabang</th>
                            <th class="px-4 py-3.5 font-bold">Status Akun</th>
                            <th class="px-4 py-3.5 text-right font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kasir as $akun)
                            <tr>
                                <td class="px-4 py-3.5 font-bold text-slate-800">{{ $akun->nama }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $akun->email }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $akun->warungs->pluck('nama_warung')->join(', ') ?: '-' }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="rounded-full px-3 py-1.5 text-sm font-bold {{ $akun->status === 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $akun->status === 'aktif' ? 'Aktif' : 'Tidak aktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('manajemen-kasir.edit', $akun) }}" class="rounded-lg bg-amber-50 px-3.5 py-2 font-bold text-amber-700 hover:bg-amber-100 text-sm">Ubah</a>
                                        <form method="POST" action="{{ route('manajemen-kasir.destroy', $akun) }}" onsubmit="return confirm('Hapus akun kasir ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg bg-rose-50 px-3.5 py-2 font-bold text-rose-700 hover:bg-rose-100 text-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
