@extends('layouts.app')

@section('title', 'Ubah Akun Kasir')

@section('content')
<div class="ak-page max-w-2xl space-y-3.5 pb-20">
    <div class="ak-head !flex-row !items-center !justify-start">
        <a href="{{ route('manajemen-kasir.index') }}" class="ak-back" title="Kembali">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </a>
        <div class="min-w-0">
            <h1 class="ak-title">Ubah Akun Kasir</h1>
            <p class="ak-desc sub-explanation">Perbarui informasi profil kasir, penugasan cabang, atau reset sandi.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('manajemen-kasir.update', $manajemenKasir) }}">
        @csrf
        @method('PUT')
        @include('manajemen_kasir._form', ['akun' => $manajemenKasir, 'submitLabel' => 'Simpan Perubahan'])
    </form>
</div>
@endsection
