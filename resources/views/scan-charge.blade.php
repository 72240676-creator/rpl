@extends('layouts.app')

@section('title', 'Scan / Charge EV')

@section('content')

<a href="{{ route('dashboard') }}"
   style="display:inline-block; margin-bottom:20px; text-decoration:none;">
    ← Kembali ke dashboard
</a>

<div style="
    padding: 30px;
    background: #f8fafc;
    min-height: calc(100vh - 80px);
">

    <!-- Header -->
    <div style="
        margin-bottom: 25px;
        text-align: center;
    ">
        <h2 style="
            margin: 0 0 8px 0;
            color: #0f172a;
            font-size: 28px;
        ">
            Mulai Pengisian Daya
        </h2>

        <p style="
            margin: 0;
            color: #64748b;
            font-size: 16px;
        ">
            Hubungkan kendaraan Anda dengan unit charger yang tersedia.
        </p>
    </div>

    <!-- Main Card -->
    <div style="
        max-width: 1400px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 28px;
        padding: 45px;
        box-sizing: border-box;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        display: flex;
        gap: 50px;
        align-items: stretch;
    ">

        <!-- BAGIAN KIRI: INSTRUKSI -->
        <div style="
            flex: 1;
            padding: 20px 10px;
        ">

            <h1 style="
                margin: 0 0 30px 0;
                color: #0f172a;
                font-size: 32px;
            ">
                Mulai Pengisian Daya
            </h1>

            <!-- Step 1 -->
            <div style="
                display: flex;
                align-items: flex-start;
                margin-bottom: 25px;
            ">
                <div style="
                    width: 52px;
                    height: 52px;
                    min-width: 52px;
                    background: #ecfdf5;
                    color: #059669;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                    font-weight: 700;
                    margin-right: 18px;
                ">
                    1
                </div>
                <div>
                    <h3 style="
                        margin: 0 0 5px 0;
                        color: #0f172a;
                        font-size: 20px;
                    ">
                        Temukan QR Code
                    </h3>
                    <p style="
                        margin: 0;
                        color: #64748b;
                        font-size: 16px;
                    ">
                        Cari QR Code yang terdapat pada unit charger.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div style="
                display: flex;
                align-items: flex-start;
                margin-bottom: 25px;
            ">
                <div style="
                    width: 52px;
                    height: 52px;
                    min-width: 52px;
                    background: #ecfdf5;
                    color: #059669;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                    font-weight: 700;
                    margin-right: 18px;
                ">
                    2
                </div>
                <div>
                    <h3 style="
                        margin: 0 0 5px 0;
                        color: #0f172a;
                        font-size: 20px;
                    ">
                        Scan QR Code
                    </h3>
                    <p style="
                        margin: 0;
                        color: #64748b;
                        font-size: 16px;
                    ">
                        Arahkan kamera ke QR Code pada charger.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div style="
                display: flex;
                align-items: flex-start;
                margin-bottom: 25px;
            ">
                <div style="
                    width: 52px;
                    height: 52px;
                    min-width: 52px;
                    background: #ecfdf5;
                    color: #059669;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                    font-weight: 700;
                    margin-right: 18px;
                ">
                    3
                </div>
                <div>
                    <h3 style="
                        margin: 0 0 5px 0;
                        color: #0f172a;
                        font-size: 20px;
                    ">
                        Periksa Charger
                    </h3>
                    <p style="
                        margin: 0;
                        color: #64748b;
                        font-size: 16px;
                    ">
                        Pastikan unit charger yang dipilih sudah benar.
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div style="
                display: flex;
                align-items: flex-start;
            ">
                <div style="
                    width: 52px;
                    height: 52px;
                    min-width: 52px;
                    background: #ecfdf5;
                    color: #059669;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                    font-weight: 700;
                    margin-right: 18px;
                ">
                    4
                </div>
                <div>
                    <h3 style="
                        margin: 0 0 5px 0;
                        color: #0f172a;
                        font-size: 20px;
                    ">
                        Lanjutkan Pengisian
                    </h3>
                    <p style="
                        margin: 0;
                        color: #64748b;
                        font-size: 16px;
                    ">
                        Setelah charger ditemukan, proses pengisian dapat dimulai.
                    </p>
                </div>
            </div>

        </div>

        <!-- BAGIAN KANAN: SCAN AREA -->
        <div style="
            flex: 1;
            border: 2px dashed #b7e4d5;
            border-radius: 24px;
            padding: 45px 35px;
            text-align: center;
            background: #fbfefd;
            display: flex;
            flex-direction: column;
            justify-content: center;
        ">

            <!-- Icon Kamera -->
            <div style="
                width: 80px;
                height: 80px;
                margin: 0 auto 20px auto;
                background: #ecfdf5;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 38px;
            ">
                📷
            </div>

            <h2 style="
                margin: 0 0 12px 0;
                color: #0f172a;
                font-size: 30px;
            ">
                Scan QR Charger
            </h2>

            <p style="
                margin: 0 auto 30px auto;
                max-width: 500px;
                color: #64748b;
                font-size: 17px;
                line-height: 1.6;
            ">
                Gunakan kamera untuk memindai QR Code yang terdapat pada unit charger.
            </p>

            <!-- Tombol Scan -->
            <a href="{{ route('scan.charge.show', 1) }}"
               style="
                    display: inline-block;
                    align-self: center;
                    background: #10b981;
                    color: #ffffff;
                    padding: 16px 38px;
                    border-radius: 14px;
                    text-decoration: none;
                    font-weight: 700;
                    font-size: 16px;
                    box-shadow: 0 8px 18px rgba(16,185,129,0.20);
               ">
                📷 &nbsp; Mulai Scan
            </a>

            <!-- Informasi -->
            <div style="
                margin-top: 32px;
                background: #fff8e7;
                border-radius: 14px;
                padding: 20px;
                color: #856404;
                text-align: left;
            ">
                <strong style="
                    display: block;
                    margin-bottom: 8px;
                    font-size: 16px;
                ">
                    Informasi
                </strong>
                <p style="
                    margin: 0;
                    font-size: 15px;
                    line-height: 1.6;
                ">
                    Setelah QR Code berhasil dipindai, sistem akan menghubungkan Anda dengan unit charger yang sesuai.
                </p>
            </div>

        </div>

    </div>

</div>

@endsection