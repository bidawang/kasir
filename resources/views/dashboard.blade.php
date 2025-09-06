@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

<div class="container">
    <div class="col-12 col-lg-8 mx-auto">
        <h1 class="mb-4">Beranda</h1>

        {{-- Saldo Total --}}
        <div class="app-card p-4 mb-3 shadow-sm rounded-3">
            <div class="row g-3">
                {{-- Kas ID = 3 --}}
                <div class="col-12 col-sm-6">
                    <div class="accordion" id="accordionKas3">
                        <div class="accordion-item shadow-sm rounded-3">
                            <h2 class="accordion-header" id="headingKas3">
                                <button class="accordion-button d-flex align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKas3" aria-expanded="false" aria-controls="collapseKas3">
                                    <i class="bi bi-cash-coin fs-4 me-3 text-success"></i>
                                    <div>
                                        <small class="text-secondary d-block">Kas BRILINK</small>
                                        <h6 class="mb-0">
                                            Rp {{ number_format($kasId3->saldo ?? 0, 0, ',', '.') }}
                                        </h6>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseKas3" class="accordion-collapse collapse" aria-labelledby="headingKas3" data-bs-parent="#accordionKas3">
                                <div class="accordion-body">
                                    {{-- Detail Pecahan Uang --}}
                                    @if($kasId3 && $kasId3->detailKas->count())
                                    @foreach($kasId3->detailKas as $detail)
                                    @if($detail->jumlah > 0)
                                    <div class="d-flex justify-content-between small border-bottom py-1">
                                        <span>{{ $detail->pecahan }} x {{ $detail->jumlah }}</span>
                                        <span>Rp {{ number_format($detail->pecahan * $detail->jumlah, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    @endforeach
                                    @else
                                    <p class="text-muted small">Belum ada detail pecahan.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Saldo Total --}}
                <div class="col-12 col-sm-6 d-flex align-items-center justify-content-end">
                    <i class="bi bi-wallet2 fs-2 me-3 text-primary"></i>
                    <div class="text-end">
                        <small class="text-secondary">Saldo Total</small>
                        <h4 class="mb-0">
                            Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Hari Ini --}}
            <div class="row text-center small text-secondary mt-3">
                <div class="col">
                    <small>Pemasukan Hari Ini</small>
                    <h6 class="mb-0 text-success">
                        Rp {{ number_format($pemasukanHariIni, 0, ',', '.') }}
                    </h6>
                </div>
                <div class="col">
                    <small>Pengeluaran Hari Ini</small>
                    <h6 class="mb-0 text-danger">
                        Rp {{ number_format($pengeluaranHariIni, 0, ',', '.') }}
                    </h6>
                </div>
            </div>
        </div>

        {{-- Saldo per Jenis Kas --}}
        <h5 class="mb-3">Saldo per Jenis Kas</h5>
        <div class="row g-3">
            @foreach($saldoPerJenis as $kas)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="p-3 rounded-3 shadow-sm h-100 text-center bg-light">
                    <small class="text-muted d-block">{{ $kas->jenis_kas }}</small>
                    <h6 class="mb-0 text-dark">
                        Rp {{ number_format($kas->saldo, 0, ',', '.') }}
                    </h6>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Riwayat Transaksi --}}
        <h5 class="mt-4 mb-3">Riwayat Transaksi Terbaru</h5>
        <div class="list-group shadow-sm rounded-3">
            @forelse($recentTransactions as $transaksi)
            <a href="#" class="list-group-item list-group-item-action py-3">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">{{ $transaksi->keterangan }}</h6>
                    <small class="text-secondary">
                        {{ $transaksi->created_at->diffForHumans() }}
                    </small>
                </div>
                <div class="d-flex w-100 justify-content-between">
                    <p class="mb-1 text-{{ $transaksi->jenis == 'masuk' ? 'success' : 'danger' }}">
                        {{ $transaksi->jenis == 'masuk' ? '+' : '-' }}
                        Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                    </p>
                    <small class="text-secondary">
                        {{ $transaksi->metode_pembayaran ?? '-' }}
                    </small>
                </div>
            </a>
            @empty
            <p class="text-center text-secondary mt-3">Belum ada transaksi.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection