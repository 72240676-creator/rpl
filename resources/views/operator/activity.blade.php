@extends('layouts.app')

@section('title', 'Aktivitas Charging')

@section('content')
<style>
    .operator-page { width: min(1180px, 100%); margin: 0 auto; padding: 34px 24px 56px; color: #172554; }
    .page-back { margin-bottom: 20px; }
    .page-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 22px; }
    .page-eyebrow { margin-bottom: 8px; color: #047857; font-size: 10px; font-weight: 800; letter-spacing: 1.15px; text-transform: uppercase; }
    .page-heading h1 { margin: 0 0 7px; color: #0f172a; font-size: clamp(25px, 4vw, 32px); letter-spacing: -.6px; }
    .page-heading p { margin: 0; color: #64748b; font-size: 13px; line-height: 1.6; }
    .activity-count { flex: 0 0 auto; padding: 9px 13px; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 999px; font-size: 11px; font-weight: 750; }
    .summary-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 13px; margin-bottom: 20px; }
    .summary-card { padding: 16px 18px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 4px 14px rgba(15, 23, 42, .025); }
    .summary-label { margin-bottom: 7px; color: #64748b; font-size: 11px; }
    .summary-value { color: #0f172a; font-size: 22px; font-weight: 750; }
    .activity-panel { overflow: hidden; background: #fff; border: 1px solid #e2e8f0; border-radius: 17px; box-shadow: 0 7px 22px rgba(15, 23, 42, .04); }
    .panel-heading { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 19px 22px; border-bottom: 1px solid #eef2f7; }
    .panel-heading h2 { margin: 0 0 4px; color: #0f172a; font-size: 15px; }
    .panel-heading p { margin: 0; color: #94a3b8; font-size: 11px; }
    .session-table-wrap { overflow-x: auto; }
    .session-table { width: 100%; border-collapse: collapse; text-align: left; }
    .session-table th { padding: 12px 16px; color: #64748b; background: #f8fafc; font-size: 10px; font-weight: 800; letter-spacing: .45px; text-transform: uppercase; white-space: nowrap; }
    .session-table td { padding: 14px 16px; color: #475569; border-top: 1px solid #f1f5f9; font-size: 11px; vertical-align: middle; }
    .session-table tbody tr:hover { background: #f8fffb; }
    .session-device { color: #1e293b; font-weight: 750; }
    .session-subtext { margin-top: 4px; color: #94a3b8; font-size: 10px; }
    .session-cost { color: #0f172a; font-weight: 750; white-space: nowrap; }
    .status-badge { display: inline-flex; align-items: center; padding: 5px 9px; color: #047857; background: #d1fae5; border-radius: 999px; font-size: 10px; font-weight: 750; white-space: nowrap; }
    .status-badge.busy { color: #1d4ed8; background: #dbeafe; }
    .status-badge.warning { color: #b45309; background: #fef3c7; }
    .status-badge.offline { color: #64748b; background: #f1f5f9; }
    .empty-state { padding: 38px 20px; color: #64748b; text-align: center; }
    @media (max-width: 650px) { .operator-page { padding: 25px 14px 40px; } .page-heading { align-items: flex-start; flex-direction: column; } .summary-grid { gap: 9px; } .summary-card { padding: 13px; } .summary-value { font-size: 19px; } .panel-heading { padding: 16px; } }
</style>

<div class="operator-page">
    <div class="page-back">
        <x-back-link href="{{ route('operator.dashboard') }}" label="Kembali ke Dashboard Operator" />
    </div>

    <header class="page-heading">
        <div>
            <div class="page-eyebrow">Pusat aktivitas</div>
            <h1>Aktivitas charging</h1>
            <p>Pantau sesi pengisian terbaru, status pembayaran, energi, dan biaya di seluruh stasiun.</p>
        </div>
        <span class="activity-count">{{ $sessions->count() }} sesi tercatat</span>
    </header>

    <section class="summary-grid" aria-label="Ringkasan aktivitas charging">
        <div class="summary-card">
            <div class="summary-label">Sedang berlangsung</div>
            <div class="summary-value">{{ $stats['ongoing_sessions'] }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Sesi hari ini</div>
            <div class="summary-value">{{ $stats['sessions_today'] }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Energi hari ini</div>
            <div class="summary-value">{{ number_format($stats['energy_today'], 1, ',', '.') }} <span style="font-size:13px;font-weight:600;color:#64748b;">kWh</span></div>
        </div>
    </section>

    <section class="activity-panel" aria-labelledby="activity-list-title">
        <header class="panel-heading">
            <div>
                <h2 id="activity-list-title">Riwayat sesi</h2>
                <p>Diurutkan dari sesi terbaru.</p>
            </div>
        </header>
        <div class="session-table-wrap">
            <table id="chargingSessionsTable" class="session-table">
                <thead>
                    <tr>
                        <th>Charger & pengguna</th>
                        <th>Lokasi</th>
                        <th>Waktu mulai</th>
                        <th>Energi</th>
                        <th>Biaya</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
            @foreach ($sessions as $session)
                @php
                    $sessionStatus = match ($session->status) {
                        'ongoing' => ['Sedang mengisi', 'busy'],
                        'paid' => ['Selesai · Lunas', 'available'],
                        'completed' => ['Selesai · Belum dibayar', 'warning'],
                        default => [ucfirst($session->status), 'offline'],
                    };
                @endphp
                <tr>
                    <td>
                        <div class="session-device">{{ $session->charger?->device_number ?? 'Charger tidak tersedia' }}</div>
                        <div class="session-subtext">{{ $session->user?->nama ?? 'Pengguna tidak tersedia' }}</div>
                    </td>
                    <td>{{ $session->charger?->location?->nama_lokasi ?? 'Lokasi tidak tersedia' }}</td>
                    <td data-order="{{ $session->start_time?->timestamp ?? 0 }}">
                        {{ $session->start_time?->timezone('Asia/Jakarta')->format('d M Y, H:i') ?? '-' }} WIB
                    </td>
                    <td data-order="{{ $session->energy_consumed_kwh ?? 0 }}">
                        {{ $session->energy_consumed_kwh !== null ? number_format((float) $session->energy_consumed_kwh, 2, ',', '.') . ' kWh' : '-' }}
                    </td>
                    <td class="session-cost" data-order="{{ $session->total_cost }}">Rp{{ number_format((float) $session->total_cost, 0, ',', '.') }}</td>
                    <td><span class="status-badge {{ $sessionStatus[1] }}">{{ $sessionStatus[0] }}</span></td>
                </tr>
            @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
