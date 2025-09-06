@extends('layouts.app')

@section('title', 'Edit Kas')

@section('content')
<div class="container">
    <h1 class="h4 mb-4">Edit Kas</h1>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <form action="{{ route('kas.update', $kas->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id_warung" value="2" id="">

                <div class="mb-3">
                    <label for="jenis_kas" class="form-label">Jenis Kas</label>
                    <input type="text" name="jenis_kas" id="jenis_kas"
                        class="form-control @error('jenis_kas') is-invalid @enderror"
                        value="{{ old('jenis_kas', $kas->jenis_kas) }}" required>
                    @error('jenis_kas')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="saldo" class="form-label">Saldo</label>
                    <input type="number" name="saldo" id="saldo"
                        class="form-control @error('saldo') is-invalid @enderror"
                        value="{{ old('saldo', $kas->saldo) }}" required>
                    @error('saldo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('kas.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection