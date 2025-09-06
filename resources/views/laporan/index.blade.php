@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="container">
    <div class="col-12 col-lg-8 mx-auto">
        <h1 class="mb-4">Laporan</h1>

        <form action="{{ route('laporan.index') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-5 mb-2">
                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                </div>
                <div class="col-md-5 mb-2">
                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                </div>
            </div>
        </form>

        <div class="app-card p-4 mb-3">
            <h5 class="mb-3">Laporan Keuangan Bulanan</h5>
            <canvas id="myChart"></canvas>
        </div>

        <div class="app-card p-4">
            <h5 class="mb-3">Ringkasan Total</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <div>Total Pemasukan</div>
                    <div class="fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <div>Total Pengeluaran</div>
                    <div class="fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <div>Saldo Akhir</div>
                    <div class="fw-bold">Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}</div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart');
    const labels = @json($labels);
    const pemasukanData = @json($pemasukanData);
    const pengeluaranData = @json($pengeluaranData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pemasukan',
                data: pemasukanData,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }, {
                label: 'Pengeluaran',
                data: pengeluaranData,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection