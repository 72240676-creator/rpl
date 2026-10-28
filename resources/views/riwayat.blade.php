<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Charging</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px 20px 56px;
            color: #172554;
            background: #f1f5f9;
            font-family: Inter, Arial, sans-serif;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            padding: 10px 15px;
            color: #fff;
            background: #1e293b;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s, transform .2s;
        }
        .back:hover { background: #334155; transform: translateY(-1px); }
        .history-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 18px; margin-bottom: 26px; }
        .page-heading { margin: 0 0 8px; font-size: clamp(25px, 4vw, 32px); }
        .intro { margin: 0; color: #64748b; line-height: 1.6; }
        .card {
            overflow: hidden;
            margin-bottom: 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 20px 24px;
            background: linear-gradient(115deg, #ecfdf5, #f0fdfa);
            border-bottom: 1px solid #dcefe8;
        }
        .session-title { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .bolt {
            display: grid;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            place-items: center;
            color: #059669;
            background: #d1fae5;
            border-radius: 13px;
            font-size: 21px;
        }
        .session-title h2 { margin: 0; color: #0f172a; font-size: 18px; }
        .session-date { margin-top: 4px; color: #64748b; font-size: 13px; }
        .badge {
            flex: 0 0 auto;
            padding: 7px 11px;
            color: #047857;
            background: #d1fae5;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }
        .badge.unpaid { color: #b45309; background: #fef3c7; }
        .card-body { padding: 22px 24px 24px; }
        .location { margin-bottom: 18px; color: #334155; font-weight: 650; }
        .location small { display: block; margin-top: 5px; color: #64748b; font-weight: 400; }
        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .detail {
            min-width: 0;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 12px;
        }
        .detail-label { display: block; margin-bottom: 6px; color: #64748b; font-size: 12px; }
        .detail-value { color: #1e293b; font-size: 14px; font-weight: 650; overflow-wrap: anywhere; }
        .expense {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 18px;
            padding: 18px 20px;
            color: #fff;
            background: linear-gradient(115deg, #047857, #059669);
            border-radius: 14px;
        }
        .expense-label { font-size: 13px; opacity: .9; }
        .expense-amount { margin-top: 4px; font-size: clamp(22px, 5vw, 28px); font-weight: 750; }
        .payment-info { margin-top: 14px; color: #64748b; font-size: 13px; line-height: 1.7; }
        .invoice-link { color: #047857; font-weight: 650; text-decoration: none; }
        .invoice-link:hover { text-decoration: underline; }
        .empty {
            padding: 48px 24px;
            text-align: center;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }
        .empty-icon { font-size: 40px; }
        .empty h2 { margin: 12px 0 8px; color: #1e293b; font-size: 20px; }
        .empty p { margin: 0; color: #64748b; line-height: 1.6; }
        @media (max-width: 600px) {
            body { padding: 22px 14px 40px; }
            .history-heading { align-items: flex-start; flex-direction: column; }
            .card-header { align-items: flex-start; padding: 17px; }
            .card-body { padding: 17px; }
            .details { gap: 9px; }
            .detail { padding: 12px; }
            .expense { padding: 16px; }
        }
        @media (max-width: 380px) {
            .details { grid-template-columns: 1fr; }
            .badge { font-size: 11px; }
        }
    </style>
</head>
<body>
<main class="container">
    <a href="{{ route('dashboard') }}" class="back">← Kembali ke Dashboard</a>

    <header class="history-heading">
        <div>
            <h1 class="page-heading">Riwayat Charging</h1>
            <p class="intro">Lihat detail pemakaian energi dan biaya untuk setiap sesi pengisian kendaraan Anda.</p>
        </div>
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

        <article class="card">
            <header class="card-header">
                <div class="session-title">
                    <span class="bolt" aria-hidden="true">⚡</span>
                    <div>
                        <h2>Sesi Charging #{{ $history->id }}</h2>
                        <div class="session-date">
                            {{ $startTimeWib?->translatedFormat('d F Y, H:i') ?? 'Waktu tidak tersedia' }}
                        </div>
                    </div>
                </div>
                <span class="badge {{ $isPaid ? '' : 'unpaid' }}">
                    {{ $isPaid ? 'Lunas' : 'Menunggu pembayaran' }}
                </span>
            </header>

            <div class="card-body">
                <div class="location">
                    {{ $charger?->location?->nama_lokasi ?? 'Lokasi SPKLU' }}
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

                <div class="details">
                    <div class="detail">
                        <span class="detail-label">Waktu mulai</span>
                        <span class="detail-value">{{ $startTimeWib?->format('H:i:s') ?? '-' }}</span>
                    </div>
                    <div class="detail">
                        <span class="detail-label">Waktu selesai</span>
                        <span class="detail-value">{{ $endTimeWib?->format('H:i:s') ?? '-' }}</span>
                    </div>
                    <div class="detail">
                        <span class="detail-label">Durasi pengisian</span>
                        <span class="detail-value">
                            @if ($durationSeconds !== null)
                                {{ sprintf('%02d:%02d:%02d', $durationHours, $durationMinutes, $remainingSeconds) }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="detail">
                        <span class="detail-label">Energi digunakan</span>
                        <span class="detail-value">{{ number_format((float) ($history->energy_consumed_kwh ?? 0), 3, ',', '.') }} kWh</span>
                    </div>
                    <div class="detail">
                        <span class="detail-label">Tarif charger</span>
                        <span class="detail-value">
                            @if ($charger?->price_per_kwh !== null)
                                Rp {{ number_format((float) $charger->price_per_kwh, 0, ',', '.') }} / kWh
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="detail">
                        <span class="detail-label">Kendaraan</span>
                        <span class="detail-value">
                            {{ trim(($history->vehicle?->merek ?? '') . ' ' . ($history->vehicle?->model ?? '')) ?: '-' }}
                        </span>
                    </div>
                </div>

                <div class="expense">
                    <div>
                        <div class="expense-label">{{ $isPaid ? 'Total dibayarkan' : 'Total tagihan' }}</div>
                        <div class="expense-amount">Rp {{ number_format((float) $amount, 0, ',', '.') }}</div>
                    </div>
                    <span aria-hidden="true" style="font-size: 26px;">💳</span>
                </div>

                @if ($transaction)
                    <div class="payment-info">
                        Pembayaran: {{ ucfirst($transaction->payment_method ?? 'e-wallet') }}
                        @if ($transaction->paid_at)
                            · {{ $paidAtWib->format('d/m/Y H:i') }}
                        @endif
                        @if ($transaction->invoice_number)
                            <br>No. invoice: {{ $transaction->invoice_number }}
                        @endif
                        <br>
                        <a class="invoice-link" href="{{ route('charging.invoice', $history->id) }}">Lihat invoice →</a>
                        ·
                        <a class="invoice-link" href="{{ route('charging.invoice.pdf', $history->id) }}">Unduh PDF</a>
                    </div>
                @endif
            </div>
        </article>
    @empty
        <section class="empty">
            <div class="empty-icon" aria-hidden="true">🔋</div>
            <h2>Belum Ada Riwayat Charging</h2>
            <p>Riwayat pengisian dan rincian biayanya akan muncul di sini setelah sesi charging selesai.</p>
        </section>
    @endforelse
</main>
</body>
</html>
