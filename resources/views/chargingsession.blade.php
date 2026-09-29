@extends('layouts.app')

@section('title', 'Pengisian Kendaraan')

@section('content')

@php
    /*
     * Target simulasi pengisian penuh = 30 menit.
     */
    $targetDurationSeconds = 30 * 60;

    /*
     * Hitung progress untuk session yang sudah selesai.
     */
    $completedProgress = 0;

    if ($session->end_time) {
        $actualDurationSeconds = $session->start_time
            ->diffInSeconds($session->end_time);

        $completedProgress = min(
            100,
            floor(
                ($actualDurationSeconds / $targetDurationSeconds) * 100
            )
        );
    }
@endphp

<div style="
    padding: 30px;
    max-width: 800px;
    margin: auto;
">

    {{-- Header --}}
    <div style="margin-bottom: 25px;">
        <h2 style="
            margin: 0 0 8px 0;
            color: #0f172a;
        ">
            ⚡ Pengisian Kendaraan
        </h2>

        <p style="
            margin: 0;
            color: #64748b;
        ">
            Charger sedang melakukan pengisian daya kendaraan.
        </p>
    </div>


    {{-- Success Message --}}
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


    {{-- Error Message --}}
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


    {{-- Charging Card --}}
    <div style="
        background: white;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    ">

        {{-- Icon --}}
        <div style="
            width: 70px;
            height: 70px;
            background: #eff6ff;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin-bottom: 20px;
        ">
            ⚡
        </div>


        {{-- Judul --}}
        <h3 style="
            margin: 0 0 8px 0;
            color: #0f172a;
            font-size: 24px;
        ">
            {{ (strtolower($session->status) === 'ongoing' || is_null($session->end_time))
                ? 'Sedang Mengisi'
                : 'Pengisian Selesai'
            }}
        </h3>


        {{-- Session ID --}}
        <p style="
            margin: 0 0 30px 0;
            color: #64748b;
        ">
            Session #{{ $session->id }}
        </p>


        {{-- Progress --}}
        <div style="margin-bottom: 25px;">

            <div style="
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            ">
                <span style="color: #64748b;">
                    Progress Pengisian
                </span>

                <strong
                    id="progressText"
                    style="
                        color: #2563eb;
                        font-size: 18px;
                    "
                >
                    @if($session->end_time)
                        {{ $completedProgress }}%
                    @else
                        0%
                    @endif
                </strong>
            </div>


            <div style="
                width: 100%;
                height: 18px;
                background: #e2e8f0;
                border-radius: 20px;
                overflow: hidden;
            ">
                <div
                    id="progressBar"
                    style="
                        width: {{ $session->end_time ? $completedProgress : 0 }}%;
                        height: 100%;
                        background: #2563eb;
                        border-radius: 20px;
                        transition: width 0.5s ease;
                    "
                ></div>
            </div>

        </div>


        {{-- Waktu Mulai & Selesai --}}
        <div style="
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        ">

            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">
                    Waktu Mulai
                </div>
                <strong style="color: #0f172a;">
                    {{ $session->start_time->format('H:i:s') }}
                </strong>
            </div>

            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">
                    Waktu Selesai
                </div>
                <strong style="color: #0f172a;">
                    @if($session->end_time)
                        {{ $session->end_time->format('H:i:s') }}
                    @else
                        -
                    @endif
                </strong>
            </div>

        </div>


        {{-- Informasi Monitoring --}}
        <div style="
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        ">

            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">
                    Durasi Pengisian
                </div>
                <strong id="durationText" style="color: #0f172a;">
                    @if($session->end_time)
                        @php
                            $durationSeconds = $session->start_time->diffInSeconds($session->end_time);
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

            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">
                    Energi Terpakai
                </div>
                <strong id="energyText" style="color: #0f172a;">
                    {{ number_format((float) $session->energy_consumed_kwh, 3) }} kWh
                </strong>
            </div>

            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">
                    Tarif per kWh
                </div>
                <strong style="color: #0f172a;">
                    Rp {{ number_format((float) $charger->price_per_kwh, 0, ',', '.') }}
                </strong>
            </div>

            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="color: #64748b; font-size: 13px; margin-bottom: 5px;">
                    Total Biaya
                </div>
                <strong id="costText" style="color: #0f172a;">
                    Rp {{ number_format((float) $session->total_cost, 0, ',', '.') }}
                </strong>
            </div>

        </div>


        {{-- Status Box --}}
        {{-- Status Box --}}
        @if(strtolower($session->status) === 'ongoing' || is_null($session->end_time))
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

        {{-- Tombol Stop (Hanya muncul saat ongoing) --}}
        @if(strtolower($session->status) === 'ongoing' || is_null($session->end_time))
            <form action="{{ route('charging.stop', $session->id) }}" method="POST" style="margin: 0;">
                @csrf
                <button
                    type="submit"
                    style="
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
                    "
                >
                    ⏹ Stop Pengisian & Lihat Tagihan
                </button>
            </form>
        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- KARTU RINCIAN TAGIHAN & PEMBAYARAN (MUNCUL SETELAH SELESAI) --}}
    {{-- ========================================================= --}}
    @if(strtolower($session->status) !== 'ongoing' && !is_null($session->end_time))
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
                <span style="color: #0f172a; font-weight: 600;">Rp {{ number_format((float) $charger->price_per_kwh, 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <span style="font-size: 16px; font-weight: bold; color: #0f172a;">Total yang Harus Dibayar:</span>
            <span style="font-size: 22px; font-weight: 800; color: #2563eb;">
                Rp {{ number_format((float) $session->total_cost, 0, ',', '.') }}
            </span>
        </div>

        {{-- Tombol Aksi Pembayaran (Sesuaikan routenya jika ada halaman pembayaran khusus, misal: charging.pay) --}}
        <form action="{{ route('charging.stop', $session->id) }}" method="POST"> <!-- Ganti route('charging.pay', ...) jika ada -->
            @csrf
            <!-- Tombol untuk Membuka Pop-up Pembayaran -->
            <!-- KODE RINCIAN TAGIHAN ANDA (Tetap di sini) -->

            <!-- Tombol Pindah Halaman Pembayaran -->
            <a href="{{ route('charging.payment.view', $session->id) }}" style="display: block; text-align: center; width: 100%; padding: 15px; border-radius: 12px; background: #16a34a; color: white; font-size: 16px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2); margin-top: 20px;">
                💳 Lakukan Pembayaran Sekarang
            </a>

            <!-- Script JavaScript untuk Kontrol Modal -->
            <script>
                function openPaymentModal() {
                    document.getElementById('paymentModal').style.display = 'flex';
                }

                function closePaymentModal() {
                    document.getElementById('paymentModal').style.display = 'none';
                }

                // Tutup modal jika user klik di luar kotak putih modal
                window.onclick = function(event) {
                    let modal = document.getElementById('paymentModal');
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                }
            </script>
        </form>
    </div>
    @endif


    {{-- Tombol Kembali --}}
    <div>
        <a
            href="{{ route('scan.charge') }}"
            style="
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
            "
        >
            ← Kembali ke Scan Charger
        </a>
    </div>

</div>


{{-- ========================================================= --}}
{{-- SCRIPT MONITORING REALTIME                                --}}
{{-- ========================================================= --}}
@if (strtolower($session->status) === 'ongoing' || is_null($session->end_time))
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

        // Menggunakan fallback jika variabel charger null agar JS tidak crash
        const maxPower = {{ (float) ($session->charger->max_power_kw ?? $charger->max_power_kw ?? 7.4) }};
        const pricePerKwh = {{ (float) ($session->charger->price_per_kwh ?? $charger->price_per_kwh ?? 2500) }};
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

            let progress = Math.floor((durationSeconds / targetDurationSeconds) * 100);
            progress = Math.min(100, progress);

            if (progressBar) progressBar.style.width = progress + '%';
            if (progressText) progressText.textContent = progress + '%';
        }

        updateMonitoring();
        setInterval(updateMonitoring, 1000);
    });
</script>
@endif

@endsection