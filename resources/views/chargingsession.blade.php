@extends('layouts.app')

@section('title', 'Pengisian Kendaraan')

@section('content')
@php
    $tariffPerKwh = $charger->price_per_kwh ?? $session->charger->price_per_kwh ?? 2500;
    $chargingPowerKw = $session->charger->max_power_kw ?? $charger->max_power_kw ?? 7.4;
    $isCompleted = (strtolower($session->status) !== 'ongoing' && !is_null($session->end_time));
@endphp

<div style="padding: 30px; max-width: 800px; margin: auto;">

    {{-- Pesan Notifikasi Success & Error --}}
    @if(session('success'))
        <div style="
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        ">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        ">
            {{ session('error') }}
        </div>
    @endif

    {{-- Kartu Utama Pengisian Daya --}}
    <div style="
        background: white;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    ">

        {{-- Header & Judul --}}
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
            <div style="
                width: 50px;
                height: 50px;
                background: #eff6ff;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
            ">
                ⚡
            </div>
            <div>
                <h3 style="margin: 0; color: #0f172a; font-size: 22px; font-weight: 700;">
                    {{ !$isCompleted ? 'Sedang Mengisi' : 'Pengisian Selesai' }}
                </h3>
                <p style="margin: 4px 0 0 0; color: #64748b; font-size: 14px;">
                    Session #{{ $session->id }}
                </p>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div style="margin-bottom: 25px;">
            <div style="
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            ">
                <span style="color: #64748b; font-size: 15px;">Progress Pengisian</span>
                <strong id="progressText" style="color: #2563eb; font-size: 18px;">
                    {{ $isCompleted ? '100%' : '0%' }}
                </strong>
            </div>

            <div style="
                width: 100%;
                height: 16px;
                background: #e2e8f0;
                border-radius: 20px;
                overflow: hidden;
            ">
                <div id="progressBar" style="
                    width: {{ $isCompleted ? 100 : 0 }}%;
                    height: 100%;
                    background: #2563eb;
                    border-radius: 20px;
                    transition: width 0.5s ease;
                "></div>
            </div>
        </div>

        {{-- Informasi Monitoring --}}
        <div style="
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        ">
            <div style="background: #f8fafc; padding: 18px; border-radius: 14px;">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Waktu Mulai</div>
                <strong style="color: #0f172a; font-size: 16px;">
                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i:s') }}
                </strong>
            </div>

            <div style="background: #f8fafc; padding: 18px; border-radius: 14px;">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Waktu Selesai</div>
                <strong style="color: #0f172a; font-size: 16px;">
                    {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i:s') : '-' }}
                </strong>
            </div>

            <div style="background: #f8fafc; padding: 18px; border-radius: 14px;">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Durasi Pengisian</div>
                <strong id="durationText" style="color: #0f172a; font-size: 16px;">
                    @if($isCompleted)
                        @php
                            $durationSeconds = \Carbon\Carbon::parse($session->start_time)->diffInSeconds($session->end_time);
                            $hours = intdiv($durationSeconds, 3600);
                            $minutes = intdiv($durationSeconds % 3600, 60);
                            $seconds = $durationSeconds % 60;
                        @endphp
                        {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                    @else
                        00:00:00
                    @endif
                </strong>
            </div>

            <div style="background: #f8fafc; padding: 18px; border-radius: 14px;">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Energi Terpakai</div>
                <strong id="energyText" style="color: #0f172a; font-size: 16px;">
                    {{ number_format((float) ($session->energy_consumed_kwh ?? 0), 3) }} kWh
                </strong>
            </div>

            <div style="background: #f8fafc; padding: 18px; border-radius: 14px;">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Tarif per kWh</div>
                <strong style="color: #0f172a; font-size: 16px;">
                    Rp {{ number_format((float) $tariffPerKwh, 0, ',', '.') }}
                </strong>
            </div>

            <div style="background: #f8fafc; padding: 18px; border-radius: 14px;">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">Total Biaya</div>
                <strong id="costText" style="color: #0f172a; font-size: 16px;">
                    Rp {{ number_format((float) ($session->total_cost ?? 0), 0, ',', '.') }}
                </strong>
            </div>
        </div>

        {{-- Status Box & Action Button --}}
        @if(!$isCompleted)
            <div style="
                background: #eff6ff;
                color: #1e40af;
                padding: 15px;
                border-radius: 12px;
                margin-bottom: 25px;
                text-align: center;
            ">
                🔋 Kendaraan sedang melakukan pengisian daya...
            </div>

            <form action="{{ route('charging.stop', $session->id) }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="energy_consumed_kwh" id="inputEnergy" value="0">
                <input type="hidden" name="total_cost" id="inputCost" value="0">
                <button type="submit" style="
                    width: 100%;
                    padding: 15px;
                    border: none;
                    border-radius: 12px;
                    background: #dc2626;
                    color: white;
                    font-size: 16px;
                    font-weight: bold;
                    cursor: pointer;
                    box-shadow: 0 4px 12px rgba(220,38,38,0.25);
                ">
                    ⏹ Stop Pengisian & Lihat Tagihan
                </button>
            </form>
        @else
            <div style="
                background: #d1fae5;
                color: #065f46;
                padding: 15px;
                border-radius: 12px;
                margin-bottom: 25px;
                text-align: center;
                font-weight: 700;
            ">
                ✓ Pengisian Selesai
            </div>
        @endif

    </div>

    {{-- Kartu Rincian Tagihan & Pembayaran (Tampil Saat Selesai) --}}
    @if($isCompleted)
    <div style="
        background: white;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    ">
        <h3 style="margin: 0 0 15px 0; color: #0f172a; font-size: 20px;">
            🧾 Rincian Tagihan & Pembayaran
        </h3>

        <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 15px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #64748b;">
                <span>Total kWh Terpakai</span>
                <span style="color: #0f172a; font-weight: 600;">{{ number_format((float) $session->energy_consumed_kwh, 3) }} kWh</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #64748b;">
                <span>Tarif per kWh</span>
                <span style="color: #0f172a; font-weight: 600;">Rp {{ number_format((float) $tariffPerKwh, 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <span style="font-size: 16px; font-weight: bold; color: #0f172a;">Total yang Harus Dibayar:</span>
            <span style="font-size: 22px; font-weight: 800; color: #2563eb;">
                Rp {{ number_format((float) $session->total_cost, 0, ',', '.') }}
            </span>
        </div>

        <a href="{{ route('charging.payment.view', $session->id) }}" style="
            display: block;
            text-align: center;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            background: #16a34a;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
            box-sizing: border-box;
        ">
            💳 Lakukan Pembayaran Sekarang
        </a>
    </div>
    @endif

    {{-- Tombol Navigasi Kembali --}}
    <div>
        <a href="{{ route('scan.charge') }}" style="
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 15px;
            border-radius: 12px;
            background: #f1f5f9;
            color: #475569;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
        ">
            ← Kembali ke Scan Charger
        </a>
    </div>

</div>

{{-- Realtime Monitoring Script untuk Sesi Aktif --}}
@if(!$isCompleted)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startTimeStr = "{{ optional($session->start_time)->toIso8601String() }}";
        if (!startTimeStr) return;

        const startTime = new Date(startTimeStr);
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const energyText = document.getElementById('energyText');
        const durationText = document.getElementById('durationText');
        const costText = document.getElementById('costText');
        const inputEnergy = document.getElementById('inputEnergy');
        const inputCost = document.getElementById('inputCost');

        const maxPower = {{ (float) $chargingPowerKw }};
        const pricePerKwh = {{ (float) $tariffPerKwh }};
        const targetDurationSeconds = 30 * 60;

        function updateMonitoring() {
            const now = new Date();
            const durationSeconds = Math.max(0, Math.floor((now - startTime) / 1000));

            const hours = Math.floor(durationSeconds / 3600);
            const minutes = Math.floor((durationSeconds % 3600) / 60);
            const seconds = durationSeconds % 60;

            if (durationText) {
                durationText.textContent = 
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0');
            }

            const durationHours = durationSeconds / 3600;
            const energy = durationHours * maxPower;
            if (energyText) {
                energyText.textContent = energy.toFixed(3) + ' kWh';
            }

            const cost = energy * pricePerKwh;
            if (costText) {
                costText.textContent = 'Rp ' + Math.round(cost).toLocaleString('id-ID');
            }

            let progress = Math.min(100, Math.floor((durationSeconds / targetDurationSeconds) * 100));

            if (progressBar) progressBar.style.width = progress + '%';
            if (progressText) progressText.textContent = progress + '%';

            if (inputEnergy) inputEnergy.value = energy.toFixed(4);
            if (inputCost) inputCost.value = Math.round(cost);
        }

        updateMonitoring();
        setInterval(updateMonitoring, 1000);
    });
</script>
@endif
@endsection