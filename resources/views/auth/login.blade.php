
@extends('layouts.app')
@section('title', 'Login EV Charge')

@section('content')

<style>
    /* ===== CONTAINER UTAMA ===== */
    .ev-login-page {
        width: 100%;
        max-width: 1450px;
        height: min(660px, calc(100vh - 130px));
        min-height: 540px;
        display: grid;
        grid-template-columns: 1.05fr 0.95fr;
        background: white;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(30,64,175,.10);
        border: 1px solid #e5edf8;
        margin: auto;
    }

    /* ===== GAMBAR BYD DAN SPKLU ===== */
    .ev-hero {
        position: relative;
        min-height: 0;
        overflow: hidden;
        background: #dff2ff;
    }

    .ev-hero img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        inset: 0;
        animation: carEntrance 1.2s ease-out;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            180deg,
            rgba(219,240,255,.05),
            transparent 60%,
            rgba(4,50,90,.12)
        );
        pointer-events: none;
    }

    /* ===== BAGIAN LOGIN ===== */
    .ev-login-right {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 25px 45px;
        background: #fff;
        min-width: 0;
    }

    .login-box {
        width: 100%;
        max-width: 450px;
        animation: fadeUp .8s ease;
    }

    /* ===== BRAND ===== */
    .login-brand {
        text-align: center;
        margin-bottom: 20px;
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
        margin-top: 5px;
    }

    /* ===== JUDUL ===== */
    .login-heading {
        text-align: center;
        margin-bottom: 22px;
    }

    .login-heading h2 {
        font-size: 29px;
        font-weight: 800;
        color: #102b50;
        margin: 0 0 8px;
    }

    .login-heading p {
        font-size: 14px;
        color: #8493a8;
        margin: 0;
    }

    /* ===== INPUT ===== */
    .login-box .input-group {
        margin-bottom: 15px;
    }

    .login-box .input-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }

    .login-box .input-group input {
        width: 100%;
        height: 49px;
        padding: 0 16px;
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

    /* ===== TOMBOL LOGIN ===== */
    .login-box .btn-submit {
        width: 100%;
        height: 51px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(100deg,#059669,#10b981);
        color: white;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 7px;
        transition: .3s;
        box-shadow: 0 8px 20px rgba(16,185,129,.2);
    }

    .login-box .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(16,185,129,.3);
    }

    /* ===== TOMBOL BUAT AKUN ===== */
    .register-wrapper {
        text-align: center;
        margin-top: 16px;
    }

    .register-wrapper p {
        font-size: 12px;
        color: #94a3b8;
        margin: 0 0 8px;
    }

    .login-box .btn-outline {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-width: 190px;
        height: 40px;
        padding: 0 22px;
        border: 1px solid #10b981;
        border-radius: 10px;
        color: #059669;
        text-decoration: none;
        font-size: 13px;
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

    /* ===== ANIMASI ===== */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes carEntrance {
        from {
            opacity: .5;
            transform: scale(1.04);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ===== TABLET ===== */
    @media(max-width: 900px) {
        .ev-login-page {
            grid-template-columns: 1fr;
            max-width: 550px;
            height: auto;
            min-height: 0;
        }

        .ev-hero {
            min-height: 260px;
        }

        .ev-login-right {
            padding: 35px 30px;
        }
    }

    /* ===== HP ===== */
    @media(max-width: 480px) {
        .ev-login-page {
            border-radius: 18px;
        }

        .ev-hero {
            min-height: 190px;
        }

        .ev-login-right {
            padding: 28px 22px;
        }

        .login-brand {
            margin-bottom: 15px;
        }

        .login-brand .logo {
            font-size: 29px;
        }

        .login-heading {
            margin-bottom: 20px;
        }

        .login-heading h2 {
            font-size: 23px;
        }
    }

    /* ===== LAPTOP PENDEK ===== */
    @media(min-width: 901px) and (max-height: 700px) {
        .ev-login-page {
            height: calc(100vh - 115px);
            min-height: 0;
        }

        .ev-login-right {
            padding: 15px 35px;
        }

        .login-brand {
            margin-bottom: 12px;
        }

        .login-brand .logo {
            font-size: 29px;
        }

        .login-heading {
            margin-bottom: 15px;
        }

        .login-heading h2 {
            font-size: 25px;
        }

        .login-box .input-group {
            margin-bottom: 11px;
        }
    }
</style>

<div class="ev-login-page">

    {{-- BAGIAN KIRI: GAMBAR BYD DAN SPKLU --}}
    <div class="ev-hero">
        <img
            src="{{ asset('images/ev-login-hero.png') }}"
            alt="BYD Seal sedang mengisi daya di SPKLU"
        >
        <div class="hero-overlay"></div>
    </div>

    {{-- BAGIAN KANAN: FORM LOGIN --}}
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

                {{-- TOMBOL BUAT AKUN LEBIH KECIL --}}
                <div class="register-wrapper">
                    <p>Belum memiliki akun?</p>

                    <a href="{{ url('/register') }}" class="btn-outline">
                        + Buat Akun Baru
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
```