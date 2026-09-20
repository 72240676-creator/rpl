@extends('layouts.app')

@section('title', 'Pengisian Kendaraan')

@section('content')

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


    {{-- Success --}}
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


    {{-- Error --}}
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


        <h3 style="
            margin: 0 0 8px 0;
            color: #0f172a;
            font-size: 24px;
        ">
            Sedang Mengisi
        </h3>

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

                <strong id="progressText" style="
                    color: #2563eb;
                    font-size: 18px;
                ">
                    0%
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
                        width: 0%;
                        height: 100%;
                        background: #2563eb;
                        border-radius: 20px;
                        transition: width 0.5s ease;
                    "
                ></div>
            </div>

        </div>


        {{-- Information --}}
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
                    Energi
                </div>

                <strong id="energyText" style="color: #0f172a;">
                    0.00 kWh
                </strong>
            </div>

        </div>


        {{-- Status --}}
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


        {{-- Stop --}}
        @if($session->status === 'ongoing')

            <form
                action="{{ route('charging.stop', $session->id) }}"
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

        @else

            <div style="
                background: #f1f5f9;
                color: #475569;
                padding: 15px;
                border-radius: 12px;
                text-align: center;
                font-weight: 600;
            ">
                ✓ Pengisian telah selesai
            </div>

        @endif

    </div>

</div>


{{-- Interaksi progress --}}
@if($session->status === 'ongoing')

<script>
    let progress = 0;
    let energy = 0;

    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const energyText = document.getElementById('energyText');

    const chargingInterval = setInterval(function () {

        if (progress < 60) {
            progress += 1;
            energy += 0.05;

            progressBar.style.width = progress + '%';
            progressText.textContent = progress + '%';
            energyText.textContent = energy.toFixed(2) + ' kWh';
        } else {
            clearInterval(chargingInterval);
        }

    }, 300);
</script>

@endif

@endsection