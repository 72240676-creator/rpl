@extends('layouts.app')

@section('title', 'Invoice #' . ($transaction->invoice_number ?? $session->id))

@section('content')
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

<style>
    .invoice-wrapper {
        max-width: 820px;
        margin: 0 auto;
        padding: 24px 20px 60px;
    }

    /* Top Action Bar (Tombol Kembali di Kiri Atas) */
    .top-action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .btn-top-pdf {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        color: #0284c7;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-top-pdf:hover {
        background: #e0f2fe;
        transform: translateY(-1px);
    }

    /* Main Invoice Paper Card */
    .invoice-card {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        padding: 40px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.04);
        position: relative;
    }

    /* Card Header */
    .invoice-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        padding-bottom: 24px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 24px;
    }

    .brand-logo-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #10b981, #0284c7);
        color: white;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .invoice-badge-status {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #059669;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Success Alert Banner */
    .success-alert-strip {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #166534;
        font-size: 13px;
    }

    /* Two-Column Info Grid */
    .info-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px 24px;
        border: 1px solid #e2e8f0;
    }

    .info-section-title {
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .info-item {
        font-size: 13px;
        margin-bottom: 4px;
        color: #334155;
    }

    .info-item strong {
        color: #0f172a;
    }

    /* Itemized Bill Table */
    .bill-table-wrapper {
        margin-bottom: 28px;
        overflow-x: auto;
    }

    .bill-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }

    .bill-table th {
        background: #f1f5f9;
        padding: 12px 16px;
        color: #475569;
        font-weight: 700;
        border-bottom: 1px solid #e2e8f0;
    }

    .bill-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .bill-summary-row {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 8px;
        font-size: 13px;
        color: #64748b;
    }

    .bill-summary-row span:first-child {
        width: 200px;
        text-align: right;
        padding-right: 20px;
    }

    .bill-summary-row span:last-child {
        width: 140px;
        text-align: right;
        font-weight: 600;
        color: #0f172a;
    }

    .bill-grand-total {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-top: 14px;
        margin-top: 14px;
        border-top: 2px dashed #cbd5e1;
    }

    .bill-grand-total span:first-child {
        width: 200px;
        text-align: right;
        padding-right: 20px;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .bill-grand-total span:last-child {
        width: 180px;
        text-align: right;
        font-size: 24px;
        font-weight: 900;
        color: #059669;
    }

    /* Invoice Footer Note */
    .invoice-footer-note {
        text-align: center;
        padding-top: 24px;
        border-top: 1px solid #f1f5f9;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.5;
    }

    /* Bottom Action Buttons */
    .bottom-actions-row {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .btn-download-pdf {
        background: #10b981;
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
        transition: transform 0.15s ease, background 0.15s ease;
    }

    .btn-download-pdf:hover {
        background: #059669;
        transform: translateY(-2px);
    }

    .btn-history-secondary {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
    }

    .btn-history-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    @media (max-width: 640px) {
        .invoice-card {
            padding: 24px 18px;
        }
        .info-two-col {
            grid-template-columns: 1fr;
        }
        .bill-summary-row span:first-child, .bill-grand-total span:first-child {
            width: auto;
            flex: 1;
        }
    }
</style>

<div class="invoice-wrapper">

    {{-- Top Action Bar: Tombol Kembali di Kiri Atas, Unduh PDF di Kanan --}}
    <div class="top-action-bar">
        <div>
            <x-back-link href="{{ route('dashboard') }}" label="Kembali ke Dashboard" />
        </div>

        <div>
            <a href="{{ route('charging.invoice.pdf', $session->id) }}" class="btn-top-pdf">
                <span>📄</span> <span>Unduh PDF</span>
            </a>
        </div>
    </div>

    {{-- Kartu Utama Invoice --}}
    <div class="invoice-card">

        {{-- Header Kartu Invoice --}}
        <div class="invoice-card-header">
            <div class="brand-logo-title">
                <div class="brand-icon">⚡</div>
                <div>
                    <h3 style="margin: 0 0 4px; font-size: 22px; font-weight: 800; color: #0f172a;">
                        EV Charging Invoice
                    </h3>
                    <p style="margin: 0; font-size: 13px; color: #64748b;">
                        Faktur & Struk Resmi Pengisian Daya Kendaraan Listrik
                    </p>
                </div>
            </div>

            <div style="text-align: right;">
                <div class="invoice-badge-status">
                    <span>✓</span> <span>PEMBAYARAN LUNAS</span>
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-top: 8px;">
                    {{ $transaction->invoice_number }}
                </div>
                <div style="font-size: 12px; color: #64748b;">
                    {{ $transaction->created_at ? $transaction->created_at->format('d M Y, H:i') : $endTime->format('d M Y, H:i') }} WIB
                </div>
            </div>
        </div>

        {{-- Success Alert Banner --}}
        <div class="success-alert-strip">
            <span style="font-size: 20px;">🎉</span>
            <div>
                <strong>Pembayaran Berhasil Dikonfirmasi!</strong> Saldo e-wallet Anda telah dipotong sebesar <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong> dan salinan struk telah dikirimkan ke email <em>{{ $user->email ?? 'Anda' }}</em>.
            </div>
        </div>

        {{-- Informasi 2 Kolom (Customer & Lokasi Stasiun) --}}
        <div class="info-two-col">
            {{-- Kolom Kiri: Pelanggan & Kendaraan --}}
            <div>
                <div class="info-section-title">Ditagihkan Kepada:</div>
                <div class="info-item"><strong>{{ $user->nama ?? $user->name ?? 'Pengemudi EV' }}</strong></div>
                <div class="info-item">{{ $user->email ?? '-' }}</div>
                <div class="info-item">📱 {{ $user->nomor_telepon ?? '-' }}</div>
                <div class="info-item" style="margin-top: 8px;">
                    🚗 Kendaraan: <strong>{{ $vehicle ? ($vehicle->merek . ' ' . $vehicle->model) : 'Mobil Listrik' }}</strong>
                </div>
                <div class="info-item">
                    Plat No: <strong>{{ $vehicle->nomor_polisi ?? '-' }}</strong>
                </div>
            </div>

            {{-- Kolom Kanan: Detail SPKLU & Charger --}}
            <div>
                <div class="info-section-title">Lokasi & Charger SPKLU:</div>
                <div class="info-item"><strong>{{ $location->nama_lokasi ?? 'Stasiun SPKLU' }}</strong></div>
                <div class="info-item" style="color: #64748b;">{{ $location->alamat ?? '-' }}</div>
                <div class="info-item" style="margin-top: 8px;">
                    🔌 Unit: <strong>{{ $charger->device_number ?? 'CHG-DEV' }}</strong>
                </div>
                <div class="info-item">
                    Tipe Soket: <strong>{{ $charger->connector_type ?? 'CCS2' }} ({{ round($charger->max_power_kw ?? 0) }} kW)</strong>
                </div>
                <div class="info-item">
                    Metode Bayar: <strong>{{ strtoupper($transaction->payment_method ?? 'E-WALLET') }}</strong>
                </div>
            </div>
        </div>

        {{-- Rincian Pengisian Daya (Itemized Bill Table) --}}
        <div class="bill-table-wrapper">
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
                            <span style="font-size: 11px; color: #64748b;">Sesi #{{ $session->id }} • {{ $charger->connector_type ?? 'Fast Charging' }}</span>
                        </td>
                        <td>
                            {{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }} WIB<br>
                            <span style="font-size: 11px; color: #64748b;">Durasi: {{ $durationFormatted }}</span>
                        </td>
                        <td style="text-align: right;">
                            <span style="font-weight: 700; color: #0284c7;">{{ $session->start_battery_soc ?? 25 }}% ➔ {{ $session->end_battery_soc ?? ($vehicle->current_battery_soc ?? 80) }}%</span>
                        </td>
                        <td style="text-align: right; font-weight: 700;">
                            {{ number_format($energy, 3) }} kWh
                        </td>
                        <td style="text-align: right;">
                            Rp {{ number_format($tariff, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Ringkasan Total Tagihan --}}
        <div style="margin-bottom: 24px;">
            <div class="bill-summary-row">
                <span>Subtotal Energi:</span>
                <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>
            <div class="bill-summary-row">
                <span>Biaya Layanan & Admin:</span>
                <span style="color: #059669;">Rp 0 (Bebas Biaya)</span>
            </div>
            <div class="bill-summary-row">
                <span>Pajak (PPN):</span>
                <span>Termasuk</span>
            </div>
            <div class="bill-grand-total">
                <span>TOTAL DIBAYAR:</span>
                <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Catatan Kaki Resmi --}}
        <div class="invoice-footer-note">
            🌱 <strong>Terima kasih telah berkontribusi mengurangi emisi karbon dengan kendaraan listrik!</strong><br>
            Invoice ini sah dan diterbitkan secara elektronik oleh sistem EV Charging Platform.<br>
            Jika ada pertanyaan seputar tagihan ini, silakan hubungi Customer Service kami.
        </div>

    </div>

    {{-- Tombol Aksi Bawah --}}
    <div class="bottom-actions-row">
        <a href="{{ route('charging.invoice.pdf', $session->id) }}" class="btn-download-pdf">
            <span>📄</span> <span>Download Struk PDF</span>
        </a>
        <a href="{{ route('charging.history') }}" class="btn-history-secondary">
            <span>📜</span> <span>Lihat Riwayat Transaksi</span>
        </a>
        <a href="{{ route('locations.index') }}" class="btn-history-secondary">
            <span>🗺️</span> <span>Cari SPKLU Lain</span>
        </a>
    </div>

</div>
@endsection
