@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
<div class="container">
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
                        @foreach ([100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100] as $nominal)
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal"
                                        value="{{ number_format($nominal, 0, ',', '.') }}" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <input type="number" class="form-control quantity"
                                        data-nominal="{{ $nominal }}" value="0" min="0">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <h4 class="fw-bold">Total: <span id="total_display">Rp 0</span></h4>
                    </div>
                </div>

                {{-- Hidden input --}}
                <input type="hidden" name="total" id="total_transaksi" required>
                <input type="hidden" name="denominations_json" id="denominations_json">
                <input type="hidden" name="change_denominations_json" id="change_denominations_json">

                <div class="mb-3">
                    <label for="jenis" class="form-label">Jenis Transaksi</label>
                    <select class="form-select" id="jenis" name="jenis" required>
                        <option value="keluar">Pengeluaran</option>
                        <option value="masuk">Pemasukan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_kas" class="form-label">Kas Asal</label>
                    <select class="form-select" id="id_kas" name="id_kas" required>
                        <option value="">-- Pilih Kas --</option>
                        @foreach ($kasList as $kas)
                        <option value="{{ $kas->id }}">
                            {{ $kas->jenis_kas }} (Saldo: Rp {{ number_format($kas->saldo, 0, ',', '.') }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bagian transfer baru --}}
                <div id="transfer-section" class="mb-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_transfer" name="is_transfer" value="1">
                        <label class="form-check-label" for="is_transfer">
                            Transaksi ini adalah transfer
                        </label>
                    </div>
                    <div id="kas-tujuan-container" style="display: none;">
                        <label for="id_kas_tujuan" class="form-label">Kas Tujuan</label>
                        <select class="form-select" id="id_kas_tujuan" name="id_kas_tujuan">
                            <option value="">-- Pilih Kas Tujuan --</option>
                            @foreach ($kasList as $kas)
                            @if ($kas->id != 3) {{-- Pilihan Kas Utama (ID 3) tidak ditampilkan sebagai tujuan --}}
                            <option value="{{ $kas->id }}">
                                {{ $kas->jenis_kas }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="biaya_admin" class="form-label">Biaya Admin</label>
                    <input type="number" class="form-control" id="biaya_admin" name="biaya_admin" value="0" min="0">
                </div>

                {{-- Bagian kembalian --}}
                <div id="change-section" class="mb-3" style="display: none;">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="has_change" name="has_change">
                        <label class="form-check-label" for="has_change">
                            Ada Kembalian
                        </label>
                    </div>
                    <div id="change-denominations-container" style="display: none;">
                        <label class="form-label d-block">Detail Uang Kembalian</label>
                        <p class="small text-secondary mb-2">Masukkan jumlah lembar untuk setiap nominal yang digunakan untuk kembalian.</p>
                        @if($kasId3 && $kasId3->detailKas->count())
                        @foreach($kasId3->detailKas as $detail)
                        @if($detail->jumlah > 0)
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-5">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control nominal"
                                        value="{{ number_format($detail->pecahan, 0, ',', '.') }}" disabled>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="input-group">
                                    <span class="input-group-text available-quantity">Tersedia: {{ $detail->jumlah }}</span>
                                    <input type="number" class="form-control change-quantity"
                                        data-nominal="{{ $detail->pecahan }}" value="0" min="0" max="{{ $detail->jumlah }}">
                                    <span class="input-group-text">lembar</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                        @else
                        <p class="text-muted small">Belum ada detail pecahan untuk kas kembalian.</p>
                        @endif
                        <div class="mt-3">
                            <h4 class="fw-bold">Total Kembalian: <span id="change_total_display">Rp 0</span></h4>
                            <input type="hidden" name="change_total" id="change_total_input" value="0">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan">
                </div>

                <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisSelect = document.getElementById('jenis');
        const changeSection = document.getElementById('change-section');
        const hasChangeCheckbox = document.getElementById('has_change');
        const changeDenominationsContainer = document.getElementById('change-denominations-container');
        const changeTotalDisplay = document.getElementById('change_total_display');
        const changeTotalInput = document.getElementById('change_total_input');
        const changeDenomInput = document.getElementById('change_denominations_json');

        const denominationsContainer = document.getElementById('denominations-container');
        const totalDisplay = document.getElementById('total_display');
        const totalInput = document.getElementById('total_transaksi');
        const denomInput = document.getElementById('denominations_json');

        // Elemen-elemen baru untuk transfer
        const transferSection = document.getElementById('transfer-section');
        const isTransferCheckbox = document.getElementById('is_transfer');
        const kasTujuanContainer = document.getElementById('kas-tujuan-container');

        // Function to calculate total transaction amount
        function calculateTotal() {
            let total = 0;
            let denominations = [];
            const quantityInputs = denominationsContainer.querySelectorAll('.quantity');
            quantityInputs.forEach(input => {
                const nominal = parseInt(input.dataset.nominal);
                const quantity = parseInt(input.value) || 0;
                if (quantity > 0) {
                    denominations.push({
                        pecahan: nominal,
                        jumlah: quantity
                    });
                }
                total += nominal * quantity;
            });
            totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
            totalInput.value = total;
            denomInput.value = JSON.stringify(denominations);
        }

        // Function to calculate total change amount
        function calculateChangeTotal() {
            let changeTotal = 0;
            let changeDenominations = [];
            const changeQuantityInputs = changeDenominationsContainer.querySelectorAll('.change-quantity');
            changeQuantityInputs.forEach(input => {
                const nominal = parseInt(input.dataset.nominal);
                const quantity = parseInt(input.value) || 0;
                if (quantity > 0) {
                    changeDenominations.push({
                        pecahan: nominal,
                        jumlah: quantity
                    });
                }
                changeTotal += nominal * quantity;
            });
            changeTotalDisplay.innerText = 'Rp ' + changeTotal.toLocaleString('id-ID');
            changeTotalInput.value = changeTotal;
            changeDenomInput.value = JSON.stringify(changeDenominations);
        }

        // Event listener untuk perubahan jenis transaksi (masuk/keluar)
        jenisSelect.addEventListener('change', function() {
            if (this.value === 'masuk') {
                changeSection.style.display = 'block';
                transferSection.style.display = 'none'; // Sembunyikan transfer untuk transaksi masuk
                isTransferCheckbox.checked = false;
                kasTujuanContainer.style.display = 'none';
            } else { // 'keluar'
                changeSection.style.display = 'none';
                hasChangeCheckbox.checked = false;
                changeDenominationsContainer.style.display = 'none';
                changeTotalInput.value = 0;
                changeTotalDisplay.innerText = 'Rp 0';
                changeDenomInput.value = '[]';

                transferSection.style.display = 'block'; // Tampilkan transfer untuk transaksi keluar
            }
        });

        // Event listener untuk checkbox "Ada Kembalian"
        hasChangeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                changeDenominationsContainer.style.display = 'block';
            } else {
                changeDenominationsContainer.style.display = 'none';
                // Reset change fields
                const changeQuantityInputs = changeDenominationsContainer.querySelectorAll('.change-quantity');
                changeQuantityInputs.forEach(input => input.value = 0);
                calculateChangeTotal();
            }
        });

        // Event listener untuk checkbox "Transfer"
        isTransferCheckbox.addEventListener('change', function() {
            if (this.checked) {
                kasTujuanContainer.style.display = 'block';
            } else {
                kasTujuanContainer.style.display = 'none';
            }
        });

        // Event listeners for quantity inputs
        denominationsContainer.addEventListener('input', calculateTotal);
        changeDenominationsContainer.addEventListener('input', calculateChangeTotal);

        // Initial calculation on page load
        calculateTotal();
        calculateChangeTotal();
    });
</script>
@endsection