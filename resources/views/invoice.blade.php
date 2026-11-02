<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Invoice Charging</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 30px;
        }

        .invoice {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin-bottom: 5px;
        }

        /*
         * Notifikasi pembayaran dan email
         */
        .success {
            text-align: center;
            padding: 12px;
            background: #e8f7ee;
            color: #198754;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        /*
         * Detail invoice
         */
        .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
        }

        /*
         * Tombol
         */
        .actions {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #198754;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 5px;
        }

        .btn-dashboard {
            background: #475569;
        }

    </style>

</head>

<body>

<div class="invoice">

    {{-- ====================================================== --}}
    {{-- HEADER --}}
    {{-- ====================================================== --}}

    <div class="header">

        <h1>
            INVOICE
        </h1>

        <p>
            EV Charging
        </p>

    </div>


    {{-- ====================================================== --}}
    {{-- NOTIFIKASI --}}
    {{-- ====================================================== --}}

    <div class="success">

        <div>
            PEMBAYARAN BERHASIL
        </div>

        <div style="
            margin-top: 5px;
            font-size: 14px;
            font-weight: normal;
        ">
            📧 Invoice telah dikirim ke Gmail Anda.
        </div>

    </div>


    {{-- ====================================================== --}}
    {{-- INFORMASI INVOICE --}}
    {{-- ====================================================== --}}

    <div class="row">

        <span>
            No. Invoice
        </span>

        <strong>
            {{ $transaction->invoice_number }}
        </strong>

    </div>


    <div class="row">

        <span>
            Session
        </span>

        <strong>
            #{{ $session->id }}
        </strong>

    </div>


    <div class="row">

        <span>
            Metode Pembayaran
        </span>

        <strong>
            {{ $transaction->payment_method }}
        </strong>

    </div>


    <div class="row">

        <span>
            Status
        </span>

        <strong>
            LUNAS
        </strong>

    </div>


    <br>


    {{-- ====================================================== --}}
    {{-- DETAIL CHARGING --}}
    {{-- ====================================================== --}}

    <h3>
        Detail Charging
    </h3>


    <div class="row">

        <span>
            Mulai
        </span>

        <span>
            {{ $session->start_time }}
        </span>

    </div>


    <div class="row">

        <span>
            Selesai
        </span>

        <span>
            {{ $session->end_time }}
        </span>

    </div>


    <div class="row">

        <span>
            Energi
        </span>

        <span>
            {{ $session->energy_consumed_kwh }} kWh
        </span>

    </div>


    {{-- ====================================================== --}}
    {{-- TOTAL PEMBAYARAN --}}
    {{-- ====================================================== --}}

    <div class="row total">

        <span>
            Total Pembayaran
        </span>

        <span>
            Rp {{ number_format(
                $transaction->amount,
                0,
                ',',
                '.'
            ) }}
        </span>

    </div>


    {{-- ====================================================== --}}
    {{-- TOMBOL --}}
    {{-- ====================================================== --}}

    <div class="actions">

        {{-- Download PDF --}}
        <a
            href="{{ route(
                'charging.invoice.pdf',
                $session->id
            ) }}"
            class="btn"
        >
            📄 Download Invoice PDF
        </a>


        {{-- Kembali ke Dashboard --}}
        <a
            href="{{ route('dashboard') }}"
            class="btn btn-dashboard"
        >
            ← Kembali ke Dashboard
        </a>

    </div>

</div>

</body>

</html>