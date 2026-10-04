<?php

namespace App\Http\Controllers;

use App\Models\Charger;
use Illuminate\Http\Request;

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
    public function show($charger)
    {
        // Ambil charger berdasarkan primary key id_charger
        $charger = Charger::findOrFail($charger);

        return view(
            'charger-detail',
            compact('charger')
        );
    }
}