@extends('layouts.app')

@section('title', 'Dashboard Pelanggan')

@section('content')
@php
    $user = auth()->user();
    $points = $user->points ?? $user->poin ?? 0;
    $visibleBalance = 'Rp ' . number_format($user->saldo ?? 0, 0, ',', '.');
@endphp

<style>
    .customer-dashboard {
        --ink: #172b4d;
        --muted: #64748b;
        --line: #e5ebf2;
        width: 100%;
        max-width: 1440px;
        margin: 0 auto;
        padding: 4px 0 32px;
        color: var(--ink);
    }

    .customer-dashboard * { box-sizing: border-box; }
    .customer-dashboard a { text-decoration: none; }

    .dashboard-alert {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 16px;
        padding: 13px 17px;
        border: 1px solid #a7f3d0;
        border-radius: 12px;
        background: #ecfdf5;
        color: #065f46;
        font-size: 13px;
        font-weight: 600;
    }

    .dashboard-alert.is-error { border-color: #fecaca; background: #fef2f2; color: #991b1b; }
    .dashboard-alert button { border: 0; background: none; color: inherit; font-size: 19px; cursor: pointer; }

    .dashboard-overview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        padding: 24px 30px;
        border: 1px solid var(--line);
        border-radius: 19px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .035);
    }

    .dashboard-welcome h1 { margin: 0 0 6px; color: var(--ink); font-size: 23px; letter-spacing: -.5px; }
    .dashboard-welcome p { max-width: 440px; margin: 0; color: var(--muted); font-size: 13px; line-height: 1.6; }
    .dashboard-stats { display: grid; width: min(660px, 60%); grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }

    .dashboard-stat {
        display: flex;
        min-width: 0;
        min-height: 66px;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: #fff;
        color: inherit;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    a.dashboard-stat:hover { border-color: #a7f3d0; box-shadow: 0 5px 15px rgba(15, 23, 42, .06); }
    .stat-icon { display: grid; width: 36px; height: 36px; flex: 0 0 36px; place-items: center; border-radius: 10px; background: #ecfdf5; font-size: 16px; }
    .stat-icon.blue { background: #eff6ff; }
    .stat-icon.amber { background: #fffbeb; }
    .stat-copy { min-width: 0; flex: 1; }
    .stat-label { display: block; margin-bottom: 3px; color: var(--muted); font-size: 10px; font-weight: 600; }
    .stat-value { display: block; overflow: hidden; color: var(--ink); font-size: 13px; font-weight: 750; text-overflow: ellipsis; white-space: nowrap; }
    .balance-stat { padding-right: 7px; transition: border-color .2s ease, box-shadow .2s ease; }
    .balance-stat:hover, .balance-stat:focus-within { border-color: #a7f3d0; box-shadow: 0 5px 15px rgba(15, 23, 42, .06); }
    .balance-toggle { flex: 0 0 auto; padding: 5px; border: 0; border-radius: 7px; background: #f1f5f9; cursor: pointer; }

    .dashboard-body { display: grid; gap: 19px; margin-top: 19px; }

    .dashboard-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 22px 28px;
        border-radius: 17px;
        background: linear-gradient(110deg, #059669, #10b981);
        color: #fff;
        box-shadow: 0 8px 18px rgba(16, 185, 129, .16);
    }

    .hero-copy { min-width: 0; }
    .hero-eyebrow { margin: 0 0 6px; color: rgba(255,255,255,.82); font-size: 10px; font-weight: 750; letter-spacing: .9px; text-transform: uppercase; }
    .hero-copy h2 { margin: 0 0 6px; font-size: 18px; letter-spacing: -.2px; }
    .hero-copy p { margin: 0; color: rgba(255,255,255,.91); font-size: 12px; line-height: 1.55; }
    .hero-action { display: inline-flex; min-height: 42px; flex: 0 0 auto; align-items: center; justify-content: center; gap: 8px; padding: 0 19px; border-radius: 999px; background: #fff; color: #047857; font-size: 12px; font-weight: 750; transition: transform .2s ease, box-shadow .2s ease; }
    .hero-action:hover { transform: translateY(-2px); box-shadow: 0 5px 14px rgba(0,0,0,.12); }

    .dashboard-panel {
        padding: 22px 24px;
        border: 1px solid var(--line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .03);
    }

    .panel-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 15px; }
    .panel-heading h2 { margin: 0; color: var(--ink); font-size: 17px; letter-spacing: -.25px; }
    .panel-heading p { margin: 4px 0 0; color: var(--muted); font-size: 11px; line-height: 1.5; }
    .text-link { flex: 0 0 auto; color: #047857; font-size: 11px; font-weight: 750; }
    .text-link:hover { text-decoration: underline; }
    .station-tools { display: flex; flex: 0 0 auto; align-items: center; gap: 12px; }
    .location-button { display: inline-flex; min-height: 34px; align-items: center; justify-content: center; gap: 7px; padding: 0 11px; border: 1px solid #b7e9d4; border-radius: 9px; background: #f3fcf8; color: #06734f; font: inherit; font-size: 10px; font-weight: 750; cursor: pointer; transition: background .2s ease, border-color .2s ease; }
    .location-button:hover:not(:disabled) { border-color: #6ee7b7; background: #dcfce7; }
    .location-button:disabled { cursor: wait; opacity: .7; }
    .location-status { min-height: 15px; margin: -5px 0 13px; color: var(--muted); font-size: 10px; }
    .location-status:empty { display: none; }

    .vehicle-summary, .vehicle-empty {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 15px 17px;
        border: 1px solid #d6f2e7;
        border-radius: 13px;
        background: #f3fcf8;
    }

    .vehicle-info { display: flex; min-width: 0; align-items: center; gap: 12px; }
    .vehicle-icon { display: grid; width: 40px; height: 40px; flex: 0 0 40px; place-items: center; border-radius: 12px; background: #d7f7e9; font-size: 19px; }
    .vehicle-info h3 { overflow: hidden; margin: 0 0 4px; color: var(--ink); font-size: 13px; text-overflow: ellipsis; white-space: nowrap; }
    .vehicle-info p { margin: 0; color: var(--muted); font-size: 11px; }
    .vehicle-action { flex: 0 0 auto; padding: 9px 13px; border: 1px solid #b7e9d4; border-radius: 9px; background: #fff; color: #06734f; font-size: 11px; font-weight: 700; }
    .vehicle-select-form { display: flex; flex: 0 1 540px; align-items: flex-end; justify-content: flex-end; gap: 9px; }
    .vehicle-select-field { display: grid; min-width: 220px; flex: 1; gap: 5px; }
    .vehicle-select-field label { color: #526579; font-size: 10px; font-weight: 700; }
    .vehicle-select-field select { width: 100%; min-height: 38px; padding: 0 10px; border: 1px solid #cbd5e1; border-radius: 9px; background: #fff; color: var(--ink); font: inherit; font-size: 11px; }
    .vehicle-select-field select:focus { border-color: #10b981; outline: 3px solid rgba(16, 185, 129, .16); }
    .vehicle-empty { border-color: #fde7ba; background: #fffbeb; }
    .vehicle-empty strong { display: block; margin-bottom: 4px; color: #713f12; font-size: 13px; }
    .vehicle-empty p { margin: 0; color: #854d0e; font-size: 11px; }
    .vehicle-empty .vehicle-action { border-color: #f5d28d; color: #854d0e; }

    .station-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .station-card[hidden] { display: none; }
    .station-card { display: flex; min-width: 0; flex-direction: column; padding: 15px; border: 1px solid var(--line); border-radius: 13px; background: #fff; color: inherit; transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease; }
    .station-card:hover { transform: translateY(-2px); border-color: #a7f3d0; box-shadow: 0 7px 17px rgba(15, 23, 42, .06); }
    .station-status { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 9px; color: #06734f; font-size: 9px; font-weight: 800; letter-spacing: .4px; text-transform: uppercase; }
    .station-status::before { width: 7px; height: 7px; border-radius: 50%; background: #10b981; content: ""; }
    .station-card h3 { margin: 0 0 5px; color: var(--ink); font-size: 12px; line-height: 1.45; }
    .station-address { display: -webkit-box; min-height: 32px; overflow: hidden; margin: 0; color: var(--muted); font-size: 10px; line-height: 1.55; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
    .station-meta { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .station-meta span { padding: 5px 7px; border-radius: 7px; background: #f1f5f9; color: #475569; font-size: 9px; font-weight: 700; }
    .station-connectors { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 8px; }
    .station-connectors span { padding: 4px 6px; border: 1px solid var(--line); border-radius: 6px; color: #526579; font-size: 9px; font-weight: 650; }
    .station-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: auto; padding-top: 11px; color: #07805f; font-size: 10px; font-weight: 750; }

    .station-empty { padding: 20px; border: 1px dashed #cbd5e1; border-radius: 13px; background: #f8fafc; color: var(--muted); text-align: center; }
    .station-empty strong { display: block; margin-bottom: 5px; color: var(--ink); font-size: 13px; }
    .station-empty p { margin: 0 0 10px; font-size: 11px; }

    .dashboard-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
    .dashboard-action { display: inline-flex; min-height: 38px; align-items: center; gap: 8px; padding: 0 13px; border: 1px solid var(--line); border-radius: 10px; background: #fff; color: #334e68; font-size: 11px; font-weight: 700; transition: border-color .2s ease, background .2s ease; }
    .dashboard-action:hover { border-color: #a7f3d0; background: #f3fcf8; }
    .dashboard-action.primary { border-color: #059669; background: #059669; color: #fff; }
    .dashboard-action.primary:hover { border-color: #047857; background: #047857; }

    @media (max-width: 1000px) {
        .dashboard-overview { align-items: flex-start; flex-direction: column; }
        .dashboard-stats { width: 100%; }
        .station-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 700px) {
        .customer-dashboard { padding-top: 0; }
        .dashboard-overview { gap: 16px; padding: 18px 16px; border-radius: 15px; }
        .dashboard-welcome h1 { font-size: 20px; }
        .dashboard-welcome p { font-size: 12px; }
        .dashboard-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 7px; }
        .dashboard-stat { min-height: 68px; flex-direction: column; align-items: flex-start; gap: 6px; padding: 8px; border-radius: 10px; }
        .stat-icon { width: 30px; height: 30px; flex-basis: 30px; border-radius: 8px; font-size: 14px; }
        .stat-label { font-size: 9px; }
        .stat-value { font-size: 10px; }
        .balance-stat { position: relative; padding-right: 5px; }
        .balance-toggle { position: absolute; top: 4px; right: 3px; padding: 3px; font-size: 9px; }
        .dashboard-body { gap: 13px; margin-top: 13px; }
        .dashboard-hero { align-items: flex-start; flex-direction: column; gap: 14px; padding: 19px 17px; border-radius: 15px; }
        .hero-copy h2 { font-size: 16px; }
        .hero-copy p { font-size: 11px; }
        .hero-action { min-height: 38px; padding: 0 15px; font-size: 11px; }
        .dashboard-panel { padding: 17px 14px; border-radius: 15px; }
        .panel-heading { align-items: flex-start; }
        .panel-heading h2 { font-size: 15px; }
        .station-tools { align-items: flex-end; flex-direction: column-reverse; gap: 8px; }
        .location-button { min-height: 32px; padding: 0 8px; font-size: 9px; }
        .station-grid { grid-template-columns: 1fr; }
        .station-address { min-height: 0; }
        .vehicle-summary, .vehicle-empty { align-items: flex-start; flex-direction: column; }
        .vehicle-select-form { width: 100%; flex-basis: auto; align-items: stretch; flex-direction: column; }
        .vehicle-select-field { min-width: 0; }
        .vehicle-action { align-self: flex-start; }
    }

    @media (prefers-reduced-motion: reduce) {
        .station-card, .dashboard-stat, .hero-action, .balance-stat { transition: none; }
        .station-card:hover, .dashboard-stat:hover, .hero-action:hover { transform: none; }
    }
</style>

<div class="customer-dashboard">
    @if (session('success'))
        <div class="dashboard-alert" role="status">
            <span>{{ session('success') }}</span>
            <button type="button" aria-label="Tutup notifikasi" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if (session('error'))
        <div class="dashboard-alert is-error" role="alert">
            <span>{{ session('error') }}</span>
            <button type="button" aria-label="Tutup notifikasi" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    <section class="dashboard-overview" aria-label="Ringkasan akun">
        <div class="dashboard-welcome">
            <h1>Selamat datang, {{ $user->nama }}</h1>
            <p>Ringkasan kendaraan dan informasi pengisian daya untuk perjalanan Anda.</p>
        </div>

        <div class="dashboard-stats">
            <a class="dashboard-stat" href="{{ route('charging.history') }}">
                <span class="stat-icon blue" aria-hidden="true">📋</span>
                <span class="stat-copy">
                    <span class="stat-label">Aktivitas</span>
                    <span class="stat-value">Riwayat charging</span>
                </span>
            </a>

            <div class="dashboard-stat balance-stat">
                <span class="stat-icon" aria-hidden="true">💳</span>
                <a class="stat-copy" href="{{ route('topup.index') }}" aria-label="Isi ulang saldo">
                    <span class="stat-label">Saldo aktif</span>
                    <span class="stat-value" id="saldoText">{{ $visibleBalance }}</span>
                </a>
                <button class="balance-toggle" type="button" id="toggleSaldo" aria-label="Sembunyikan saldo" aria-pressed="false">◉</button>
            </div>

            <a class="dashboard-stat" href="{{ route('points.index') }}">
                <span class="stat-icon amber" aria-hidden="true">⚡</span>
                <span class="stat-copy">
                    <span class="stat-label">Poin saya</span>
                    <span class="stat-value">{{ number_format($points, 0, ',', '.') }} poin</span>
                </span>
            </a>
        </div>
    </section>

    <div class="dashboard-body">
        <section class="dashboard-hero" aria-labelledby="dashboardHeroTitle">
            <div class="hero-copy">
                <p class="hero-eyebrow">EV Charge · Pengisian daya</p>
                <h2 id="dashboardHeroTitle">
                    @if ($ongoingSession)
                        Sesi charging Anda sedang berlangsung
                    @elseif ($vehicle)
                        Kendaraan siap untuk mengisi daya
                    @else
                        Daftarkan kendaraan listrik Anda
                    @endif
                </h2>
                <p>
                    @if ($ongoingSession)
                        Lanjutkan pemantauan sesi, atau hentikan pengisian dari halaman sesi aktif.
                    @elseif ($vehicle)
                        Cari unit dengan konektor {{ $vehicle->tipe_konektor }}, periksa tarif, lalu mulai pengisian daya.
                    @else
                        Tambahkan data kendaraan dan tipe konektor agar dapat memulai proses charging.
                    @endif
                </p>
            </div>
            @if ($ongoingSession)
                <a class="hero-action" href="{{ route('charging.session', $ongoingSession->id) }}">Lanjutkan sesi <span aria-hidden="true">→</span></a>
            @elseif ($vehicle)
                <a class="hero-action" href="{{ route('scan.charge') }}">Mulai charging <span aria-hidden="true">→</span></a>
            @else
                <a class="hero-action" href="{{ url('/profile') }}">Daftarkan kendaraan <span aria-hidden="true">→</span></a>
            @endif
        </section>

        <section class="dashboard-panel" aria-labelledby="stationHeading">
            <div class="panel-heading">
                <div>
                    <h2 id="stationHeading">SPKLU terdekat dengan charger tersedia</h2>
                    <p>Urutkan dari lokasi Anda, lalu bandingkan konektor, daya, dan tarif.</p>
                </div>
                <div class="station-tools">
                    @if ($dashboardLocations->isNotEmpty())
                        <button class="location-button" type="button" id="sortByLocation" aria-describedby="locationStatus">
                            <span aria-hidden="true">◎</span> Gunakan lokasi saya
                        </button>
                    @endif
                    <a class="text-link" href="{{ route('locations.index') }}">Lihat peta <span aria-hidden="true">→</span></a>
                </div>
            </div>

            @if ($dashboardLocations->isNotEmpty())
                <p class="location-status" id="locationStatus" role="status" aria-live="polite">Aktifkan lokasi untuk menampilkan stasiun terdekat. Tanpa lokasi, daftar diurutkan berdasarkan nama.</p>
                <div class="station-grid" id="stationGrid">
                    @foreach ($dashboardLocations as $station)
                        <a class="station-card"
                           href="{{ route('locations.index') }}"
                           data-lat="{{ $station->latitude }}"
                           data-lng="{{ $station->longitude }}"
                           data-name="{{ $station->nama_lokasi }}"
                           aria-label="Lihat {{ $station->nama_lokasi }} di daftar SPKLU"
                           @if ($loop->iteration > 3) hidden @endif>
                            <span class="station-status">Charger tersedia</span>
                            <h3>{{ $station->nama_lokasi }}</h3>
                            <p class="station-address">{{ $station->alamat }}</p>
                            <div class="station-meta">
                                <span>{{ $station->available_chargers_count }} unit tersedia</span>
                                <span>{{ $station->jam_operasional ?? 'Jam operasional tidak tersedia' }}</span>
                            </div>
                            <div class="station-connectors">
                                @foreach ($station->chargers->take(3) as $charger)
                                    <span>{{ $charger->connector_type }} · {{ number_format((float) $charger->max_power_kw, 1, ',', '.') }} kW · Rp {{ number_format((float) $charger->price_per_kwh, 0, ',', '.') }}/kWh</span>
                                @endforeach
                            </div>
                            <div class="station-foot">
                                <span class="station-distance" hidden></span>
                                <span class="station-open-label">Lihat detail stasiun</span>
                                <span aria-hidden="true">→</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="station-empty">
                    <strong>Belum ada charger yang tersedia</strong>
                    <p>Silakan cek kembali nanti atau lihat semua lokasi SPKLU.</p>
                    <a class="text-link" href="{{ route('locations.index') }}">Buka peta SPKLU <span aria-hidden="true">→</span></a>
                </div>
            @endif
        </section>

        @if ($vehicle)
            <section class="dashboard-panel" aria-labelledby="vehicleHeading">
                <div class="panel-heading">
                    <div>
                        <h2 id="vehicleHeading">Pilih kendaraan</h2>
                        <p>Kendaraan yang dipilih akan digunakan saat memulai sesi charging.</p>
                    </div>
                    <a class="text-link" href="{{ url('/profile') }}">Kelola kendaraan <span aria-hidden="true">→</span></a>
                </div>
                <div class="vehicle-summary">
                    <div class="vehicle-info">
                        <span class="vehicle-icon" aria-hidden="true">🚘</span>
                        <div>
                            <h3>Kendaraan aktif</h3>
                            <p>{{ $vehicle->merek }} {{ $vehicle->model }} · {{ $vehicle->nomor_polisi }} · {{ $vehicle->tipe_konektor }}</p>
                        </div>
                    </div>
                    <form class="vehicle-select-form" action="{{ route('vehicles.select') }}" method="POST">
                        @csrf
                        <div class="vehicle-select-field">
                            <label for="dashboardVehicle">Pilih kendaraan untuk charging</label>
                            <select id="dashboardVehicle" name="vehicle_id" required>
                                @foreach ($vehicles as $option)
                                    <option value="{{ $option->id_vehicle }}" @selected($option->id_vehicle === $vehicle->id_vehicle)>
                                        {{ $option->merek }} {{ $option->model }} · {{ $option->nomor_polisi }} · {{ $option->tipe_konektor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="vehicle-action" type="submit">Pilih kendaraan</button>
                    </form>
                </div>
            </section>
        @endif

        <nav class="dashboard-actions" aria-label="Akses cepat">
            @if ($vehicle && !$ongoingSession)
                <a class="dashboard-action primary" href="{{ route('scan.charge') }}">📷 Scan / mulai charging</a>
            @endif
            <a class="dashboard-action" href="{{ route('locations.index') }}">🗺️ Cari SPKLU</a>
            <a class="dashboard-action" href="{{ route('charging.history') }}">📋 Riwayat dan tagihan</a>
        </nav>
    </div>
</div>

<script>
    (() => {
        const toggle = document.getElementById('toggleSaldo');
        const balance = document.getElementById('saldoText');
        const visibleBalance = @json($visibleBalance);
        const locationButton = document.getElementById('sortByLocation');
        const locationStatus = document.getElementById('locationStatus');
        const stationGrid = document.getElementById('stationGrid');

        if (toggle && balance) {
            toggle.addEventListener('click', () => {
                const hidden = toggle.getAttribute('aria-pressed') !== 'true';
                balance.textContent = hidden ? 'Rp •••••••' : visibleBalance;
                toggle.setAttribute('aria-pressed', String(hidden));
                toggle.setAttribute('aria-label', hidden ? 'Tampilkan saldo' : 'Sembunyikan saldo');
            });
        }

        if (!locationButton || !locationStatus || !stationGrid) return;

        const stationCards = Array.from(stationGrid.querySelectorAll('.station-card'));
        const showStations = (stations) => {
            stations.forEach((station, index) => {
                stationGrid.appendChild(station);
                station.hidden = index >= 3;
            });
        };
        const showLocationError = (message) => {
            locationStatus.textContent = message;
            locationButton.disabled = false;
        };
        const distanceInKm = (latitudeA, longitudeA, latitudeB, longitudeB) => {
            const radians = (degrees) => degrees * Math.PI / 180;
            const latitudeDelta = radians(latitudeB - latitudeA);
            const longitudeDelta = radians(longitudeB - longitudeA);
            const arc = Math.sin(latitudeDelta / 2) ** 2
                + Math.cos(radians(latitudeA)) * Math.cos(radians(latitudeB))
                * Math.sin(longitudeDelta / 2) ** 2;
            const boundedArc = Math.min(1, Math.max(0, arc));

            return 6371 * 2 * Math.atan2(Math.sqrt(boundedArc), Math.sqrt(1 - boundedArc));
        };

        locationButton.addEventListener('click', () => {
            if (!navigator.geolocation) {
                showLocationError('Browser ini tidak mendukung fitur lokasi. Daftar tetap diurutkan berdasarkan nama.');
                return;
            }

            locationButton.disabled = true;
            locationStatus.textContent = 'Meminta akses lokasi perangkat Anda…';

            navigator.geolocation.getCurrentPosition((position) => {
                const { latitude, longitude } = position.coords;
                const sortedStations = stationCards
                    .map((station) => {
                        const stationLatitude = Number(station.dataset.lat);
                        const stationLongitude = Number(station.dataset.lng);
                        const distance = distanceInKm(latitude, longitude, stationLatitude, stationLongitude);

                        return { station, distance };
                    })
                    .filter(({ distance }) => Number.isFinite(distance))
                    .sort((first, second) => first.distance - second.distance);

                sortedStations.forEach(({ station, distance }) => {
                    const distanceLabel = station.querySelector('.station-distance');
                    distanceLabel.hidden = false;
                    distanceLabel.textContent = `${distance < 1
                        ? `${Math.round(distance * 1000)} m`
                        : `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 }).format(distance)} km`} dari Anda`;
                });

                showStations(sortedStations.map(({ station }) => station));
                locationStatus.textContent = sortedStations.length
                    ? 'Menampilkan hingga 3 SPKLU terdekat. Jarak merupakan perkiraan garis lurus.'
                    : 'Koordinat stasiun belum tersedia, daftar tidak dapat diurutkan berdasarkan jarak.';
                locationButton.disabled = false;
            }, (error) => {
                const messages = {
                    1: 'Izin lokasi ditolak. Izinkan akses lokasi di browser untuk mengurutkan SPKLU terdekat.',
                    2: 'Lokasi perangkat tidak dapat ditemukan. Coba lagi atau gunakan peta SPKLU.',
                    3: 'Permintaan lokasi terlalu lama. Coba lagi atau gunakan peta SPKLU.',
                };
                showLocationError(messages[error.code] || 'Lokasi tidak dapat diakses. Daftar tetap diurutkan berdasarkan nama.');
            }, { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 });
        });
    })();
</script>
@endsection
