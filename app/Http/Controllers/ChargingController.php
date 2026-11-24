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
        $user = Auth::user();
        $selectedVehicle = null;
        if ($user) {
            $selectedVehicleId = session('selected_vehicle_id');
            $selectedVehicle = $user->vehicles->firstWhere('id_vehicle', $selectedVehicleId)
                ?? $user->vehicles->first();
        }

        // Ambil unit charger yang aktif dan siap pakai dengan variasi konektor (CCS2 & Type 2)
        $availableChargers = Charger::with('location')
            ->where('is_online', true)
            ->where('status', 'tersedia')
            ->orderBy('id_location')
            ->orderByDesc('max_power_kw')
            ->limit(8)
            ->get();

        // Jika user memiliki kendaraan aktif, urutkan agar charger yang cocok muncul di awal
        if ($selectedVehicle && !empty($selectedVehicle->tipe_konektor)) {
            $vConn = strtolower($selectedVehicle->tipe_konektor);
            $availableChargers = $availableChargers->sortByDesc(function ($chg) use ($vConn) {
                $cConn = strtolower($chg->connector_type ?? '');
                $isCcs = str_contains($cConn, 'ccs');
                $isType2 = str_contains($cConn, 'type');
                if (str_contains($vConn, 'ccs') && ($isCcs || $isType2)) return 2;
                if (str_contains($vConn, 'type') && $isType2) return 2;
                if ($vConn === $cConn) return 2;
                return 0;
            })->values();
        }

        return view('scan-charge', compact('availableChargers', 'selectedVehicle'));
    }

    /**
     * Cari charger berdasarkan kode/nomor unit (Input Manual).
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'charger_code' => ['required', 'string'],
        ], [
            'charger_code.required' => 'Masukkan kode atau nomor unit charger.',
        ]);

        $code = trim($request->charger_code);

        // Cari berdasarkan device_number atau id_charger
        $charger = Charger::where('device_number', 'LIKE', $code)
            ->orWhere('id_charger', $code)
            ->first();

        if (!$charger) {
            return back()->with('error', "Charger dengan kode '{$code}' tidak ditemukan atau sedang tidak aktif.")->withInput();
        }

        return redirect()->route('scan.charge.show', $charger->id_charger);
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

        // Ambil semua kendaraan milik user yang sedang login
        $vehicles = auth()->user()->vehicles;
        $selectedVehicleId = session('selected_vehicle_id');

        return view(
            'charger-detail',
            compact('charger', 'vehicles', 'selectedVehicleId')
        );
    }
}