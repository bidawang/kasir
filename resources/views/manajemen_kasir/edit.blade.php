@extends('layouts.app')

@section('title', 'Ubah Akun Kasir')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <h1 class="text-2xl font-black text-slate-900">Ubah Akun Kasir</h1>
    <form method="POST" action="{{ route('manajemen-kasir.update', $manajemenKasir) }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
        @csrf
        @method('PUT')
        @include('manajemen_kasir._form', ['akun' => $manajemenKasir, 'submitLabel' => 'Simpan Perubahan'])
    </form>
    <a href="{{ route('manajemen-kasir.index') }}" class="text-sm sm:text-base font-semibold text-slate-600 hover:text-slate-900">Kembali ke daftar kasir</a>
</div>
@endsection
