@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <h1 class="mb-4">Riwayat Transaksi</h1>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('transaksi.index') }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-5 mb-2">
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                    </div>
                </div>
            </form>
            
            <ul class="list-group mb-3">
                @forelse($transaksi as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">{{ $item->keterangan }}</div>
                        <small class="text-secondary">{{ $item->metode_pembayaran }} - {{ $item->created_at->translatedFormat('d F Y') }}</small>
                    </div>
                    <span class="text-{{ $item->jenis == 'masuk' ? 'success' : 'danger' }} fw-bold">
                        {{ $item->jenis == 'masuk' ? '+' : '-' }} Rp {{ number_format($item->total, 0, ',', '.') }}
                    </span>
                </li>
                @empty
                <li class="list-group-item text-center text-secondary">Belum ada data transaksi.</li>
                @endforelse
            </ul>
            
            {{ $transaksi->links() }}
        </div>
    </div>
@endsection
