<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use App\Notifications\ChargingFinishedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargingSessionController extends Controller
{
    /**
     * Menghentikan pengisian daya & memicu notifikasi email charging selesai
     */
    public function stopCharging(Request $request, $id)
    {
        // 1. Update data sesi charging
        $session = ChargingSession::findOrFail($id);
        $session->update([
            'status' => 'completed',
            'stopped_at' => now(),
            'energy_kwh' => $request->input('energy_kwh', 15.5),
            'total_cost' => $request->input('total_cost', 38750),
        ]);

        // 2. Ambil user yang sedang login
        $user = Auth::user();

        // 3. Kirim notifikasi charging_finished
        if ($user) {
            $user->notify(new ChargingFinishedNotification('charging_finished', $session));
        }

        return redirect()->back()->with('success', 'Pengisian daya selesai dan notifikasi telah dikirim ke email!');
    }
}