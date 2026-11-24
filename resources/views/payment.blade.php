@extends('layouts.app')

@section('title', 'Review Pembayaran · EV Charge')

@section('content')
@php
    $startTime = $session->start_time;
    $endTime = $session->end_time ?? now();
    $durationMinutes = $startTime ? max(1, (int) round($startTime->diffInMinutes($endTime))) : null;
    $hours = $durationMinutes ? floor($durationMinutes / 60) : 0;
    $mins = $durationMinutes ? ($durationMinutes % 60) : 0;
    $durationText = $durationMinutes 
        ? ($hours > 0 ? "{$hours} jam {$mins} mnt" : "{$mins} menit")
        : '-';

    $userSaldo = (float) (Auth::user()->saldo ?? 0);
    $totalCost = (float) ($session->total_cost ?? 0);
    $reservedAmount = (float) ($session->reserved_amount ?? 0);
    $isPrepaid = $reservedAmount > 0;
    $refundAmount = $isPrepaid ? max(0, $reservedAmount - $totalCost) : 0;
    $isSaldoCukup = $isPrepaid ? true : ($userSaldo >= $totalCost);
    $saldoAfterPayment = $isPrepaid ? ($userSaldo + $refundAmount) : ($userSaldo - $totalCost);
    $potentialPoints = (int) floor($totalCost / 10000);
@endphp

