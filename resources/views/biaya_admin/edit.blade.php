@extends('layouts.app')

@section('title', 'Ubah Biaya Admin')

@section('content')
    @include('biaya_admin._form', [
        'title' => 'Ubah Tarif Biaya Admin',
        'action' => route('biaya-admin.update', $biayaAdmin->id),
        'method' => 'PUT',
        'biayaAdmin' => $biayaAdmin,
    ])
@endsection
