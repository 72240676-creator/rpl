@extends('layouts.app')

@section('title', 'Live Charging Telemetry')

@section('content')
@php
    $tariffPerKwh = $charger->price_per_kwh
        ?? $session->charger->price_per_kwh
        ?? 2500;

    $chargingPowerKw = (float) (
        $session->charger->max_power_kw
        ?? $charger->max_power_kw
        ?? 50
    );

    $connectorType = $session->charger->connector_type
        ?? $charger->connector_type
        ?? 'CCS2';

    $deviceNumber = $session->charger->device_number
        ?? $charger->device_number
        ?? 'CHG-DEV';

    $locationName = $session->charger->location->nama_lokasi
        ?? $charger->location->nama_lokasi
        ?? 'Stasiun SPKLU';

    $vehicleModel = $vehicle ? ($vehicle->merek . ' ' . $vehicle->model) : 'Kendaraan Listrik';
    $vehiclePlate = $vehicle->nomor_polisi ?? '-';
    $userSaldo = (float) (auth()->user()->saldo ?? 0);

    $initialBatterySoc = (int) (
        $session->start_battery_soc
        ?? ($vehicle ? $vehicle->current_battery_soc : 25)
        ?? 25
    );

    $batteryCapacity = (float) (
        ($vehicle ? $vehicle->battery_capacity_kwh : 60.0)
        ?? 60.0
    );

    $isCompleted = (
        strtolower($session->status) !== 'ongoing'
        && !is_null($session->end_time)
    );
@endphp

