<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Invoice {{ $transaction->invoice_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin-bottom: 5px;
        }

        .success {
            text-align: center;
            border: 1px solid #198754;
            padding: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .label {
            width: 40%;
        }

        .total td {
            font-weight: bold;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
        }
    </style>
</head>

<body>

<div class="header">
    <h1>INVOICE</h1>
    <div>EV Charging</div>
</div>

<div class="success">
    PEMBAYARAN BERHASIL - LUNAS
</div>

<table>

    <tr>
        <td class="label">No. Invoice</td>
        <td>{{ $transaction->invoice_number }}</td>
    </tr>

    <tr>
        <td>Session</td>
        <td>#{{ $session->id }}</td>
    </tr>

    <tr>
        <td>Metode Pembayaran</td>
        <td>{{ $transaction->payment_method }}</td>
    </tr>

    <tr>
        <td>Status</td>
        <td>LUNAS</td>
    </tr>

</table>

<h3>Detail Charging</h3>

<table>

    <tr>
        <td class="label">Waktu Mulai</td>
        <td>{{ $session->start_time }}</td>
    </tr>

    <tr>
        <td>Waktu Selesai</td>
        <td>{{ $session->end_time }}</td>
    </tr>

    <tr>
        <td>Energi</td>
        <td>{{ $session->energy_consumed_kwh }} kWh</td>
    </tr>

    <tr class="total">
        <td>Total Pembayaran</td>
        <td>
            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
        </td>
    </tr>

</table>

<div class="footer">
    Terima kasih telah menggunakan layanan EV Charging.
</div>

</body>

</html>