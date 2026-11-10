<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 20px 30px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }

        .brand-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .alert-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 10px;
            color: #166534;
            margin-bottom: 15px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 10px;
        }

        .info-cell {
            width: 50%;
            vertical-align: top;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            font-size: 10px;
            line-height: 1.5;
        }

        .info-heading {
            font-size: 9px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .bill-table th {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-size: 10px;
            color: #475569;
            text-align: left;
        }

        .bill-table td {
            padding: 9px 10px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-table td {
            padding: 4px 10px;
            font-size: 10px;
        }

        .grand-total {
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px !important;
            font-size: 14px !important;
            font-weight: bold !important;
            color: #059669 !important;
        }

        .arrow {
            font-family: 'DejaVu Sans', sans-serif;
            font-weight: bold;
        }

        .footer-note {
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            font-size: 9px;
            color: #94a3b8;
            line-height: 1.4;
        }
    </style>
</head>
<body>

@php
    $user = $session->user ?? auth()->user();
    $charger = $session->charger;
    $location = $charger ? $charger->location : null;
    $vehicle = $session->vehicle ?? ($user ? $user->vehicles()->first() : null);

    $startTime = \Carbon\Carbon::parse($session->start_time);
    $endTime = $session->end_time ? \Carbon\Carbon::parse($session->end_time) : now();
    $durationSeconds = $startTime->diffInSeconds($endTime);
    $hours = intdiv($durationSeconds, 3600);
    $minutes = intdiv($durationSeconds % 3600, 60);
    $seconds = $durationSeconds % 60;
    $durationFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

    $tariff = $charger->price_per_kwh ?? 2500;
    $energy = (float)($session->energy_consumed_kwh ?? 0);
    $totalAmount = (float)($transaction->amount ?? $session->total_cost ?? 0);
@endphp

{{-- Header --}}
<table class="header-table">
    <tr>
        <td style="vertical-align: top;">
            <div class="brand-title">EV Charging Invoice</div>
            <div class="brand-sub">Faktur & Struk Resmi Pengisian Daya Kendaraan Listrik</div>
        </td>
        <td style="text-align: right; vertical-align: top;">
            <div><span class="status-badge">PEMBAYARAN LUNAS</span></div>
            <div style="font-size: 11px; font-weight: bold; margin-top: 6px;">{{ $transaction->invoice_number }}</div>
            <div style="font-size: 10px; color: #64748b;">{{ $transaction->created_at ? $transaction->created_at->format('d M Y, H:i') : $endTime->format('d M Y, H:i') }} WIB</div>
        </td>
    </tr>
</table>

{{-- Alert Box --}}
<div class="alert-box">
    <strong>Pembayaran Berhasil Dikonfirmasi.</strong> Saldo e-wallet telah dipotong sebesar <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>.
</div>

{{-- Two-Column Info --}}
<table class="info-table">
    <tr>
        <td class="info-cell">
            <div class="info-heading">DITAGIHKAN KEPADA:</div>
            <strong>{{ $user->nama ?? $user->name ?? 'Pengemudi EV' }}</strong><br>
            {{ $user->email ?? '-' }}<br>
            {{ $user->nomor_telepon ?? '-' }}<br>
            Kendaraan: <strong>{{ $vehicle ? ($vehicle->merek . ' ' . $vehicle->model) : 'Mobil Listrik' }}</strong><br>
            Plat No: <strong>{{ $vehicle->nomor_polisi ?? '-' }}</strong>
        </td>
        <td class="info-cell">
            <div class="info-heading">LOKASI & CHARGER SPKLU:</div>
            <strong>{{ $location->nama_lokasi ?? 'Stasiun SPKLU' }}</strong><br>
            <span style="color: #64748b;">{{ $location->alamat ?? '-' }}</span><br>
            Unit: <strong>{{ $charger->device_number ?? 'CHG-DEV' }}</strong><br>
            Tipe Soket: <strong>{{ $charger->connector_type ?? 'CCS2' }} ({{ round($charger->max_power_kw ?? 0) }} kW)</strong><br>
            Metode Bayar: <strong>{{ strtoupper($transaction->payment_method ?? 'E-WALLET') }}</strong>
        </td>
    </tr>
</table>

{{-- Table --}}
<table class="bill-table">
    <thead>
        <tr>
            <th>Deskripsi Pengisian</th>
            <th>Waktu & Durasi</th>
            <th style="text-align: right;">Baterai</th>
            <th style="text-align: right;">Volume (kWh)</th>
            <th style="text-align: right;">Tarif / kWh</th>
            <th style="text-align: right;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <strong>Pengisian Listrik EV</strong><br>
                <span style="font-size: 9px; color: #64748b;">Sesi #{{ $session->id }} - {{ $charger->connector_type ?? 'Fast Charging' }}</span>
            </td>
            <td>
                {{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }} WIB<br>
                <span style="font-size: 9px; color: #64748b;">Durasi: {{ $durationFormatted }}</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight: bold; color: #0284c7;">{{ $session->start_battery_soc ?? 25 }}% <span class="arrow">&#x2794;</span> {{ $session->end_battery_soc ?? ($vehicle->current_battery_soc ?? 80) }}%</span>
            </td>
            <td style="text-align: right; font-weight: bold;">
                {{ number_format($energy, 3) }} kWh
            </td>
            <td style="text-align: right;">
                Rp {{ number_format($tariff, 0, ',', '.') }}
            </td>
            <td style="text-align: right; font-weight: bold;">
                Rp {{ number_format($totalAmount, 0, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>

{{-- Summary --}}
<table class="summary-table">
    <tr>
        <td style="width: 70%; text-align: right; color: #64748b;">Subtotal Energi:</td>
        <td style="width: 30%; text-align: right; font-weight: bold;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td style="text-align: right; color: #64748b;">Biaya Layanan & Admin:</td>
        <td style="text-align: right; color: #059669; font-weight: bold;">Rp 0 (Bebas Biaya)</td>
    </tr>
    <tr>
        <td style="text-align: right; color: #64748b;">Pajak (PPN):</td>
        <td style="text-align: right;">Termasuk</td>
    </tr>
    <tr>
        <td class="grand-total" style="text-align: right;">TOTAL DIBAYAR:</td>
        <td class="grand-total" style="text-align: right;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
    </tr>
</table>

{{-- Footer --}}
<div class="footer-note">
    Terima kasih telah berkontribusi mengurangi emisi karbon dengan kendaraan listrik!<br>
    Invoice ini sah dan diterbitkan secara elektronik oleh sistem EV Charging Platform.
</div>

</body>
</html>
