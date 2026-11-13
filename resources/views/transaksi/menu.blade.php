@extends('layouts.app')

@section('title', 'Menu Transaksi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <header class="space-y-1">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Menu Transaksi</h1>
        <p class="text-base text-slate-600">Pilih aktivitas yang ingin dilakukan.</p>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('transaksi.setor') }}" class="group flex items-center gap-4 rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 min-h-[5.5rem]">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
            </span>
            <span><span class="block text-lg font-bold text-slate-900">Setor Tunai</span><span class="mt-1 block text-base text-slate-600">Terima uang tunai dan kirim ke e-wallet/bank.</span></span>
        </a>

        <a href="{{ route('transaksi.tarik') }}" class="group flex items-center gap-4 rounded-2xl border border-rose-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-rose-500 min-h-[5.5rem]">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-800">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
            </span>
            <span><span class="block text-lg font-bold text-slate-900">Tarik Tunai</span><span class="mt-1 block text-base text-slate-600">Berikan uang tunai dari saldo e-wallet/bank.</span></span>
        </a>

        <a href="{{ route('transaksi.kas.masuk') }}" class="group flex items-center gap-4 rounded-2xl border border-sky-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 min-h-[5.5rem]">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-sky-100 text-sky-800">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
            </span>
            <span><span class="block text-lg font-bold text-slate-900">Kas Masuk</span><span class="mt-1 block text-base text-slate-600">Catat tambahan saldo kas.</span></span>
        </a>

        <a href="{{ route('transaksi.kas.keluar') }}" class="group flex items-center gap-4 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-amber-500 min-h-[5.5rem]">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-800">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/></svg>
            </span>
            <span><span class="block text-lg font-bold text-slate-900">Kas Keluar</span><span class="mt-1 block text-base text-slate-600">Catat pengeluaran dari kas.</span></span>
        </a>

        <a href="{{ route('transaksi.penjualan-item.create') }}" class="group sm:col-span-2 flex items-center gap-4 rounded-2xl border border-primary-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 min-h-[5.5rem]">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-primary-100 text-primary-800">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
            </span>
            <span><span class="block text-lg font-bold text-slate-900">Jual Barang atau Layanan</span><span class="mt-1 block text-base text-slate-600">Jual barang fisik, layanan virtual, atau layanan bank.</span></span>
        </a>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('transaksi.index') }}" class="inline-flex min-h-12 items-center rounded-xl border border-slate-300 bg-white px-5 text-base font-semibold text-slate-700 hover:bg-slate-50">
            Lihat riwayat transaksi
        </a>
        <a href="{{ route('laporan-harian.index') }}" class="inline-flex min-h-12 items-center rounded-xl border border-emerald-300 bg-emerald-50 px-5 text-base font-semibold text-emerald-800 hover:bg-emerald-100">
            Laporan kas harian
        </a>
    </div>
</div>
@endsection
