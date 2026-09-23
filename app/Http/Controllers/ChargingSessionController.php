<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ChargingSession;
use App\Models\Charger;
use Illuminate\Http\Request;

class ChargingSessionController extends Controller
 {
    /**
     * Memulai charging session.
     */
    public function start(Request $request)
    {
        $request->validate([
            'charger_id' => 'required|exists:chargers,id',
        ]);

        $user = Auth::user();

        // Cek apakah user masih mempunyai charging session yang berjalan
        $ongoingSession = ChargingSession::where('user_id', $user->id_user)
            ->where('status', 'ongoing')
            ->first();

        if ($ongoingSession) {
            return redirect()
                ->route('charging.session', $ongoingSession->id)
                ->with('error', 'Anda masih memiliki sesi pengisian yang sedang berjalan.');
        }

        // Ambil charger
        $charger = Charger::findOrFail($request->charger_id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil kendaraan pertama milik user
        $vehicle = $user->vehicles()->first();

        if (!$vehicle) {
            return back()->with('error', 'Anda belum memiliki kendaraan.');
        }

        // Buat charging session
        $session = ChargingSession::create([
            'user_id' => $user->id_user,
            'charger_id' => $charger->id,
            'vehicle_id' => $vehicle->id_vehicle,
            'start_time' => now(),
            'end_time' => null,
            'energy_consumed_kwh' => 0,
            'total_cost' => 0,
            'status' => 'ongoing',
        ]);

        return redirect()->route('charging.session', $session->id);
    }

    /**
     * Menampilkan halaman monitoring charging.
     */
    public function show(ChargingSession $session)
    {
        // Pastikan session milik user yang sedang login
        if ($session->user_id !== Auth::user()->id_user) {
            abort(403);
        }

        $charger = Charger::findOrFail($session->charger_id);

        return view('chargingsession', compact('session', 'charger'));
    }

    /**
     * Menghentikan charging session.
     */
    public function stop(ChargingSession $session)
    {
        // Pastikan session milik user yang sedang login
        if ($session->user_id !== Auth::user()->id_user) {
            abort(403);
        }

        // Jika sudah selesai
        if ($session->status !== 'ongoing') {
            return redirect()
                ->route('charging.session', $session->id)
                ->with('error', 'Charging session sudah selesai.');
        }

        $endTime = now();

        // Hitung durasi charging dalam detik
        $durationSeconds = $session->start_time->diffInSeconds($endTime);

        // Ambil charger
        $charger = Charger::findOrFail($session->charger_id);

        /*
         * Perhitungan energi:
         *
         * Energi (kWh) =
         * durasi (jam) × daya charger (kW)
         */
        $durationHours = $durationSeconds / 3600;

        $energyConsumed = $durationHours * $charger->max_power_kw;

        // Minimal 0.01 kWh agar session sangat singkat tetap tercatat
        $energyConsumed = max(0.01, $energyConsumed);

        // Ambil tarif charger
        $pricePerKwh = $charger->price_per_kwh;

        // Hitung total biaya
        $totalCost = $energyConsumed * $pricePerKwh;

        // Simpan hasil charging
        $session->update([
            'end_time' => $endTime,
            'energy_consumed_kwh' => round($energyConsumed, 3),
            'total_cost' => round($totalCost, 2),
            'status' => 'completed',
        ]);

        return redirect()
            ->route('charging.session', $session->id)
            ->with('success', 'Pengisian daya telah selesai.');
    }
}