<style>
    .telemetry-container {
        max-width: 960px;
        margin: 0 auto;
        padding: 24px 20px 50px;
    }

    /* Header Bar */
    .telemetry-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 24px;
    }

    .header-badge-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .badge-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    /* Main Cockpit Card - TEMA CERAH & BERSIH */
    .cockpit-card {
        background: #ffffff;
        color: #0f172a;
        border-radius: 28px;
        border: 1px solid #e2e8f0;
        padding: 36px 30px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    /* Ambient Soft Glow */
    .ambient-glow {
        position: absolute;
        top: -60px;
        left: 50%;
        transform: translateX(-50%);
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, rgba(6, 182, 212, 0.05) 45%, rgba(255, 255, 255, 0) 70%);
        filter: blur(40px);
        pointer-events: none;
        z-index: 0;
    }

    .cockpit-content {
        position: relative;
        z-index: 1;
    }

    /* Station Info Strip */
    .station-strip {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 28px;
    }

    .station-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .station-sub {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .pulse-live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #059669;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 10px #10b981;
        animation: pulseLive 1.5s infinite;
    }

    @keyframes pulseLive {
        0% { transform: scale(0.9); opacity: 0.7; }
        50% { transform: scale(1.3); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.7; }
    }

    /* Central Battery Gauge (Tema Cerah) */
    .gauge-center-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 15px 0 35px;
    }

    .circular-gauge-container {
        position: relative;
        width: 250px;
        height: 250px;
    }

    .circular-gauge-svg {
        transform: rotate(-90deg);
        width: 250px;
        height: 250px;
    }

    .gauge-track {
        fill: none;
        stroke: #f1f5f9;
        stroke-width: 14;
    }

    .gauge-fill {
        fill: none;
        stroke: url(#cyanEmeraldGradient);
        stroke-width: 14;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.8s ease;
    }

    .gauge-inner-info {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .gauge-bolt-icon {
        font-size: 26px;
        margin-bottom: -2px;
        animation: bounceBolt 2s infinite;
    }

    @keyframes bounceBolt {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }

    .gauge-percentage {
        font-size: 54px;
        font-weight: 900;
        letter-spacing: -1.5px;
        line-height: 1;
        margin: 2px 0 6px;
        color: #0f172a;
    }

    .gauge-range-label {
        font-size: 13px;
        font-weight: 700;
        color: #0284c7;
        background: #e0f2fe;
        padding: 4px 12px;
        border-radius: 999px;
    }

    .gauge-eta-label {
        margin-top: 14px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Target Battery Selector */
    .target-selector-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 16px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }

    .target-label-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .target-label-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .target-pills-group {
        display: flex;
        gap: 8px;
    }

    .target-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .target-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }

    .target-btn.active {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    /* 4-Grid Telemetry Cards - TEMA CERAH */
    .telemetry-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .telemetry-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
    }

    .telemetry-item:hover {
        background: #ffffff;
        border-color: #10b981;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.08);
        transform: translateY(-2px);
    }

    .telemetry-lbl {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
    }

    .telemetry-val {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    .telemetry-unit {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-left: 2px;
    }

    .telemetry-sub {
        font-size: 12px;
        font-weight: 600;
        color: #0284c7;
        margin-top: 6px;
    }

    /* Vehicle & Saldo Info Bar */
    .vehicle-status-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    .veh-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .veh-icon {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .saldo-info {
        text-align: right;
    }

    /* Action Controls Bar */
    .control-actions-row {
        display: flex;
        gap: 14px;
        align-items: center;
    }

    .btn-speed-toggle {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 15px 22px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-speed-toggle:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .btn-speed-toggle.active {
        background: #0284c7;
        color: white;
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    .btn-stop-charging {
        flex: 1;
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white;
        border: none;
        padding: 16px 24px;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.22);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-stop-charging:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.32);
    }

    /* Modal Konfirmasi */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        max-width: 440px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        animation: modalIn 0.2s ease-out;
    }

    @keyframes modalIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    /* Completed View Card */
    .completed-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        padding: 36px 30px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .telemetry-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .cockpit-card {
            padding: 24px 18px;
        }
        .vehicle-status-bar {
            flex-direction: column;
            align-items: flex-start;
        }
        .saldo-info {
            text-align: left;
        }
    }
</style>

<div class="telemetry-container">

    <x-back-link href="{{ route('scan.charge') }}" label="Kembali ke Scan Charger" />

    {{-- Telemetry Header --}}
    <div class="telemetry-header">
        <div>
            <h2 style="margin: 0 0 4px; font-size: 24px; font-weight: 800; color: #0f172a;">
                ⚡ Live Telemetry Pengisian
            </h2>
            <p style="margin: 0; font-size: 14px; color: #64748b;">
                Monitoring telemetri arus, daya, energi, dan biaya berjalan secara real-time.
            </p>
        </div>

        <div class="header-badge-group">
            <span class="badge-pill">
                <span>📍</span> <span>{{ $locationName }}</span>
            </span>
            <span class="badge-pill">
                <span>🔌</span> <span>{{ $connectorType }} ({{ $deviceNumber }})</span>
            </span>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 14px 18px; border-radius: 14px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- TAMPILAN SESI AKTIF (ONGOING CHARGING) --}}
    @if(!$isCompleted)

        {{-- Cockpit Dashboard Utama - TEMA CERAH BERSIH --}}
        <div class="cockpit-card">
            <div class="ambient-glow"></div>
            <div class="cockpit-content">

                {{-- Bar Informasi Stasiun & Live Status --}}
                <div class="station-strip">
                    <div>
                        <h3 class="station-title">
                            <span style="color: #f59e0b;">⚡</span>
                            <span>{{ $deviceNumber }} • {{ $connectorType }}</span>
                        </h3>
                        <p class="station-sub">Daya Maksimal: <strong>{{ $chargingPowerKw }} kW</strong> | Tarif: <strong>Rp {{ number_format($tariffPerKwh, 0, ',', '.') }}/kWh</strong></p>
                    </div>

                    <div class="pulse-live-indicator">
                        <span class="pulse-dot"></span>
                        <span id="liveStatusText">Charging Aktif</span>
                    </div>
                </div>

                {{-- Central Circular Battery SoC Gauge (Cerah & Kontras) --}}
                <div class="gauge-center-wrapper">
                    <div class="circular-gauge-container">
                        <svg class="circular-gauge-svg" viewBox="0 0 250 250">
                            <defs>
                                <linearGradient id="cyanEmeraldGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#0284c7" />
                                    <stop offset="100%" stop-color="#10b981" />
                                </linearGradient>
                            </defs>
                            {{-- Track --}}
                            <circle class="gauge-track" cx="125" cy="125" r="105"></circle>
                            {{-- Fill --}}
                            <circle class="gauge-fill" id="gaugeFill" cx="125" cy="125" r="105" 
                                    stroke-dasharray="660" 
                                    stroke-dashoffset="660"></circle>
                        </svg>

                        <div class="gauge-inner-info">
                            <span class="gauge-bolt-icon" style="color: #f59e0b;">⚡</span>
                            <div class="gauge-percentage" id="batterySocText">{{ $initialBatterySoc }}%</div>
                            <div class="gauge-range-label" id="rangeAddedText">+0 km jangkauan</div>
                        </div>
                    </div>

                    <div class="gauge-eta-label">
                        <span>🕒</span>
                        <span id="etaTimeText">Estimasi ~25 menit menuju target 80%</span>
                    </div>
                </div>

                {{-- Selector Target Baterai (Battery Health Feature) --}}
                <div class="target-selector-box">
                    <div class="target-label-group">
                        <span style="font-size: 20px;">🛡️</span>
                        <div>
                            <div class="target-label-title">Batas Pengisian (Battery Health)</div>
                            <div style="font-size: 12px; color: #64748b;">Batas 80% direkomendasikan untuk menjaga keawetan baterai lithium</div>
                        </div>
                    </div>

                    <div class="target-pills-group">
                        <button type="button" class="target-btn active" data-target="80" onclick="setTargetLimit(80)">80% (Harian)</button>
                        <button type="button" class="target-btn" data-target="90" onclick="setTargetLimit(90)">90%</button>
                        <button type="button" class="target-btn" data-target="100" onclick="setTargetLimit(100)">100% (Trip)</button>
                    </div>
                </div>

                {{-- 4 Core Telemetry Cards --}}
                <div class="telemetry-grid">
                    {{-- 1. Daya Aktual --}}
                    <div class="telemetry-item">
                        <div class="telemetry-lbl">
                            <span style="color: #0284c7;">⚡</span> Daya Aktual
                        </div>
                        <div class="telemetry-val">
                            <span id="liveKwVal">{{ number_format($chargingPowerKw * 0.96, 1) }}</span>
                            <span class="telemetry-unit">kW</span>
                        </div>
                        <div class="telemetry-sub" id="powerStatusSub">Fast Charging Active</div>
                    </div>

                    {{-- 2. Tegangan & Arus --}}
                    <div class="telemetry-item">
                        <div class="telemetry-lbl">
                            <span style="color: #6366f1;">🔌</span> Volt & Arus
                        </div>
                        <div class="telemetry-val">
                            <span id="liveVoltVal">398</span><span class="telemetry-unit">V</span>
                        </div>
                        <div class="telemetry-sub" id="liveAmperageSub" style="color: #6366f1;">122.4 A (DC Flow)</div>
                    </div>

                    {{-- 3. Energi Terisi --}}
                    <div class="telemetry-item">
                        <div class="telemetry-lbl">
                            <span style="color: #10b981;">🔋</span> Energi Terisi
                        </div>
                        <div class="telemetry-val">
                            <span id="liveKwhVal">{{ number_format((float)($session->energy_consumed_kwh ?? 0), 3) }}</span>
                            <span class="telemetry-unit">kWh</span>
                        </div>
                        <div class="telemetry-sub" id="energyDurationSub" style="color: #64748b;">Durasi: <span id="durationClock">00:00:00</span></div>
                    </div>

                    {{-- 4. Biaya Berjalan --}}
                    <div class="telemetry-item">
                        <div class="telemetry-lbl">
                            <span style="color: #059669;">💰</span> Biaya Berjalan
                        </div>
                        <div class="telemetry-val" style="color: #059669;">
                            <span style="font-size: 16px; font-weight: 700;">Rp</span>
                            <span id="liveCostVal">{{ number_format((float)($session->total_cost ?? 0), 0, ',', '.') }}</span>
                        </div>
                        <div class="telemetry-sub" style="color: #64748b;">Rp {{ number_format($tariffPerKwh, 0, ',', '.') }}/kWh</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Bar Status Kendaraan & Saldo Pengemudi --}}
        <div class="vehicle-status-bar">
            <div class="veh-info">
                <div class="veh-icon">🚗</div>
                <div>
                    <h4 style="margin: 0 0 4px; font-size: 16px; font-weight: 700; color: #0f172a;">{{ $vehicleModel }}</h4>
                    <p style="margin: 0; font-size: 13px; color: #64748b;">
                        No. Polisi: <strong>{{ $vehiclePlate }}</strong> • Baterai Awal: <strong style="color: #0284c7;">{{ $initialBatterySoc }}%</strong> (Kapasitas: {{ $batteryCapacity }} kWh)
                    </p>
                    @if($vehicle)
                        <form action="{{ route('vehicles.discharge', $vehicle->id_vehicle) }}" method="POST" style="margin-top: 8px;" onsubmit="return confirm('Simulasikan mobil telah dipakai jalan sehingga baterai berkurang ke 20%?');">
                            @csrf
                            <button type="submit" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 3px 8px; font-size: 11px; font-weight: 600; color: #475569; cursor: pointer;">
                                🚗 Simulasi Pakai Jalan (Discharge ke 20%)
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="saldo-info">
                <span style="font-size: 12px; color: #64748b; font-weight: 600;">Saldo E-Wallet Anda:</span>
                <div style="font-size: 20px; font-weight: 800; color: #0f172a;">
                    Rp {{ number_format($userSaldo, 0, ',', '.') }}
                </div>
                <div id="saldoWarnText" style="font-size: 11px; color: #059669; font-weight: 600;">
                    ✓ Saldo aman untuk sesi pengisian ini
                </div>
            </div>
        </div>

        {{-- Tombol Kontrol & Stop Charging --}}
        <div class="control-actions-row">
            {{-- Demo Speed Switch (Untuk Pengujian Cepat vs Real-time) --}}
            <button type="button" class="btn-speed-toggle" id="btnSpeedToggle" onclick="toggleSpeedMode()">
                <span id="speedIcon">⏱️</span>
                <span id="speedLabel">Kecepatan: Real-Time (1x)</span>
            </button>

            {{-- Stop Button yang Membuka Modal Konfirmasi --}}
            <button type="button" class="btn-stop-charging" onclick="openStopModal()">
                <span>⏹</span>
                <span>Hentikan Pengisian & Selesaikan Tagihan</span>
            </button>
        </div>

        {{-- Form Stop Charging Sebenarnya (Disubmit saat konfirmasi) --}}
        <form action="{{ route('charging.stop', $session->id) }}" method="POST" id="realStopForm" style="display: none;">
            @csrf
            <input type="hidden" name="energy_consumed_kwh" id="formEnergyKwh" value="{{ $session->energy_consumed_kwh ?? 0 }}">
            <input type="hidden" name="total_cost" id="formTotalCost" value="{{ $session->total_cost ?? 0 }}">
            <input type="hidden" name="end_battery_soc" id="formEndSoc" value="{{ $initialBatterySoc }}">
        </form>

        {{-- Modal Konfirmasi Stop Pengisian --}}
        <div class="modal-overlay" id="stopConfirmModal">
            <div class="modal-card">
                <div style="width: 60px; height: 60px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px;">
                    ⏹
                </div>
                <h3 style="margin: 0 0 8px; font-size: 20px; font-weight: 800; color: #0f172a;">
                    Hentikan Pengisian Daya?
                </h3>
                <p style="margin: 0 0 20px; font-size: 13px; color: #64748b;">
                    Arus listrik ke kendaraan akan diputus dan rincian transaksi pengisian akan dikirimkan untuk pelunasan.
                </p>

                <div style="background: #f8fafc; border-radius: 12px; padding: 14px; margin-bottom: 20px; text-align: left; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #64748b;">Energi Terpakai:</span>
                        <strong id="modalKwhSummary">0.000 kWh</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #64748b;">Baterai Kendaraan:</span>
                        <strong id="modalSocSummary">0%</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 6px; border-top: 1px dashed #e2e8f0;">
                        <span style="color: #0f172a; font-weight: 700;">Total Biaya:</span>
                        <strong style="color: #2563eb; font-size: 15px;" id="modalCostSummary">Rp 0</strong>
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeStopModal()" style="flex: 1; padding: 12px; border-radius: 10px; border: 1px solid #cbd5e1; background: white; font-weight: 600; cursor: pointer; color: #475569;">
                        Kembali Mengisi
                    </button>
                    <button type="button" onclick="confirmStopCharging()" style="flex: 1.2; padding: 12px; border-radius: 10px; border: none; background: #dc2626; color: white; font-weight: 700; cursor: pointer;">
                        Ya, Selesaikan
                    </button>
                </div>
            </div>
        </div>

    {{-- TAMPILAN SESI SELESAI (COMPLETED) --}}
    @else

        <div class="completed-card">
            <div style="text-align: center; margin-bottom: 25px;">
                <div style="width: 70px; height: 70px; background: #ecfdf5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 16px;">
                    ✓
                </div>
                <h3 style="margin: 0 0 6px; font-size: 24px; font-weight: 800; color: #0f172a;">
                    Pengisian Selesai
                </h3>
                <p style="margin: 0; font-size: 14px; color: #64748b;">
                    Nozzle telah dinonaktifkan. Silakan cabut konektor dan lanjutkan ke pembayaran.
                </p>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; margin-bottom: 12px;">
                    <span style="color: #64748b;">Stasiun & Charger</span>
                    <strong>{{ $locationName }} ({{ $deviceNumber }})</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #64748b;">Waktu Pengisian</span>
                    <span>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #64748b;">Total kWh Terisi</span>
                    <strong>{{ number_format((float)($session->energy_consumed_kwh ?? 0), 3) }} kWh</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #64748b;">Tarif per kWh</span>
                    <span>Rp {{ number_format($tariffPerKwh, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 2px dashed #cbd5e1; align-items: center;">
                    <span style="font-size: 16px; font-weight: 800; color: #0f172a;">Total Tagihan:</span>
                    <span style="font-size: 24px; font-weight: 800; color: #2563eb;">
                        Rp {{ number_format((float)($session->total_cost ?? 0), 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <a href="{{ route('charging.payment.view', $session->id) }}" style="display: block; width: 100%; box-sizing: border-box; text-align: center; background: #10b981; color: white; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                💳 Bayar Sekarang
            </a>
        </div>

    @endif

</div>

{{-- JAVASCRIPT REAL-TIME TELEMETRY ENGINE --}}
@if(!$isCompleted)
<script>
    // Konfigurasi Parameter
    const maxPowerKw = {{ $chargingPowerKw }};
    const pricePerKwh = {{ (float) $tariffPerKwh }};
    const userSaldo = {{ $userSaldo }};
    const startTimeIso = "{{ optional($session->start_time)->toIso8601String() }}";
    const startTime = startTimeIso ? new Date(startTimeIso) : new Date();

    // State Baterai & Telemetri
    const initialBatterySoc = {{ $initialBatterySoc }};
    const batteryCapacity = {{ $batteryCapacity }};
    let currentBatterySoc = initialBatterySoc;
    let targetLimit = 80; // Default limit 80%
    let totalKwh = {{ (float)($session->energy_consumed_kwh ?? 0) }};
    let totalCost = {{ (float)($session->total_cost ?? 0) }};
    let speedMultiplier = 1; // 1x Real-time atau 20x Demo Speed
    let telemetryInterval = null;

    // SVG Circumference = 2 * PI * 105 =~ 659.73
    const circumference = 2 * Math.PI * 105;

    document.addEventListener("DOMContentLoaded", function () {
        initTelemetry();
    });

    function initTelemetry() {
        updateTelemetryMetrics();
        telemetryInterval = setInterval(updateTelemetryMetrics, 1000);
    }

    function updateTelemetryMetrics() {
        const now = new Date();
        const durationSeconds = Math.max(1, Math.floor((now - startTime) / 1000) * speedMultiplier);

        // 1. Durasi Clock
        const hours = Math.floor(durationSeconds / 3600);
        const minutes = Math.floor((durationSeconds % 3600) / 60);
        const seconds = durationSeconds % 60;
        document.getElementById('durationClock').innerText = 
            String(hours).padStart(2, '0') + ':' + 
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');

        // 2. Simulasi Daya Fluktuasi Halus (Realistis +/- 2%)
        const jitter = (Math.random() - 0.5) * (maxPowerKw * 0.04);
        let liveKw = Math.max(1, maxPowerKw + jitter);
        
        // Tapering daya jika baterai > 80% (Standar kurva pengisian EV)
        if (currentBatterySoc > 80) {
            liveKw = liveKw * 0.45; // Turun ke trickle charge
            document.getElementById('powerStatusSub').innerText = "Trickle Balance Charging";
        } else {
            document.getElementById('powerStatusSub').innerText = "Fast Charging Active";
        }
        document.getElementById('liveKwVal').innerText = liveKw.toFixed(1);

        // 3. Simulasi Arus & Tegangan (V & A)
        const voltage = 395 + Math.floor(Math.random() * 8);
        const amperage = (liveKw * 1000) / voltage;
        document.getElementById('liveVoltVal').innerText = voltage;
        document.getElementById('liveAmperageSub').innerText = `${amperage.toFixed(1)} A (DC Flow)`;

        // 4. Perhitungan kWh Terisi & Biaya Berjalan
        const hoursFraction = 1 / 3600 * speedMultiplier;
        totalKwh += (liveKw * hoursFraction);
        totalCost = Math.round(totalKwh * pricePerKwh);

        document.getElementById('liveKwhVal').innerText = totalKwh.toFixed(3);
        document.getElementById('liveCostVal').innerText = totalCost.toLocaleString('id-ID');

        // Input form tersembunyi
        document.getElementById('formEnergyKwh').value = totalKwh.toFixed(3);
        document.getElementById('formTotalCost').value = totalCost;

        // 5. Update Persentase Baterai (SoC) & Radial Gauge
        currentBatterySoc = Math.min(100, initialBatterySoc + ((totalKwh / batteryCapacity) * 100));
        document.getElementById('formEndSoc').value = Math.floor(currentBatterySoc);
        
        const socDisplay = Math.floor(currentBatterySoc);
        document.getElementById('batterySocText').innerText = socDisplay + '%';

        // Estimasi jangkauan (1 kWh ~ 6.5 km)
        const rangeAdded = Math.round(totalKwh * 6.5);
        document.getElementById('rangeAddedText').innerText = `+${rangeAdded} km jangkauan`;

        // Update SVG Circle Stroke Dashoffset
        const offset = circumference - (currentBatterySoc / 100) * circumference;
        document.getElementById('gaugeFill').style.strokeDashoffset = offset;

        // Estimasi Waktu Sisa
        const remainingKwhNeeded = Math.max(0, ((targetLimit - currentBatterySoc) / 100) * batteryCapacity);
        const remainingMinutes = Math.round((remainingKwhNeeded / liveKw) * 60);
        
        if (currentBatterySoc >= targetLimit) {
            document.getElementById('etaTimeText').innerText = `🎯 Target ${targetLimit}% tercapai! Siap dicabut.`;
            document.getElementById('etaTimeText').style.color = '#059669';
        } else {
            document.getElementById('etaTimeText').innerText = `🕒 Estimasi ~${remainingMinutes} menit menuju target ${targetLimit}%`;
            document.getElementById('etaTimeText').style.color = '#475569';
        }

        // 6. Proteksi Saldo
        const saldoWarnText = document.getElementById('saldoWarnText');
        if (userSaldo > 0 && totalCost >= userSaldo * 0.9) {
            saldoWarnText.innerText = '⚠️ Biaya mendekati sisa saldo e-wallet Anda!';
            saldoWarnText.style.color = '#d97706';
        }
    }

    // Set Target Baterai (80%, 90%, 100%)
    window.setTargetLimit = function (val) {
        targetLimit = val;
        document.querySelectorAll('.target-btn').forEach(btn => {
            btn.classList.toggle('active', parseInt(btn.dataset.target) === val);
        });
        updateTelemetryMetrics();
    };

    // Toggle Mode Simulasi Cepat (1x vs 20x)
    window.toggleSpeedMode = function () {
        const btn = document.getElementById('btnSpeedToggle');
        const icon = document.getElementById('speedIcon');
        const label = document.getElementById('speedLabel');

        if (speedMultiplier === 1) {
            speedMultiplier = 20;
            btn.classList.add('active');
            icon.innerText = '🚀';
            label.innerText = 'Kecepatan: Demo Cepat (20x)';
        } else {
            speedMultiplier = 1;
            btn.classList.remove('active');
            icon.innerText = '⏱️';
            label.innerText = 'Kecepatan: Real-Time (1x)';
        }
    };

    // Modal Konfirmasi Stop
    window.openStopModal = function () {
        document.getElementById('modalKwhSummary').innerText = totalKwh.toFixed(3) + ' kWh';
        document.getElementById('modalSocSummary').innerText = Math.floor(currentBatterySoc) + '%';
        document.getElementById('modalCostSummary').innerText = 'Rp ' + totalCost.toLocaleString('id-ID');
        document.getElementById('stopConfirmModal').style.display = 'flex';
    };

    window.closeStopModal = function () {
        document.getElementById('stopConfirmModal').style.display = 'none';
    };

    window.confirmStopCharging = function () {
        clearInterval(telemetryInterval);
        document.getElementById('realStopForm').submit();
    };
</script>
@endif
@endsection
