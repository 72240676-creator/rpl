@extends('layouts.app')

@section('title', 'Riwayat Pengisian Daya · EV Charge')

@section('content')
<style>
    .history-page-wrap {
        width: 100%;
        max-width: 900px;
        margin: 20px auto 50px;
        padding: 0 16px;
        box-sizing: border-box;
    }

    .history-nav-wrap {
        margin-bottom: 20px;
    }

    .history-header {
        margin-bottom: 26px;
    }

    .history-header h1 {
        margin: 0 0 8px;
        font-size: clamp(24px, 4vw, 30px);
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    .history-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .history-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        margin-bottom: 22px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .history-card:hover {
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .history-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 18px 24px;
        background: linear-gradient(115deg, #f0fdf4 0%, #ecfdf5 100%);
        border-bottom: 1px solid #dcefe8;
    }

    .session-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .session-bolt-icon {
        display: grid;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        place-items: center;
        color: #059669;
        background: #d1fae5;
        border-radius: 12px;
        font-size: 20px;
    }

    .session-title-wrap h2 {
        margin: 0;
        color: #0f172a;
        font-size: 17px;
        font-weight: 700;
    }

    .session-date-text {
        margin-top: 3px;
        color: #64748b;
        font-size: 13px;
    }

    .status-badge-pill {
        flex: 0 0 auto;
        padding: 6px 14px;
        color: #065f46;
        background: #d1fae5;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .status-badge-pill.unpaid {
        color: #9a3412;
        background: #ffedd5;
    }

    .history-card-body {
        padding: 22px 24px 24px;
    }

    .station-location-block {
        margin-bottom: 18px;
        color: #1e293b;
        font-size: 15px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .station-location-block small {
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
    }

    .history-grid-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .detail-item-box {
        padding: 13px 16px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
    }

    .detail-item-label {
        display: block;
        margin-bottom: 5px;
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
    }

    .detail-item-value {
        color: #0f172a;
        font-size: 14px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .history-cost-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 18px 22px;
        color: #ffffff;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.2);
    }

    .cost-banner-label {
        font-size: 13px;
        opacity: 0.95;
        font-weight: 500;
    }

    .cost-banner-amount {
        margin-top: 4px;
        font-size: clamp(22px, 4vw, 26px);
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .history-payment-meta {
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px dashed #e2e8f0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.8;
    }

    .invoice-action-link {
        color: #059669;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .invoice-action-link:hover {
        color: #047857;
        text-decoration: underline;
    }

    .empty-state-box {
        padding: 56px 24px;
        text-align: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .empty-state-icon {
        font-size: 44px;
        margin-bottom: 12px;
    }

    .empty-state-box h2 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 20px;
        font-weight: 750;
    }

    .empty-state-box p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    @media (max-width: 640px) {
        .history-card-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 16px;
        }

        .history-card-body {
            padding: 16px;
        }

        .history-grid-details {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .history-cost-banner {
            padding: 16px;
        }
    }
</style>

<div class="history-page-wrap">
    {{-- Navigasi Kembali --}}
    <div class="history-nav-wrap">
        <x-back-link href="{{ route('dashboard') }}" label="Kembali ke Dashboard" />
    </div>

    {{-- Judul Halaman --}}
    <header class="history-header">
        <h1>Riwayat Charging</h1>
        <p>Lihat detail pemakaian energi dan biaya untuk setiap sesi pengisian kendaraan Anda.</p>
    </header>

    @forelse ($histories as $history)
        @php
            $transaction = $history->transactions->first();
            $charger = $history->charger;
            $isPaid = $history->status === 'paid' || $transaction !== null;
            $durationSeconds = $history->start_time && $history->end_time
                ? (int) $history->start_time->diffInSeconds($history->end_time)
                : null;
            $durationHours = $durationSeconds !== null ? intdiv($durationSeconds, 3600) : null;
            $durationMinutes = $durationSeconds !== null ? intdiv($durationSeconds % 3600, 60) : null;
            $remainingSeconds = $durationSeconds !== null ? $durationSeconds % 60 : null;
            $amount = $transaction?->amount ?? $history->total_cost ?? 0;
            $startTimeWib = $history->start_time?->copy()->timezone('Asia/Jakarta');
            $endTimeWib = $history->end_time?->copy()->timezone('Asia/Jakarta');
            $paidAtWib = $transaction?->paid_at?->copy()->timezone('Asia/Jakarta');
        @endphp

        <article class="history-card">
            <header class="history-card-header">
                <div class="session-title-wrap">
                    <span class="session-bolt-icon" aria-hidden="true">⚡</span>
                    <div>
                        <h2>Sesi Charging #{{ $history->id }}</h2>
                        <div class="session-date-text">
                            {{ $startTimeWib?->translatedFormat('d F Y, H:i') ?? 'Waktu tidak tersedia' }}
                        </div>
                    </div>
                </div>
                <span class="status-badge-pill {{ $isPaid ? '' : 'unpaid' }}">
                    {{ $isPaid ? 'Lunas' : 'Menunggu pembayaran' }}
                </span>
            </header>

            <div class="history-card-body">
                <div class="station-location-block">
                    <span>{{ $charger?->location?->nama_lokasi ?? 'Lokasi SPKLU' }}</span>
                    <small>
                        Charger {{ $charger?->device_number ?? '-' }}
                        @if ($charger?->connector_type)
                            · {{ $charger->connector_type }}
                        @endif
                        @if ($charger?->max_power_kw)
                            · {{ number_format((float) $charger->max_power_kw, 1, ',', '.') }} kW
                        @endif
                    </small>
                </div>

                <div class="history-grid-details">
                    <div class="detail-item-box">
                        <span class="detail-item-label">Waktu mulai</span>
                        <span class="detail-item-value">{{ $startTimeWib?->format('H:i:s') ?? '-' }}</span>
                    </div>
                    <div class="detail-item-box">
                        <span class="detail-item-label">Waktu selesai</span>
                        <span class="detail-item-value">{{ $endTimeWib?->format('H:i:s') ?? '-' }}</span>
                    </div>
                    <div class="detail-item-box">
                        <span class="detail-item-label">Durasi pengisian</span>
                        <span class="detail-item-value">
                            @if ($durationSeconds !== null)
                                {{ sprintf('%02d:%02d:%02d', $durationHours, $durationMinutes, $remainingSeconds) }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="detail-item-box">
                        <span class="detail-item-label">Energi digunakan</span>
                        <span class="detail-item-value">{{ number_format((float) ($history->energy_consumed_kwh ?? 0), 3, ',', '.') }} kWh</span>
                    </div>
                    <div class="detail-item-box">
                        <span class="detail-item-label">Tarif charger</span>
                        <span class="detail-item-value">
                            @if ($charger?->price_per_kwh !== null)
                                Rp {{ number_format((float) $charger->price_per_kwh, 0, ',', '.') }} / kWh
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="detail-item-box">
                        <span class="detail-item-label">Kendaraan</span>
                        <span class="detail-item-value">
                            {{ trim(($history->vehicle?->merek ?? '') . ' ' . ($history->vehicle?->model ?? '')) ?: '-' }}
                        </span>
                    </div>
                </div>

                <div class="history-cost-banner">
                    <div>
                        <div class="cost-banner-label">{{ $isPaid ? 'Total dibayarkan' : 'Total tagihan' }}</div>
                        <div class="cost-banner-amount">Rp {{ number_format((float) $amount, 0, ',', '.') }}</div>
                    </div>
                    <span aria-hidden="true" style="font-size: 26px;">💳</span>
                </div>

                @if ($transaction)
                    <div class="history-payment-meta">
                        Pembayaran: {{ ucfirst($transaction->payment_method ?? 'e-wallet') }}
                        @if ($transaction->paid_at)
                            · {{ $paidAtWib->format('d/m/Y H:i') }}
                        @endif
                        @if ($transaction->invoice_number)
                            <br>No. invoice: {{ $transaction->invoice_number }}
                        @endif
                        <br>
                        <a class="invoice-action-link" href="{{ route('charging.invoice', $history->id) }}">Lihat invoice →</a>
                        ·
                        <a class="invoice-action-link" href="{{ route('charging.invoice.pdf', $history->id) }}">Unduh PDF</a>
                    </div>
                @endif
            </div>
        </article>
    @empty
        <section class="empty-state-box">
            <div class="empty-state-icon" aria-hidden="true">🔋</div>
            <h2>Belum Ada Riwayat Charging</h2>
            <p>Riwayat pengisian dan rincian biayanya akan muncul di sini setelah sesi charging selesai.</p>
        </section>
    @endforelse
</div>
@endsection
