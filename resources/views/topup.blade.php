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
        
        <!-- Pilihan Preset -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="radio-card">
                <input type="radio" id="nom_50" name="preset_nominal" value="50000" onclick="selectPreset(50000)">
                <label for="nom_50" style="text-align: center; font-weight: 600; color: #334155; font-size: 16px;">
                    Rp 50.000
                </label>
            </div>
            <div class="radio-card">
                <input type="radio" id="nom_100" name="preset_nominal" value="100000" onclick="selectPreset(100000)">
                <label for="nom_100" style="text-align: center; font-weight: 600; color: #334155; font-size: 16px;">
                    Rp 100.000
                </label>
            </div>
            <div class="radio-card">
                <input type="radio" id="nom_250" name="preset_nominal" value="250000" onclick="selectPreset(250000)">
                <label for="nom_250" style="text-align: center; font-weight: 600; color: #334155; font-size: 16px;">
                    Rp 250.000
                </label>
            </div>
            <div class="radio-card">
                <input type="radio" id="nom_500" name="preset_nominal" value="500000" onclick="selectPreset(500000)">
                <label for="nom_500" style="text-align: center; font-weight: 600; color: #334155; font-size: 16px;">
                    Rp 500.000
                </label>
            </div>
        </div>

        <!-- Input Ketik Nominal Sendiri -->
        <div style="margin-bottom: 30px;">
            <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 6px; font-weight: 500;">
                Atau Ketik Nominal Sendiri (Minimal Rp 10.000):
            </label>
            <div style="position: relative; display: flex; align-items: center;">
                <span style="position: absolute; left: 14px; font-weight: 600; color: #64748b; font-size: 15px;">Rp</span>
                <input type="number" 
                       id="nominal_input" 
                       name="nominal" 
                       min="10000" 
                       placeholder="Masukkan nominal" 
                       required
                       style="width: 100%; padding: 12px 14px 12px 42px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 15px; font-weight: 600; color: #1e293b; outline: none; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='#10b981';" 
                       onblur="this.style.borderColor='#e2e8f0';"
                       oninput="handleCustomInput()">
            </div>
            <p id="error_msg" style="color: #ef4444; font-size: 12px; margin: 6px 0 0 0; display: none;">
                Nominal top up minimal adalah Rp 10.000.
            </p>
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

<script>
    function selectPreset(amount) {
        const input = document.getElementById('nominal_input');
        input.value = amount;
        validateNominal();
    }

    function handleCustomInput() {
        // Lepas piliham radio preset jika pengguna mengetik angka sendiri
        const radios = document.querySelectorAll('input[name="preset_nominal"]');
        radios.forEach(radio => radio.checked = false);
        validateNominal();
    }

    function validateNominal() {
        const input = document.getElementById('nominal_input');
        const errorMsg = document.getElementById('error_msg');
        if (input.value && parseInt(input.value) < 10000) {
            errorMsg.style.display = 'block';
        } else {
            errorMsg.style.display = 'none';
        }
    }
</script>
@endsection