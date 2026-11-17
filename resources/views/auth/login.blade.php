
@extends('layouts.app')
@section('title', 'Login EV Charge')

@section('content')

<style>
    .ev-login-page {
        width: 100%;
        max-width: 1450px;
        min-height: 680px;
        display: grid;
        grid-template-columns: 1.05fr 0.95fr;
        background: white;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(30, 64, 175, .10);
        border: 1px solid #e5edf8;
        margin: auto;
    }

    .ev-hero {
        position: relative;
        min-height: 680px;
        overflow: hidden;
        background: linear-gradient(135deg, #dff2ff, #e9f8f5);
        display: flex;
        align-items: flex-end;
    }

    .ev-hero img {
        position: absolute;
        width: 200%;
        height: 100%;
        max-width: none;
        object-fit: cover;
        object-position: left center;
        left: 0;
        top: 0;
        animation: carEntrance 1.2s ease-out;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            180deg,
            rgba(219,240,255,.12),
            transparent 55%,
            rgba(4,50,90,.12)
        );
        pointer-events: none;
    }

        .ev-login-right {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 75px 55px 35px;
        background: #ffffff;
        box-sizing: border-box;
    }
    

    .login-box {
    width: 100%;
    max-width: 450px;
    animation: fadeUp .8s ease;
    transform: translateY(10px);
    
    }
    

    .login-brand {
        text-align: center;
        margin-bottom: 35px;
    }

    .login-brand .logo {
        font-size: 34px;
        font-weight: 800;
        color: #059669;
        letter-spacing: -1px;
    }

    .login-brand .logo span {
        color: #2563eb;
    }

    .login-brand p {
        color: #64748b;
        font-size: 14px;
        margin-top: 12px;
    }

    .login-heading {
        text-align: center;
        margin-bottom: 35px;
    }

    .login-heading h2 {
        font-size: 30px;
        font-weight: 800;
        color: #102b50;
        margin-bottom: 10px;
    }

    .login-heading p {
        font-size: 14px;
        color: #8493a8;
    }

    .login-box .input-group {
        margin-bottom: 23px;
    }

    .login-box .input-group label {
        display: block;
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }

    .login-box .input-group input {
        width: 100%;
        height: 55px;
        padding: 0 17px;
        background: #f8fbff;
        border: 1px solid #dce6f2;
        border-radius: 12px;
        font-size: 14px;
        color: #334155;
        outline: none;
        box-sizing: border-box;
        transition: .3s;
    }

    .login-box .input-group input:focus {
        border-color: #10b981;
        background: white;
        box-shadow: 0 0 0 4px rgba(16,185,129,.10);
    }

    .login-box .btn-submit {
        width: 100%;
        height: 55px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(100deg,#059669,#10b981);
        color: white;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 8px;
        transition: .3s;
        box-shadow: 0 8px 20px rgba(16,185,129,.2);
    }

    .login-box .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(16,185,129,.3);
    }

    .login-divider {
        display: flex;
        align-items: center;
        gap: 15px;
        color: #94a3b8;
        font-size: 13px;
        margin: 25px 0;
    }

    .login-divider::before,
    .login-divider::after {
        content: "";
        height: 1px;
        flex: 1;
        background: #e2e8f0;
    }

    .login-box .btn-outline {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 53px;
        border: 1.5px solid #10b981;
        border-radius: 12px;
        color: #059669;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: .3s;
    }

    .login-box .btn-outline:hover {
        background: #ecfdf5;
        transform: translateY(-2px);
    }

    .login-box .error-text {
        color: #ef4444;
        font-size: 12px;
        display: block;
        margin-top: 6px;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes carEntrance {
        from { opacity: .5; transform: scale(1.04); }
        to { opacity: 1; transform: scale(1); }
    }

    @media(max-width: 900px) {
        .ev-login-page {
            grid-template-columns: 1fr;
            max-width: 550px;
        }

        .ev-hero {
            min-height: 330px;
        }

        .ev-login-right {
            padding: 40px 30px;
        }

        .ev-hero img {
            width: 200%;
        }
    }

    @media(max-width: 480px) {
        .ev-login-page {
            border-radius: 18px;
        }

        .ev-hero {
            min-height: 240px;
        }

        .ev-login-right {
            padding: 35px 22px;
        }

        .login-heading h2 {
            font-size: 25px;
        }
    }

/* FIX LOGIN AGAR MUAT DALAM 1 LAYAR */
@media (min-width: 901px) {
    main {
        padding: 12px 28px !important;
        align-items: center !important;
        min-height: 0;
        box-sizing: border-box;
    }

    .ev-login-page {
        height: calc(100vh - 120px);
        min-height: 0;
        max-height: 760px;
        margin: auto;
        grid-template-columns: 1.05fr 0.95fr;
    }

    .ev-hero {
        min-height: 0;
        height: 100%;
    }

    .ev-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
    }

    .ev-login-right {
        padding: 25px 45px;
        min-height: 0;
        box-sizing: border-box;
    }

    .login-box {
        max-width: 430px;
    }

    .login-brand {
        margin-bottom: 18px;
    }

    .login-brand .logo {
        font-size: 28px;
    }

    .login-brand p {
        margin-top: 6px;
    }

    .login-heading {
        margin-bottom: 22px;
    }

    .login-heading h2 {
        font-size: 25px;
        margin-bottom: 7px;
    }

    .login-heading p {
        font-size: 13px;
    }

    .login-box .input-group {
        margin-bottom: 14px;
    }

    .login-box .input-group input {
        height: 46px;
    }

    .login-box .btn-submit {
        height: 47px;
        margin-top: 3px;
    }

    .login-divider {
        margin: 14px 0;
    }

    .login-box .btn-outline {
        height: 45px;
    }
}

</style>

<div class="ev-login-page">

    <!-- BAGIAN KIRI: ILUSTRASI BYD DAN SPKLU -->
    <div class="ev-hero">
        <img src="{{ asset('images/ev-login-hero.png') }}"
         alt="BYD Seal sedang mengisi daya di SPKLU">
        <div class="hero-overlay"></div>
    </div>

    <!-- BAGIAN KANAN: FORM LOGIN -->
    <div class="ev-login-right">
        <div class="login-box">

            <div class="login-brand">
                <div class="logo">⚡ EV <span>Charge</span></div>
                <p>Smart Charging • Green Energy</p>
            </div>

            <div class="login-heading">
                <h2>Selamat Datang Kembali!</h2>
                <p>Masuk untuk melanjutkan ke akun EV Charge Anda.</p>
            </div>

            <form action="{{ url('/login') }}" method="POST">
                @csrf

                <div class="input-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email Anda"
                        required
                        autocomplete="email"
                    >

                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Masukkan password Anda"
                        required
                        autocomplete="current-password"
                    >

                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    ⚡ Login ke Akun
                </button>

                <div class="login-divider">atau</div>

                <a href="{{ url('/register') }}" class="btn-outline">
                    + Buat Akun Baru
                </a>

            </form>

        </div>
    </div>

</div>

@endsection
