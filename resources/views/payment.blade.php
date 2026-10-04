<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pembayaran</title>
</head>
<body style="background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px;">

    <!-- Kotak Rincian Form Pembayaran Ala GoPay -->
    <div style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #f3f4f6; width: 100%; max-width: 480px;">
        
        <!-- ===== TARUH NOTIFIKASI ERROR DI SINI ===== -->
        @if (session('error'))
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: left;">
                ❌ {{ session('error') }}
            </div>
        @endif
        <!-- ========================================= -->

        <!-- Header Form dengan Tombol Kembali -->
        <div style="display: flex; align-items: center; margin-bottom: 25px;">
            <!-- Tombol Kembali ke Halaman Rincian -->
            <a href="{{ route('charging.session', $session->id) }}" style="text-decoration: none; font-size: 24px; color: #374151; margin-right: 15px;">
                ←
            </a>
            <h2 style="margin: 0; font-size: 20px; font-weight: bold; color: #1f2937;">Review pembayaran</h2>
        </div>

        <!-- Merchant / Tagihan Info -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
            <div style="font-size: 13px; color: #64748b; margin-bottom: 6px;">Merchant</div>
            <div style="font-weight: bold; color: #0f172a; font-size: 15px;">⚡ EV Charging Station - Sesi #{{ $session->id }}</div>
        </div>

        <!-- Total Nominal Besar -->
        <div style="background: #f1f5f9; border-radius: 12px; padding: 25px; text-align: center; margin-bottom: 25px;">
            <div style="font-size: 14px; color: #64748b; margin-bottom: 8px;">Total Tagihan</div>
            <div style="font-size: 32px; font-weight: bold; color: #0f172a;">Rp {{ number_format($session->total_cost, 0, ',', '.') }}</div>
        </div>

        <!-- Opsi Sumber Dana / Saldo E-Wallet -->
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background: #0ea5e9; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; box-shadow: 0 2px 6px rgba(14, 165, 233, 0.3);">
                    💳
                </div>
                <div>
                    <div style="font-weight: bold; color: #1e293b; font-size: 15px;">Saldo Aktif</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 2px;">Sisa saldo: <strong style="color: #16a34a;">Rp {{ number_format(Auth::user()->saldo ?? 0, 0, ',', '.') }}</strong></div>
                </div>
            </div>
            <span style="color: #94a3b8; font-size: 16px;">▼</span>
        </div>

        <!-- Form Aksi Tombol Bayar -->
        <form action="{{ route('charging.pay', $session->id) }}" method="POST">
            @csrf
            <button type="submit" style="width: 100%; padding: 18px; border: none; background: #16a34a; color: white; border-radius: 14px; font-size: 18px; font-weight: bold; cursor: pointer; display: flex; justify-content: space-between; align-items: center; padding-left: 25px; padding-right: 25px; box-shadow: 0 6px 15px rgba(22, 163, 74, 0.25); transition: background 0.2s;">
                <span>Bayar</span>
                <span>Rp {{ number_format($session->total_cost, 0, ',', '.') }} ➔</span>
            </button>
        </form>

    </div>
</body>
</html>