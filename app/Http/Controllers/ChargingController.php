<?php

namespace App\Http\Controllers;

use App\Models\Charger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class ChargingController extends Controller
{
    /**
     * Halaman scan charger.
     */
    public function scan()
    {
        return view('scan-charge');
    }

    /**
     * Memproses hasil scan QR.
     */
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
     * Menampilkan detail charger.
     */
    public function show(int $charger)
    {
        // Ambil charger berdasarkan primary key id_charger
        $charger = Charger::findOrFail($charger);

        return view(
            'charger-detail',
            compact('charger')
        );
    }

    public function start(Request $request)
    {
        // Validasi: Pastikan charger_id yang dikirim benar-benar ada di kolom id_charger tabel chargers
        $request->validate([
            'charger_id' => 'required|exists:chargers,id_charger', 
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
            'user_id'    => Auth::id(), // Akan otomatis mengambil id_user karena sudah diset di User.php
            'vehicle_id' => $vehicle->id_vehicle ?? $vehicle->id, // Menyesuaikan dengan primary key Vehicle Anda
            'charger_id' => $request->charger_id,
            'status'     => 1, // Angka 1 mewakili status aktif
            'start_time' => now(),
        ]);

        return redirect()->route('charging.session', $session->id)
                         ->with('success', 'Pengisian daya berhasil dimulai!');
    }
}