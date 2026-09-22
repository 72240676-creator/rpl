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
            {{ $session->status === 'ongoing'
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

            {{-- Waktu Mulai --}}
            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="
                    color: #64748b;
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Waktu Mulai
                </div>

                <strong style="color: #0f172a;">
                    {{ $session->start_time->format('H:i:s') }}
                </strong>
            </div>


            {{-- Waktu Selesai --}}
            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="
                    color: #64748b;
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
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

            {{-- Durasi --}}
            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="
                    color: #64748b;
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Durasi Pengisian
                </div>

                <strong
                    id="durationText"
                    style="color: #0f172a;"
                >
                    @if($session->end_time)

                        @php
                            $durationSeconds = $session->start_time
                                ->diffInSeconds($session->end_time);

                            $hours = intdiv(
                                $durationSeconds,
                                3600
                            );

                            $minutes = intdiv(
                                $durationSeconds % 3600,
                                60
                            );

                            $seconds = $durationSeconds % 60;
                        @endphp

                        {{ sprintf(
                            '%02d:%02d:%02d',
                            $hours,
                            $minutes,
                            $seconds
                        ) }}

                    @else
                        00:00:00
                    @endif
                </strong>
            </div>


            {{-- Energi --}}
            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="
                    color: #64748b;
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Energi Terpakai
                </div>

                <strong
                    id="energyText"
                    style="color: #0f172a;"
                >
                    {{ number_format(
                        (float) $session->energy_consumed_kwh,
                        3
                    ) }} kWh
                </strong>
            </div>


            {{-- Tarif --}}
            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="
                    color: #64748b;
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Tarif per kWh
                </div>

                <strong style="color: #0f172a;">
                    Rp {{ number_format(
                        (float) $charger->price_per_kwh,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </div>


            {{-- Total Biaya --}}
            <div style="
                background: #f8fafc;
                padding: 18px;
                border-radius: 14px;
            ">
                <div style="
                    color: #64748b;
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Total Biaya
                </div>

                <strong
                    id="costText"
                    style="color: #0f172a;"
                >
                    Rp {{ number_format(
                        (float) $session->total_cost,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </div>

        </div>


        {{-- Status --}}
        @if($session->status === 'ongoing')

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
            ">
                ✓ Pengisian telah selesai
            </div>

        @endif


        {{-- Tombol Stop --}}
        @if($session->status === 'ongoing')

            <form
                action="{{ route(
                    'charging.stop',
                    $session->id
                ) }}"
                method="POST"
            >
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
                    "
                >
                    ⏹ Stop Pengisian
                </button>
            </form>

        @endif


        {{-- Tombol Kembali --}}
        <a
            href="{{ route('scan.charge') }}"
            style="
                display: block;
                width: 100%;
                box-sizing: border-box;
                margin-top: 15px;
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
{{-- MONITORING REALTIME                                      --}}
{{-- ========================================================= --}}

@if($session->status === 'ongoing')

<script>

    /*
     * Waktu mulai dari database
     */
    const startTime = new Date(
        "{{ $session->start_time->toIso8601String() }}"
    );


    /*
     * Elemen tampilan
     */
    const progressBar =
        document.getElementById('progressBar');

    const progressText =
        document.getElementById('progressText');

    const energyText =
        document.getElementById('energyText');

    const durationText =
        document.getElementById('durationText');

    const costText =
        document.getElementById('costText');


    /*
     * Data charger
     */
    const maxPower =
        {{ (float) $charger->max_power_kw }};

    const pricePerKwh =
        {{ (float) $charger->price_per_kwh }};


    /*
     * Target simulasi penuh:
     *
     * 30 menit = 1800 detik
     */
    const targetDurationSeconds = 30 * 60;


    /*
     * Update monitoring
     */
    function updateMonitoring() {

        const now = new Date();


        /*
         * Hitung durasi aktual
         */
        const durationSeconds = Math.max(
            0,
            Math.floor(
                (now - startTime) / 1000
            )
        );


        /*
         * Hitung jam, menit, detik
         */
        const hours = Math.floor(
            durationSeconds / 3600
        );

        const minutes = Math.floor(
            (durationSeconds % 3600) / 60
        );

        const seconds = durationSeconds % 60;


        durationText.textContent =
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');


        /*
         * =====================================================
         * PERHITUNGAN ENERGI
         * =====================================================
         *
         * Energi (kWh)
         * = Daya (kW) × Waktu (jam)
         *
         * Contoh:
         * 50 kW × 133/3600 jam
         * ≈ 1.847 kWh
         */
        const durationHours =
            durationSeconds / 3600;

        const energy =
            durationHours * maxPower;


        energyText.textContent =
            energy.toFixed(3) + ' kWh';


        /*
         * =====================================================
         * PERHITUNGAN BIAYA
         * =====================================================
         *
         * Biaya = Energi × Tarif
         */
        const cost =
            energy * pricePerKwh;


        costText.textContent =
            'Rp ' +
            Math.round(cost)
                .toLocaleString('id-ID');


        /*
         * =====================================================
         * PERHITUNGAN PROGRESS
         * =====================================================
         *
         * Target simulasi = 30 menit
         *
         * Progress =
         * durasi aktual / 30 menit × 100
         */
        let progress = Math.floor(
            (
                durationSeconds /
                targetDurationSeconds
            ) * 100
        );


        /*
         * Maksimal 100%
         */
        progress = Math.min(
            100,
            progress
        );


        progressBar.style.width =
            progress + '%';

        progressText.textContent =
            progress + '%';
    }


    /*
     * Jalankan pertama kali
     */
    updateMonitoring();


    /*
     * Update setiap 1 detik
     */
    const monitoringInterval =
        setInterval(
            updateMonitoring,
            1000
        );

</script>

@endif

@endsection