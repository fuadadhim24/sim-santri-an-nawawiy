<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan SIM Santri An-Nawawiy</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a202c;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 0;
            color: #718096;
            font-size: 12px;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 11px;
            color: #4a5568;
            background: #f7fafc;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2d3748;
            margin: 25px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #cbd5e0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #edf2f7;
            color: #2d3748;
            text-align: left;
            font-weight: bold;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: Courier, monospace;
        }
        .summary-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            background: #fdfdfd;
        }
        .summary-card h4 {
            margin: 0 0 5px 0;
            font-size: 10px;
            text-transform: uppercase;
            color: #718096;
            letter-spacing: 0.5px;
        }
        .summary-card p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }
        .grand-total {
            background-color: #ebf8ff !important;
            font-weight: bold;
        }
        .footer-sig {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #4a5568;
            padding-top: 5px;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
                color: #000;
            }
            .no-print {
                display: none;
            }
            .summary-card {
                border: 1px solid #000;
            }
            table, th, td {
                border: 1px solid #000;
            }
            th {
                background-color: #eee !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #2b6cb0; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cetak</button>
        <button onclick="window.close()" style="padding: 8px 16px; background-color: #718096; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 10px;">Tutup</button>
    </div>

    <div class="header">
        <h1>Laporan Keuangan SIM Santri An-Nawawiy</h1>
        <p>Pondok Pesantren An-Nawawiy</p>
    </div>

    <div class="meta-info">
        <div>
            <strong>Periode Laporan:</strong> 
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') : 'Awal' }}
            s/d
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') : 'Akhir' }}
        </div>
        <div>
            <strong>Tanggal Cetak:</strong> {{ now()->locale('id')->isoFormat('D MMMM Y HH:mm') }}
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-card">
            <h4>Total Tunai (Cash)</h4>
            <p>Rp {{ number_format($totalCash, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <h4>Total Cashless (Duitku)</h4>
            <p>Rp {{ number_format($totalCashless, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card" style="background-color: #ebf8ff; border-color: #bee3f8;">
            <h4>Total Pendapatan</h4>
            <p style="color: #2b6cb0;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Section Cash -->
    <div class="section-title">Bagian 1: Pembayaran Tunai (Cash)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal Bayar</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 25%;">Nama Santri</th>
                <th style="width: 30%;">Deskripsi Pembayaran</th>
                <th style="width: 15%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cashPayments as $payment)
                <tr>
                    <td>{{ $payment->paid_at ? $payment->paid_at->locale('id')->isoFormat('D/MM/Y HH:mm') : '-' }}</td>
                    <td class="font-mono">{{ $payment->billing->student->nis }}</td>
                    <td>{{ $payment->billing->student->full_name }}</td>
                    <td>{{ $payment->billing->title }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #718096; padding: 20px;">Tidak ada transaksi pembayaran tunai.</td>
                </tr>
            @endforelse
            <tr class="grand-total">
                <td colspan="4" class="text-right">TOTAL TUNAI (CASH):</td>
                <td class="text-right font-mono">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Section Cashless -->
    <div class="section-title">Bagian 2: Pembayaran Cashless (Duitku)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal Bayar</th>
                <th style="width: 12%;">NIS</th>
                <th style="width: 20%;">Nama Santri</th>
                <th style="width: 23%;">Deskripsi Pembayaran</th>
                <th style="width: 15%;">Referensi Duitku</th>
                <th style="width: 15%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cashlessPayments as $payment)
                <tr>
                    <td>{{ $payment->paid_at ? $payment->paid_at->locale('id')->isoFormat('D/MM/Y HH:mm') : '-' }}</td>
                    <td class="font-mono">{{ $payment->billing->student->nis }}</td>
                    <td>{{ $payment->billing->student->full_name }}</td>
                    <td>{{ $payment->billing->title }}</td>
                    <td class="font-mono" style="font-size: 10px;">{{ $payment->duitku_reference ?? '-' }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #718096; padding: 20px;">Tidak ada transaksi pembayaran cashless.</td>
                </tr>
            @endforelse
            <tr class="grand-total">
                <td colspan="5" class="text-right">TOTAL CASHLESS (DUITKU):</td>
                <td class="text-right font-mono">Rp {{ number_format($totalCashless, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-sig">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <div class="signature-line">Bendahara Pesantren</div>
        </div>
    </div>
</body>
</html>
