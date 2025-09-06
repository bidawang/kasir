@extends('layouts.app')

@section('title', 'Kas')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4">Daftar Kas</h1>
        <a href="{{ route('kas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm rounded-3">
        <div class="card-body p-0">
            <table class="table table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Jenis Kas</th>
                        <th>Saldo</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kas as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->jenis_kas }}</td>
                            <td>Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <a href="{{ route('kas.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('kas.destroy', $item->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus kas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada data kas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
