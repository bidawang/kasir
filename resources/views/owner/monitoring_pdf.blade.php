<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Konsolidasi Multi-Cabang - {{ $periodeLabel }}</title>
    <style>
        @page {
            margin: 20px 25px;
            size: A4 landscape;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header table {
            width: 100%;
        }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-sub {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 2px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 14px;
        }
        .kpi-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }
        .kpi-label {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .kpi-val {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 3px;
        }
        .kpi-green {
            color: #059669;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 5px;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
            padding: 6px 6px;
            border: 1px solid #0f172a;
        }
        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .footer-table {
            width: 100%;
            margin-top: 25px;
            font-size: 8pt;
        }
        .sign-space {
            height: 45px;
        }
    </style>
</head>
<body>

    <!-- HEADER LAPORAN -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="header-title">Laporan Konsolidasi Multi-Cabang</div>
                    <div class="header-sub">Pemilik Usaha: <strong>{{ $user->nama ?? $user->username }}</strong> | Total Cabang: <strong>{{ count($branchStats) }} Cabang</strong></div>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <div style="font-weight: bold; font-size: 9pt; color: #1e40af;">PERIODE: {{ strtoupper($periodeLabel) }}</div>
                    <div class="header-sub">Dicetak pada: {{ now()->translatedFormat('d F Y H:i:s') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- KPI BOXES -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Total Likuiditas Gabungan</div>
                <div class="kpi-val">Rp {{ number_format($totalLikuiditas, 0, ',', '.') }}</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Kas Tunai (Fisik)</div>
                <div class="kpi-val">Rp {{ number_format($totalKasTunai, 0, ',', '.') }}</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Kas Bank / Digital</div>
                <div class="kpi-val">Rp {{ number_format($totalKasBank, 0, ',', '.') }}</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Total Laba Bersih</div>
                <div class="kpi-val kpi-green">+Rp {{ number_format($totalLaba, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- DATA TABEL KONSOLIDASI CABANG -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 20%;" class="text-left">Nama Cabang</th>
                <th style="width: 8%;" class="text-center">Bank Utama</th>
                <th style="width: 11%;" class="text-right">Kas Tunai</th>
                <th style="width: 12%;" class="text-right">Bank / Digital</th>
                <th style="width: 13%;" class="text-right">Total Likuiditas</th>
                <th style="width: 6%;" class="text-center">Trx</th>
                <th style="width: 12%;" class="text-right">Perputaran Omset</th>
                <th style="width: 11%;" class="text-right">Laba Bersih</th>
                <th style="width: 11%;" class="text-center">Status Tutup Kas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branchStats as $idx => $b)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold text-left">{{ $b['nama'] }}</td>
                    <td class="text-center font-bold">{{ $b['bank_utama'] }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($b['kas_tunai'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($b['kas_bank'], 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #0f172a;">Rp {{ number_format($b['total_saldo'], 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ $b['transaksi_count'] }}</td>
                    <td class="text-right">Rp {{ number_format($b['omset'], 0, ',', '.') }}</td>
                    <td class="text-right font-bold kpi-green">+Rp {{ number_format($b['laba'], 0, ',', '.') }}</td>
                    <td class="text-center font-bold" style="font-size: 7.5pt;">{{ $b['status_laporan'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="3" class="text-center uppercase">TOTAL KONSOLIDASI</td>
                <td class="text-right">Rp {{ number_format($totalKasTunai, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalKasBank, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($totalLikuiditas, 0, ',', '.') }}</td>
                <td class="text-center">{{ $totalTransaksi }}</td>
                <td class="text-right">Rp {{ number_format($totalMasuk + $totalKeluar, 0, ',', '.') }}</td>
                <td class="text-right kpi-green">+Rp {{ number_format($totalLaba, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
            </tr>
        </tfoot>
    </table>

    <!-- TANDA TANGAN OWNER -->
    <table class="footer-table">
        <tr>
            <td style="width: 70%;">
                <div style="font-size: 7.5pt; color: #64748b;">
                    * Dokumen rekapitulasi monitoring ini di-generate secara otomatis oleh sistem Agent Kas.
                </div>
            </td>
            <td style="width: 30%; text-align: center;">
                <div>Pemilik Usaha / Owner,</div>
                <div class="sign-space"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $user->nama ?? $user->username }}</div>
                <div style="font-size: 7.5pt; color: #64748b;">Pimpinan Manajemen</div>
            </td>
        </tr>
    </table>

</body>
</html>
