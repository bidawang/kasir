@extends('layouts.app')

@section('title', 'Manajemen Kasir')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-slate-900">Manajemen Kasir</h1>
            <p class="text-sm text-slate-500">Kelola akun staf dan cabang tempat mereka bertugas.</p>
        </div>
        <a href="{{ route('manajemen-kasir.create') }}" class="rounded-xl bg-primary-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-primary-700">
            Tambah Kasir
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @if($kasir->isEmpty())
            <div class="p-8 text-center">
                <p class="font-bold text-slate-800">Belum ada akun kasir</p>
                <p class="mt-1 text-sm text-slate-500">Buat akun kasir dan tentukan cabang penugasannya.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Cabang</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kasir as $akun)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $akun->nama }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $akun->email }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $akun->warungs->pluck('nama_warung')->join(', ') ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $akun->status === 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($akun->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('manajemen-kasir.edit', $akun) }}" class="rounded-lg bg-amber-50 px-3 py-1.5 font-bold text-amber-700 hover:bg-amber-100">Edit</a>
                                        <form method="POST" action="{{ route('manajemen-kasir.destroy', $akun) }}" onsubmit="return confirm('Hapus akun kasir ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg bg-rose-50 px-3 py-1.5 font-bold text-rose-700 hover:bg-rose-100">Hapus</button>
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
