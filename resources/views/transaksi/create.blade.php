@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
    <div class="container-fluid py-3" x-data="transactionApp()">
        <div class="col-12 col-lg-8 mx-auto p-0">
            <h2 class="h4 fw-bold mb-3 px-2">Tambah Transaksi Baru</h2>

            @if ($errors->any() || session('error'))
                <div class="px-2 mb-3">
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                        <div>
                            <ul class="mb-0 small ps-3">
                                @if (session('error'))
                                    <li>{{ session('error') }}</li>
                                @endif
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('transaksi.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Jenis Transaksi</label>
                            <select class="form-select form-select-lg border-2" x-model="jenis" name="jenis" required>
                                <option value="masuk">Setor Tunai (Masuk)</option>
                                <option value="keluar">Tarik Tunai (Keluar)</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label small text-uppercase fw-bold text-secondary">Kas Asal</label>
                                @if (isset($kasId3))
                                    <div class="p-3 bg-light rounded-3 border">
                                        <span class="d-block fw-bold">{{ $kasId3->jenis_kas }}</span>
                                        <small class="text-muted">Saldo: Rp
                                            {{ number_format($kasId3->saldo, 0, ',', '.') }}</small>
                                    </div>
                                    <input type="hidden" name="id_kas" value="{{ $kasId3->id }}">
                                @endif
                            </div>
                            <div class="col-12">
                                <label for="id_kas_tujuan"
                                    class="form-label small text-uppercase fw-bold text-secondary">Kas Tujuan</label>
                                <select class="form-select border-2" name="id_kas_tujuan" required>
                                    <option value="">-- Pilih Kas Tujuan --</option>
                                    @foreach ($kasList as $kas)
                                        @if (!isset($kasId3) || $kas->id != $kasId3->id)
                                            <option value="{{ $kas->id }}">{{ $kas->jenis_kas }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Catat Detail Duit Keluar atau Masuk</label>
                            <div class="bg-light p-3 rounded-3 mb-3">
                                <template x-for="(item, index) in denominations" :key="index">
                                    <div class="row g-2 mb-2 align-items-center">
                                        <div class="col-5">
                                            <span class="fw-bold" x-text="'Rp ' + formatIDR(item.pecahan)"></span>
                                        </div>
                                        <div class="col-7">
                                            <div class="input-group">
                                                <input type="number" class="form-control" x-model.number="item.jumlah"
                                                    min="0">
                                                <span class="input-group-text px-2 small">lbr</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="d-flex justify-content-between align-items-center bg-dark text-white p-3 rounded-3">
                                <span>TOTAL UANG:</span>
                                <h4 class="mb-0 fw-bold" x-text="'Rp ' + formatIDR(totalUang)"></h4>
                            </div>
                        </div>

                        <div class="card border-primary mb-4 bg-light-primary">
                            <div class="card-body">
                                <label class="form-label fw-bold text-primary">Biaya Admin</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control form-control-lg" name="biaya_admin"
                                        x-model.number="biayaAdmin" min="0">
                                </div>

                                <div class="mb-3">

                                    {{-- Jika tarik tunai --}}
                                    <template x-if="jenis === 'keluar'">
                                        <div
                                            class="alert alert-warning border-0 rounded-4 py-2 px-3 mb-0 d-flex align-items-center">
                                            <i class="bi bi-info-circle-fill me-2"></i>

                                            <div class="small fw-semibold">
                                                Biaya admin otomatis dipotong dari total uang pada transaksi tarik tunai.
                                            </div>

                                            {{-- auto ON --}}
                                            <input type="hidden" x-model="adminPotongTotal">
                                        </div>
                                    </template>

                                    {{-- Jika setor tunai --}}
                                    <template x-if="jenis === 'masuk'">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="adminPotong"
                                                x-model="adminPotongTotal">

                                            <label class="form-check-label" for="adminPotong">
                                                Potong dari total uang
                                            </label>
                                        </div>
                                    </template>

                                </div>

                                <div x-show="jenis === 'masuk' && !adminPotongTotal && biayaAdmin > 0" x-transition>
                                    <p class="small text-danger fw-bold mb-2">Detail Uang Fisik Admin:</p>
                                    <template x-for="(item, index) in adminDenominations" :key="'adm-' + index">
                                        <div class="row g-2 mb-2 align-items-center" x-show="item.pecahan <= biayaAdmin">
                                            <div class="col-5">
                                                <small x-text="'Rp ' + formatIDR(item.pecahan)"></small>
                                            </div>
                                            <div class="col-7">
                                                <input type="number" class="form-control form-control-sm"
                                                    x-model.number="item.jumlah" min="0">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div x-show="jenis === 'masuk'" class="mb-4" x-transition>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="has_change" x-model="hasChange">
                                <label class="form-check-label fw-bold" for="has_change">Ada Kembalian</label>
                            </div>

                            <div x-show="hasChange" class="p-3 border rounded-3 bg-white shadow-sm">
                                <template x-for="(item, index) in changeDenominations" :key="'chg-' + index">
                                    <div class="row g-2 mb-2 align-items-center" x-show="item.stok > 0">
                                        <div class="col-5 text-truncate">
                                            <small class="fw-bold" x-text="'Rp ' + formatIDR(item.pecahan)"></small>
                                        </div>
                                        <div class="col-7">
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control" x-model.number="item.jumlah"
                                                    :max="item.stok" min="0">
                                                <span class="input-group-text small" x-text="'Sisa:' + item.stok"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div class="mt-2 text-end fw-bold text-danger">
                                    Kembalian: <span x-text="'Rp ' + formatIDR(totalKembalian)"></span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="total" :value="grandTotal">
                        <input type="hidden" name="denominations_json"
                            :value="JSON.stringify(denominations.filter(d => d.jumlah > 0))">
                        <input type="hidden" name="change_denominations_json"
                            :value="JSON.stringify(changeDenominations.filter(d => d.jumlah > 0))">
                        <input type="hidden" name="admin_denominations_json"
                            :value="JSON.stringify(adminDenominations.filter(d => d.jumlah > 0))">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="2" placeholder="Catatan transaksi..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold">Simpan
                                Transaksi</button>
                            <a href="{{ route('transaksi.index') }}" class="btn btn-light btn-lg rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function transactionApp() {
            return {
                init() {

                    this.$watch('jenis', (value) => {

                        if (value === 'keluar') {
                            this.adminPotongTotal = true;
                        }

                    });

                },

                jenis: 'masuk',
                biayaAdmin: 0,
                adminPotongTotal: true,
                hasChange: false,

                // Inisialisasi Denominasi (Bisa di-passing dari Backend)
                denominations: [{
                        pecahan: 100000,
                        jumlah: 0
                    },
                    {
                        pecahan: 50000,
                        jumlah: 0
                    },
                    {
                        pecahan: 20000,
                        jumlah: 0
                    },
                    {
                        pecahan: 10000,
                        jumlah: 0
                    },
                    {
                        pecahan: 5000,
                        jumlah: 0
                    },
                    {
                        pecahan: 2000,
                        jumlah: 0
                    },
                    {
                        pecahan: 1000,
                        jumlah: 0
                    },
                    {
                        pecahan: 500,
                        jumlah: 0
                    },
                    {
                        pecahan: 200,
                        jumlah: 0
                    },
                    {
                        pecahan: 100,
                        jumlah: 0
                    },
                ],

                // Denominasi untuk Biaya Admin (jika uang terpisah)
                adminDenominations: [{
                        pecahan: 100000,
                        jumlah: 0
                    },
                    {
                        pecahan: 50000,
                        jumlah: 0
                    },
                    {
                        pecahan: 20000,
                        jumlah: 0
                    },
                    {
                        pecahan: 10000,
                        jumlah: 0
                    },
                    {
                        pecahan: 5000,
                        jumlah: 0
                    },
                    {
                        pecahan: 2000,
                        jumlah: 0
                    },
                    {
                        pecahan: 1000,
                        jumlah: 0
                    },
                    {
                        pecahan: 500,
                        jumlah: 0
                    },
                    {
                        pecahan: 200,
                        jumlah: 0
                    },
                    {
                        pecahan: 100,
                        jumlah: 0
                    },
                ],

                // Data Kembalian (Diambil dari stok Kas ID 3)
                // @php asumsikan $kasId3->detailKas di-map ke object JS
                changeDenominations: [
                    @if (isset($kasId3))
                        @foreach ($kasId3->detailKas as $detail)
                            {
                                pecahan: {{ $detail->pecahan }},
                                jumlah: 0,
                                stok: {{ $detail->jumlah }}
                            },
                        @endforeach
                    @endif
                ],

                get totalUang() {
                    return this.denominations.reduce((sum, item) => sum + (item.pecahan * (item.jumlah || 0)), 0);
                },

                get totalKembalian() {
                    if (!this.hasChange) return 0;
                    return this.changeDenominations.reduce((sum, item) => sum + (item.pecahan * (item.jumlah || 0)), 0);
                },

                get grandTotal() {
                    let base = this.totalUang;
                    // Jika potong admin, total transaksi dikurangi admin
                    if (this.adminPotongTotal) {
                        return base - this.biayaAdmin - this.totalKembalian;
                    }
                    // Jika uang admin terpisah, total transaksi adalah total uang fisik utama (biaya admin dicatat terpisah di backend)
                    return base - this.totalKembalian;
                },

                formatIDR(val) {
                    return new Intl.NumberFormat('id-ID').format(val);
                }
            }
        }
    </script>

    <style>
        .bg-light-primary {
            background-color: #f0f7ff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endsection
