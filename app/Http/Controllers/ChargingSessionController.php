<?php

namespace App\Http\Controllers;

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
        $user = auth()->user();

        // Cek apakah user masih punya session yang sedang berjalan
        $ongoingSession = ChargingSession::where('user_id', $user->id_user)
            ->where('status', 'ongoing')
            ->first();

        if ($ongoingSession) {
            return redirect()
                ->route('charging.session', $ongoingSession->id)
                ->with('error', 'Anda masih memiliki sesi pengisian yang sedang berjalan.');
        }

        // Ambil charger yang dipilih
        $charger = Charger::findOrFail($request->charger_id);

        // Ambil kendaraan pertama milik user
        $vehicle = $user->vehicles()->first();

        if (!$vehicle) {
            return back()->with('error', 'Anda belum memiliki kendaraan.');
        }

        // Buat charging session baru
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
     * Menampilkan charging session yang sedang berjalan.
     */
    public function show(ChargingSession $session)
    {
        return view('chargingsession', compact('session'));
    }

    /**
     * Menghentikan charging session.
     */
    public function stop(ChargingSession $session)
    {
        $session->update([
            'end_time' => now(),
            'status' => 'completed',
        ]);

        return redirect()
            ->route('charging.session', $session->id)
            ->with('success', 'Pengisian daya telah selesai.');
    }
}