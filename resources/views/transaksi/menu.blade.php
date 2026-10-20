@extends('layouts.app')

@section('title', 'Menu Transaksi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <header class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Transaksi</h1>
        <p class="text-sm text-slate-600">Pilih aktivitas yang ingin dilakukan.</p>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('transaksi.setor') }}" class="group min-h-36 flex items-center gap-4 rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 text-2xl font-black">↓</span>
            <span><span class="block text-lg font-bold text-slate-900">Setor Tunai</span><span class="mt-1 block text-sm text-slate-600">Terima uang dan kirim saldo ke wallet.</span></span>
        </a>

        <a href="{{ route('transaksi.tarik') }}" class="group min-h-36 flex items-center gap-4 rounded-2xl border border-rose-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-rose-500">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-800 text-2xl font-black">↑</span>
            <span><span class="block text-lg font-bold text-slate-900">Tarik Tunai</span><span class="mt-1 block text-sm text-slate-600">Berikan uang tunai dari saldo wallet.</span></span>
        </a>

        <a href="{{ route('transaksi.kas.masuk') }}" class="group min-h-36 flex items-center gap-4 rounded-2xl border border-sky-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-800 text-2xl font-black">＋</span>
            <span><span class="block text-lg font-bold text-slate-900">Kas Masuk</span><span class="mt-1 block text-sm text-slate-600">Catat tambahan saldo kas.</span></span>
        </a>

        <a href="{{ route('transaksi.kas.keluar') }}" class="group min-h-36 flex items-center gap-4 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800 text-2xl font-black">−</span>
            <span><span class="block text-lg font-bold text-slate-900">Kas Keluar</span><span class="mt-1 block text-sm text-slate-600">Catat pengeluaran dari kas.</span></span>
        </a>

        <a href="{{ route('transaksi.penjualan-item.create') }}" class="group min-h-36 flex items-center gap-4 rounded-2xl border border-primary-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-800 text-2xl font-black">▣</span>
            <span><span class="block text-lg font-bold text-slate-900">Jual Item</span><span class="mt-1 block text-sm text-slate-600">Jual item fisik, virtual, atau bank.</span></span>
        </a>
    </div>

    <a href="{{ route('transaksi.index') }}" class="inline-flex min-h-11 items-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
        Lihat riwayat transaksi
    </a>
</div>
@endsection
