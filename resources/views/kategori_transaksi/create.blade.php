@extends('layouts.app')

@section('title', 'Tambah Kategori Transaksi')

@section('content')
    @include('kategori_transaksi._form', [
        'title' => 'Tambah Kategori Transaksi',
        'action' => route('kategori-transaksi.store'),
        'method' => 'POST',
        'kategori' => null,
    ])
@endsection
