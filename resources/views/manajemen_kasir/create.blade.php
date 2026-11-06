@extends('layouts.app')

@section('title', 'Tambah Kasir')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <h1 class="text-xl font-black text-slate-900">Tambah Akun Kasir</h1>
    @if($warungs->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Buat cabang terlebih dahulu sebelum menambahkan kasir.
            <a href="{{ route('cabang.create') }}" class="font-bold underline">Tambah cabang</a>
        </div>
    @else
        <form method="POST" action="{{ route('manajemen-kasir.store') }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5">
            @csrf
            @include('manajemen_kasir._form', ['submitLabel' => 'Simpan Kasir'])
        </form>
    @endif
    <a href="{{ route('manajemen-kasir.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Kembali ke daftar kasir</a>
</div>
@endsection
