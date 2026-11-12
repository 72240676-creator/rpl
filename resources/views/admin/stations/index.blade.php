@extends('layouts.app')

@section('title', 'Manajemen SPKLU')

@section('content')
@php
    $stationCount = $locations->count();
    $activeStationCount = $locations->where('status', 'aktif')->count();
    $chargerCount = $locations->sum(fn ($location) => $location->chargers->count());
    $readyChargerCount = $locations->sum(fn ($location) => $location->chargers
        ->filter(fn ($charger) => $charger->is_online && $charger->status === 'tersedia')
        ->count());
    $attentionStationCount = $locations->filter(fn ($location) => $location->status === 'perawatan'
        || $location->chargers->contains(fn ($charger) => $charger->needs_inspection || $charger->status === 'rusak'))
        ->count();
@endphp

<style>
    .station-page {
        width: min(100%, 1360px);
        align-self: flex-start;
        margin: 0 auto;
        padding: 28px clamp(16px, 3vw, 40px) 48px;
        box-sizing: border-box;
        color: #0f172a;
    }

    .station-page-top {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        margin: 22px 0 25px;
    }

    .station-eyebrow {
        margin-bottom: 8px;
        color: #059669;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.1px;
        text-transform: uppercase;
    }

    .station-title {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: clamp(26px, 3vw, 34px);
        font-weight: 750;
        letter-spacing: -.8px;
    }

    .station-description {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .station-button {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        border: 1px solid transparent;
        border-radius: 10px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease, transform .18s ease;
        white-space: nowrap;
    }

    .station-button:hover {
        transform: translateY(-1px);
    }

    .station-button-primary {
        color: #fff;
        background: #059669;
        box-shadow: 0 5px 12px rgba(5, 150, 105, .16);
    }

    .station-button-primary:hover {
        background: #047857;
    }

    .station-button-secondary {
        color: #334155;
        background: #fff;
        border-color: #e2e8f0;
    }

    .station-button-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .station-button-danger {
        color: #b91c1c;
        background: #fff;
        border-color: #fee2e2;
    }

    .station-button-danger:hover {
        background: #fef2f2;
    }

    .station-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .station-stat {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 14px;
        padding: 17px 18px;
        border: 1px solid #e8edf2;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .025);
    }

    .station-stat-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border-radius: 12px;
        color: #047857;
        background: #ecfdf5;
        font-size: 18px;
    }

    .station-stat-label {
        margin-bottom: 4px;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
    }

    .station-stat-value {
        color: #0f172a;
        font-size: 23px;
        font-weight: 750;
        line-height: 1.1;
    }

    .station-stat-note {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 10px;
    }

    .station-panel {
        overflow: hidden;
        border: 1px solid #e6edf2;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .035);
    }

    .station-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 20px 22px;
        border-bottom: 1px solid #eef2f6;
    }

    .station-panel-title {
        margin: 0 0 5px;
        color: #0f172a;
        font-size: 16px;
        font-weight: 750;
    }

    .station-panel-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .station-tools {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .station-count {
        padding: 7px 10px;
        border-radius: 999px;
        color: #047857;
        background: #ecfdf5;
        font-size: 11px;
        font-weight: 750;
        white-space: nowrap;
    }

    .station-search {
        width: min(240px, 30vw);
        min-height: 38px;
        box-sizing: border-box;
        padding: 8px 11px;
        border: 1px solid #dbe3eb;
        border-radius: 9px;
        color: #334155;
        background: #fff;
        font: inherit;
        font-size: 12px;
    }

    .station-search:focus,
    .station-form input:focus,
    .station-form select:focus,
    .station-form textarea:focus,
    .charger-form input:focus,
    .charger-form select:focus {
        border-color: #34d399;
        outline: none;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, .12);
    }

    .station-list {
        display: grid;
        gap: 12px;
        padding: 16px;
    }

    .station-card {
        min-width: 0;
        padding: 18px;
        border: 1px solid #e8edf2;
        border-radius: 13px;
        background: #fff;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .station-card:hover {
        border-color: #cdeee1;
        box-shadow: 0 5px 16px rgba(15, 23, 42, .035);
    }

    .station-card-main {
        display: grid;
        grid-template-columns: minmax(240px, 1.5fr) minmax(140px, .7fr) minmax(170px, .9fr) auto;
        align-items: center;
        gap: 20px;
    }

    .station-identity {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 13px;
    }

    .station-photo {
        display: grid;
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        color: #047857;
        background: linear-gradient(145deg, #f0fdf8, #e2f5ec);
        font-size: 21px;
        object-fit: cover;
    }

    .station-name {
        overflow-wrap: anywhere;
        margin: 0 0 5px;
        color: #172554;
        font-size: 14px;
        font-weight: 750;
        line-height: 1.4;
    }

    .station-address {
        display: -webkit-box;
        overflow: hidden;
        color: #64748b;
        font-size: 11px;
        line-height: 1.5;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .station-facilities {
        margin-top: 5px;
        color: #94a3b8;
        font-size: 10px;
    }

    .station-info-label {
        margin-bottom: 6px;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .45px;
        text-transform: uppercase;
    }

    .station-info-value {
        color: #334155;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.5;
    }

    .station-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 750;
        white-space: nowrap;
    }

    .station-status::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        content: "";
    }

    .status-aktif { color: #047857; background: #dcfce7; }
    .status-penuh { color: #c2410c; background: #ffedd5; }
    .status-tutup_sementara { color: #a16207; background: #fef3c7; }
    .status-perawatan { color: #b91c1c; background: #fee2e2; }

    .station-charger-summary {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
    }

    .charger-summary-pill {
        padding: 4px 7px;
        border-radius: 999px;
        color: #047857;
        background: #ecfdf5;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .charger-summary-pill.muted {
        color: #64748b;
        background: #f1f5f9;
    }

    .station-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 7px;
    }

    .station-action {
        min-height: 36px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 11px;
    }

    .station-action-chargers {
        color: #fff;
        background: #0f766e;
    }

    .station-action-chargers:hover {
        background: #115e59;
    }

    .station-charger-details {
        margin: 16px 0 0 65px;
        border-top: 1px solid #eef2f6;
    }

    .station-charger-details summary {
        padding: 12px 0 2px;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .station-charger-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 9px;
        padding-top: 11px;
    }

    .station-charger-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 12px;
        border: 1px solid #eef2f6;
        border-radius: 10px;
        background: #fbfdfe;
    }

    .station-charger-name {
        color: #1e293b;
        font-size: 11px;
        font-weight: 750;
    }

    .station-charger-meta {
        margin-top: 4px;
        color: #64748b;
        font-size: 10px;
        line-height: 1.5;
    }

    .station-charger-actions {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 8px;
    }

    .station-link {
        color: #047857;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
    }

    .station-link:hover {
        text-decoration: underline;
    }

    .station-delete-link {
        padding: 0;
        border: 0;
        color: #b91c1c;
        background: transparent;
        font: inherit;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
    }

    .station-delete-link:hover {
        text-decoration: underline;
    }

    .station-empty {
        padding: 48px 20px;
        color: #64748b;
        text-align: center;
    }

    .station-empty-icon {
        display: grid;
        width: 52px;
        height: 52px;
        margin: 0 auto 13px;
        place-items: center;
        border-radius: 15px;
        color: #047857;
        background: #ecfdf5;
        font-size: 23px;
    }

    .station-alert {
        margin-bottom: 16px;
        padding: 12px 15px;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        color: #047857;
        background: #f0fdf4;
        font-size: 13px;
    }

    .station-alert.error {
        border-color: #fecaca;
        color: #b91c1c;
        background: #fef2f2;
    }

    .station-modal-backdrop {
        position: fixed;
        z-index: 1000;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 23, 42, .58);
        backdrop-filter: blur(3px);
    }

    .station-modal-backdrop.open {
        display: flex;
    }

    .station-modal {
        width: min(100%, 680px);
        max-height: min(92vh, 850px);
        overflow-y: auto;
        padding: 24px;
        border: 1px solid rgba(255,255,255,.7);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 25px 80px rgba(15, 23, 42, .28);
    }

    .station-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
    }

    .station-modal-title {
        margin: 0 0 5px;
        color: #0f172a;
        font-size: 19px;
        font-weight: 750;
    }

    .station-modal-description {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .station-modal-close {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border: 0;
        border-radius: 9px;
        color: #64748b;
        background: #f1f5f9;
        font-size: 17px;
        cursor: pointer;
    }

    .station-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .station-form-field {
        min-width: 0;
    }

    .station-form-field.full {
        grid-column: 1 / -1;
    }

    .station-form-field label {
        display: block;
        margin-bottom: 6px;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
    }

    .station-form input,
    .station-form select,
    .station-form textarea,
    .charger-form input,
    .charger-form select {
        width: 100%;
        min-height: 41px;
        box-sizing: border-box;
        padding: 9px 11px;
        border: 1px solid #dbe3eb;
        border-radius: 9px;
        color: #334155;
        background: #fff;
        font: inherit;
        font-size: 12px;
    }

    .station-form textarea {
        min-height: 76px;
        resize: vertical;
    }

    .station-form input[type="file"] {
        padding: 8px;
        background: #f8fafc;
    }

    .station-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 21px;
        padding-top: 16px;
        border-top: 1px solid #eef2f6;
    }

    .charger-modal {
        width: min(100%, 540px);
    }

    .charger-form {
        padding: 16px;
        border: 1px solid #e8edf2;
        border-radius: 12px;
        background: #f8fafc;
    }

    .charger-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 10px;
    }

    .charger-form-grid.three {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .charger-form-label {
        display: block;
        margin: 0 0 10px;
        color: #334155;
        font-size: 12px;
        font-weight: 750;
    }

    .station-no-results {
        display: none;
        padding: 30px 18px;
        color: #64748b;
        text-align: center;
        font-size: 12px;
    }

    @media (max-width: 1050px) {
        .station-card-main {
            grid-template-columns: minmax(0, 1fr) minmax(130px, .6fr) minmax(145px, .7fr);
        }

        .station-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 760px) {
        .station-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .station-card-main {
            grid-template-columns: minmax(0, 1fr) minmax(120px, .7fr);
            gap: 15px;
        }

        .station-identity {
            grid-column: 1 / -1;
        }

        .station-charger-details {
            margin-left: 0;
        }
    }

    @media (max-width: 560px) {
        .station-page {
            padding: 20px 13px 32px;
        }

        .station-page-top {
            align-items: stretch;
            flex-direction: column;
            gap: 15px;
            margin: 18px 0 20px;
        }

        .station-page-top .station-button {
            width: 100%;
        }

        .station-stats {
            gap: 9px;
        }

        .station-stat {
            gap: 10px;
            padding: 13px 11px;
        }

        .station-stat-icon {
            width: 35px;
            height: 35px;
            flex-basis: 35px;
            font-size: 15px;
        }

        .station-stat-value {
            font-size: 19px;
        }

        .station-panel-header {
            align-items: stretch;
            flex-direction: column;
            padding: 17px;
        }

        .station-tools {
            justify-content: space-between;
        }

        .station-search {
            width: 100%;
            flex: 1;
        }

        .station-list {
            gap: 10px;
            padding: 10px;
        }

        .station-card {
            padding: 14px;
        }

        .station-card-main {
            grid-template-columns: minmax(0, 1fr);
            gap: 13px;
        }

        .station-identity {
            grid-column: auto;
        }

        .station-actions {
            grid-column: auto;
        }

        .station-actions .station-button {
            flex: 1;
        }

        .station-form-grid,
        .charger-form-grid,
        .charger-form-grid.three {
            grid-template-columns: minmax(0, 1fr);
        }

        .station-form-field.full {
            grid-column: auto;
        }

        .station-modal {
            padding: 19px;
        }

        .station-modal-actions .station-button {
            flex: 1;
        }
    }
</style>

<div class="station-page">
    <x-back-link href="{{ route('operator.dashboard') }}" label="Kembali ke Dashboard Operator" />

    <header class="station-page-top">
        <div>
            <div class="station-eyebrow">Operasional</div>
            <h1 class="station-title">Manajemen SPKLU</h1>
            <p class="station-description">Kelola lokasi, pantau ketersediaan charger, dan atur informasi operasional.</p>
        </div>
        <button class="station-button station-button-primary" type="button" onclick="openStationModal('create')">
            <span aria-hidden="true">＋</span> Tambah Stasiun
        </button>
    </header>

    @if (session('success'))
        <div class="station-alert" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="station-alert error" role="alert">
            <strong>Periksa kembali data yang dimasukkan.</strong>
            <ul style="margin: 7px 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="station-stats" aria-label="Ringkasan stasiun">
        <article class="station-stat">
            <span class="station-stat-icon" aria-hidden="true">⌖</span>
            <div>
                <div class="station-stat-label">Total stasiun</div>
                <div class="station-stat-value">{{ $stationCount }}</div>
                <div class="station-stat-note">Lokasi terdaftar</div>
            </div>
        </article>
        <article class="station-stat">
            <span class="station-stat-icon" aria-hidden="true">●</span>
            <div>
                <div class="station-stat-label">Stasiun aktif</div>
                <div class="station-stat-value">{{ $activeStationCount }}</div>
                <div class="station-stat-note">Siap melayani pengguna</div>
            </div>
        </article>
        <article class="station-stat">
            <span class="station-stat-icon" aria-hidden="true">⚡</span>
            <div>
                <div class="station-stat-label">Total charger</div>
                <div class="station-stat-value">{{ $chargerCount }}</div>
                <div class="station-stat-note">Unit di seluruh stasiun</div>
            </div>
        </article>
        <article class="station-stat">
            <span class="station-stat-icon" aria-hidden="true">↗</span>
            <div>
                <div class="station-stat-label">Siap digunakan</div>
                <div class="station-stat-value">{{ $readyChargerCount }}</div>
                <div class="station-stat-note">
                    @if ($attentionStationCount > 0)
                        {{ $attentionStationCount }} stasiun perlu perhatian
                    @else
                        Ketersediaan charger saat ini
                    @endif
                </div>
            </div>
        </article>
    </section>

    <section class="station-panel" aria-labelledby="station-list-title">
        <header class="station-panel-header">
            <div>
                <h2 class="station-panel-title" id="station-list-title">Daftar stasiun</h2>
                <p class="station-panel-subtitle">Informasi operasional dan ketersediaan unit charger.</p>
            </div>
            <div class="station-tools">
                <label for="stationSearch" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Cari stasiun</label>
                <input class="station-search" id="stationSearch" type="search" placeholder="Cari nama atau alamat...">
                <span class="station-count"><span id="visibleStationCount">{{ $stationCount }}</span> lokasi</span>
            </div>
        </header>

        <div class="station-list" id="stationList">
            @forelse ($locations as $location)
                @php
                    $chargers = $location->chargers;
                    $readyChargers = $chargers->filter(fn ($charger) => $charger->is_online && $charger->status === 'tersedia')->count();
                    $inUseChargers = $chargers->filter(fn ($charger) => $charger->is_online && $charger->status === 'digunakan')->count();
                    $faultyChargers = $chargers->where('status', 'rusak')->count();
                    $offlineChargers = $chargers->filter(fn ($charger) => !$charger->is_online && $charger->status !== 'rusak')->count();
                    $statusValue = $location->status ?? 'aktif';
                    $statusLabels = [
                        'aktif' => 'Aktif',
                        'penuh' => 'Penuh',
                        'tutup_sementara' => 'Tutup sementara',
                        'perawatan' => 'Dalam perawatan',
                    ];
                    $searchContent = strtolower($location->nama_lokasi . ' ' . $location->alamat . ' ' . $statusValue);
                @endphp
                <article class="station-card" aria-label="Ketersediaan charger di {{ $location->nama_lokasi }}" data-search="{{ $searchContent }}">
                    <div class="station-card-main">
                        <div class="station-identity">
                            @if ($location->foto)
                                <img class="station-photo" src="{{ asset('storage/' . $location->foto) }}" alt="Foto {{ $location->nama_lokasi }}">
                            @else
                                <span class="station-photo" aria-hidden="true">⚡</span>
                            @endif
                            <div style="min-width:0;">
                                <h3 class="station-name">{{ $location->nama_lokasi }}</h3>
                                <div class="station-address">{{ $location->alamat }}</div>
                                @if ($location->fasilitas)
                                    <div class="station-facilities">Fasilitas: {{ $location->fasilitas }}</div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="station-info-label">Jam operasional</div>
                            <div class="station-info-value">{{ $location->jam_operasional ?: 'Belum diatur' }}</div>
                            <div class="station-charger-summary">
                                <span class="charger-summary-pill {{ $readyChargers ? '' : 'muted' }}">{{ $readyChargers }} siap digunakan</span>
                                <span class="charger-summary-pill muted">dari {{ $chargers->count() }} unit</span>
                            </div>
                        </div>

                        <div>
                            <div class="station-info-label">Status stasiun</div>
                            <span class="station-status status-{{ $statusValue }}">{{ $statusLabels[$statusValue] ?? ucfirst(str_replace('_', ' ', $statusValue)) }}</span>
                            @if ($chargers->isNotEmpty())
                                <div class="station-charger-summary">
                                    @if ($readyChargers > 0)
                                        <span class="charger-summary-pill">{{ $readyChargers }} tersedia</span>
                                    @endif
                                    @if ($inUseChargers > 0)
                                        <span class="charger-summary-pill muted">{{ $inUseChargers }} digunakan</span>
                                    @endif
                                    @if ($offlineChargers > 0)
                                        <span class="charger-summary-pill muted">{{ $offlineChargers }} offline</span>
                                    @endif
                                    @if ($faultyChargers > 0)
                                        <span class="charger-summary-pill muted">{{ $faultyChargers }} rusak</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="station-actions">
                            <button
                                class="station-button station-action station-action-chargers"
                                type="button"
                                onclick="openChargerModal('{{ $location->id_location }}', @js($location->nama_lokasi))"
                            >＋ Charger</button>
                            <button
                                class="station-button station-button-secondary station-action"
                                type="button"
                                data-update-url="{{ route('operator.stations.update', $location->id_location) }}"
                                data-station-id="{{ $location->id_location }}"
                                data-name="{{ $location->nama_lokasi }}"
                                data-address="{{ $location->alamat }}"
                                data-hours="{{ $location->jam_operasional }}"
                                data-status="{{ $statusValue }}"
                                data-latitude="{{ $location->latitude }}"
                                data-longitude="{{ $location->longitude }}"
                                data-facilities="{{ $location->fasilitas }}"
                                onclick="openStationModal('edit', this)"
                            >Edit</button>
                            <form action="{{ route('operator.stations.destroy', $location->id_location) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus stasiun ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="station-button station-button-danger station-action" type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>

                    @if ($chargers->isNotEmpty())
                        <details class="station-charger-details">
                            <summary>Lihat {{ $chargers->count() }} unit charger</summary>
                            <div class="station-charger-list">
                                @foreach ($chargers as $charger)
                                    <div class="station-charger-item">
                                        <div>
                                            <div class="station-charger-name">{{ $charger->device_number }}</div>
                                            <div class="station-charger-meta">
                                                {{ $charger->connector_type }} · {{ number_format((float) $charger->max_power_kw, 0, ',', '.') }} kW
                                                <br>Rp{{ number_format((float) $charger->price_per_kwh, 0, ',', '.') }}/kWh ·
                                                {{ !$charger->is_online ? 'Offline' : ucfirst($charger->status) }}
                                            </div>
                                        </div>
                                        <div class="station-charger-actions">
                                            <a class="station-link" href="{{ route('operator.chargers.tariff.edit', $charger->id_charger) }}">Atur tarif</a>
                                            <form action="{{ route('operator.chargers.destroy', $charger->id_charger) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus charger ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="station-delete-link" type="submit">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </article>
            @empty
                <div class="station-empty">
                    <span class="station-empty-icon" aria-hidden="true">⚡</span>
                    <strong style="display:block;margin-bottom:5px;color:#334155;">Belum ada stasiun SPKLU</strong>
                    Tambahkan stasiun pertama untuk mulai mengelola jaringan charger.
                </div>
            @endforelse
            <div class="station-no-results" id="stationNoResults">Tidak ada stasiun yang cocok dengan pencarian.</div>
        </div>
    </section>
</div>

<div class="station-modal-backdrop" id="stationModal" onclick="if (event.target === this) closeStationModal()">
    <section class="station-modal" role="dialog" aria-modal="true" aria-labelledby="stationModalTitle">
        <header class="station-modal-header">
            <div>
                <h2 class="station-modal-title" id="stationModalTitle">Tambah stasiun</h2>
                <p class="station-modal-description" id="stationModalDescription">Lengkapi detail untuk mendaftarkan lokasi SPKLU.</p>
            </div>
            <button class="station-modal-close" type="button" aria-label="Tutup" onclick="closeStationModal()">×</button>
        </header>

        <form class="station-form" id="stationForm" action="{{ route('operator.stations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input id="stationMethod" type="hidden" name="_method" value="PUT" disabled>
            <input id="stationFormMode" type="hidden" name="form_mode" value="create">
            <input id="stationFormId" type="hidden" name="form_station_id" value="">

            <div class="station-form-grid">
                <div class="station-form-field">
                    <label for="stationName">Nama stasiun</label>
                    <input id="stationName" name="nama_lokasi" type="text" placeholder="Contoh: SPKLU Sudirman" value="{{ old('nama_lokasi') }}" required>
                </div>
                <div class="station-form-field">
                    <label for="stationHours">Jam operasional</label>
                    <input id="stationHours" name="jam_operasional" type="text" placeholder="24 Jam atau 08.00–22.00" value="{{ old('jam_operasional', '24 Jam') }}" required>
                </div>
                <div class="station-form-field">
                    <label for="stationStatus">Status operasional</label>
                    <select id="stationStatus" name="status" required>
                        @foreach (['aktif' => 'Aktif', 'tutup_sementara' => 'Tutup sementara', 'penuh' => 'Penuh', 'perawatan' => 'Dalam perawatan'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'aktif') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="station-form-field">
                    <label for="stationPhoto">Foto lokasi <span style="color:#94a3b8;font-weight:500;">(opsional)</span></label>
                    <input id="stationPhoto" name="foto" type="file" accept="image/jpeg,image/png,image/jpg">
                </div>
                <div class="station-form-field">
                    <label for="stationLatitude">Latitude</label>
                    <input id="stationLatitude" name="latitude" type="number" step="any" placeholder="-7.7828" value="{{ old('latitude') }}" required>
                </div>
                <div class="station-form-field">
                    <label for="stationLongitude">Longitude</label>
                    <input id="stationLongitude" name="longitude" type="number" step="any" placeholder="110.3671" value="{{ old('longitude') }}" required>
                </div>
                <div class="station-form-field full">
                    <label for="stationAddress">Alamat lengkap</label>
                    <textarea id="stationAddress" name="alamat" placeholder="Nama jalan, nomor, kota, dan kode pos" required>{{ old('alamat') }}</textarea>
                </div>
                <div class="station-form-field full">
                    <label for="stationFacilities">Fasilitas <span style="color:#94a3b8;font-weight:500;">(opsional)</span></label>
                    <input id="stationFacilities" name="fasilitas" type="text" placeholder="Toilet, area istirahat, Wi-Fi" value="{{ old('fasilitas') }}">
                </div>
            </div>

            <footer class="station-modal-actions">
                <button class="station-button station-button-secondary" type="button" onclick="closeStationModal()">Batal</button>
                <button class="station-button station-button-primary" type="submit" id="stationSubmitButton">Simpan Stasiun</button>
            </footer>
        </form>
    </section>
</div>

<div class="station-modal-backdrop" id="chargerModal" onclick="if (event.target === this) closeChargerModal()">
    <section class="station-modal charger-modal" role="dialog" aria-modal="true" aria-labelledby="chargerModalTitle">
        <header class="station-modal-header">
            <div>
                <h2 class="station-modal-title" id="chargerModalTitle">Tambah unit charger</h2>
                <p class="station-modal-description">Daftarkan charger baru untuk stasiun yang dipilih.</p>
            </div>
            <button class="station-modal-close" type="button" aria-label="Tutup" onclick="closeChargerModal()">×</button>
        </header>

        <form class="charger-form" id="chargerForm" method="POST">
            @csrf
            <input type="hidden" name="price_per_kwh" value="2500">
            <label class="charger-form-label" for="chargerDeviceNumber">Informasi unit</label>
            <div class="charger-form-grid">
                <input id="chargerDeviceNumber" type="text" name="device_number" placeholder="Nomor perangkat, contoh CHG-01" required>
                <select name="connector_type" aria-label="Tipe konektor" required>
                    <option value="">Pilih konektor</option>
                    <option value="CCS2">CCS2</option>
                    <option value="Type 2">Type 2</option>
                    <option value="CHAdeMO">CHAdeMO</option>
                    <option value="GB/T">GB/T</option>
                </select>
            </div>
            <div class="charger-form-grid three">
                <input type="number" step="0.01" min="1" name="max_power_kw" placeholder="Daya (kW)" aria-label="Daya maksimum (kW)" required>
                <select name="status" aria-label="Status charger" required>
                    <option value="tersedia">Tersedia</option>
                    <option value="digunakan">Digunakan</option>
                    <option value="rusak">Rusak</option>
                </select>
                <select name="is_online" aria-label="Koneksi charger" required>
                    <option value="1">Online</option>
                    <option value="0">Offline</option>
                </select>
            </div>
            <button class="station-button station-button-primary" type="submit" style="width:100%;">Simpan Charger</button>
        </form>
    </section>
</div>

<script>
    (() => {
        const stationModal = document.getElementById('stationModal');
        const stationForm = document.getElementById('stationForm');
        const stationMethod = document.getElementById('stationMethod');
        const stationSearch = document.getElementById('stationSearch');
        const stationCards = [...document.querySelectorAll('.station-card')];
        const oldFormValues = @js(old());

        const setField = (name, value) => {
            const field = stationForm.elements.namedItem(name);
            if (field && value !== undefined && value !== null) field.value = value;
        };

        window.openStationModal = (mode, trigger = null) => {
            stationForm.reset();
            stationForm.action = @js(route('operator.stations.store'));
            stationMethod.disabled = true;
            document.getElementById('stationFormMode').value = mode;
            document.getElementById('stationFormId').value = '';

            if (mode === 'edit' && trigger) {
                stationForm.action = trigger.dataset.updateUrl;
                stationMethod.disabled = false;
                document.getElementById('stationFormId').value = trigger.dataset.stationId;
                document.getElementById('stationModalTitle').textContent = 'Edit informasi stasiun';
                document.getElementById('stationModalDescription').textContent = 'Perbarui informasi operasional dan lokasi SPKLU.';
                document.getElementById('stationSubmitButton').textContent = 'Simpan Perubahan';
                setField('nama_lokasi', trigger.dataset.name);
                setField('jam_operasional', trigger.dataset.hours);
                setField('status', trigger.dataset.status);
                setField('latitude', trigger.dataset.latitude);
                setField('longitude', trigger.dataset.longitude);
                setField('alamat', trigger.dataset.address);
                setField('fasilitas', trigger.dataset.facilities);
            } else {
                document.getElementById('stationModalTitle').textContent = 'Tambah stasiun';
                document.getElementById('stationModalDescription').textContent = 'Lengkapi detail untuk mendaftarkan lokasi SPKLU.';
                document.getElementById('stationSubmitButton').textContent = 'Simpan Stasiun';
                setField('jam_operasional', '24 Jam');
                setField('status', 'aktif');
            }

            stationModal.classList.add('open');
            document.body.style.overflow = 'hidden';
            const firstField = document.getElementById('stationName');
            if (firstField) firstField.focus();
        };

        window.closeStationModal = () => {
            stationModal.classList.remove('open');
            document.body.style.overflow = '';
        };

        window.openChargerModal = (locationId, stationName) => {
            const modal = document.getElementById('chargerModal');
            const form = document.getElementById('chargerForm');
            form.reset();
            form.action = `/operator/stations/${encodeURIComponent(locationId)}/chargers`;
            document.getElementById('chargerModalTitle').textContent = `Tambah charger · ${stationName}`;
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
            document.getElementById('chargerDeviceNumber').focus();
        };

        window.closeChargerModal = () => {
            document.getElementById('chargerModal').classList.remove('open');
            document.body.style.overflow = '';
        };

        if (stationSearch) {
            stationSearch.addEventListener('input', () => {
                const query = stationSearch.value.trim().toLowerCase();
                let visibleCount = 0;
                stationCards.forEach((card) => {
                    const visible = card.dataset.search.includes(query);
                    card.hidden = !visible;
                    if (visible) visibleCount++;
                });
                document.getElementById('visibleStationCount').textContent = visibleCount;
                document.getElementById('stationNoResults').style.display = visibleCount === 0 && stationCards.length ? 'block' : 'none';
            });
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeStationModal();
                closeChargerModal();
            }
        });

        if (oldFormValues.form_mode === 'create' || oldFormValues.form_mode === 'edit') {
            const mode = oldFormValues.form_mode;
            if (mode === 'edit') {
                const editButton = document.querySelector(`[data-station-id="${CSS.escape(String(oldFormValues.form_station_id))}"]`);
                if (editButton) openStationModal('edit', editButton);
            } else {
                openStationModal('create');
            }
            Object.entries(oldFormValues).forEach(([name, value]) => setField(name, value));
        }
    })();
</script>
@endsection
