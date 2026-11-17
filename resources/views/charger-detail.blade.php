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
@endphp

<div style="
    padding: 30px;
    max-width: 800px;
    margin: auto;
">

    <div style="margin-bottom: 25px;">

        <x-back-link href="{{ url()->previous() }}" label="Kembali" />

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
                    Pilih Kendaraan untuk Charging:
                </label>

                <select
                    name="vehicle_id"
                    id="vehicle_id"
                    required
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
                    <option value="">
                        -- Pilih Kendaraan Anda --
                    </option>

                    @if(isset($vehicles) && count($vehicles) > 0)
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id_vehicle }}" @selected((string) $selectedVehicleId === (string) $vehicle->id_vehicle)>
                                {{ $vehicle->merek }}
                                {{ $vehicle->model }}
                                ({{ $vehicle->nomor_polisi }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="reserved_amount" style="display: block; margin-bottom: 8px; font-weight: 600; color: #0f172a; font-size: 14px;">
                    Batas dana sesi (Rp):
                </label>
                <input
                    type="number"
                    name="reserved_amount"
                    id="reserved_amount"
                    min="10000"
                    max="{{ $maxReservation }}"
                    step="1000"
                    value="{{ old('reserved_amount', min(50000, $maxReservation)) }}"
                    required
                    style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 14px;"
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

@endsection