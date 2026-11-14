@extends('layouts.app')

@section('title', 'Tambah Biaya Admin')

@section('content')
    @include('biaya_admin._form', [
        'title' => 'Tambah Tarif Biaya Admin',
        'action' => route('biaya-admin.store'),
        'method' => 'POST',
        'biayaAdmin' => null,
    ])
@endsection
