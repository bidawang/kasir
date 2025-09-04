@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <h1 class="mb-4">Beranda</h1>

            <div class="app-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-wallet2 fs-2 me-3"></i>
                    <div>
                        <small class="text-secondary">Saldo Kas</small>
                        <h4 class="mb-0">Rp {{ number_format($kasir->kas->saldo ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr>
                <div class="row text-center small text-secondary">
                    <div class="col">
                        <small>Pemasukan Hari Ini</small>
                        <h6 class="mb-0 text-success">Rp {{ number_format($pemasukanHariIni, 0, ',', '.') }}</h6>
                    </div>
                    <div class="col">
                        <small>Pengeluaran Hari Ini</small>
                        <h6 class="mb-0 text-danger">Rp {{ number_format($pengeluaranHariIni, 0, ',', '.') }}</h6>
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Riwayat Transaksi Terbaru</h5>
            <div class="list-group">
                @forelse($recentTransactions as $transaksi)
                    <a href="#" class="list-group-item list-group-item-action py-3">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $transaksi->keterangan }}</h6>
                            <small class="text-secondary">{{ $transaksi->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1 text-{{ $transaksi->jenis == 'masuk' ? 'success' : 'danger' }}">
                                {{ $transaksi->jenis == 'masuk' ? '+' : '-' }} Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                            </p>
                            <small class="text-secondary">{{ $transaksi->metode_pembayaran }}</small>
                        </div>
                    </a>
                @empty
                    <p class="text-center text-secondary mt-3">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection