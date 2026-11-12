@extends('layouts.app')

@section('title', 'Peta & Jaringan SPKLU')

@section('content')
<!-- Leaflet CSS & JS (Gratis & Ringan, Tanpa API Key) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* Styling Dasar Peta & Komponen EV Charging */
    .spklu-container {
        max-width: 1380px;
        margin: 0 auto;
        padding: 24px 20px 40px;
    }

    .spklu-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .spklu-title-group h2 {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        margin: 8px 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .spklu-title-group p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    /* Stat Badges */
    .stat-pill-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .stat-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .stat-pill .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Filter & Search Bar */
    .filter-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
    }

    .filter-row-top {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 260px;
    }

    .search-box input {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 16px 12px 42px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        outline: none;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .search-box input:focus {
        border-color: #10b981;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }

    .search-box .icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
        pointer-events: none;
    }

    .btn-gps {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        white-space: nowrap;
    }

    .btn-gps:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    .btn-gps.active {
        background: linear-gradient(135deg, #059669, #047857);
    }

    /* Filter Pills Group */
    .filter-row-bottom {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 4px;
    }

    .filter-chip {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }

    .filter-chip:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .filter-chip.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    /* Layout Utama: 2 Kolom (List & Map) */
    .main-grid {
        display: grid;
        grid-template-columns: 460px 1fr;
        gap: 20px;
        align-items: start;
    }

    /* Mobile View Tabs Switcher */
    .mobile-tab-switch {
        display: none;
        margin-bottom: 16px;
        background: #e2e8f0;
        border-radius: 12px;
        padding: 4px;
    }

    .mobile-tab-btn {
        flex: 1;
        padding: 10px;
        border: none;
        background: transparent;
        font-weight: 600;
        font-size: 14px;
        color: #64748b;
        border-radius: 8px;
        cursor: pointer;
    }

    .mobile-tab-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 5px rgba(0,0,0,0.06);
    }

    /* List Container */
    .station-list-container {
        display: flex;
        flex-direction: column;
        gap: 14px;
        max-height: 680px;
        overflow-y: auto;
        padding-right: 6px;
    }

    /* Custom Scrollbar */
    .station-list-container::-webkit-scrollbar {
        width: 6px;
    }
    .station-list-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    .station-list-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }

    /* Station Card */
    .station-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 18px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
        cursor: pointer;
        position: relative;
    }

    .station-card:hover {
        border-color: #10b981;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.1);
        transform: translateY(-2px);
    }

    .station-card.selected {
        border-color: #0284c7;
        background: #f0f9ff;
        box-shadow: 0 0 0 2px #0284c7;
    }

    .station-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 8px;
    }

    .station-name {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.3;
    }

    .station-distance-badge {
        background: #e0f2fe;
        color: #0369a1;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 8px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .station-address {
        font-size: 13px;
        color: #64748b;
        margin: 0 0 10px;
        line-height: 1.4;
    }

    .station-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #e2e8f0;
        margin-bottom: 12px;
        font-size: 12px;
        color: #475569;
    }

    /* Charger Nozzles List inside Card */
    .chargers-box {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 14px;
    }

    .charger-pill-item {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }

    .charger-info-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .charger-type-tag {
        background: #0f172a;
        color: #ffffff;
        font-weight: 700;
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 6px;
    }

    .charger-power-tag {
        color: #0284c7;
        font-weight: 700;
    }

    .charger-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-available { background: #10b981; }
    .status-busy { background: #f59e0b; }
    .status-maintenance { background: #ef4444; }

    /* Card Action Footer */
    .card-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }

    .btn-action-map {
        flex: 1;
        background: #f1f5f9;
        color: #1e293b;
        text-align: center;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background 0.15s ease;
    }

    .btn-action-map:hover {
        background: #e2e8f0;
    }

    .btn-action-charge {
        flex: 1.2;
        background: #10b981;
        color: #ffffff;
        text-align: center;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background 0.15s ease;
    }

    .btn-action-charge:hover {
        background: #059669;
    }

    /* Peta Container */
    .map-wrapper {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        height: 680px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        position: sticky;
        top: 20px;
        z-index: 1;
    }

    #map {
        width: 100%;
        height: 100%;
    }

    /* Custom Leaflet Map Pin */
    .custom-ev-pin {
        background: transparent;
        border: none;
    }

    .pin-body {
        width: 36px;
        height: 44px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        cursor: pointer;
        position: relative;
        filter: drop-shadow(0 3px 6px rgba(0,0,0,0.3));
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .pin-body:hover, .pin-body.active {
        transform: scale(1.15) translateY(-4px);
    }

    .pin-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #0f172a;
        color: white;
        border: 2px solid white;
        font-size: 10px;
        font-weight: 800;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Radar Pulse for User Location */
    .user-pulse-marker {
        width: 16px;
        height: 16px;
        background: #2563eb;
        border: 3px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(37, 99, 235, 0.6);
        position: relative;
    }

    .user-pulse-marker::after {
        content: '';
        position: absolute;
        top: -10px;
        left: -10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid #3b82f6;
        animation: pulseRadar 2s infinite;
    }

    @keyframes pulseRadar {
        0% { transform: scale(0.5); opacity: 1; }
        100% { transform: scale(2.2); opacity: 0; }
    }

    /* Custom Popup Styling */
    .leaflet-popup-content-wrapper {
        border-radius: 16px;
        padding: 0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        overflow: hidden;
    }

    .leaflet-popup-content {
        margin: 0;
        width: 290px !important;
        font-family: 'Inter', sans-serif;
    }

    .popup-header {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: white;
        padding: 14px 16px;
    }

    .popup-header h4 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 700;
        color: white;
    }

    .popup-header p {
        margin: 0;
        font-size: 12px;
        color: #94a3b8;
    }

    .popup-body {
        padding: 14px 16px;
        background: white;
    }

    .popup-chargers-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 12px;
    }

    .popup-btn-group {
        display: flex;
        gap: 8px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
        .map-wrapper {
            height: 480px;
            position: relative;
            top: 0;
        }
        .station-list-container {
            max-height: 500px;
        }
    }

    @media (max-width: 640px) {
        .mobile-tab-switch {
            display: flex;
        }
        .map-wrapper.hidden-mobile, .station-list-container.hidden-mobile {
            display: none !important;
        }
    }
</style>

<div class="spklu-container">
    
    <x-back-link href="{{ route('dashboard') }}" />

    {{-- Header & Statistik Cepat --}}
    <div class="spklu-header">
        <div class="spklu-title-group">
            <h2>⚡ Peta Jaringan SPKLU Interaktif</h2>
            <p>Temukan stasiun pengisian daya terdekat lengkap dengan status ketersediaan & jenis soket real-time.</p>
        </div>

        @php
            $totalStations = $locations->count();
            $totalChargers = $locations->sum(function($loc) { return $loc->chargers->count(); });
            $availableChargers = $locations->sum(function($loc) { 
                return $loc->chargers->where('status', 'tersedia')->where('is_online', true)->count(); 
            });
        @endphp

        <div class="stat-pill-group">
            <div class="stat-pill">
                <span>📍</span> <span><strong>{{ $totalStations }}</strong> Lokasi</span>
            </div>
            <div class="stat-pill">
                <span>🔌</span> <span><strong>{{ $totalChargers }}</strong> Port Charger</span>
            </div>
            <div class="stat-pill" style="color: #065f46; background: #ecfdf5; border-color: #a7f3d0;">
                <span class="dot" style="background: #10b981;"></span>
                <span><strong>{{ $availableChargers }}</strong> Siap Pakai</span>
            </div>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="filter-panel">
        <div class="filter-row-top">
            <div class="search-box">
                <span class="icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Cari SPKLU (contoh: Tugu, Malioboro, Sleman)...">
            </div>

            <button type="button" class="btn-gps" id="btnGps" aria-describedby="gpsStatus">
                <span aria-hidden="true">🎯</span>
                <span id="gpsBtnText">Urutkan dari yang terdekat</span>
            </button>
        </div>
        <p id="gpsStatus" role="status" aria-live="polite" style="margin: -4px 0 14px; color: #64748b; font-size: 12px;">
            Aktifkan lokasi untuk mengurutkan stasiun berdasarkan jarak dari posisi Anda.
        </p>

        <div class="filter-row-bottom">
            <span class="filter-label">Tipe Soket:</span>
            <button type="button" class="filter-chip active" data-filter-type="connector" data-val="all">Semua</button>
            <button type="button" class="filter-chip" data-filter-type="connector" data-val="CCS2">⚡ CCS2 (DC Fast)</button>
            <button type="button" class="filter-chip" data-filter-type="connector" data-val="Type 2">🔌 Type 2 (AC)</button>
            <button type="button" class="filter-chip" data-filter-type="connector" data-val="CHAdeMO">🔋 CHAdeMO</button>

            <span class="filter-label" style="margin-left: 10px;">Daya:</span>
            <button type="button" class="filter-chip active" data-filter-type="power" data-val="all">Semua kW</button>
            <button type="button" class="filter-chip" data-filter-type="power" data-val="fast">Fast (50 kW)</button>
            <button type="button" class="filter-chip" data-filter-type="power" data-val="standard">Standar (22 kW)</button>

            <span class="filter-label" style="margin-left: 10px;">Status:</span>
            <button type="button" class="filter-chip active" data-filter-type="status" data-val="all">Semua</button>
            <button type="button" class="filter-chip" data-filter-type="status" data-val="available">🟢 Tersedia Saja</button>
        </div>
    </div>

    {{-- Mobile View Tab Switcher --}}
    <div class="mobile-tab-switch">
        <button type="button" class="mobile-tab-btn active" id="tabBtnList">📋 Daftar SPKLU (<span id="countDisplay">{{ $totalStations }}</span>)</button>
        <button type="button" class="mobile-tab-btn" id="tabBtnMap">🗺️ Tampilan Peta</button>
    </div>

    {{-- Main Grid --}}
    <div class="main-grid">

        {{-- Kolom Kiri: Daftar Card SPKLU --}}
        <div class="station-list-container" id="stationListContainer">
            @forelse($locations as $loc)
                @php
                    $chargers = $loc->chargers;
                    $locAvailable = $chargers->where('status', 'tersedia')->where('is_online', true)->count();
                    $locTotal = $chargers->count();
                    $connectorTypes = $chargers->pluck('connector_type')->unique()->toArray();
                    $maxPower = $chargers->max('max_power_kw') ?? 0;
                @endphp

                <div class="station-card" 
                     id="card-{{ $loc->id_location }}"
                     data-id="{{ $loc->id_location }}"
                     data-name="{{ strtolower($loc->nama_lokasi) }}"
                     data-address="{{ strtolower($loc->alamat) }}"
                     data-connectors="{{ json_encode($connectorTypes) }}"
                     data-max-power="{{ $maxPower }}"
                     data-available="{{ $locAvailable }}"
                     data-lat="{{ $loc->latitude }}"
                     data-lng="{{ $loc->longitude }}">
                     
                    <div class="station-card-header">
                        <h3 class="station-name">{{ $loc->nama_lokasi }}</h3>
                        <span class="station-distance-badge" id="dist-badge-{{ $loc->id_location }}" style="display: none;">
                            📍 <span class="dist-text">-- km</span>
                        </span>
                    </div>

                    <p class="station-address">📍 {{ $loc->alamat }}</p>

                    <div class="station-meta-row">
                        <span>🕒 <strong>{{ $loc->jam_operasional }}</strong></span>
                        <span>
                            @if($locAvailable > 0)
                                <span style="color: #059669; font-weight: 700;">🟢 {{ $locAvailable }}/{{ $locTotal }} Tersedia</span>
                            @else
                                <span style="color: #dc2626; font-weight: 700;">🔴 Penuh / Nonaktif</span>
                            @endif
                        </span>
                    </div>

                    {{-- Daftar Charger Pada Stasiun Ini --}}
                    <div class="chargers-box">
                        @foreach($chargers as $chg)
                            <div class="charger-pill-item">
                                <div class="charger-info-left">
                                    <span class="charger-status-dot {{ $chg->status == 'tersedia' && $chg->is_online ? 'status-available' : ($chg->status == 'sedang_mengisi' ? 'status-busy' : 'status-maintenance') }}"></span>
                                    <span class="charger-type-tag">{{ $chg->connector_type }}</span>
                                    <span class="charger-power-tag">{{ round($chg->max_power_kw) }} kW</span>
                                    <span style="color: #64748b; font-size: 11px;">({{ $chg->device_number }})</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 700; color: #1e293b; font-size: 11px;">Rp {{ number_format($chg->price_per_kwh, 0, ',', '.') }}/kWh</span>
                                    <a href="{{ route('scan.charge.show', $chg->id_charger) }}" class="btn-action-charge" style="padding: 4px 8px; font-size: 11px;" title="Pilih Charger Ini">
                                        ⚡ Pilih
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Fasilitas SPKLU --}}
                    @if($loc->fasilitas)
                        <div style="font-size: 11px; color: #64748b; margin-bottom: 12px; display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
                            <span style="font-weight: 600;">Fasilitas:</span>
                            @foreach(explode(',', $loc->fasilitas) as $fas)
                                <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 6px;">{{ trim($fas) }}</span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Card Actions --}}
                    <div class="card-actions">
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $loc->latitude }},{{ $loc->longitude }}" 
                           target="_blank" 
                           class="btn-action-map"
                           onclick="event.stopPropagation();">
                            🧭 Buka Rute Maps
                        </a>
                        <button type="button" 
                                class="btn-action-map" 
                                style="background: #0f172a; color: white;"
                                onclick="focusOnMap({{ $loc->id_location }}); event.stopPropagation();">
                            🗺️ Lihat di Peta
                        </button>
                    </div>

                </div>
            @empty
                <div style="text-align: center; padding: 40px; background: white; border-radius: 16px; border: 1px dashed #cbd5e1;">
                    <p style="color: #64748b; margin: 0;">Belum ada stasiun SPKLU yang terdaftar di sistem.</p>
                </div>
            @endforelse

            <div id="noMatchAlert" style="display: none; text-align: center; padding: 30px; background: white; border-radius: 16px; border: 1px dashed #cbd5e1; color: #64748b;">
                🔍 Tidak ditemukan SPKLU yang cocok dengan kriteria filter Anda. Coba ubah pencarian atau filter.
            </div>
        </div>

        {{-- Kolom Kanan: Peta Interaktif Leaflet --}}
        <div class="map-wrapper" id="mapWrapper">
            <div id="map"></div>
        </div>

    </div>

</div>

{{-- Skrip JavaScript Lengkap: Leaflet Map, Haversine GPS Distance, Interactive Filters --}}
<script>
    const locationsData = @json($locations);
    let map = null;
    let markersMap = {}; // id_location -> L.marker
    let userLocation = null;
    let userMarker = null;

    // Filter states
    let activeFilter = {
        search: '',
        connector: 'all',
        power: 'all',
        status: 'all'
    };

    document.addEventListener("DOMContentLoaded", function () {
        initLeafletMap();
        initFilterEvents();
        initMobileTabs();
    });

    // 1. Inisialisasi Peta Leaflet
    function initLeafletMap() {
        // Default center ke Yogyakarta (atau stasiun pertama jika ada)
        let defaultCenter = [-7.7892, 110.3634];
        if (locationsData.length > 0 && locationsData[0].latitude && locationsData[0].longitude) {
            defaultCenter = [parseFloat(locationsData[0].latitude), parseFloat(locationsData[0].longitude)];
        }

        map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView(defaultCenter, 13);

        // Menggunakan tile openstreetmap yang bersih dan responsif
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Tambahkan tombol navigasi kustom ke Leaflet
        addMapControls();

        // Render semua marker SPKLU
        renderMarkers();
    }

    // 2. Buat Custom SVG Pin untuk Marker EV Charging
    function createCustomPin(availableCount, totalCount) {
        // Warna pin: Hijau jika ada port kosong, Amber jika terisi semua, Abu-abu jika 0 port
        let pinColor = '#10b981'; // green
        if (totalCount === 0) pinColor = '#94a3b8';
        else if (availableCount === 0) pinColor = '#ef4444';

        const svgIcon = `
            <div class="pin-body">
                <div class="pin-badge">${availableCount}</div>
                <svg width="34" height="42" viewBox="0 0 34 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 0C7.61116 0 0 7.61116 0 17C0 27.2 15.2 40.8 15.9 41.4C16.2 41.7 16.6 41.9 17 41.9C17.4 41.9 17.8 41.7 18.1 41.4C18.8 40.8 34 27.2 34 17C34 7.61116 26.3888 0 17 0Z" fill="${pinColor}"/>
                    <circle cx="17" cy="17" r="12" fill="white"/>
                    <path d="M18.5 8L11 18H16.5L14.5 26L23 15H17.5L18.5 8Z" fill="${pinColor}"/>
                </svg>
            </div>
        `;

        return L.divIcon({
            html: svgIcon,
            className: 'custom-ev-pin',
            iconSize: [36, 44],
            iconAnchor: [18, 42],
            popupAnchor: [0, -40]
        });
    }

    // 3. Render Marker ke Peta
    function renderMarkers() {
        locationsData.forEach(loc => {
            if (!loc.latitude || !loc.longitude) return;

            const lat = parseFloat(loc.latitude);
            const lng = parseFloat(loc.longitude);
            const chargers = loc.chargers || [];
            const availableCount = chargers.filter(c => c.status === 'tersedia' && c.is_online).length;

            const icon = createCustomPin(availableCount, chargers.length);
            const marker = L.marker([lat, lng], { icon: icon }).addTo(map);

            // Bangun popup HTML profesional
            let chargersHtml = '';
            chargers.forEach(chg => {
                const statusDot = chg.status === 'tersedia' && chg.is_online 
                    ? '<span style="color:#10b981;">● Siap Pakai</span>' 
                    : '<span style="color:#ef4444;">● Terpakai / Offline</span>';

                chargersHtml += `
                    <div style="background:#f8fafc; padding:6px 10px; border-radius:8px; font-size:12px; display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <div>
                            <strong>${chg.connector_type}</strong> (${Math.round(chg.max_power_kw)} kW)
                        </div>
                        <div>${statusDot}</div>
                    </div>
                `;
            });

            const popupContent = `
                <div class="popup-header">
                    <h4>${loc.nama_lokasi}</h4>
                    <p>🕒 ${loc.jam_operasional} • ${availableCount}/${chargers.length} Port Siap</p>
                </div>
                <div class="popup-body">
                    <p style="margin: 0 0 10px; font-size: 12px; color: #475569;">📍 ${loc.alamat}</p>
                    <div class="popup-chargers-list">
                        ${chargersHtml || '<div style="font-size:12px; color:#94a3b8;">Belum ada data charger</div>'}
                    </div>
                    <div class="popup-btn-group">
                        <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" style="flex:1; background:#0284c7; color:white; padding:8px; border-radius:8px; font-size:11px; font-weight:700; text-decoration:none; text-align:center;">
                            🧭 Rute Maps
                        </a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent);

            // Interaksi klik marker: Sorot kartu di list
            marker.on('click', () => {
                highlightCard(loc.id_location);
            });

            markersMap[loc.id_location] = marker;
        });
    }

    // 4. Tombol Kontrol Peta
    function addMapControls() {
        const ControlUI = L.Control.extend({
            options: { position: 'topright' },
            onAdd: function () {
                const container = L.DomUtil.create('div', 'leaflet-bar');
                container.style.background = 'white';
                container.style.border = 'none';
                container.style.borderRadius = '8px';
                container.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
                container.style.overflow = 'hidden';

                const btn = L.DomUtil.create('a', '', container);
                btn.innerHTML = '🎯';
                btn.title = 'Pusatkan ke Posisi Saya';
                btn.href = '#';
                btn.style.width = '36px';
                btn.style.height = '36px';
                btn.style.lineHeight = '36px';
                btn.style.textAlign = 'center';
                btn.style.fontSize = '16px';
                btn.style.display = 'block';

                L.DomEvent.on(btn, 'click', function (e) {
                    L.DomEvent.stopPropagation(e);
                    L.DomEvent.preventDefault(e);
                    locateUser(true);
                });

                return container;
            }
        });
        map.addControl(new ControlUI());
    }

    // 5. Geolocation GPS Pengguna & Perhitungan Jarak (Formula Haversine)
    function locateUser(centerMap = false) {
        const btnGps = document.getElementById('btnGps');
        const gpsBtnText = document.getElementById('gpsBtnText');
        const gpsStatus = document.getElementById('gpsStatus');

        if (!("geolocation" in navigator)) {
            gpsStatus.innerText = "Browser Anda tidak mendukung fitur lokasi. Daftar tetap ditampilkan menurut urutan awal.";
            return;
        }

        btnGps.disabled = true;
        gpsBtnText.innerText = "Mendeteksi lokasi...";
        gpsStatus.innerText = "Izinkan akses lokasi untuk mencari stasiun terdekat.";

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                userLocation = L.latLng(lat, lng);

                btnGps.classList.add('active');
                gpsBtnText.innerText = "Lokasi aktif · urutkan ulang";
                btnGps.disabled = false;

                // Marker radar posisi pengguna
                if (userMarker) {
                    userMarker.setLatLng(userLocation);
                } else {
                    const pulseIcon = L.divIcon({
                        html: '<div class="user-pulse-marker"></div>',
                        className: '',
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });
                    userMarker = L.marker(userLocation, { icon: pulseIcon }).addTo(map);
                    userMarker.bindPopup("<b>📍 Lokasi Anda Saat Ini</b>");
                }

                if (centerMap) {
                    map.flyTo(userLocation, 14, { duration: 1.2 });
                    userMarker.openPopup();
                }

                // Hitung jarak ke setiap SPKLU & Urutkan List
                calculateDistancesAndSort();
                gpsStatus.innerText = "Daftar diurutkan dari yang terdekat. Jarak adalah perkiraan garis lurus, bukan jarak perjalanan.";
            },
            function (err) {
                const errorMessages = {
                    1: "Izin lokasi ditolak. Izinkan akses lokasi di pengaturan browser untuk mengurutkan stasiun terdekat.",
                    2: "Lokasi perangkat tidak dapat ditemukan. Coba lagi atau gunakan peta.",
                    3: "Permintaan lokasi terlalu lama. Coba lagi.",
                };
                gpsBtnText.innerText = "Coba urutkan lagi";
                gpsStatus.innerText = errorMessages[err.code] || "Lokasi tidak dapat diakses. Coba lagi.";
                btnGps.disabled = false;
            },
            { enableHighAccuracy: false, timeout: 12000, maximumAge: 60000 }
        );
    }

    // Rumus Haversine untuk menghitung km akurat antara 2 koordinat
    function calculateDistanceKm(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius bumi dalam km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const boundedA = Math.min(1, Math.max(0, a));
        const c = 2 * Math.atan2(Math.sqrt(boundedA), Math.sqrt(1 - boundedA));
        return R * c;
    }

    function calculateDistancesAndSort() {
        if (!userLocation) return;

        const container = document.getElementById('stationListContainer');
        const cards = Array.from(container.getElementsByClassName('station-card'));

        cards.forEach(card => {
            const lat = Number(card.dataset.lat);
            const lng = Number(card.dataset.lng);
            const id = card.dataset.id;

            if (Number.isFinite(lat) && Number.isFinite(lng)) {
                const distKm = calculateDistanceKm(userLocation.lat, userLocation.lng, lat, lng);
                card.dataset.distance = distKm;

                const badge = document.getElementById(`dist-badge-${id}`);
                if (badge) {
                    badge.style.display = 'inline-flex';
                    const formattedDistance = distKm < 1
                        ? `${Math.round(distKm * 1000)} m`
                        : `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 }).format(distKm)} km`;
                    badge.querySelector('.dist-text').innerText = `${formattedDistance} dari Anda`;
                }
            } else {
                card.dataset.distance = Number.POSITIVE_INFINITY;
            }
        });

        // Urutkan kartu berdasarkan jarak terdekat
        cards.sort((a, b) => Number(a.dataset.distance) - Number(b.dataset.distance));
        cards.forEach(card => container.appendChild(card));
        applyFilters();
    }

    // 6. Fokuskan Peta ke Stasiun yang Dipilih
    window.focusOnMap = function (id) {
        const marker = markersMap[id];
        if (marker) {
            map.flyTo(marker.getLatLng(), 15, { duration: 1.0 });
            setTimeout(() => {
                marker.openPopup();
            }, 800);
        }

        // Jika di layar mobile, pindahkan tab ke peta otomatis
        if (window.innerWidth <= 640) {
            document.getElementById('tabBtnMap').click();
        }
    };

    function highlightCard(id) {
        document.querySelectorAll('.station-card').forEach(c => c.classList.remove('selected'));
        const target = document.getElementById(`card-${id}`);
        if (target) {
            target.classList.add('selected');
            target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // 7. Event Handler Filter Cerdas
    function initFilterEvents() {
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function (e) {
            activeFilter.search = e.target.value.toLowerCase().trim();
            applyFilters();
        });

        document.getElementById('btnGps').addEventListener('click', function () {
            locateUser(true);
        });

        document.querySelectorAll('.filter-chip').forEach(btn => {
            btn.addEventListener('click', function () {
                const type = this.dataset.filterType;
                const val = this.dataset.val;

                // Ganti status aktif pada grup filter yang sama
                document.querySelectorAll(`.filter-chip[data-filter-type="${type}"]`).forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                activeFilter[type] = val;
                applyFilters();
            });
        });
    }

    function applyFilters() {
        const cards = document.querySelectorAll('.station-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name;
            const address = card.dataset.address;
            const connectors = JSON.parse(card.dataset.connectors || '[]');
            const maxPower = parseFloat(card.dataset.maxPower || 0);
            const available = parseInt(card.dataset.available || 0);
            const id = card.dataset.id;

            let matches = true;

            // Search filter
            if (activeFilter.search && !name.includes(activeFilter.search) && !address.includes(activeFilter.search)) {
                matches = false;
            }

            // Connector filter
            if (activeFilter.connector !== 'all') {
                if (!connectors.some(c => c.toLowerCase().includes(activeFilter.connector.toLowerCase()))) {
                    matches = false;
                }
            }

            // Power filter
            if (activeFilter.power === 'fast' && maxPower < 50) matches = false;
            if (activeFilter.power === 'standard' && maxPower >= 50) matches = false;

            // Availability filter
            if (activeFilter.status === 'available' && available === 0) matches = false;

            // Tampilkan / sembunyikan card & marker
            if (matches) {
                card.style.display = 'block';
                visibleCount++;
                if (markersMap[id]) map.addLayer(markersMap[id]);
            } else {
                card.style.display = 'none';
                if (markersMap[id]) map.removeLayer(markersMap[id]);
            }
        });

        const noMatchAlert = document.getElementById('noMatchAlert');
        if (noMatchAlert) {
            noMatchAlert.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        const countDisplay = document.getElementById('countDisplay');
        if (countDisplay) {
            countDisplay.innerText = visibleCount;
        }
    }

    // 8. Mobile Tabs Switcher
    function initMobileTabs() {
        const tabBtnList = document.getElementById('tabBtnList');
        const tabBtnMap = document.getElementById('tabBtnMap');
        const listContainer = document.getElementById('stationListContainer');
        const mapWrapper = document.getElementById('mapWrapper');

        if (!tabBtnList || !tabBtnMap) return;

        tabBtnList.addEventListener('click', function () {
            tabBtnList.classList.add('active');
            tabBtnMap.classList.remove('active');
            listContainer.classList.remove('hidden-mobile');
            mapWrapper.classList.add('hidden-mobile');
        });

        tabBtnMap.addEventListener('click', function () {
            tabBtnMap.classList.add('active');
            tabBtnList.classList.remove('active');
            mapWrapper.classList.remove('hidden-mobile');
            listContainer.classList.add('hidden-mobile');
            if (map) map.invalidateSize();
        });
    }
</script>
@endsection
