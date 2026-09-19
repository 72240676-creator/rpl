<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Scan / Charge EV</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f8fa;
            color: #10233f;
        }

        .navbar {
            height: 75px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        .logo {
            font-size: 23px;
            font-weight: bold;
            color: #10233f;
        }

        .logo span {
            color: #08b47e;
        }

        .back {
            text-decoration: none;
            color: #10233f;
            font-weight: 600;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 45px auto;
        }

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            color: #68778b;
            font-size: 16px;
        }

        .main-card {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(16,35,63,.08);

            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            align-items: center;
        }

        .instruction {
            padding: 10px;
        }

        .instruction h2 {
            font-size: 25px;
            margin-bottom: 25px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .number {
            min-width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e8f8f2;
            color: #08a875;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: bold;
        }

        .step-text strong {
            display: block;
            margin-bottom: 4px;
        }

        .step-text span {
            color: #7a8797;
            font-size: 14px;
        }

        .scan-box {
            background: #f8fbfa;
            border: 2px dashed #b8ddd0;
            border-radius: 22px;
            padding: 45px 30px;
            text-align: center;
        }

        .camera {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;

            border-radius: 50%;
            background: #e8f8f2;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 42px;
        }

        .scan-box h2 {
            margin-bottom: 10px;
        }

        .scan-box p {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .scan-button {
            display: inline-block;
            padding: 14px 28px;

            background: #08b47e;
            color: white;

            border-radius: 12px;
            text-decoration: none;

            font-weight: bold;
            border: none;
            cursor: pointer;

            box-shadow: 0 6px 15px rgba(8,180,126,.2);
        }

        .scan-button:hover {
            background: #079b6e;
        }

        .notice {
            margin-top: 25px;
            padding: 15px;
            background: #fff8e6;
            border-radius: 12px;
            color: #806b32;
            font-size: 14px;
            line-height: 1.5;
        }

        @media (max-width: 768px) {

            .main-card {
                grid-template-columns: 1fr;
                padding: 25px;
            }

            .header h1 {
                font-size: 26px;
            }

            .navbar {
                padding: 0 5%;
            }

        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">

        <div class="logo">
            EV<span>Charge</span>
        </div>

        <a href="{{ route('dashboard') }}" class="back">
            ← Kembali ke Dashboard
        </a>

    </div>


    <!-- CONTENT -->
    <div class="container">

        <div class="header">

            <h1>Scan / Charge EV</h1>

            <p>
                Hubungkan kendaraan Anda dengan unit charger yang tersedia.
            </p>

        </div>


        <div class="main-card">

            <!-- INSTRUKSI -->
            <div class="instruction">

                <h2>Mulai Pengisian Daya</h2>

                <div class="step">

                    <div class="number">1</div>

                    <div class="step-text">
                        <strong>Temukan QR Code</strong>
                        <span>
                            Cari QR Code yang terdapat pada unit charger.
                        </span>
                    </div>

                </div>


                <div class="step">

                    <div class="number">2</div>

                    <div class="step-text">
                        <strong>Scan QR Code</strong>
                        <span>
                            Arahkan kamera ke QR Code pada charger.
                        </span>
                    </div>

                </div>


                <div class="step">

                    <div class="number">3</div>

                    <div class="step-text">
                        <strong>Periksa Charger</strong>
                        <span>
                            Pastikan unit charger yang dipilih sudah benar.
                        </span>
                    </div>

                </div>


                <div class="step">

                    <div class="number">4</div>

                    <div class="step-text">
                        <strong>Lanjutkan Pengisian</strong>
                        <span>
                            Setelah charger ditemukan, proses pengisian dapat dimulai.
                        </span>
                    </div>

                </div>

            </div>


            <!-- SCAN AREA -->
            <div class="scan-box">

                <div class="camera">
                    📷
                </div>

                <h2>Scan QR Charger</h2>

                <p>
                    Gunakan kamera untuk memindai QR Code
                    yang terdapat pada unit charger.
                </p>

                <!-- BELUM ADA PROSES SCAN -->
                <a href="{{ route('scan.charge.show', 1) }}"
                style="
                        display: inline-block;
                        background: #059669;
                        color: white;
                        padding: 13px 25px;
                        border-radius: 12px;
                        text-decoration: none;
                        font-weight: 700;
                        font-size: 14px;
                ">
                    📷 &nbsp; Mulai Scan
                </a>

                <div class="notice">

                    <strong>Informasi</strong><br>

                    Setelah QR Code berhasil dipindai,
                    sistem akan menghubungkan Anda dengan
                    unit charger yang sesuai.

                </div>

            </div>

        </div>

    </div>

</body>
</html>