@extends('layouts.app')

@section('title', 'Detail Kas')

@section('content')
<div class="container">
    <div class="col-12 col-lg-8 mx-auto">
        <h1 class="mb-4">Detail Kas - {{ $kas->jenis_kas ?? 'Kas ID ' . $kas->id }}</h1>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(isset($detailKas))
        <form action="{{ route('kas.updateDetail', $kas->id) }}" method="POST">
            @csrf

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Pecahan</th>
                        <th>Jumlah Lembar</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailKas as $detail)
                    <tr>
                        <td>
                            Rp {{ number_format($detail->pecahan, 0, ',', '.') }}
                            <input type="hidden" name="pecahan[]" value="{{ $detail->pecahan }}">
                        </td>
                        <td>
                            <input type="number" class="form-control" name="jumlah[]" value="{{ $detail->jumlah }}" min="0">
                        </td>
                        <td>
                            Rp {{ number_format($detail->pecahan * $detail->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">Update Detail Kas</button>
        </form>
        @else
        <p class="text-muted">Tidak ada detail kas untuk kas ini.</p>
        @endif
    </div>
</div>
@endsection