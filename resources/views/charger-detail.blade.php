@extends('layouts.app')

@section('title', 'Detail Charger')

@section('content')
<div style="padding: 30px; max-width: 800px; margin: auto;">
    <div style="margin-bottom: 25px;">
        <a href="{{ url()->previous() }}" style="text-decoration: none; color: #3b82f6; font-weight: 600;">← Kembali</a>
        <h2 style="margin: 15px 0 8px 0; color: #0f172a;">Detail Unit Charger</h2>
        <p style="margin: 0; color: #64748b;">Informasi spesifikasi dan status pengisi daya kendaraan listrik.</p>
    </div>

    <div style="
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #1e293b;">Charger ID: #{{ $charger->id ?? $charger }}</h3>
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

        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0;">
                <span style="color: #64748b;">Tipe Daya</span>
                <strong style="color: #0f172a;">Fast Charging (DC)</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0;">
                <span style="color: #64748b;">Daya Maksimal</span>
                <strong style="color: #0f172a;">50 kW</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                <span style="color: #64748b;">Tarif per kWh</span>
                <strong style="color: #2563eb;">Rp 2.467 / kWh</strong>
            </div>
        </div>

        <!-- FORM UTAMA MULAI PENGISIAN -->
        <form action="{{ route('charging.start') }}" method="POST">
            @csrf
            <input type="hidden" name="charger_id" value="{{ $charger->id ?? $charger }}">

            <button type="submit" style="
                width: 100%;
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                color: #ffffff;
                border: none;
                padding: 16px;
                border-radius: 14px;
                font-size: 16px;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
                transition: transform 0.1s ease;
            ">
                ⚡ Mulai Pengisian Daya
            </button>
        </form>
    </div>
</div>
@endsection