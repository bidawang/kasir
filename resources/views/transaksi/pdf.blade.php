<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $nomorLaporan }} - Laporan Transaksi</title>
    <style>
        @page {
            margin: 28px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .logo-text {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }
        .branch-title {
            font-size: 11pt;
            font-weight: bold;
            color: #334155;
            margin-top: 2px;
        }
        .report-meta {
            text-align: right;
            font-size: 8pt;
            color: #64748b;
        }
        .report-meta strong {
            color: #0f172a;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-bank {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-masuk {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-keluar {
            background-color: #ffe4e6;
            color: #9f1239;
        }
        
        /* Metric Summary Table */
        .metric-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .metric-box {
            padding: 8px 10px;
            border-radius: 6px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .metric-box.primary {
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }
        .metric-box.success {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
        }
        .metric-title {
            font-size: 7pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }
        .metric-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .metric-sub {
            font-size: 7pt;
            color: #94a3b8;
            margin-top: 2px;
        }
        
        /* Main Transaction Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7pt;
            padding: 6px 5px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        .data-table td {
            padding: 5px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-emerald { color: #047857; }
        .text-rose { color: #be123c; }
        .text-muted { color: #64748b; }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
            font-size: 8pt;
            color: #475569;
        }
        .signature-space {
            height: 48px;
        }
        .signature-name {
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #94a3b8;
            display: inline-block;
            padding-top: 4px;
            min-width: 160px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: middle;">
                <div class="logo-text">AGENT KAS</div>
                <div class="branch-title">{{ $warung->nama_warung ?? 'Cabang Agen' }}</div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                    Bank Utama: <span class="badge badge-bank">{{ $bankUtama }}</span>
                    @if($warung->alamat_warung) &bull; {{ $warung->alamat_warung }} @endif
                </div>
            </td>
            <td style="width: 45%; vertical-align: middle;" class="report-meta">
                <div>No. Dokumen: <strong>{{ $nomorLaporan }}</strong></div>
                <div>Periode: <strong>{{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Semua' }} s/d {{ $endDate ? date('d/m/Y', strtotime($endDate)) : 'Hari Ini' }}</strong></div>
                <div>Kategori: <strong>{{ $kategoriLabel }}</strong></div>
                <div>Waktu Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                <div>Operator: <strong>{{ auth()->user()->nama ?? auth()->user()->username }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- Metric Summary Section -->
    <table class="metric-table">
        <tr>
            <td style="width: 25%;">
                <div class="metric-box success">
                    <div class="metric-title" style="color: #047857;">Total Laba Bersih</div>
                    <div class="metric-value" style="color: #065f46;">Rp {{ number_format($summary->total_keuntungan ?? 0, 0, ',', '.') }}</div>
                    <div class="metric-sub">Admin Toko - Modal Bank</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="metric-box">
                    <div class="metric-title">Uang Masuk (Setor)</div>
                    <div class="metric-value text-emerald">Rp {{ number_format($summary->total_setor ?? 0, 0, ',', '.') }}</div>
                    <div class="metric-sub">Fisik diterima kasir</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="metric-box">
                    <div class="metric-title">Uang Keluar (Tarik)</div>
                    <div class="metric-value text-rose">Rp {{ number_format($summary->total_tarik ?? 0, 0, ',', '.') }}</div>
                    <div class="metric-sub">Fisik diserahkan nasabah</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="metric-box primary">
                    <div class="metric-title" style="color: #1e40af;">Volume Transaksi</div>
                    <div class="metric-value" style="color: #1e3a8a;">{{ number_format($summary->total_transaksi ?? 0, 0, ',', '.') }} Trx</div>
                    <div class="metric-sub">Toko: Rp {{ number_format($summary->total_admin_toko ?? 0, 0, ',', '.') }} | Bank: Rp {{ number_format($summary->total_admin_bank ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Transactions Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">No</th>
                <th style="width: 13%;">Waktu</th>
                <th style="width: 22%;">Kategori & Keterangan</th>
                <th class="text-center" style="width: 8%;">Aliran</th>
                <th class="text-right" style="width: 15%;">Nominal</th>
                <th class="text-right" style="width: 11%;">Admin Toko</th>
                <th class="text-right" style="width: 11%;">Admin Bank</th>
                <th class="text-right" style="width: 11%;">Laba Bersih</th>
                <th class="text-center" style="width: 5%;">Kasir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $item)
                @php
                    $isMasuk = $item->jenis === 'masuk';
                    $labaItem = $item->keuntungan != 0 ? $item->keuntungan : ($item->biaya_admin - $item->biaya_admin_bank);
                    $namaKat = $item->kategoriTransaksi ? $item->kategoriTransaksi->getNamaDinamis($bankUtama) : ($item->itemJualan ? $item->itemJualan->nama_item : 'Transaksi');
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="font-bold">{{ $item->created_at->format('d/m/Y') }}</div>
                        <div class="text-muted" style="font-size: 7pt;">{{ $item->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td>
                        <div class="font-bold">{{ $namaKat }}</div>
                        @if($item->keterangan)
                            <div class="text-muted" style="font-size: 7pt;">{{ $item->keterangan }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $isMasuk ? 'badge-masuk' : 'badge-keluar' }}">
                            {{ $isMasuk ? 'MASUK' : 'KELUAR' }}
                        </span>
                    </td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->biaya_admin, 0, ',', '.') }}
                    </td>
                    <td class="text-right text-muted">
                        Rp {{ number_format($item->biaya_admin_bank, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-bold text-emerald">
                        Rp {{ number_format($labaItem, 0, ',', '.') }}
                    </td>
                    <td class="text-center text-muted" style="font-size: 7pt;">
                        {{ $item->user->nama ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted" style="padding: 20px;">
                        Tidak ada transaksi pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1;">
                <td colspan="4" class="text-right" style="padding: 7px; text-transform: uppercase; font-size: 7.5pt;">TOTAL KESELURUHAN:</td>
                <td class="text-right" style="padding: 7px; color: #0f172a;">Rp {{ number_format($summary->total_volume ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="padding: 7px;">Rp {{ number_format($summary->total_admin_toko ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="padding: 7px; color: #64748b;">Rp {{ number_format($summary->total_admin_bank ?? 0, 0, ',', '.') }}</td>
                <td class="text-right text-emerald" style="padding: 7px; font-size: 8.5pt;">Rp {{ number_format($summary->total_keuntungan ?? 0, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Signature Section -->
    <table class="signature-table">
        <tr>
            <td class="signature-cell">
                <div>Dicatat & Dicetak Oleh:</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ auth()->user()->nama ?? auth()->user()->username }}</div>
                <div style="font-size: 7.5pt; color: #94a3b8; margin-top: 2px;">Petugas Kasir</div>
            </td>
            <td class="signature-cell">
                <div>Mengetahui / Penanggung Jawab:</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $warung->user->nama ?? 'Pengelola Cabang' }}</div>
                <div style="font-size: 7.5pt; color: #94a3b8; margin-top: 2px;">Owner / Admin Agen</div>
            </td>
        </tr>
    </table>

</body>
</html>
