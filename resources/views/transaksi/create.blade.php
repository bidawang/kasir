@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
<div class="row">
    <div class="col-12 col-lg-8 mx-auto">
        <h1 class="mb-4">Tambah Transaksi Baru</h1>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="app-card p-4">
            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf


                {{-- Bagian detail jumlah uang --}}
                <div class="mb-3">
                    <label class="form-label d-block">Detail Jumlah Uang</label>
                    <p class="small text-secondary mb-2">Masukkan jumlah lembar untuk setiap nominal.</p>
                    <div id="denominations-container">
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal" value="100.000" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <input type="number" class="form-control quantity" data-nominal="100000" value="0" min="0">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal" value="50.000" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <input type="number" class="form-control quantity" data-nominal="50000" value="0" min="0">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal" value="20.000" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <input type="number" class="form-control quantity" data-nominal="20000" value="0" min="0">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal" value="10.000" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <input type="number" class="form-control quantity" data-nominal="10000" value="0" min="0">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal" value="5.000" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <input type="number" class="form-control quantity" data-nominal="5000" value="0" min="0">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal" value="2.000" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <input type="number" class="form-control quantity" data-nominal="2000" value="0" min="0">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal" value="1.000" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <input type="number" class="form-control quantity" data-nominal="1000" value="0" min="0">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h4 class="fw-bold">Total: <span id="total_display">Rp 0</span></h4>
                    </div>
                </div>

                {{-- Hidden input untuk total yang akan dikirim ke controller --}}
                <input type="hidden" name="total" id="total_transaksi" required>

                <div class="mb-3">
                    <label for="jenis" class="form-label">Jenis Transaksi</label>
                    <select class="form-select" id="jenis" name="jenis" required>
                        <option value="masuk">Pemasukan</option>
                        <option value="keluar">Pengeluaran</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                </div>

                <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('denominations-container');
        const totalDisplay = document.getElementById('total_display');
        const totalInput = document.getElementById('total_transaksi');

        function calculateTotal() {
            let total = 0;
            const quantityInputs = container.querySelectorAll('.quantity');
            quantityInputs.forEach(input => {
                const nominal = parseInt(input.dataset.nominal);
                const quantity = parseInt(input.value) || 0;
                total += nominal * quantity;
            });
            totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
            totalInput.value = total;
        }

        container.addEventListener('input', calculateTotal);
        calculateTotal();
    });
</script>
@endsection