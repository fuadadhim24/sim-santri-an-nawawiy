<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $billing->title }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            line-height: 1.4;
            max-width: 300px;
            /* Thermal printer width approx */
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .school-name {
            font-weight: bold;
            font-size: 16px;
        }

        .details {
            margin-bottom: 10px;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .total {
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="school-name">SIM-SANTRI</div>
        <div>An-Nawawiy</div>
        <div>Receipt / Bukti Pembayaran</div>
    </div>

    <div class="details">
        <div class="row">
            <span>Date:</span>
            <span>{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span>Invoice:</span>
            <span>#{{ str_pad($billing->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="row">
            <span>Student:</span>
            <span>{{ $billing->student->full_name }}</span>
        </div>
        <div class="row">
            <span>NIS:</span>
            <span>{{ $billing->student->nis }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="items">
        <div>{{ $billing->title }}</div>
        <div class="row">
            <span>Status:</span>
            <span>{{ $billing->status }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="row">
        <span>Amount:</span>
        <span>Rp {{ number_format($billing->original_amount, 0, ',', '.') }}</span>
    </div>
    @if ($billing->discount_applied > 0)
        <div class="row">
            <span>Discount:</span>
            <span>- Rp {{ number_format($billing->discount_applied, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="divider"></div>

    <div class="row total">
        <span>TOTAL:</span>
        <span>Rp {{ number_format($billing->final_amount, 0, ',', '.') }}</span>
    </div>

    <div class="footer">
        <p>Thank you for your payment.</p>
        <p>Simpan resi ini sebagai bukti pembayaran yang sah.</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Receipt</button>
    </div>

    @if ($billing->status == 'UNPAID')
        <div class="no-print" style="margin-top: 10px; text-align: center; color: red; font-weight: bold;">
            WARNING: THIS INVOICE IS UNPAID
        </div>
    @endif
</body>

</html>
