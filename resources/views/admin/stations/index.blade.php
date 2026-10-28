@extends('layouts.app')

@section('title', 'Manajemen SPKLU & Charger')

@section('content')
<style>
    .station-management-page {
        width: min(100%, 1440px) !important;
        align-self: flex-start;
        margin: 0 auto !important;
        padding: 34px clamp(16px, 3vw, 40px) 48px !important;
        box-sizing: border-box;
    }

    .station-page-heading {
        margin-bottom: 26px !important;
    }

    .station-back-link {
        transition: color .2s ease, background .2s ease, transform .2s ease;
    }

    .station-back-link:hover {
        color: #047857 !important;
        background: #d1fae5 !important;
        transform: translateX(-2px);
    }

    .station-page-title {
        margin-bottom: 7px !important;
        font-size: clamp(24px, 3vw, 30px) !important;
        letter-spacing: -.7px;
    }

    .station-page-description {
        max-width: 640px;
        line-height: 1.65;
    }

    .station-layout {
        display: grid !important;
        grid-template-columns: minmax(310px, 370px) minmax(0, 1fr) !important;
        gap: 24px !important;
        align-items: start !important;
    }

    .station-panel {
        min-width: 0;
        padding: 26px !important;
        border: 1px solid #e6edf2 !important;
        border-radius: 18px !important;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .055) !important;
    }

    .station-panel-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 6px !important;
        color: #172554 !important;
        font-size: 17px !important;
        letter-spacing: -.2px;
    }

    .station-panel-subtitle {
        margin: 0 0 21px;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.5;
    }

    .station-heading-icon {
        display: grid;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        place-items: center;
        border-radius: 11px;
        color: #047857;
        background: #ecfdf5;
        font-size: 17px;
    }

    .station-form-group {
        margin-bottom: 16px !important;
    }

    .station-form-group label {
        display: block;
        margin-bottom: 7px !important;
        color: #475569 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }

    .station-management-page input:not([type="file"]),
    .station-management-page select,
    .station-management-page textarea {
        min-height: 42px;
        padding: 10px 12px !important;
        border: 1px solid #dbe3eb !important;
        border-radius: 9px !important;
        color: #334155 !important;
        background-color: #fff;
        font: inherit;
        font-size: 13px !important;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .station-management-page input:focus,
    .station-management-page select:focus,
    .station-management-page textarea:focus {
        border-color: #34d399 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, .13);
    }

    .station-management-page input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px dashed #cbd5e1;
        border-radius: 9px;
        background: #f8fafc;
        box-sizing: border-box;
    }

    .station-submit-button {
        min-height: 44px;
        box-shadow: 0 5px 12px rgba(5, 150, 105, .17);
        transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    .station-submit-button:hover {
        background: #047857 !important;
        box-shadow: 0 7px 16px rgba(5, 150, 105, .23);
        transform: translateY(-1px);
    }

    .station-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .station-count {
        padding: 6px 10px;
        border-radius: 999px;
        color: #047857;
        background: #ecfdf5;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .station-table-scroll {
        overflow-x: auto;
        border: 1px solid #edf1f5;
        border-radius: 12px;
    }

    .station-table {
        min-width: 720px;
    }

    .station-table thead {
        background: #f8fafc;
    }

    .station-table th {
        padding: 13px 14px !important;
        color: #64748b !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: .45px;
        text-transform: uppercase;
    }

    .station-table td {
        padding: 14px !important;
        vertical-align: middle;
    }

    .station-table tbody tr {
        transition: background .18s ease;
    }

    .station-table tbody tr:hover {
        background: #f8fbfa;
    }

    .station-identity {
        min-width: 250px;
    }

    .station-avatar {
        width: 46px !important;
        height: 46px !important;
        flex: 0 0 46px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(145deg, #f1f5f9, #e2e8f0) !important;
    }

    .station-address {
        display: block;
        max-width: 280px;
        margin-top: 3px;
        color: #94a3b8 !important;
        font-size: 11px !important;
        line-height: 1.5;
    }

    .charger-availability {
        margin-top: 9px;
    }

    .charger-availability-summary {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 6px;
    }

    .charger-availability-ready {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 999px;
        color: #047857;
        background: #ecfdf5;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .charger-availability-ready.none {
        color: #64748b;
        background: #f1f5f9;
    }

    .charger-availability-total {
        color: #94a3b8;
        font-size: 10px;
        white-space: nowrap;
    }

    .charger-availability-breakdown {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px 9px;
        color: #64748b;
        font-size: 10px;
    }

    .charger-availability-state {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .charger-availability-state::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #94a3b8;
        content: "";
    }

    .charger-availability-state.available::before {
        background: #10b981;
    }

    .charger-availability-state.in-use::before {
        background: #3b82f6;
    }

    .charger-availability-state.offline::before {
        background: #f59e0b;
    }

    .charger-availability-state.faulty::before {
        background: #ef4444;
    }

    .station-status {
        display: inline-flex;
        padding: 5px 10px !important;
        border-radius: 999px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        white-space: nowrap;
    }

    .station-status.status-aktif {
        color: #047857 !important;
        background: #dcfce7 !important;
    }

    .station-status.status-penuh {
        color: #c2410c !important;
        background: #ffedd5 !important;
    }

    .station-status.status-tutup_sementara {
        color: #b45309 !important;
        background: #fef3c7 !important;
    }

    .station-status.status-perawatan {
        color: #b91c1c !important;
        background: #fee2e2 !important;
    }

    .station-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px !important;
    }

    .station-action {
        min-height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 10px !important;
        border-radius: 8px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-decoration: none;
        white-space: nowrap;
        transition: background .2s ease, transform .2s ease;
    }

    .station-action:hover {
        transform: translateY(-1px);
    }

    .station-action-edit {
        color: #4338ca !important;
        background: #eef2ff !important;
    }

    .station-action-edit:hover {
        background: #e0e7ff !important;
    }

    .station-action-chargers {
        color: #fff !important;
        background: #2563eb !important;
    }

    .station-action-chargers:hover {
        background: #1d4ed8 !important;
    }

    .station-action-delete {
        min-height: 32px;
        padding: 6px 8px !important;
        border-radius: 8px;
        color: #dc2626 !important;
        background: transparent;
        transition: background .2s ease;
    }

    .station-action-delete:hover {
        background: #fef2f2 !important;
    }

    .station-empty-state {
        padding: 44px 20px !important;
        color: #64748b !important;
        text-align: center;
    }

    .station-alert {
        border: 1px solid #bbf7d0;
        box-shadow: 0 4px 14px rgba(22, 163, 74, .06);
    }

    .station-error {
        border: 1px solid #fecaca;
    }

    .charger-modal-backdrop {
        padding: 20px;
        background: rgba(15, 23, 42, .58) !important;
        backdrop-filter: blur(4px);
    }

    .charger-modal {
        box-shadow: 0 24px 70px rgba(15, 23, 42, .25);
        border: 1px solid rgba(255, 255, 255, .7);
    }

    .charger-modal input:not([type="hidden"]),
    .charger-modal select {
        width: 100%;
        min-height: 40px;
        box-sizing: border-box;
        border: 1px solid #dbe3eb !important;
        border-radius: 8px !important;
        background-color: #fff;
        font: inherit;
        font-size: 13px !important;
    }

    .charger-modal input:focus,
    .charger-modal select:focus {
        border-color: #34d399 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, .13);
    }

    @media (max-width: 920px) {
        .station-layout {
            grid-template-columns: minmax(0, 1fr) !important;
        }
    }

    @media (max-width: 600px) {
        .station-management-page {
            padding: 22px 14px 32px !important;
        }

        .station-page-heading {
            margin-bottom: 20px !important;
        }

        .station-panel {
            padding: 19px !important;
            border-radius: 15px !important;
        }

        .station-table-toolbar {
            align-items: flex-start;
        }

        .charger-modal-backdrop {
            padding: 12px;
        }

        .charger-modal {
            padding: 20px !important;
            max-height: 92vh !important;
        }

        .charger-modal-fields-two,
        .charger-modal-fields-three {
            grid-template-columns: minmax(0, 1fr) !important;
        }
    }
</style>

<div class="station-management-page" style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 20px;">
    
    <div class="station-page-heading" style="margin-bottom: 25px;">
        <a class="station-back-link" href="{{ route('operator.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 16px; padding: 9px 14px; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 9px; font-size: 13px; font-weight: 700; text-decoration: none;">
            ← Kembali ke Dashboard Operator
        </a>
        <h2 class="station-page-title" style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Manajemen Lokasi SPKLU & Charger</h2>
        <p class="station-page-description" style="color: #64748b; margin: 0;">Kelola pendaftaran stasiun SPKLU, status operasional, dan unit charger.</p>
    </div>

    @if(session('success'))
        <div class="station-alert" role="status" style="background: #dcfce7; color: #15803d; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="station-error" role="alert" style="background: #fee2e2; color: #b91c1c; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="station-layout" style="display: grid; grid-template-columns: 380px 1fr; gap: 25px; align-items: start;">
        
        <!-- FORM TAMBAH LOKASI STASIUN -->
        <div class="station-panel" style="background: #ffffff; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <h3 class="station-panel-heading"><span class="station-heading-icon" aria-hidden="true">+</span> Tambah Stasiun SPKLU Baru</h3>
            <p class="station-panel-subtitle">Lengkapi informasi lokasi untuk mendaftarkan stasiun baru.</p>
            
            <form action="{{ route('operator.stations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="station-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Nama Stasiun</label>
                    <input type="text" name="nama_lokasi" placeholder="SPKLU Duta Wacana" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                </div>

                <div class="station-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Jam Operasional</label>
                    <input type="text" name="jam_operasional" placeholder="24 Jam / 08.00 - 22.00" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                </div>

                <div class="station-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Status Lokasi</label>
                    <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; /* styling Anda yang lain... */ ">
                        <option value="aktif">Aktif</option>
                        <option value="tutup_sementara">Tutup Sementara</option>
                        <option value="penuh">Penuh</option>
                        <option value="perawatan">Dalam Perawatan</option>
                    </select>
                </div>

                <div class="station-form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Latitude</label>
                        <input type="text" name="latitude" placeholder="-7.7828" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Longitude</label>
                        <input type="text" name="longitude" placeholder="110.3671" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                    </div>
                </div>

                <div class="station-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Fasilitas</label>
                    <input type="text" name="fasilitas" placeholder="Toilet, Rest Area, WiFi" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                </div>

                <div class="station-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Foto Lokasi</label>
                    <input type="file" name="foto" accept="image/*" style="width: 100%; font-size: 12px; color: #64748b;">
                </div>

                <div class="station-form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" placeholder="Jl. Dr. Wahidin Sudirohusodo No. 5-25..." required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; box-sizing: border-box; resize: vertical;"></textarea>
                </div>

                <button class="station-submit-button" type="submit" style="width: 100%; background: #059669; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s;">
                    + Simpan Stasiun
                </button>
            </form>
        </div>

        <!-- DAFTAR STASIUN TERDAFTAR -->
        <div class="station-panel" style="background: #ffffff; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <div class="station-table-toolbar">
                <div>
                    <h3 class="station-panel-heading"><span class="station-heading-icon" aria-hidden="true">⚡</span> Daftar Stasiun SPKLU</h3>
                    <p class="station-panel-subtitle" style="margin: 0;">Pantau ketersediaan charger dan kelola stasiun terdaftar.</p>
                </div>
                <span class="station-count">{{ isset($locations) ? count($locations) : 0 }} lokasi</span>
            </div>

            <div class="station-table-scroll" style="overflow-x: auto;">
                <table class="station-table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b;">
                            <th style="padding: 12px; font-weight: 600;">Stasiun</th>
                            <th style="padding: 12px; font-weight: 600;">Operasional</th>
                            <th style="padding: 12px; font-weight: 600;">Status</th>
                            <th style="padding: 12px; font-weight: 600; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($locations) && count($locations) > 0)
                            @foreach($locations as $location)
                                <tr class="station-row" style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 12px;">
                                        <div class="station-identity" style="display: flex; gap: 10px; align-items: center;">
                                            @if(!empty($location->foto))
                                                <img class="station-avatar" src="{{ asset('storage/' . $location->foto) }}" alt="Foto {{ $location->nama_lokasi }}" style="width: 45px; height: 45px; border-radius: 8px; object-fit: cover;">
                                            @else
                                                <div class="station-avatar" style="width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px;">⚡</div>
                                            @endif
                                            <div>
                                                <strong style="color: #1e293b; display: block;">{{ $location->nama_lokasi }}</strong>
                                                <small class="station-address">{{ Str::limit($location->alamat, 45) }}</small>
                                                @php
                                                    $chargers = $location->chargers;
                                                    $readyChargers = $chargers->filter(fn ($charger) => $charger->is_online && $charger->status === 'tersedia')->count();
                                                    $inUseChargers = $chargers->filter(fn ($charger) => $charger->is_online && $charger->status === 'digunakan')->count();
                                                    $faultyChargers = $chargers->where('status', 'rusak')->count();
                                                    $offlineChargers = $chargers->filter(fn ($charger) => !$charger->is_online && $charger->status !== 'rusak')->count();
                                                @endphp
                                                <div class="charger-availability" aria-label="Ketersediaan charger di {{ $location->nama_lokasi }}">
                                                    <div class="charger-availability-summary">
                                                        <span class="charger-availability-ready {{ $readyChargers === 0 ? 'none' : '' }}">
                                                            {{ $readyChargers }} siap digunakan
                                                        </span>
                                                        <span class="charger-availability-total">
                                                            dari {{ $chargers->count() }} unit
                                                        </span>
                                                    </div>
                                                    @if($chargers->isNotEmpty())
                                                        <div class="charger-availability-breakdown">
                                                            @if($readyChargers > 0)
                                                                <span class="charger-availability-state available">{{ $readyChargers }} tersedia</span>
                                                            @endif
                                                            @if($inUseChargers > 0)
                                                                <span class="charger-availability-state in-use">{{ $inUseChargers }} digunakan</span>
                                                            @endif
                                                            @if($offlineChargers > 0)
                                                                <span class="charger-availability-state offline">{{ $offlineChargers }} offline</span>
                                                            @endif
                                                            @if($faultyChargers > 0)
                                                                <span class="charger-availability-state faulty">{{ $faultyChargers }} rusak</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 12px; color: #475569;">{{ $location->jam_operasional }}</td>
                                    <td style="padding: 12px;">
                                        @php
                                            $statusVal = $location->status ?? 'aktif';
                                        @endphp
                                        <span class="station-status status-{{ $statusVal }}">
                                            {{ str_replace('_', ' ', $statusVal) }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; text-align: right;">
                                        <div class="station-actions" style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <a class="station-action station-action-edit" href="{{ route('operator.stations.edit', $location->id_location) }}">Edit</a>
                                            <button class="station-action station-action-chargers" type="button" onclick="openChargerModal('{{ $location->id_location }}', @js($location->nama_lokasi))">
                                                🔌 Charger
                                            </button>
                                            <form action="{{ route('operator.stations.destroy', $location->id_location) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus stasiun ini?')" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="station-action-delete" type="submit">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="station-empty-state" colspan="4">
                                    <div style="font-size: 28px; margin-bottom: 9px;" aria-hidden="true">⚡</div>
                                    <strong style="display: block; margin-bottom: 4px; color: #334155;">Belum ada stasiun SPKLU</strong>
                                    Tambahkan stasiun pertama melalui formulir di samping.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KELOLA CHARGER -->
<div id="chargerModal" class="charger-modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center;">
    <div class="charger-modal" role="dialog" aria-modal="true" aria-labelledby="modalStationTitle" style="background: white; padding: 30px; border-radius: 16px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; color: #1e293b;" id="modalStationTitle">Kelola Charger</h3>
            <button type="button" onclick="closeChargerModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">✕</button>
        </div>

        <!-- FORM TAMBAH CHARGER BARU -->
        <form id="chargerForm" method="POST" style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
            @csrf
            
            <!-- 1. Input Hidden ID Lokasi (Diisi otomatis via JavaScript) -->
            <input type="hidden" name="location_id" id="modal_location_id">

            <!-- 2. Input Hidden Harga per kWh (Memenuhi validasi price_per_kwh required) -->
            <input type="hidden" name="price_per_kwh" value="2500">

            <h4 style="margin: 0 0 12px 0; font-size: 13px; color: #334155;">+ Tambah Unit Charger Baru</h4>
            
            <div class="charger-modal-fields-two" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                <!-- Disesuaikan ke device_number -->
                <input type="text" name="device_number" placeholder="No. Perangkat (Contoh: CHG-01)" required style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
                
                <!-- Disesuaikan ke connector_type -->
                <select name="connector_type" required style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: white;">
                    <option value="">-- Pilih Konektor --</option>
                    <option value="CCS2">CCS2 (Fast Charging)</option>
                    <option value="Type 2">Type 2 (AC)</option>
                    <option value="CHAdeMO">CHAdeMO</option>
                    <option value="GB/T">GB/T</option>
                </select>
            </div>

            <div class="charger-modal-fields-three" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                <!-- Disesuaikan ke max_power_kw -->
                <input type="number" step="0.01" name="max_power_kw" placeholder="Daya (kW)" required style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;">
                
                <!-- Disesuaikan ke is_online (Nilai 1 = Online, 0 = Offline) -->
                <select name="is_online" style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: white;">
                    <option value="1">Online</option>
                    <option value="0">Offline</option>
                </select>

                <!-- Disesuaikan ke status -->
                <select name="status" style="padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: white;">
                    <option value="tersedia">Tersedia</option>
                    <option value="digunakan">Digunakan</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>

            <button type="submit" style="width: 100%; background: #10b981; color: white; border: none; padding: 8px; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">Simpan Charger</button>
        </form>

        <p style="font-size: 12px; color: #64748b; margin-bottom: 10px;">Fitur pendaftaran charger akan langsung terhubung ke database stasiun ini.</p>
    </div>
</div>

<script>
    function openChargerModal(locationId, stationName) {
        document.getElementById('chargerForm').action = `/operator/stations/${locationId}/chargers`;
        document.getElementById('modal_location_id').value = locationId;
        document.getElementById('modalStationTitle').innerText = 'Kelola Charger - ' + stationName;
        document.getElementById('chargerModal').style.display = 'flex';
    }

    function closeChargerModal() {
        document.getElementById('chargerModal').style.display = 'none';
    }
</script>
@endsection