@extends('layouts.app')

@section('title', 'Edit Kasir')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <h1 class="text-xl font-black text-slate-900">Edit Akun Kasir</h1>
    <form method="POST" action="{{ route('manajemen-kasir.update', $manajemenKasir) }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5">
        @csrf
        @method('PUT')
        @include('manajemen_kasir._form', ['akun' => $manajemenKasir, 'submitLabel' => 'Simpan Perubahan'])
    </form>
    <a href="{{ route('manajemen-kasir.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Kembali ke daftar kasir</a>
</div>
@endsection
