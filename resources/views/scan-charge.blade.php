@extends('layouts.app')

@section('title', 'Mulai Pengisian Daya · EV Charge')

@section('content')

<style>
    .scan-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 20px 60px;
        width: 100%;
        box-sizing: border-box;
    }

    .scan-header {
        margin-top: 16px;
        margin-bottom: 28px;
        text-align: center;
    }

    .scan-header h1 {
        margin: 0 0 8px 0;
        color: #0f172a;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .scan-header p {
        margin: 0 auto;
        color: #64748b;
        font-size: 15px;
        max-width: 600px;
        line-height: 1.5;
    }

    .scan-card-wrapper {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        display: grid;
        grid-template-columns: 1fr 1.25fr;
        gap: 0;
        overflow: hidden;
    }

    /* Bagian Kiri: Instruksi & Info */
    .scan-guide-pane {
        padding: 40px 36px;
        background: #fafcff;
        border-right: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .guide-title {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 24px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .guide-title span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: #ecfdf5;
        color: #059669;
        border-radius: 8px;
        font-size: 16px;
    }

    .step-list {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .step-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .step-num {
        width: 36px;
        height: 36px;
        min-width: 36px;
        border-radius: 10px;
        background: #ffffff;
        color: #059669;
        border: 1.5px solid #a7f3d0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.08);
    }

    .step-content h4 {
        margin: 0 0 4px 0;
        color: #1e293b;
        font-size: 15px;
        font-weight: 700;
    }

    .step-content p {
        margin: 0;
        color: #64748b;
        font-size: 13.5px;
        line-height: 1.45;
    }

    .guide-tip-box {
        margin-top: 32px;
        padding: 16px 18px;
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .guide-tip-box span.icon {
        font-size: 20px;
        line-height: 1;
    }

    .guide-tip-box p {
        margin: 0;
        color: #065f46;
        font-size: 13px;
        line-height: 1.5;
    }

    /* Bagian Kanan: Scanner & Form Input */
    .scan-action-pane {
        padding: 40px 36px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
    }

    /* Tab Switcher */
    .tab-pills {
        display: flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 14px;
        margin-bottom: 26px;
        gap: 6px;
    }

    .tab-pill-btn {
        flex: 1;
        border: none;
        background: transparent;
        color: #64748b;
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13.5px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .tab-pill-btn.active {
        background: #ffffff;
        color: #059669;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    /* Viewfinder Kamera */
    .scanner-viewfinder {
        position: relative;
        width: 100%;
        max-width: 380px;
        margin: 0 auto 20px;
        height: 250px;
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.6);
    }

    .viewfinder-laser {
        position: absolute;
        top: 0;
        left: 5%;
        width: 90%;
        height: 2px;
        background: #10b981;
        box-shadow: 0 0 12px #10b981, 0 0 20px #34d399;
        animation: laserScan 2.5s infinite ease-in-out;
        z-index: 5;
    }

    @keyframes laserScan {
        0% { top: 15%; opacity: 0.2; }
        50% { top: 85%; opacity: 1; }
        100% { top: 15%; opacity: 0.2; }
    }

    .viewfinder-target {
        width: 160px;
        height: 160px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .viewfinder-target::before, .viewfinder-target::after {
        content: '';
        position: absolute;
        width: 24px;
        height: 24px;
        border-color: #10b981;
        border-style: solid;
    }

    .viewfinder-target::before {
        top: -2px; left: -2px;
        border-width: 3px 0 0 3px;
        border-top-left-radius: 8px;
    }

    .viewfinder-target::after {
        bottom: -2px; right: -2px;
        border-width: 0 3px 3px 0;
        border-bottom-right-radius: 8px;
    }

    .viewfinder-text {
        position: absolute;
        bottom: 14px;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 500;
        text-align: center;
        width: 100%;
        z-index: 6;
    }

    .btn-action-emerald {
        background: linear-gradient(135deg, #059669, #10b981);
        color: #ffffff;
        border: none;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        width: 100%;
        box-sizing: border-box;
    }

    .btn-action-emerald:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(5, 150, 105, 0.35);
    }

    /* Manual Input Form */
    .manual-pane {
        display: none;
    }

    .input-group-modern {
        margin-bottom: 18px;
        text-align: left;
    }

    .input-group-modern label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .input-group-modern input {
        width: 100%;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        font-size: 15px;
        color: #0f172a;
        font-weight: 600;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .input-group-modern input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12);
        background: #ffffff;
    }

    /* Quick Select Grid */
    .quick-select-wrap {
        margin-top: 30px;
        border-top: 1px dashed #e2e8f0;
        padding-top: 24px;
        text-align: left;
    }

    .quick-select-title {
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 14px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .quick-chargers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
    }

    .quick-charger-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
    }

    .quick-charger-card:hover {
        border-color: #10b981;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
        transform: translateY(-2px);
    }

    .qc-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .qc-code {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
        flex: 1;
    }

    .qc-badges-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .qc-badge-match {
        font-size: 10px;
        font-weight: 700;
        background: #bbf7d0;
        color: #166534;
        padding: 2px 8px;
        border-radius: 8px;
        white-space: nowrap;
        display: inline-block;
        line-height: 1.4;
    }

    .qc-badge {
        font-size: 10px;
        font-weight: 700;
        background: #d1fae5;
        color: #065f46;
        padding: 2px 8px;
        border-radius: 10px;
        white-space: nowrap;
        flex-shrink: 0;
        display: inline-block;
        line-height: 1.4;
    }

    .qc-loc {
        font-size: 11.5px;
        color: #64748b;
        margin: 0 0 6px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .qc-spec {
        font-size: 12px;
        font-weight: 700;
        color: #0284c7;
    }

    /* Responsivitas Layar HP */
    @media (max-width: 900px) {
        .scan-card-wrapper {
            grid-template-columns: 1fr;
        }

        .scan-guide-pane {
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
            padding: 28px 20px;
        }

        .scan-action-pane {
            padding: 28px 20px;
        }
    }
</style>

<div class="scan-container">
    {{-- Tombol Navigasi Kembali --}}
    <div>
        <x-back-link href="{{ route('dashboard') }}" label="Kembali ke Dashboard" />
    </div>

    {{-- Judul Halaman Bersih (Tanpa Duplikasi) --}}
    <div class="scan-header">
        <h1>Mulai Pengisian Daya SPKLU</h1>
        <p>Pindai QR Code pada unit stasiun pengisian daya atau masukkan nomor unit charger untuk memulai sesi charging.</p>
    </div>

    {{-- Notifikasi Error / Sukses --}}
    @if (session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Container Card --}}
    <div class="scan-card-wrapper">
        
        {{-- KOLOM KIRI: PANDUAN LANGKAH CEPAT --}}
        <div class="scan-guide-pane">
            <div>
                <h3 class="guide-title">
                    <span>⚡</span> Alur Pengisian Kendaraan
                </h3>

                <div class="step-list">
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <div class="step-content">
                            <h4>Pasang Konektor ke Mobil</h4>
                            <p>Buka penutup soket daya kendaraan Anda dan pasang nozzle charger hingga berbunyi klik.</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-num">2</div>
                        <div class="step-content">
                            <h4>Pindai / Masukkan ID Charger</h4>
                            <p>Arahkan kamera ke stiker QR Code pada tiang unit charger atau ketikkan kode unitnya.</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-num">3</div>
                        <div class="step-content">
                            <h4>Konfirmasi Spesifikasi & Tarif</h4>
                            <p>Periksa kecocokan jenis soket (CCS2 / Type 2) serta pastikan saldo e-wallet Anda mencukupi.</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-num">4</div>
                        <div class="step-content">
                            <h4>Pemantauan Telemetri Real-time</h4>
                            <p>Daya, voltase, dan persentase baterai akan termonitor langsung di layar hingga pengisian tuntas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="guide-tip-box">
                <span class="icon">💡</span>
                <p>
                    <strong>Tips Efisiensi:</strong> Gunakan unit <strong>DC Fast Charging (50 kW)</strong> untuk pengisian optimal dari 20% ke 80% dalam waktu ~35–45 menit.
                </p>
            </div>
        </div>

        {{-- KOLOM KANAN: SCANNER / INPUT MANUAL --}}
        <div class="scan-action-pane">
            {{-- Tab Switcher --}}
            <div class="tab-pills" role="tablist">
                <button type="button" class="tab-pill-btn active" id="tabBtnCamera" onclick="switchScanMode('camera')">
                    📷 Pindai Kamera QR
                </button>
                <button type="button" class="tab-pill-btn" id="tabBtnManual" onclick="switchScanMode('manual')">
                    ⌨️ Masukkan ID Unit
                </button>
            </div>

            {{-- Mode 1: Scanner Kamera Interaktif --}}
            <div id="modeCamera" class="camera-pane">
                <div class="scanner-viewfinder" id="scannerViewfinder">
                    <div class="viewfinder-laser"></div>
                    <div class="viewfinder-target">
                        <span style="font-size: 40px; opacity: 0.7;">⚡</span>
                    </div>
                    <div class="viewfinder-text">Arahkan kamera ke QR Code unit charger</div>
                </div>

                @php
                    // Ambil sampel charger pertama yang tersedia jika ada
                    $firstChargerId = $availableChargers->first()?->id_charger ?? 1;
                @endphp

                <a href="{{ route('scan.charge.show', $firstChargerId) }}" class="btn-action-emerald">
                    <span>📷</span> Pindai &amp; Hubungkan Charger
                </a>

                <p style="text-align: center; color: #94a3b8; font-size: 12.5px; margin: 12px 0 0 0;">
                    Sistem akan memverifikasi unit charger secara otomatis setelah pemindaian berhasil.
                </p>
            </div>

            {{-- Mode 2: Input Manual Kode Charger --}}
            <div id="modeManual" class="manual-pane">
                <form action="{{ route('scan.charge.lookup') }}" method="POST">
                    @csrf
                    <div class="input-group-modern">
                        <label for="charger_code">Kode / Nomor Unit Charger:</label>
                        <input 
                            type="text" 
                            id="charger_code" 
                            name="charger_code" 
                            placeholder="Contoh: CHG-01 atau ID Charger"
                            value="{{ old('charger_code') }}"
                            required
                            autocomplete="off"
                        >
                    </div>

                    <button type="submit" class="btn-action-emerald">
                        <span>🔍</span> Cari &amp; Hubungkan Unit
                    </button>
                </form>

                <p style="text-align: center; color: #94a3b8; font-size: 12.5px; margin: 14px 0 0 0;">
                    Kode unit tertera di bawah stiker QR Code pada tiang fisik stasiun pengisian daya.
                </p>
            </div>

            {{-- Opsi Cepat: Unit Charger Siap Pakai di Sekitar --}}
            @if(isset($availableChargers) && $availableChargers->count() > 0)
                <div class="quick-select-wrap">
                    <div class="quick-select-title">
                        <span>⚡</span> Atau Pilih Unit Charger di Sekitar Anda:
                    </div>

                    <div class="quick-chargers-grid">
                        @foreach($availableChargers as $chg)
                            @php
                                $cConn = strtolower($chg->connector_type ?? '');
                                $isCcs = str_contains($cConn, 'ccs');
                                $isType2 = str_contains($cConn, 'type');
                                
                                $isMatch = false;
                                if (isset($selectedVehicle) && !empty($selectedVehicle->tipe_konektor)) {
                                    $vConn = strtolower($selectedVehicle->tipe_konektor);
                                    if (str_contains($vConn, 'ccs') && ($isCcs || $isType2)) {
                                        $isMatch = true;
                                    } elseif (str_contains($vConn, 'type') && $isType2) {
                                        $isMatch = true;
                                    } elseif ($vConn === $cConn) {
                                        $isMatch = true;
                                    }
                                }
                            @endphp
                            <a href="{{ route('scan.charge.show', $chg->id_charger) }}" class="quick-charger-card" style="{{ $isMatch ? 'border-color: #10b981; background: #f0fdf4;' : '' }}">
                                <div>
                                    <div class="qc-top">
                                        <span class="qc-code">{{ $chg->device_number }}</span>
                                        <div class="qc-badges-group">
                                            @if($isMatch)
                                                <span class="qc-badge-match">✓ Cocok</span>
                                            @endif
                                            <span class="qc-badge">Tersedia</span>
                                        </div>
                                    </div>
                                    <div class="qc-loc">{{ $chg->location->nama_lokasi ?? 'Stasiun SPKLU' }}</div>
                                </div>
                                <div class="qc-spec" style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                                    <span>{{ $chg->connector_type }} · {{ round($chg->max_power_kw) }} kW</span>
                                    <span style="font-size: 10.5px; font-weight: 800; color: {{ $isCcs ? '#0284c7' : '#d97706' }};">
                                        {{ $isCcs ? '⚡ DC Fast' : '🔌 AC Normal' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>

<script>
    function switchScanMode(mode) {
        const btnCamera = document.getElementById('tabBtnCamera');
        const btnManual = document.getElementById('tabBtnManual');
        const paneCamera = document.getElementById('modeCamera');
        const paneManual = document.getElementById('modeManual');

        if (mode === 'manual') {
            btnCamera.classList.remove('active');
            btnManual.classList.add('active');
            paneCamera.style.display = 'none';
            paneManual.style.display = 'block';
            document.getElementById('charger_code').focus();
        } else {
            btnManual.classList.remove('active');
            btnCamera.classList.add('active');
            paneManual.style.display = 'none';
            paneCamera.style.display = 'block';
        }
    }
</script>

@endsection
