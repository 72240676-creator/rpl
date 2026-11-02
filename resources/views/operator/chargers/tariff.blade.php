
@extends('layouts.app')

@section('title', 'Edit Tarif Charger')

@section('content')
<style>
    .tariff-page {
        max-width: 850px;
        margin: 0 auto;
        padding: 35px 22px;
        color: #172554;
    }

    .tariff-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 8px 25px rgba(15, 23, 42, .05);
    }

    .tariff-back {
        display: inline-block;
        margin-bottom: 20px;
        color: #047857;
        text-decoration: none;
        font-weight: 700;
    }

    .tariff-subtitle {
        color: #64748b;
        margin-top: 7px;
        line-height: 1.6;
    }

    .tariff-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin: 25px 0;
    }

    .info-box {
        padding: 17px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
    }

    .info-label {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 7px;
    }

    .info-value {
        font-size: 17px;
        font-weight: 750;
        color: #0f172a;
    }

    .tariff-input {
        width: 100%;
        box-sizing: border-box;
        padding: 13px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 11px;
        font-size: 17px;
        margin: 9px 0 5px;
    }

    .tariff-help {
        font-size: 12px;
        color: #64748b;
    }

    .tariff-actions {
        display: flex;
        gap: 10px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .tariff-btn {
        padding: 12px 19px;
        border: 0;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
    }

    .tariff-save {
        background: #059669;
        color: white;
    }

    .tariff-save:hover {
        background: #047857;
    }

    .tariff-cancel {
        background: #f1f5f9;
        color: #475569;
    }

    .tariff-alert {
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 16px;
        background: #d1fae5;
        color: #047857;
    }

    .tariff-error {
        background: #fee2e2;
        color: #b91c1c;
    }

    @media(max-width: 600px) {
        .tariff-info {
            grid-template-columns: 1fr;
        }

        .tariff-card {
            padding: 20px;
        }
    }
</style>

<div class="tariff-page">

    <a class="tariff-back"
       href="{{ route('operator.dashboard') }}">
        ← Kembali ke Dashboard
    </a>

    <div class="tariff-card">

        <h1 style="margin:0; font-size:26px;">
            Pengaturan Tarif Charger
        </h1>

        <p class="tariff-subtitle">
            Perbarui tarif pengisian listrik untuk unit charger yang dipilih.
        </p>

        {{-- Pesan berhasil --}}
        @if(session('success'))
            <div class="tariff-alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Pesan validasi --}}
        @if($errors->any())
            <div class="tariff-alert tariff-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Informasi charger --}}
        <div class="tariff-info">

            <div class="info-box">
                <div class="info-label">Nomor Charger</div>
                <div class="info-value">
                    {{ $charger->device_number }}
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">Lokasi Stasiun</div>
                <div class="info-value">
                    {{ $charger->location?->nama_lokasi ?? 'Tidak tersedia' }}
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">Tipe Konektor</div>
                <div class="info-value">
                    {{ $charger->connector_type }}
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">Daya Maksimum</div>
                <div class="info-value">
                    {{ $charger->max_power_kw }} kW
                </div>
            </div>

        </div>

        {{-- Form ubah tarif --}}
        <form
            action="{{ route('operator.chargers.tariff.update', $charger->id_charger) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <label for="price_per_kwh" style="font-weight:700;">
                Tarif baru per kWh (Rp)
            </label>

            <input
                class="tariff-input"
                id="price_per_kwh"
                type="number"
                name="price_per_kwh"
                min="1"
                max="99999999.99"
                step="1"
                value="{{ old('price_per_kwh', (float) $charger->price_per_kwh) }}"
                required
            >

            <div class="tariff-help">
                Tarif saat ini:
                Rp{{ number_format($charger->price_per_kwh, 0, ',', '.') }}/kWh
            </div>

            <div class="tariff-actions">

                <button
                    class="tariff-btn tariff-save"
                    type="submit"
                >
                    Simpan Perubahan
                </button>

                <a
                    class="tariff-btn tariff-cancel"
                    href="{{ route('operator.dashboard') }}"
                >
                    Batal
                </a>

            </div>
        </form>

    </div>
</div>
@endsection
