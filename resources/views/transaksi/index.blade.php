@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container">
    <div class="col-12 col-lg-8 mx-auto">
        <h1 class="mb-4">Riwayat Transaksi</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filter tanggal --}}
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

        {{-- Accordion --}}
        <div class="accordion" id="accordionTransaksi">
            @forelse($transaksi as $item)
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="heading-{{ $item->id }}">
                    <button class="accordion-button collapsed d-flex justify-content-between" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $item->id }}"
                        aria-expanded="false" aria-controls="collapse-{{ $item->id }}">
                        <div>
                            <div class="fw-bold">{{ $item->keterangan }}</div>
                            <small class="text-secondary">
                                {{ $item->kas->jenis_kas ?? 'Tanpa Kategori' }} |
                                {{ $item->created_at->translatedFormat('d F Y H:i') }}
                            </small>
                        </div>
                        <span class="ms-auto text-{{ $item->jenis == 'masuk' ? 'success' : 'danger' }} fw-bold">
                            {{ $item->jenis == 'masuk' ? '+' : '-' }} Rp {{ number_format($item->total, 0, ',', '.') }}
                        </span>
                    </button>
                </h2>
                <div id="collapse-{{ $item->id }}" class="accordion-collapse collapse"
                    aria-labelledby="heading-{{ $item->id }}" data-bs-parent="#accordionTransaksi">
                    <div class="accordion-body">
                        <p><strong>Jenis:</strong> {{ ucfirst($item->jenis) }}</p>
                        <p><strong>Keterangan:</strong> {{ $item->keterangan ?? '-' }}</p>
                        <p><strong>Kas:</strong> {{ $item->kas->jenis_kas ?? '-' }} ({{ $item->kas->warung->nama ?? '-' }})</p>
                        <p><strong>Biaya Admin:</strong> Rp {{ number_format($item->biaya_admin ?? 0, 0, ',', '.') }}</p>

                        <h6 class="mt-3">Detail Pecahan:</h6>
                        @if($item->detailTransaksi->count())
                        <ul class="list-group">
                            @foreach($item->detailTransaksi as $detail)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Rp {{ number_format($detail->pecahan, 0, ',', '.') }} x {{ $detail->jumlah }}</span>
                                <span>= Rp {{ number_format($detail->pecahan * $detail->jumlah, 0, ',', '.') }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-secondary">Tidak ada detail pecahan.</p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-secondary">Belum ada data transaksi.</p>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $transaksi->links() }}
        </div>
    </div>
</div>
@endsection