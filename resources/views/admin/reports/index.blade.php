@extends('layouts.app')

@section('content')
<div style="max-w-7xl; width: 100%; padding: 20px;">
    <!-- Header Halaman -->
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">📊 Laporan Transaksi & Pendapatan SPKLU</h1>
        <p style="font-size: 14px; color: #64748b; margin: 0;">Rekapitulasi transaksi pengisian daya dan pendapatan sistem SPKLU.</p>
    </div>

    <!-- Ringkasan Kartu Statistik -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
            <span style="font-size: 13px; color: #64748b; font-weight: 500;">Total Pendapatan</span>
            <h3 style="font-size: 22px; font-weight: 700; color: #059669; margin: 8px 0 0 0;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>
        <div style="background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
            <span style="font-size: 13px; color: #64748b; font-weight: 500;">Total Sesi Pengisian</span>
            <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 8px 0 0 0;">{{ $totalPengisian }} Sesi</h3>
        </div>
        <div style="background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
            <span style="font-size: 13px; color: #64748b; font-weight: 500;">Total Daya Terpakai</span>
            <h3 style="font-size: 22px; font-weight: 700; color: #2563eb; margin: 8px 0 0 0;">{{ number_format($totalDayaKwh, 2) }} kWh</h3>
        </div>
    </div>

    <!-- Form Filter Tanggal -->
    <div style="background: #ffffff; padding: 16px; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.reports.index') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
            </div>
            <button type="submit" style="background: #059669; color: #ffffff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">Filter</button>
            <a href="{{ route('admin.reports.index') }}" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;">Reset</a>
        </form>
    </div>

    <!-- Tabel Data Laporan -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600;">
                    <th style="padding: 12px 16px;">No. Invoice</th>
                    <th style="padding: 12px 16px;">Pengguna</th>
                    <th style="padding: 12px 16px;">Lokasi SPKLU</th>
                    <th style="padding: 12px 16px;">Daya (kWh)</th>
                    <th style="padding: 12px 16px;">Metode</th>
                    <th style="padding: 12px 16px;">Total Biaya</th>
                    <th style="padding: 12px 16px;">Status</th>
                    <th style="padding: 12px 16px;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr style="border-bottom: 1px solid #f1f5f9; color: #334155;">
                    <td style="padding: 12px 16px; font-family: monospace; font-weight: 600; color: #047857;">
                        {{ $trx->invoice_number ?? '#TRX-'.$trx->id }}
                    </td>
                    <td style="padding: 12px 16px; font-weight: 600;">
                        {{ $trx->chargingSession->user->nama ?? 'Pengguna' }}
                    </td>
                    <td style="padding: 12px 16px;">
                        {{ $trx->chargingSession->charger->location->nama_lokasi ?? '-' }}
                    </td>
                    <td style="padding: 12px 16px;">
                        {{ number_format($trx->chargingSession->energy_consumed_kwh ?? 0, 2) }} kWh
                    </td>
                    <td style="padding: 12px 16px; text-transform: uppercase;">
                        {{ $trx->payment_method ?? '-' }}
                    </td>
                    <td style="padding: 12px 16px; font-weight: 600;">
                        Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </td>
                    <td style="padding: 12px 16px;">
                        @php
                            $status = strtolower($trx->status);
                            $badgeStyle = match($status) {
                                'paid', 'success', 'completed' => 'background: #d1fae5; color: #047857;',
                                'pending' => 'background: #fef3c7; color: #b45309;',
                                default => 'background: #fee2e2; color: #b91c1c;'
                            };
                        @endphp
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; {{ $badgeStyle }}">
                            {{ strtoupper($trx->status) }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px; color: #64748b; font-size: 12px;">
                        {{ $trx->created_at ? $trx->created_at->format('d M Y H:i') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 30px; text-align: center; color: #94a3b8;">Belum ada data transaksi laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding: 16px; border-top: 1px solid #e2e8f0;">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection