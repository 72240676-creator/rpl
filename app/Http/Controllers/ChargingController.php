<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Vehicle;

class ChargingController extends Controller
{
    /**
     * Menampilkan halaman scan/pilihan charger
     */
    public function scan()
    {
    // Ubah dari 'charging.scan' menjadi 'scan-charge'
    return view('scan-charge');
    }

    /**
     * Menampilkan detail charger sebelum mulai charging
     */
    public function show($charger)
    {
        $chargerModel = Charger::findOrFail($charger);
        $vehicles = Auth::user()->vehicles;

        return view('charger-detail', [
            'charger' => $chargerModel,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Memulai sesi pengisian daya (Start Charging)
     */
    public function start(Request $request, $charger = null)
    {
        // Validasi input kendaraan yang dipilih user
        $request->validate([
            'vehicle_id' => 'required',
        ]);

        // Jika ID charger dikirim via URL parameter atau dari Request
        $chargerId = $charger ?? $request->input('charger_id');

        if (!$chargerId) {
            return back()->with('error', 'Data charger tidak ditemukan.');
        }

        // Ambil user yang sedang login
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        // Buat sesi charging baru
        $session = ChargingSession::create([
            'user_id'             => $userId,
            'vehicle_id'          => $request->vehicle_id,
            'charger_id'          => $chargerId,
            'status'              => 'ongoing',
            'start_time'          => now(),
            'energy_consumed_kwh' => 0,
            'total_cost'          => 0,
        ]);

        $sessionId = $session->getKey();

        // Redirect ke halaman sesi charging aktif
        return redirect()->route('charging.session', $sessionId)
                         ->with('success', 'Pengisian daya berhasil dimulai!');
    }

    /**
     * Menampilkan halaman sesi charging yang sedang aktif
     */
    public function sessionDetail($id)
    {
        $session = ChargingSession::with(['charger', 'vehicle'])->findOrFail($id);

        // Menggunakan view 'chargingsessions' sesuai file yang ada di folder views kamu
        return view('chargingsessions', [
            'session' => $session
        ]);
    }
}