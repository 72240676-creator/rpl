<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\ChargingSession;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Transaksi di-load bersama relasi ChargingSession -> User & Charger -> Location
        $query = Transaction::with([
            'chargingSession.user',
            'chargingSession.charger.location'
        ]);

        // Filter tanggal transaksi
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $transactions = $query->latest()->paginate(10);

        // Ringkasan Statistik Laporan
        $totalPendapatan = Transaction::whereIn('status', ['paid', 'success', 'completed'])->sum('amount');
        $totalPengisian  = ChargingSession::count();
        $totalDayaKwh    = ChargingSession::sum('energy_consumed_kwh') ?? 0;

        return view('admin.reports.index', compact(
            'transactions',
            'totalPendapatan',
            'totalPengisian',
            'totalDayaKwh'
        ));
    }
}