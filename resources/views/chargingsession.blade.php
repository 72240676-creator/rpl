@extends('layouts.app')

@section('title', 'Pengisian Kendaraan')

@section('content')
@php
    $tariffPerKwh = 2500; // Rp 2.500
    $chargingPowerKw = 50; // Daya charger 50 kW
    $isCompleted = ($session->status == 'completed');
@endphp

<div style="padding: 30px; max-width: 800px; margin: auto;">

    <!-- HEADER / BADGE STYLING -->
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
        <div style="
            width: 42px; 
            height: 42px; 
            background: #eff6ff; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        ">
            ⚡
        </div>
    </div>

    <h2 style="margin: 0 0 4px 0; color: #0f172a; font-size: 28px; font-weight: 800;">
        {{ $isCompleted ? 'Pengisian Selesai' : 'Sedang Mengisi' }}
    </h2>
    <p style="margin: 0 0 30px 0; color: #64748b; font-size: 16px;">
        Session #{{ $session->id }}
    </p>

    <!-- PROGRESS BAR SECTION -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <span style="color: #64748b; font-size: 16px; font-weight: 500;">Progress Pengisian</span>
            <strong style="color: #2563eb; font-size: 20px; font-weight: 800;" id="progressPercentage">
                0%
            </strong>
        </div>
        <div style="width: 100%; background: #e2e8f0; height: 14px; border-radius: 10px; overflow: hidden;">
            <div id="progressBar" style="
                width: 0%; 
                background: #2563eb; 
                height: 100%; 
                border-radius: 10px; 
                transition: width 0.5s ease;
            "></div>
        </div>
    </div>

    <!-- GRID INFORMASI UTAMA -->
    <div style="
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 16px; 
        margin-bottom: 25px;
    ">
        <!-- WAKTU MULAI -->
        <div style="background: #f8fafc; padding: 20px; border-radius: 16px;">
            <div style="color: #64748b; font-size: 14px; margin-bottom: 8px;">Waktu Mulai</div>
            <strong style="color: #0f172a; font-size: 18px; font-weight: 700;" id="startTimeText">
                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i:s') }}
            </strong>
        </div>

        <!-- WAKTU SELESAI -->
        <div style="background: #f8fafc; padding: 20px; border-radius: 16px;">
            <div style="color: #64748b; font-size: 14px; margin-bottom: 8px;">Waktu Selesai</div>
            <strong style="color: #0f172a; font-size: 18px; font-weight: 700;" id="endTimeText">
                {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i:s') : '-' }}
            </strong>
        </div>

        <!-- DURASI PENGISIAN -->
        <div style="background: #f8fafc; padding: 20px; border-radius: 16px;">
            <div style="color: #64748b; font-size: 14px; margin-bottom: 8px;">Durasi Pengisian</div>
            <strong style="color: #0f172a; font-size: 18px; font-weight: 700;" id="durationText">
                00:00:00
            </strong>
        </div>

        <!-- ENERGI TERPAKAI -->
        <div style="background: #f8fafc; padding: 20px; border-radius: 16px;">
            <div style="color: #64748b; font-size: 14px; margin-bottom: 8px;">Energi Terpakai</div>
            <strong style="color: #0f172a; font-size: 18px; font-weight: 700;" id="energyText">
                {{ number_format($session->energy_consumed_kwh ?? 0, 3) }} kWh
            </strong>
        </div>

        <!-- TARIF PER KWH -->
        <div style="background: #f8fafc; padding: 20px; border-radius: 16px;">
            <div style="color: #64748b; font-size: 14px; margin-bottom: 8px;">Tarif per kWh</div>
            <strong style="color: #0f172a; font-size: 18px; font-weight: 700;">
                Rp {{ number_format($tariffPerKwh, 0, ',', '.') }}
            </strong>
        </div>

        <!-- TOTAL BIAYA -->
        <div style="background: #f8fafc; padding: 20px; border-radius: 16px;">
            <div style="color: #64748b; font-size: 14px; margin-bottom: 8px;">Total Biaya</div>
            <strong style="color: #0f172a; font-size: 18px; font-weight: 700;" id="costText">
                Rp {{ number_format($session->total_cost ?? 0, 0, ',', '.') }}
            </strong>
        </div>
    </div>

    <!-- BANNER INFORMASI / STATUS -->
    <div style="
        background: {{ $isCompleted ? '#dcfce7' : '#eff6ff' }}; 
        color: {{ $isCompleted ? '#15803d' : '#1e40af' }}; 
        padding: 16px 20px; 
        border-radius: 16px; 
        font-size: 15px; 
        display: flex; 
        align-items: center; 
        gap: 10px;
        margin-bottom: 25px;
    ">
        {{ $isCompleted ? '✅ Sesi pengisian daya telah selesai.' : '🔋 Kendaraan sedang melakukan pengisian daya...' }}
    </div>

    @if(!$isCompleted)
        <!-- TOMBOL HENTIKAN PENGISIAN -->
        <form action="{{ route('charging.stop', $session->id) }}" method="POST">
            @csrf
            <input type="hidden" name="energy_consumed_kwh" id="inputEnergy" value="0">
            <input type="hidden" name="total_cost" id="inputCost" value="0">

            <button type="submit" style="
                width: 100%;
                background: #ef4444;
                color: white;
                padding: 16px;
                border: none;
                border-radius: 14px;
                font-weight: 700;
                font-size: 16px;
                cursor: pointer;
            ">
                🛑 Hentikan Pengisian
            </button>
        </form>
    @else
        <a href="{{ route('dashboard') }}" style="
            display: block;
            text-align: center;
            background: #0f172a;
            color: white;
            padding: 16px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
        ">
            Kembali ke Dashboard
        </a>
    @endif

</div>

<!-- SCRIPT TIMER LOGIKA MURNI -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const isCompleted = {{ $isCompleted ? 'true' : 'false' }};
        const tariffPerKwh = {{ $tariffPerKwh }};
        const chargingPowerKw = {{ $chargingPowerKw }}; // 50 kW
        const targetDurationSeconds = 1800; // Simulasi 30 menit (100%)

        // Jika selesai, gunakan data murni database
        if (isCompleted) {
            return;
        }

        // Ambil atau set waktu mulai di browser agar TIMER PASTI MULAI DARI 0 SEKITAR SAAT DIBUKA
        let storageKey = "charging_start_time_session_{{ $session->id }}";
        let localStartTime = localStorage.getItem(storageKey);

        if (!localStartTime) {
            localStartTime = new Date().getTime();
            localStorage.setItem(storageKey, localStartTime);
        } else {
            localStartTime = parseInt(localStartTime);
        }

        // Format Tampilan Waktu Mulai lokal
        let startDateObj = new Date(localStartTime);
        let startHours = String(startDateObj.getHours()).padStart(2, '0');
        let startMinutes = String(startDateObj.getMinutes()).padStart(2, '0');
        let startSeconds = String(startDateObj.getSeconds()).padStart(2, '0');
        document.getElementById('startTimeText').innerText = startHours + ':' + startMinutes + ':' + startSeconds;

        function updateMetrics() {
            let now = new Date().getTime();
            let elapsedSeconds = Math.max(0, Math.floor((now - localStartTime) / 1000));

            // 1. Format Durasi (HH:MM:SS) - Murni berjalan dari 00:00:00
            let hours = Math.floor(elapsedSeconds / 3600);
            let minutes = Math.floor((elapsedSeconds % 3600) / 60);
            let seconds = elapsedSeconds % 60;

            let formattedDuration = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');

            document.getElementById('durationText').innerText = formattedDuration;

            // 2. Energi Terpakai (kWh)
            let energyConsumed = (chargingPowerKw * (elapsedSeconds / 3600));
            document.getElementById('energyText').innerText = energyConsumed.toFixed(3) + ' kWh';

            // 3. Total Biaya (Rp)
            let totalCost = Math.round(energyConsumed * tariffPerKwh);
            document.getElementById('costText').innerText = 'Rp ' + totalCost.toLocaleString('id-ID');

            // 4. Progress %
            let progress = Math.min(100, Math.floor((elapsedSeconds / targetDurationSeconds) * 100));
            document.getElementById('progressPercentage').innerText = progress + '%';
            document.getElementById('progressBar').style.width = progress + '%';

            // Update nilai form tersembunyi untuk dikirim saat klik stop
            const inputEnergy = document.getElementById('inputEnergy');
            const inputCost = document.getElementById('inputCost');
            if (inputEnergy) inputEnergy.value = energyConsumed.toFixed(4);
            if (inputCost) inputCost.value = totalCost;
        }

        updateMetrics();
        setInterval(updateMetrics, 1000);
    });
</script>
@endsection