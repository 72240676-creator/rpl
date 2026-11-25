@extends('layouts.app')

@section('title', 'Detail Charger')

@section('content')

@php
    $chargerId = is_object($charger)
        ? ($charger->id_charger ?? $charger->id)
        : $charger;

    $connectorType = is_object($charger)
        ? ($charger->connector_type ?? 'Fast Charging (DC)')
        : 'Fast Charging (DC)';

    $maxPower = is_object($charger)
        ? ($charger->max_power_kw ?? 50)
        : 50;

    $pricePerKwh = is_object($charger)
        ? ($charger->price_per_kwh ?? 2467)
        : 2467;

    $availableBalance = (int) (auth()->user()->saldo ?? 0);
    $maxReservation = min($availableBalance, 5000000);

    $previousUrl = url()->previous();
    $currentUrl = url()->current();
    $backUrl = ($previousUrl && $previousUrl !== $currentUrl && !str_contains($previousUrl, 'login'))
        ? $previousUrl
        : route('scan.charge');
@endphp

<div style="
    padding: 30px;
    max-width: 800px;
    margin: auto;
">

    <div style="margin-bottom: 25px;">

        <x-back-link 
            href="{{ $backUrl }}" 
            label="Kembali" 
            onclick="if (window.history.length > 1 && document.referrer && document.referrer !== window.location.href) { window.history.back(); return false; }"
        />

        <h2 style="
            margin: 15px 0 8px 0;
            color: #0f172a;
        ">
            Detail Unit Charger
        </h2>

        <p style="
            margin: 0;
            color: #64748b;
        ">
            Informasi spesifikasi dan status pengisi daya kendaraan listrik.
        </p>

    </div>

    <div style="
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    ">

        @if (session('error'))
            <div style="
                background-color: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
                padding: 14px 18px;
                border-radius: 12px;
                margin-bottom: 20px;
                font-size: 14px;
            ">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="
                background-color: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
                padding: 14px 18px;
                border-radius: 12px;
                margin-bottom: 20px;
                font-size: 14px;
            ">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        ">

            <h3 style="
                margin: 0;
                color: #1e293b;
            ">
                Charger ID: #{{ $chargerId }}
            </h3>

            <span style="
                background: #dcfce7;
                color: #15803d;
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 700;
            ">
                Tersedia
            </span>

        </div>

        <div style="
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        ">

            <div style="
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #e2e8f0;
            ">
                <span style="color: #64748b;">
                    Tipe Daya
                </span>

                <strong style="color: #0f172a;">
                    {{ $connectorType }}
                </strong>
            </div>

            <div style="
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #e2e8f0;
            ">
                <span style="color: #64748b;">
                    Daya Maksimal
                </span>

                <strong style="color: #0f172a;">
                    {{ $maxPower }} kW
                </strong>
            </div>

            <div style="
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
            ">
                <span style="color: #64748b;">
                    Tarif per kWh
                </span>

                <strong style="color: #2563eb;">
                    Rp {{ number_format((float)$pricePerKwh, 0, ',', '.') }} / kWh
                </strong>
            </div>

        </div>

        <div style="
            background: #fffbeb;
            color: #92400e;
            padding: 15px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 25px;
        ">
            💡 Pastikan kendaraan sudah terhubung dengan charger sebelum memulai pengisian daya.
        </div>

        {{-- FORM MULAI PENGISIAN --}}
        <form
            id="startChargingForm"
            action="{{ route('charging.start') }}"
            method="POST"
            style="margin: 0; width: 100%;"
        >

            @csrf

            <input
                type="hidden"
                name="charger_id"
                value="{{ $chargerId }}"
            >

            {{-- Pilihan Kendaraan --}}
            <div style="margin-bottom: 20px;">
                <label
                    for="vehicle_id"
                    style="
                        display: block;
                        margin-bottom: 8px;
                        font-weight: 600;
                        color: #0f172a;
                        font-size: 14px;
                    "
                >
                    Pilih Kendaraan untuk Pengisian Daya:
                </label>

                <select
                    name="vehicle_id"
                    id="vehicle_id"
                    required
                    onchange="updateCompatibilityNotice()"
                    style="
                        width: 100%;
                        padding: 12px;
                        border-radius: 12px;
                        border: 1px solid #cbd5e1;
                        background: #f8fafc;
                        color: #0f172a;
                        font-size: 14px;
                    "
                >
                    <option value="" data-connector="" data-compatible="0">
                        -- Pilih Kendaraan Anda --
                    </option>

                    @if(isset($vehicles) && count($vehicles) > 0)
                        @foreach($vehicles as $vehicle)
                            @php
                                $vConn = strtolower($vehicle->tipe_konektor ?? '');
                                $cConn = strtolower($connectorType);
                                $isMatch = false;
                                if (str_contains($vConn, 'ccs') && (str_contains($cConn, 'ccs') || str_contains($cConn, 'type'))) {
                                    $isMatch = true;
                                } elseif (str_contains($vConn, 'type') && str_contains($cConn, 'type')) {
                                    $isMatch = true;
                                } elseif ($vConn === $cConn) {
                                    $isMatch = true;
                                }
                            @endphp
                            <option 
                                value="{{ $vehicle->id_vehicle }}" @selected((string) $selectedVehicleId === (string) $vehicle->id_vehicle)
                                data-connector="{{ $vehicle->tipe_konektor ?? 'Standar' }}"
                                data-name="{{ $vehicle->merek }} {{ $vehicle->model }}"
                                data-compatible="{{ $isMatch ? '1' : '0' }}"
                            >
                                {{ $vehicle->merek }} {{ $vehicle->model }} ({{ $vehicle->nomor_polisi }}) · 🔌 {{ $vehicle->tipe_konektor ?? 'Standar' }} {{ $isMatch ? '✓ Cocok' : '' }}
                            </option>
                        @endforeach
                    @endif
                </select>

                {{-- Kotak Status Kompatibilitas Realtime --}}
                <div id="compat-notice" style="margin-top: 10px; padding: 12px 14px; border-radius: 10px; font-size: 13px; display: none;"></div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="reserved_amount" style="display: block; margin-bottom: 8px; font-weight: 600; color: #0f172a; font-size: 14px;">
                    Batas dana sesi (Rp):
                </label>
                
                {{-- Quick Nominal Chips --}}
                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px;">
                    @php
                        $nominalPresets = [25000, 50000, 100000];
                    @endphp
                    @foreach($nominalPresets as $preset)
                        @if($preset <= $maxReservation)
                            <button 
                                type="button" 
                                class="chip-preset-btn"
                                onclick="setNominal({{ $preset }}, this)"
                                style="
                                    background: #f1f5f9;
                                    border: 1px solid #cbd5e1;
                                    color: #334155;
                                    padding: 6px 14px;
                                    border-radius: 20px;
                                    font-size: 12.5px;
                                    font-weight: 700;
                                    cursor: pointer;
                                    transition: all 0.15s ease;
                                "
                            >
                                Rp {{ number_format($preset, 0, ',', '.') }}
                            </button>
                        @endif
                    @endforeach

                    @if($maxReservation >= 10000)
                        <button 
                            type="button" 
                            class="chip-preset-btn"
                            onclick="setNominal({{ $maxReservation }}, this)"
                            style="
                                background: #f1f5f9;
                                border: 1px solid #cbd5e1;
                                color: #334155;
                                padding: 6px 14px;
                                border-radius: 20px;
                                font-size: 12.5px;
                                font-weight: 700;
                                cursor: pointer;
                                transition: all 0.15s ease;
                            "
                        >
                            Maksimal (Rp {{ number_format($maxReservation, 0, ',', '.') }})
                        </button>
                    @endif
                </div>

                <input
                    type="number"
                    name="reserved_amount"
                    id="reserved_amount"
                    min="10000"
                    max="{{ $maxReservation }}"
                    step="1000"
                    value="{{ old('reserved_amount', min(50000, $maxReservation)) }}"
                    required
                    style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 14px; box-sizing: border-box;"
                >
                <p style="margin: 7px 0 0; color: #64748b; font-size: 12px;">
                    Saldo tersedia Rp {{ number_format($availableBalance, 0, ',', '.') }}.
                    Dana ini ditahan saat mulai, biaya aktual dipotong dan sisanya dikembalikan otomatis.
                    @if($maxReservation < 10000)
                        <a href="{{ route('topup.index') }}" style="color: #059669; font-weight: 700;">Top up minimal Rp 10.000 untuk mulai.</a>
                    @endif
                </p>
            </div>

            <div style="
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                align-items: center;
            ">

                <button
                    type="submit"
                    style="
                        flex: 1;
                        background: linear-gradient(135deg, #10b981, #059669);
                        color: #ffffff;
                        border: none;
                        padding: 16px;
                        border-radius: 14px;
                        font-size: 16px;
                        font-weight: 700;
                        cursor: {{ $maxReservation < 10000 ? 'not-allowed' : 'pointer' }};
                        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
                        transition: transform 0.1s ease;
                    "
                    @disabled($maxReservation < 10000)
                >
                    ⚡ Mulai Pengisian Daya
                </button>

            </div>

        </form>

    </div>

</div>

<script>
    function updateCompatibilityNotice() {
        const select = document.getElementById('vehicle_id');
        const notice = document.getElementById('compat-notice');
        if (!select || !notice) return;

        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            notice.style.display = 'none';
            return;
        }

        const isCompatible = selectedOption.getAttribute('data-compatible') === '1';
        const vConn = selectedOption.getAttribute('data-connector') || 'Standar';
        const vName = selectedOption.getAttribute('data-name') || 'Kendaraan';
        const cConn = @json($connectorType);

        notice.style.display = 'block';
        if (isCompatible) {
            notice.style.background = '#f0fdf4';
            notice.style.border = '1px solid #bbf7d0';
            notice.style.color = '#166534';
            notice.innerHTML = `<strong>✓ Konektor Cocok:</strong> Port ${vConn} pada ${vName} kompatibel dengan charger unit ini (${cConn}).`;
        } else {
            notice.style.background = '#fffbeb';
            notice.style.border = '1px solid #fde68a';
            notice.style.color = '#92400e';
            notice.innerHTML = `<strong>⚠️ Perhatian:</strong> Port ${vConn} (${vName}) berbeda dengan spesifikasi charger ini (${cConn}). Pastikan Anda menggunakan adaptor resmi yang sesuai.`;
        }
    }

    function setNominal(amount, btnElement) {
        const input = document.getElementById('reserved_amount');
        if (input) {
            input.value = amount;
        }
        document.querySelectorAll('.chip-preset-btn').forEach(b => {
            b.style.background = '#f1f5f9';
            b.style.borderColor = '#cbd5e1';
            b.style.color = '#334155';
        });
        if (btnElement) {
            btnElement.style.background = '#d1fae5';
            btnElement.style.borderColor = '#10b981';
            btnElement.style.color = '#065f46';
        }
    }

    // Jalankan pengecekan kompatibilitas saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateCompatibilityNotice();

        // Highlight preset aktif jika ada yang cocok dengan nilai awal
        const currentVal = document.getElementById('reserved_amount')?.value;
        if (currentVal) {
            document.querySelectorAll('.chip-preset-btn').forEach(b => {
                if (b.innerText.replace(/[^0-9]/g, '') === currentVal) {
                    b.style.background = '#d1fae5';
                    b.style.borderColor = '#10b981';
                    b.style.color = '#065f46';
                }
            });
        }
    });
</script>

@endsection