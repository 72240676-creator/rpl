<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Charging</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h1 {
            color: #1e293b;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 16px;
            background: #1e293b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .card {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .row {
            margin: 8px 0;
        }

        .label {
            font-weight: bold;
            color: #475569;
        }

        .empty {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            color: #64748b;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="{{ route('dashboard') }}" class="back">
        ← Kembali ke Dashboard
    </a>

    <h1>📋 Riwayat Charging</h1>

    @if($histories->count() == 0)

        <div class="empty">
            <h3>Belum Ada Riwayat Charging</h3>

            <p>
                Riwayat charging kamu akan muncul setelah
                proses charging selesai dan pembayaran dilakukan.
            </p>
        </div>

    @else

        @foreach($histories as $history)

            <div class="card">

                <h2>
                    ⚡ Charging #{{ $history->id }}
                </h2>

                <div class="row">
                    <span class="label">Status:</span>
                    {{ $history->status }}
                </div>

                <div class="row">
                    <span class="label">Mulai:</span>

                    @if($history->start_time)
                        {{ $history->start_time->format('d-m-Y H:i') }}
                    @else
                        -
                    @endif
                </div>

                <div class="row">
                    <span class="label">Selesai:</span>

                    @if($history->end_time)
                        {{ $history->end_time->format('d-m-Y H:i') }}
                    @else
                        -
                    @endif
                </div>

                <div class="row">
                    <span class="label">Energi:</span>
                    {{ number_format($history->energy_used ?? 0, 2, ',', '.') }} kWh
                </div>

                <div class="row">
                    <span class="label">Total Biaya:</span>
                    Rp {{ number_format($history->total_cost ?? 0, 0, ',', '.') }}
                </div>

            </div>

        @endforeach

    @endif

</div>

</body>
</html>