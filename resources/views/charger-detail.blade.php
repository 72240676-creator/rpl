@extends('layouts.app')

@section('title', 'Charger Ditemukan')

@section('content')

<div style="padding: 30px;">

    <div style="margin-bottom: 25px;">
        <h2 style="margin: 0 0 8px 0; color: #0f172a;">
            Charger Ditemukan
        </h2>

        <p style="margin: 0; color: #64748b;">
            QR Code berhasil mengidentifikasi unit charger.
        </p>
    </div>

    <div style="
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        max-width: 700px;
    ">

        <!-- BLOK NOTIFIKASI ERROR -->
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
            width: 70px;
            height: 70px;
            background: #ecfdf5;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin-bottom: 20px;
        ">
            🔌
        </div>

        @php
            $chargerId = is_object($charger) ? ($charger->id_charger ?? $charger->id) : $charger;
        @endphp

        <h3 style="
            margin: 0 0 8px 0;
            color: #0f172a;
            font-size: 24px;
        ">
            Charger #{{ $chargerId }}
        </h3>

        <p style="
            margin: 0 0 25px 0;
            color: #64748b;
        ">
            Unit charger berhasil ditemukan melalui QR Code.
        </p>

        <div style="
            background: #f8fafc;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 25px;
        ">

            <div style="
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
            ">
                <span style="color: #64748b;">
                    ID Charger
                </span>

                <strong style="color: #0f172a;">
                    #{{ $chargerId }}
                </strong>
            </div>

            <div style="
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
            ">
                <span style="color: #64748b;">
                    Status
                </span>

                <span style="
                    background: #d1fae5;
                    color: #065f46;
                    padding: 5px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 700;
                ">
                    ● TERSEDIA
                </span>
            </div>

            <div style="
                display: flex;
                justify-content: space-between;
            ">
                <span style="color: #64748b;">
                    Koneksi
                </span>

                <strong style="color: #0f172a;">
                    Siap digunakan
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

        {{-- Form Mulai Pengisian Lengkap dengan Pilihan Kendaraan --}}
        <form id="startChargingForm" action="{{ route('charging.start', $chargerId) }}" method="POST" style="margin: 0; width: 100%;">
            @csrf
            <input type="hidden" name="charger_id" value="{{ $chargerId }}">

            {{-- Pilihan Kendaraan User --}}
            <div style="margin-bottom: 20px;">
                <label for="vehicle_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #0f172a; font-size: 14px;">
                    Pilih Kendaraan untuk Charging:
                </label>
                <select name="vehicle_id" id="vehicle_id" required style="
                    width: 100%;
                    padding: 12px;
                    border-radius: 12px;
                    border: 1px solid #cbd5e1;
                    background: #f8fafc;
                    color: #0f172a;
                    font-size: 14px;
                ">
                    <option value="">-- Pilih Kendaraan Anda --</option>
                    @if(isset($vehicles) && count($vehicles) > 0)
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id_vehicle }}">
                                {{ $vehicle->merek }} {{ $vehicle->model }} ({{ $vehicle->nomor_polisi }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div style="
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                align-items: center;
            ">
                <button
                    type="submit"
                    id="btnMulai"
                    style="
                        background: #059669;
                        color: white;
                        padding: 13px 25px;
                        border: none;
                        border-radius: 12px;
                        font-weight: 700;
                        font-size: 14px;
                        cursor: pointer;
                        transition: background 0.2s;
                    "
                    onmouseover="this.style.background='#047857'"
                    onmouseout="this.style.background='#059669'"
                >
                    ⚡ Mulai Pengisian
                </button>

                <a href="{{ route('scan.charge') }}"
                   style="
                        background: #f1f5f9;
                        color: #475569;
                        padding: 13px 25px;
                        border-radius: 12px;
                        text-decoration: none;
                        font-weight: 600;
                        font-size: 14px;
                   ">
                    ← Scan Lagi
                </a>
            </div>
        </form>

    </div>

</div>

@endsection