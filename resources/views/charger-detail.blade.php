@extends('layouts.app')

@section('title', 'Charger Ditemukan')

@section('content')

<div style="padding: 30px;">

    <div style="margin-bottom: 25px;">
        <h2 style="margin: 0 0 8px 0; color: #0f172a;">
            Charger Ditemukan
        </h2>

        <p style="margin: 0; color: #64748b;">
            QR Code berhasil mengidentifikasi unit charger.
        </p>
    </div>


    <div style="
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        max-width: 700px;
    ">

        <div style="
            width: 70px;
            height: 70px;
            background: #ecfdf5;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin-bottom: 20px;
        ">
            🔌
        </div>


        <h3 style="
            margin: 0 0 8px 0;
            color: #0f172a;
            font-size: 24px;
        ">
            Charger #{{ $charger }}
        </h3>


        <p style="
            margin: 0 0 25px 0;
            color: #64748b;
        ">
            Unit charger berhasil ditemukan melalui QR Code.
        </p>


        <div style="
            background: #f8fafc;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 25px;
        ">

            <div style="
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
            ">
                <span style="color: #64748b;">
                    ID Charger
                </span>

                <strong style="color: #0f172a;">
                    #{{ $charger }}
                </strong>
            </div>


            <div style="
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
            ">
                <span style="color: #64748b;">
                    Status
                </span>

                <span style="
                    background: #d1fae5;
                    color: #065f46;
                    padding: 5px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 700;
                ">
                    ● TERSEDIA
                </span>
            </div>


            <div style="
                display: flex;
                justify-content: space-between;
            ">
                <span style="color: #64748b;">
                    Koneksi
                </span>

                <strong style="color: #0f172a;">
                    Siap digunakan
                </strong>
            </div>

        </div>


        <div style="
            background: #fffbeb;
            color: #92400e;
            padding: 15px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 25px;
        ">
            💡 Pastikan kendaraan sudah terhubung dengan charger
            sebelum memulai pengisian daya.
        </div>


        <div style="
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        ">

            <!-- Ini nanti dilanjutkan teman -->
            <a href="#"
               style="
                    background: #059669;
                    color: white;
                    padding: 13px 25px;
                    border-radius: 12px;
                    text-decoration: none;
                    font-weight: 700;
                    font-size: 14px;
               ">
                ⚡ Mulai Pengisian
            </a>


            <a href="{{ route('scan.charge') }}"
               style="
                    background: #f1f5f9;
                    color: #475569;
                    padding: 13px 25px;
                    border-radius: 12px;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: 14px;
               ">
                ← Scan Lagi
            </a>

        </div>

    </div>

</div>

@endsection