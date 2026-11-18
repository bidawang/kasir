@extends('layouts.app')

@section('title', 'Ubah Cabang')

@section('content')
<div class="ak-page max-w-2xl">
    <div class="ak-head !flex-row !items-center !justify-start">
        <a href="{{ route('cabang.index') }}" class="ak-back" title="Kembali">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </a>
        <div class="min-w-0">
            <h1 class="ak-title truncate">Ubah Cabang: {{ $warung->nama_warung }}</h1>
            <p class="ak-desc sub-explanation">Perbarui profil cabang dan rekening bank utama operasional.</p>
        </div>
    </div>

    <form action="{{ route('cabang.update', $warung->id) }}" method="POST" class="space-y-3">
        @csrf
        @method('PUT')
        @include('cabang._form', ['warung' => $warung])
    </form>
</div>
@endsection
