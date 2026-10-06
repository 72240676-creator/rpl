@extends('layouts.app')

@section('title', 'Top Up Saldo')

@section('content')
<style>
    /* Styling untuk radio button berbentuk kartu */
    .radio-card input[type="radio"] {
        display: none;
    }
    .radio-card label {
        display: block;
        padding: 15px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .radio-card label:hover {
        border-color: #cbd5e1;
    }
    .radio-card input[type="radio"]:checked + label {
        border-color: #10b981; /* Warna hijau khas EV Charge */
        background-color: #f0fdf4;
    }
    .ewallet-logo {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 10px;
        color: #475569;
    }
</style>

<div style="width: 100%; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('dashboard') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">
            &larr; Kembali ke Dashboard
        </a>
    </div>
    <div style="margin-bottom: 25px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Top Up Saldo EV Charge</h2>
        <p style="color: #64748b; margin: 0;">Pilih nominal dan e-wallet untuk mengisi saldo pengisian daya Anda.</p>
    </div>

    <form action="{{ route('topup.store') }}" method="POST" style="background: #ffffff; padding: 30px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        @csrf

        <!-- SECTION 1: NOMINAL -->
        <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 15px;">1. Pilih Nominal Top Up</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
            <div class="radio-card">
                <input type="radio" id="nom_50" name="nominal" value="50000" required>
                <label for="nom_50" style="text-align: center; font-weight: 600; color: #334155; font-size: 16px;">
                    Rp 50.000
                </label>
            </div>
            <div class="radio-card">
                <input type="radio" id="nom_100" name="nominal" value="100000">
                <label for="nom_100" style="text-align: center; font-weight: 600; color: #334155; font-size: 16px;">
                    Rp 100.000
                </label>
            </div>
            <div class="radio-card">
                <input type="radio" id="nom_250" name="nominal" value="250000">
                <label for="nom_250" style="text-align: center; font-weight: 600; color: #334155; font-size: 16px;">
                    Rp 250.000
                </label>
            </div>
            <div class="radio-card">
                <input type="radio" id="nom_500" name="nominal" value="500000">
                <label for="nom_500" style="text-align: center; font-weight: 600; color: #334155; font-size: 16px;">
                    Rp 500.000
                </label>
            </div>
        </div>

        <!-- SECTION 2: METODE PEMBAYARAN -->
        <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 15px;">2. Pilih E-Wallet</h3>
        <div style="display: grid; grid-template-columns: 1fr; gap: 12px; margin-bottom: 30px;">
            
            <div class="radio-card">
                <input type="radio" id="ewallet_gopay" name="metode_pembayaran" value="gopay" required>
                <label for="ewallet_gopay" style="display: flex; align-items: center; gap: 15px;">
                    <div class="ewallet-logo" style="background: #e0f2fe; color: #0284c7;">GOPAY</div>
                    <span style="font-weight: 600; color: #334155;">GoPay</span>
                </label>
            </div>

            <div class="radio-card">
                <input type="radio" id="ewallet_ovo" name="metode_pembayaran" value="ovo">
                <label for="ewallet_ovo" style="display: flex; align-items: center; gap: 15px;">
                    <div class="ewallet-logo" style="background: #ede9fe; color: #6d28d9;">OVO</div>
                    <span style="font-weight: 600; color: #334155;">OVO</span>
                </label>
            </div>

            <div class="radio-card">
                <input type="radio" id="ewallet_dana" name="metode_pembayaran" value="dana">
                <label for="ewallet_dana" style="display: flex; align-items: center; gap: 15px;">
                    <div class="ewallet-logo" style="background: #cffafe; color: #0891b2;">DANA</div>
                    <span style="font-weight: 600; color: #334155;">DANA</span>
                </label>
            </div>

            <div class="radio-card">
                <input type="radio" id="ewallet_shopeepay" name="metode_pembayaran" value="shopeepay">
                <label for="ewallet_shopeepay" style="display: flex; align-items: center; gap: 15px;">
                    <div class="ewallet-logo" style="background: #ffedd5; color: #ea580c;">SHOPEE</div>
                    <span style="font-weight: 600; color: #334155;">ShopeePay</span>
                </label>
            </div>

        </div>

        <button type="submit" style="width: 100%; background: #10b981; color: white; border: none; padding: 14px; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; transition: 0.2s;">
            Bayar Sekarang
        </button>
    </form>
</div>
@endsection