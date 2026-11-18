@extends('layouts.app')

@section('title', 'Tambah Cabang')

@section('content')
<div class="ak-page max-w-2xl">
    <div class="ak-head !flex-row !items-center !justify-start">
        <a href="{{ route('cabang.index') }}" class="ak-back" title="Kembali">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </a>
        <div class="min-w-0">
            <h1 class="ak-title">Tambah Cabang</h1>
            <p class="ak-desc sub-explanation">Buat outlet baru beserta kas tunai & rekening bank utama secara otomatis.</p>
        </div>
    </div>

    <form action="{{ route('cabang.store') }}" method="POST" class="space-y-3">
        @csrf
        @include('cabang._form', ['warung' => null])
    </form>
</div>
@endsection
