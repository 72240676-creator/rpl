<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan facade Auth di atas

class ChargingController extends Controller
{
    public function scan()
    {
        return view('scan-charge');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $image = $request->file('qr_image');

        return view('scan-result', [
            'image' => $image->getClientOriginalName(),
        ]);
    }

    /**
     * @param mixed $charger
     */
    public function show($charger)
    {
        return view('charger-detail', compact('charger'));
    }

    public function start(Request $request)
    {
        // Validasi: Pastikan charger_id yang dikirim benar-benar ada di kolom id_charger tabel chargers
        $request->validate([
            'charger_id' => 'required|exists:chargers,id', 
        ], [
            'charger_id.exists' => 'Charger yang Anda pilih tidak tersedia di database.',
        ]);

        // Ambil data user secara eksplisit menggunakan Model User agar Intelephense mengenalinya
        $user = \App\Models\User::find(Auth::id());
        $vehicle = $user ? $user->vehicles()->first() : null;

        // Cegah error jika user belum mendaftarkan kendaraan di profilnya
        if (!$vehicle) {
            return back()->with('error', 'Anda harus mendaftarkan data kendaraan terlebih dahulu.');
        }

        // Simpan sesi pengisian daya dengan data dinamis yang valid
        $session = \App\Models\ChargingSession::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $vehicle->id_vehicle ?? $vehicle->id,
            'charger_id' => $request->charger_id,
            'status' => 'ongoing',
            'start_time' => now(),
            'end_time' => null,
            'energy_consumed_kwh' => 0,
            'total_cost' => 0,
        ]);
        return redirect()->route('charging.session', $session->id)
                         ->with('success', 'Pengisian daya berhasil dimulai!');
    }
}