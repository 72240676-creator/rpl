<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Penggunaan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
        }

        .history-card {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .session-title {
            font-weight: bold;
            font-size: 17px;
        }

        .status {
            color: #198754;
            font-weight: bold;
        }

        .history-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .label {
            color: #777;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .value {
            font-weight: bold;
        }

        .empty {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 10px;
            color: #777;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #333;
        }

        @media (max-width: 700px) {
            .history-info {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a href="{{ route('dashboard') }}" class="back-button">
    ← Kembali ke Dashboard
    </a>

    <h1>Riwayat Penggunaan</h1>

    <p class="subtitle">
        Riwayat aktivitas pengisian daya dan transaksi Anda.
    </p>

    @if($histories->count() > 0)

        @foreach($histories as $history)

            <div class="history-card">

                <div class="history-header">

                    <div class="session-title">
                        Sesi Pengisian #{{ $history->id }}
                    </div>

                    <div class="status">
                        Selesai
                    </div>

                </div>

                <div class="history-info">

                    <div>
                        <div class="label">
                            Tanggal
                        </div>

                        <div class="value">
                            {{ $history->end_time?->format('d/m/Y') }}
                        </div>
                    </div>

                    <div>
                        <div class="label">
                            Waktu
                        </div>

                        <div class="value">
                            {{ $history->start_time?->format('H:i') }}
                            -
                            {{ $history->end_time?->format('H:i') }}
                        </div>
                    </div>

                    <div>
                        <div class="label">
                            Energi Digunakan
                        </div>

                        <div class="value">
                            {{ $history->energy_consumed_kwh }} kWh
                        </div>
                    </div>

                    <div>
                        <div class="label">
                            Total Biaya
                        </div>

                        <div class="value">
                            Rp {{ number_format($history->total_cost, 0, ',', '.') }}
                        </div>
                    </div>

                </div>

            </div>

        @endforeach

    @else

        <div class="empty">
            Belum ada riwayat pengisian daya.
        </div>

    @endif

</div>

</body>
</html>