<style>
    .payment-page-container {
        width: 100%;
        max-width: 540px;
        margin: 20px auto 40px;
        padding: 0 16px;
        box-sizing: border-box;
    }

    .payment-nav-header {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .payment-heading-wrap h1 {
        margin: 4px 0 6px;
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    .payment-heading-wrap p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.5;
    }

    .payment-card-box {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        padding: 28px 24px;
    }

    /* Alert Box */
    .alert-box {
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* Tagihan Hero Section */
    .amount-hero {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 24px 20px;
        text-align: center;
        margin-bottom: 24px;
        position: relative;
    }

    .amount-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #dcfce7;
        color: #15803d;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .amount-badge-pill .dot {
        width: 7px;
        height: 7px;
        background: #16a34a;
        border-radius: 50%;
        display: inline-block;
    }

    .amount-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .amount-value {
        font-size: 34px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    .amount-prepaid-note {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #cbd5e1;
        font-size: 13px;
        color: #334155;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Detail Sesi Section */
    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .details-table-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 24px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-val {
        color: #0f172a;
        font-weight: 600;
        text-align: right;
    }

    /* Payment Method Box */
    .wallet-box {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 20px;
        transition: all 0.2s ease;
    }

    .wallet-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .wallet-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .wallet-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .wallet-name {
        font-weight: 700;
        font-size: 15px;
        color: #0f172a;
    }

    .wallet-balance {
        font-size: 13px;
        color: #64748b;
        margin-top: 2px;
    }

    .wallet-status-badge {
        font-size: 12px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 8px;
    }

    .status-ok {
        background: #dcfce7;
        color: #166534;
    }

    .status-insufficient {
        background: #fee2e2;
        color: #991b1b;
    }

    .wallet-aftermath {
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: #64748b;
    }

    /* Loyalty Point Card */
    .reward-box {
        background: #fefce8;
        border: 1px solid #fef08a;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #854d0e;
        font-weight: 600;
    }

    /* Action Buttons */
    .pay-btn-primary {
        width: 100%;
        background: #059669;
        color: #ffffff;
        border: none;
        border-radius: 14px;
        padding: 16px 20px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35);
        transition: all 0.2s ease;
    }

    .pay-btn-primary:hover {
        background: #047857;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(5, 150, 105, 0.45);
    }

    .pay-btn-primary:active {
        transform: translateY(0);
    }

    .pay-btn-disabled {
        width: 100%;
        background: #cbd5e1;
        color: #64748b;
        border: none;
        border-radius: 14px;
        padding: 16px 20px;
        font-size: 15px;
        font-weight: 700;
        cursor: not-allowed;
        text-align: center;
    }

    .topup-btn-alert {
        display: block;
        margin-top: 10px;
        width: 100%;
        background: #0284c7;
        color: white;
        text-align: center;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        box-sizing: border-box;
        transition: background 0.2s;
    }

    .topup-btn-alert:hover {
        background: #0369a1;
    }

    .security-note {
        margin-top: 20px;
        text-align: center;
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
</style>

<div class="payment-page-container">
    {{-- Header Navigasi & Judul --}}
    <div class="payment-nav-header">
        <x-back-link href="{{ route('charging.session', $session->id) }}" label="Kembali ke sesi" />
        <div class="payment-heading-wrap">
            <h1>Review pembayaran</h1>
            <p>Periksa rincian sesi pengisian daya Anda sebelum menyelesaikan pembayaran.</p>
        </div>
    </div>

    {{-- Notifikasi Error Jika Ada --}}
    @if (session('error'))
        <div class="alert-box alert-danger">
            <span>⚠️</span>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert-box alert-danger" role="alert">
            <span>⚠️</span>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="payment-card-box">
        {{-- Total Tagihan Hero Box --}}
        <div class="amount-hero">
            <div class="amount-badge-pill">
                <span class="dot"></span> Sesi Selesai
            </div>
            <div class="amount-label">Total Tagihan</div>
            <div class="amount-value">Rp {{ number_format($session->total_cost, 0, ',', '.') }}</div>

            @if($isPrepaid)
                <div class="amount-prepaid-note">
                    <span>Dana Reservasi Awal (Deposit)</span>
                    <strong>Rp {{ number_format($reservedAmount, 0, ',', '.') }}</strong>
                </div>
                @if($refundAmount > 0)
                    <div class="amount-prepaid-note" style="border-top: none; padding-top: 4px; color: #15803d;">
                        <span>Pengembalian Selisih Saldo</span>
                        <strong>+Rp {{ number_format($refundAmount, 0, ',', '.') }}</strong>
                    </div>
                @endif
            @endif
        </div>

        {{-- Rincian Sesi Pengisian --}}
        <div class="section-title">
            <span>⚡</span> Rincian Sesi Pengisian #{{ $session->id }}
        </div>
        <div class="details-table-card">
            <div class="detail-row">
                <span class="detail-label">Stasiun SPKLU</span>
                <span class="detail-val">{{ $charger->location->nama_lokasi ?? 'Stasiun SPKLU' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Unit & Konektor</span>
                <span class="detail-val">{{ $charger->device_number ?? ('Charger #' . $charger->id_charger) }} ({{ $charger->connector_type ?? 'Tipe 2' }})</span>
            </div>
            @if($session->vehicle)
            <div class="detail-row">
                <span class="detail-label">Kendaraan Listrik</span>
                <span class="detail-val">{{ $session->vehicle->merek }} {{ $session->vehicle->model }} ({{ $session->vehicle->nomor_polisi }})</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Energi Terisi</span>
                <span class="detail-val">{{ number_format($session->energy_consumed_kwh ?? 0, 3, ',', '.') }} kWh</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Durasi Pengisian</span>
                <span class="detail-val">{{ $durationText }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tarif Listrik</span>
                <span class="detail-val">Rp {{ number_format($charger->price_per_kwh ?? 2500, 0, ',', '.') }} / kWh</span>
            </div>
            @if($session->start_battery_soc !== null && $session->end_battery_soc !== null)
            <div class="detail-row">
                <span class="detail-label">Persentase Baterai</span>
                <span class="detail-val">{{ $session->start_battery_soc }}% → {{ $session->end_battery_soc }}% (+{{ max(0, $session->end_battery_soc - $session->start_battery_soc) }}%)</span>
            </div>
            @endif
        </div>

        {{-- Opsi Pembayaran / Dompet Saldo --}}
        <div class="section-title">
            <span>💳</span> Metode Pembayaran
        </div>
        <div class="wallet-box">
            <div class="wallet-header">
                <div class="wallet-left">
                    <div class="wallet-icon">👛</div>
                    <div>
                        <div class="wallet-name">Saldo EV Wallet</div>
                        <div class="wallet-balance">
                            Sisa saldo: <strong style="color: #0f172a;">Rp {{ number_format($userSaldo, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                @if($isPrepaid)
                    <span class="wallet-status-badge status-ok">Dipotong Otomatis</span>
                @elseif($isSaldoCukup)
                    <span class="wallet-status-badge status-ok">✓ Saldo Cukup</span>
                @else
                    <span class="wallet-status-badge status-insufficient">⚠️ Kurang</span>
                @endif
            </div>

            @if(!$isPrepaid)
                <div class="wallet-aftermath">
                    <span>Estimasi Sisa Saldo Setelah Bayar:</span>
                    <strong style="color: {{ $saldoAfterPayment >= 0 ? '#16a34a' : '#dc2626' }};">
                        Rp {{ number_format(max(0, $saldoAfterPayment), 0, ',', '.') }}
                    </strong>
                </div>
            @endif
        </div>

        {{-- Point Rewards --}}
        @if($potentialPoints > 0)
            <div class="reward-box">
                <span style="font-size: 16px;">🎁</span>
                <span>Dapatkan <strong>+{{ $potentialPoints }} Poin EV</strong> dari transaksi pengisian ini!</span>
            </div>
        @endif

        {{-- Tombol Aksi Pembayaran --}}
        @if($isSaldoCukup)
            <form action="{{ route('charging.pay', $session->id) }}" method="POST" id="formPayment">
                @csrf
                <button type="submit" class="pay-btn-primary" id="btnPay">
                    <span>Konfirmasi & Bayar</span>
                    <span>Rp {{ number_format($session->total_cost, 0, ',', '.') }} ➔</span>
                </button>
            </form>
        @else
            <button type="button" class="pay-btn-disabled" disabled>
                Saldo Tidak Mencukupi (Kurang Rp {{ number_format($totalCost - $userSaldo, 0, ',', '.') }})
            </button>
            @if(Route::has('topup.index'))
                <a href="{{ route('topup.index') }}" class="topup-btn-alert">
                    ➕ Top Up Saldo Sekarang
                </a>
            @endif
        @endif

        {{-- Security Note --}}
        <div class="security-note">
            <span>🔒</span> Transaksi aman & terenkripsi oleh sistem EV Charge
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formPayment');
        const btn = document.getElementById('btnPay');
        if (form && btn) {
            form.addEventListener('submit', function () {
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';
                btn.innerHTML = '<span>Memproses Pembayaran...</span> <span>⏳</span>';
            });
        }
    });
</script>
@endsection
