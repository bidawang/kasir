<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    @php
        $nomorLaporan = $nomorLaporan ?? ('RPT-' . date('Ymd-His'));
        $periodeLabel = $periodeLabel ?? date('d/m/Y');
        $warung = $warung ?? null;
        $kasBankDb = $kasBankDb ?? collect([]);
        $subtotalBank = $subtotalBank ?? 0;
        $kasTunai = $kasTunai ?? null;
        $uangTunai = $uangTunai ?? 0;
        $detailPecahan = $detailPecahan ?? collect([]);
        $totalHitunganFisik = $totalHitunganFisik ?? 0;
        $itemTrxList = $itemTrxList ?? collect([]);
        $totalItemOmset = $totalItemOmset ?? 0;
        $totalItemLaba = $totalItemLaba ?? 0;
        $totalItemModal = $totalItemModal ?? 0;
        $totalItemQty = $totalItemQty ?? 0;
        $totalLikuiditas = $totalLikuiditas ?? ($subtotalBank + $uangTunai);
        $totalLabaPeriode = $totalLabaPeriode ?? ($summary->total_keuntungan ?? 0);
        $summary = $summary ?? (object)['total_transaksi' => 0, 'total_setor' => 0, 'total_tarik' => 0, 'total_keuntungan' => 0, 'total_admin_toko' => 0, 'total_admin_bank' => 0];
        $kategoriLabel = $kategoriLabel ?? 'Semua Kategori & Aktivitas';
    @endphp
    <title>Laporan Keuangan - {{ $nomorLaporan }}</title>
    <style>
        @page {
            margin: 12mm 12mm 15mm 12mm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.35;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Typography & Utilities */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .text-muted { color: #64748b; }
        .text-success { color: #059669; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #1d4ed8; }

        /* Document Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
        }
        .header-table td {
            vertical-align: top;
        }
        .brand-title {
            font-size: 15pt;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin: 0 0 2px 0;
        }
        .brand-sub {
            font-size: 9pt;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 4px 0;
        }
        .branch-info {
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.4;
        }
        .meta-box {
            text-align: right;
            font-size: 7.5pt;
            color: #334155;
            line-height: 1.45;
        }
        .meta-box strong {
            color: #0f172a;
        }
        .meta-badge {
            display: inline-block;
            background: #e2e8f0;
            color: #0f172a;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 7.5pt;
            margin-top: 2px;
        }

        /* KPI Cards in Table Grid */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 12px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
        }
        .kpi-card.highlight {
            background: #f0fdf4;
            border-color: #86efac;
        }
        .kpi-card.highlight-blue {
            background: #eff6ff;
            border-color: #93c5fd;
        }
        .kpi-label {
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }
        .kpi-value {
            font-size: 10.5pt;
            font-weight: 900;
            color: #0f172a;
        }

        /* Section Title Header */
        .section-header {
            background: #0f172a;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px 4px 0 0;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .section-header-sub {
            background: #334155;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 3.5px 8px;
            border-radius: 4px 4px 0 0;
            margin-top: 8px;
            text-transform: uppercase;
        }

        /* Tables */
        tr {
            page-break-inside: avoid;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 10px;
            border: 1px solid #cbd5e1;
        }
        table.data-table th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: left;
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            font-size: 7pt;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 3.5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        table.data-table tr.total-row td {
            background: #e2e8f0;
            font-weight: bold;
            color: #0f172a;
            border-top: 1.5px solid #64748b;
            border-bottom: 1.5px solid #64748b;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #d1fae5; color: #065f46; border: 0.5px solid #a7f3d0; }
        .badge-danger  { background: #fee2e2; color: #991b1b; border: 0.5px solid #fecaca; }
        .badge-info    { background: #e0e7ff; color: #3730a3; border: 0.5px solid #c7d2fe; }
        .badge-slate   { background: #f1f5f9; color: #334155; border: 0.5px solid #cbd5e1; }

        /* Signature block */
        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .sign-box {
            text-align: center;
            vertical-align: top;
            width: 50%;
            padding: 0 15px;
        }
        .sign-title {
            font-size: 8pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 45px;
        }
        .sign-line {
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #475569;
            display: inline-block;
            min-width: 160px;
            padding-top: 3px;
        }

        /* Footer */
        .footer-note {
            text-align: center;
            font-size: 6.5pt;
            color: #94a3b8;
            margin-top: 14px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- 1. HEADER DOKUMEN LAPORAN -->
    <table class="header-table">
        <tr>
            <td style="width: 58%;">
                <div class="brand-title">LAPORAN KEUANGAN &amp; ARUS KAS</div>
                <div class="brand-sub">AGEN BRILINK &bull; {{ strtoupper($warung->nama_warung ?? 'CABANG UTAMA') }}</div>
                <div class="branch-info">
                    <strong>Alamat:</strong> {{ $warung->alamat ?? '-' }} &bull; 
                    <strong>No. Telp:</strong> {{ $warung->no_telepon ?? '-' }}<br>
                    <strong>Bank Operasional:</strong> {{ $bankUtama }} &bull; 
                    <strong>No. Rek Utama:</strong> {{ $warung->nomor_rekening_utama ?? '-' }} (a.n. {{ $warung->atas_nama_utama ?? '-' }})
                </div>
            </td>
            <td style="width: 42%;" class="meta-box">
                <div><strong>No. Laporan:</strong> <code>{{ $nomorLaporan }}</code></div>
                <div><strong>Periode:</strong> {{ $periodeLabel }}</div>
                <div><strong>Kategori:</strong> <span class="meta-badge">{{ $kategoriLabel }}</span></div>
                <div><strong>Waktu Cetak:</strong> {{ now()->translatedFormat('d M Y, H:i') }} WIB</div>
                <div><strong>Dicetak Oleh:</strong> {{ auth()->user()->nama ?? auth()->user()->username ?? 'Petugas' }} ({{ ucfirst(auth()->user()->role ?? 'Kasir') }})</div>
            </td>
        </tr>
    </table>

    <!-- 2. RINGKASAN FINANSIAL EKSEKUTIF (KPI CARDS) -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card highlight-blue" style="width: 20%;">
                <div class="kpi-label">Total Likuiditas Kas</div>
                <div class="kpi-value text-primary">Rp {{ number_format($totalLikuiditas, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 6pt;">Bank + Uang Tunai</div>
            </td>
            <td class="kpi-card highlight" style="width: 20%;">
                <div class="kpi-label">Total Laba Bersih</div>
                <div class="kpi-value text-success">Rp {{ number_format($totalLabaPeriode, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 6pt;">Jasa BRILink + Laba Item</div>
            </td>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-label">Arus Uang Masuk</div>
                <div class="kpi-value text-success">+Rp {{ number_format($summary->total_setor ?? 0, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 6pt;">Setoran &amp; Penerimaan</div>
            </td>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-label">Arus Uang Keluar</div>
                <div class="kpi-value text-danger">-Rp {{ number_format($summary->total_tarik ?? 0, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 6pt;">Tarik Tunai &amp; Pengeluaran</div>
            </td>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-label">Volume Transaksi</div>
                <div class="kpi-value">{{ number_format($summary->total_transaksi ?? 0, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 6pt;">Catatan Transaksi Aktif</div>
            </td>
        </tr>
    </table>

    <!-- 3. POSISI SALDO AKUN BANK & DIGITAL -->
    <div class="section-header">1. Posisi Saldo Akun Bank &amp; E-Wallet (Digital)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 32%;">Nama Akun / Bank</th>
                <th style="width: 15%;">Tipe / Status</th>
                <th style="width: 23%;">No. Rekening / ID</th>
                <th style="width: 25%;" class="text-right">Saldo Terkini</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kasBankDb as $idx => $bank)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">
                        {{ $bank->jenis_kas }}
                        @if($bank->is_primary)
                            <span class="badge badge-info" style="font-size: 5.5pt;">Utama</span>
                        @endif
                    </td>
                    <td class="uppercase text-muted">{{ $bank->status === 'ewallet' ? 'E-Wallet / QRIS' : 'Rekening Bank' }}</td>
                    <td>{{ $bank->nomor_rekening ?? '-' }} {{ $bank->atas_nama ? '(' . $bank->atas_nama . ')' : '' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($bank->saldo, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding: 8px;">Tidak ada akun bank/digital yang terdaftar.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL SALDO BANK &amp; DIGITAL:</td>
                <td class="text-right text-primary font-bold">Rp {{ number_format($subtotalBank, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- 4. POSISI FISIK UANG TUNAI KASIR (LACI KAS) -->
    <div class="section-header">2. Posisi Fisik Uang Tunai Kasir (Laci Kas)</div>
    <table class="data-table" style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">No</th>
                <th style="width: 37%;">Pecahan Fisik Uang Kertas / Koin</th>
                <th style="width: 25%;" class="text-center">Jumlah Lembar</th>
                <th style="width: 30%;" class="text-right">Subtotal Fisik</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detailPecahan as $i => $pecah)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="font-bold">Rp {{ number_format($pecah->pecahan, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ number_format($pecah->jumlah, 0, ',', '.') }} lembar</td>
                    <td class="text-right font-bold">Rp {{ number_format($pecah->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted" style="padding: 6px;">Belum ada rincian pecahan fisik yang diinput untuk sesi ini.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2" class="text-right font-bold">TOTAL HITUNGAN FISIK LACI KASIR:</td>
                <td class="text-center font-bold">{{ number_format($detailPecahan->sum('jumlah'), 0, ',', '.') }} lembar</td>
                <td class="text-right font-bold text-primary" style="font-size: 8.5pt;">Rp {{ number_format($totalHitunganFisik, 0, ',', '.') }}</td>
            </tr>
            @php
                $selisih = $totalHitunganFisik - $uangTunai;
            @endphp
            <tr>
                <td colspan="3" class="text-right font-bold" style="background: #f8fafc; color: #334155;">Saldo Kas Tunai Buku Sistem:</td>
                <td class="text-right font-bold" style="background: #f8fafc; font-size: 8.5pt;">Rp {{ number_format($uangTunai, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right font-bold" style="background: #f1f5f9;">Status Selisih (Fisik vs Sistem):</td>
                <td class="text-right font-bold {{ $selisih == 0 ? 'text-success' : ($selisih > 0 ? 'text-primary' : 'text-danger') }}" style="background: #f1f5f9; font-size: 8.5pt;">
                    {{ $selisih > 0 ? '+' : '' }}Rp {{ number_format($selisih, 0, ',', '.') }}
                    <span style="font-size: 6.5pt; font-weight: normal; margin-left: 4px;">
                        {{ $selisih == 0 ? '(Sesuai / Klop)' : ($selisih > 0 ? '(Fisik Lebih)' : '(Fisik Kurang)') }}
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 5. PENGHASILAN & PENJUALAN ITEM (TOKO / PULSA) -->
    <div class="section-header">3. Penghasilan &amp; Penjualan Item Toko / Pulsa</div>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px;">
        <tr>
            <td style="padding: 6px 10px; width: 25%; border-right: 1px solid #e2e8f0;">
                <span class="text-muted" style="font-size: 6.5pt; text-transform: uppercase; font-weight: bold; display: block;">Item Terjual:</span>
                <span style="font-size: 10pt; font-weight: 900; color: #0f172a;">{{ number_format($totalItemQty, 0, ',', '.') }} <span style="font-size: 7.5pt; font-weight: normal;">transaksi</span></span>
            </td>
            <td style="padding: 6px 10px; width: 25%; border-right: 1px solid #e2e8f0;">
                <span class="text-muted" style="font-size: 6.5pt; text-transform: uppercase; font-weight: bold; display: block;">Total Omset Penjualan:</span>
                <span style="font-size: 10pt; font-weight: 900; color: #0f172a;">Rp {{ number_format($totalItemOmset, 0, ',', '.') }}</span>
            </td>
            <td style="padding: 6px 10px; width: 25%; border-right: 1px solid #e2e8f0;">
                <span class="text-muted" style="font-size: 6.5pt; text-transform: uppercase; font-weight: bold; display: block;">Total Modal (HPP):</span>
                <span style="font-size: 10pt; font-weight: 900; color: #64748b;">Rp {{ number_format($totalItemModal, 0, ',', '.') }}</span>
            </td>
            <td style="padding: 6px 10px; width: 25%;">
                <span class="text-muted" style="font-size: 6.5pt; text-transform: uppercase; font-weight: bold; display: block;">Laba Bersih Toko:</span>
                <span style="font-size: 10pt; font-weight: 900; color: #059669;">Rp {{ number_format($totalItemLaba, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    @if($itemTrxList->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">No</th>
                    <th style="width: 14%;">Waktu</th>
                    <th style="width: 33%;">Nama Barang / Pulsa</th>
                    <th style="width: 14%;">Pembayaran</th>
                    <th style="width: 12%;" class="text-right">Harga Jual</th>
                    <th style="width: 11%;" class="text-right">Modal</th>
                    <th style="width: 11%;" class="text-right">Laba</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemTrxList as $idx => $it)
                    @php
                        $hargaJual = (int) ($it->harga_item ?: $it->total);
                        $labaItem = (int) $it->keuntungan;
                        $modalItem = $hargaJual - $labaItem;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $it->created_at ? $it->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="font-bold">{{ $it->nama_item ?: ($it->itemJualan->nama_item ?? 'Barang Toko') }}</td>
                        <td class="uppercase text-muted" style="font-size: 6.5pt;">{{ $it->metode_pembayaran ?? 'Cash' }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
                        <td class="text-right text-muted">Rp {{ number_format($modalItem, 0, ',', '.') }}</td>
                        <td class="text-right font-bold text-success">+Rp {{ number_format($labaItem, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL PENJUALAN ITEM:</td>
                    <td class="text-right font-bold">Rp {{ number_format($totalItemOmset, 0, ',', '.') }}</td>
                    <td class="text-right font-bold text-muted">Rp {{ number_format($totalItemModal, 0, ',', '.') }}</td>
                    <td class="text-right font-bold text-success">+Rp {{ number_format($totalItemLaba, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- 6. RINCIAN SELURUH TRANSAKSI & MUTASI KAS -->
    <div class="section-header" style="page-break-before: auto;">4. Rincian Seluruh Transaksi &amp; Mutasi Kas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 11%;">Waktu</th>
                <th style="width: 15%;">No. Referensi</th>
                <th style="width: 16%;">Layanan / Kategori</th>
                <th style="width: 20%;">Keterangan &amp; Akun</th>
                <th style="width: 7%;" class="text-center">Arah</th>
                <th style="width: 13%;" class="text-right">Nominal</th>
                <th style="width: 7%;" class="text-right">Admin</th>
                <th style="width: 7%;" class="text-right">Laba</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotalNominal = 0;
                $subtotalAdmin = 0;
                $subtotalLaba = 0;
            @endphp
            @forelse($transaksi as $idx => $t)
                @php
                    $isMasuk = $t->jenis === 'masuk';
                    $isTransfer = $t->kategori === 'pemindahan_dana';
                    $nom = (int) ($t->nominal > 0 ? $t->nominal : $t->total);
                    $laba = $isTransfer ? 0 : (int) ($t->keuntungan != 0 ? $t->keuntungan : ($t->biaya_admin - $t->biaya_admin_bank));
                    $subtotalNominal += $nom;
                    $subtotalAdmin += (int) $t->biaya_admin;
                    $subtotalLaba += $laba;

                    $kategoriText = $isTransfer 
                        ? 'Pemindahan Dana' 
                        : ($t->kategoriTransaksi ? $t->kategoriTransaksi->getNamaDinamis($bankUtama ?? 'BRI') : ($t->kategori === 'penjualan_item' ? 'Penjualan Toko' : 'Transaksi Kas'));
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $t->created_at ? $t->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td><code>{{ $t->kode_transaksi ?? ('TRX-' . str_pad($t->id, 6, '0', STR_PAD_LEFT)) }}</code></td>
                    <td class="font-bold">{{ $kategoriText }}</td>
                    <td>
                        {{ $t->keterangan ?? '-' }}
                        @if($t->kas)
                            <div class="text-muted" style="font-size: 6.5pt;">Akun: {{ $t->kas->jenis_kas }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($isTransfer)
                            <span class="badge badge-info">{{ $isMasuk ? 'T-Masuk' : 'T-Keluar' }}</span>
                        @else
                            <span class="badge {{ $isMasuk ? 'badge-success' : 'badge-danger' }}">{{ $isMasuk ? 'Masuk' : 'Keluar' }}</span>
                        @endif
                    </td>
                    <td class="text-right font-bold {{ $isMasuk ? 'text-success' : 'text-danger' }}">
                        {{ $isMasuk ? '+' : '-' }}Rp {{ number_format($nom, 0, ',', '.') }}
                    </td>
                    <td class="text-right text-muted">
                        {{ $t->biaya_admin > 0 ? 'Rp ' . number_format($t->biaya_admin, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-bold {{ $laba > 0 ? 'text-success' : 'text-muted' }}">
                        {{ $laba > 0 ? '+Rp ' . number_format($laba, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted" style="padding: 10px;">Tidak ada riwayat transaksi pada filter tanggal ini.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL AKUMULASI TRANSAKSI:</td>
                <td class="text-right font-bold">Rp {{ number_format($subtotalNominal, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($subtotalAdmin, 0, ',', '.') }}</td>
                <td class="text-right font-bold text-success">+Rp {{ number_format($subtotalLaba, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- 7. TANDA TANGAN & PENGESAHAN -->
    <table class="sign-table">
        <tr>
            <td class="sign-box">
                <div class="sign-title">Kasir Jaga / Petugas Shift,</div>
                <div class="sign-line">
                    ( {{ auth()->user()->nama ?? auth()->user()->username ?? 'Kasir Bertugas' }} )
                </div>
                <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">Tgl: {{ now()->translatedFormat('d F Y') }}</div>
            </td>
            <td class="sign-box">
                <div class="sign-title">Pemilik (Owner) / Supervisor,</div>
                <div class="sign-line">
                    ( {{ $warung->user->nama ?? 'Owner Agen BRILink' }} )
                </div>
                <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">Tgl: {{ now()->translatedFormat('d F Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- 8. FOOTER DOKUMEN -->
    <div class="footer-note">
        Dokumen ini dibuat otomatis oleh Sistem Manajemen Operasional Agen BRILink &bull; Dicetak pada {{ now()->translatedFormat('d F Y, H:i:s') }} WIB
    </div>

</body>
</html>
