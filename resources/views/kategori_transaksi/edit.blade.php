@extends('layouts.app')

@section('title', 'Ubah Kategori Transaksi')

@section('content')
    @include('kategori_transaksi._form', [
        'title' => 'Ubah Kategori Transaksi',
        'action' => route('kategori-transaksi.update', $kategori->id),
        'method' => 'PUT',
        'kategori' => $kategori,
    ])
@endsection
