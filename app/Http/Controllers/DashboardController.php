<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard pelanggan.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isOperator()) {
            return redirect()->route('operator.dashboard');
        }

        $availableChargers = static fn ($query) => $query
            ->where('status', 'tersedia')
            ->where('is_online', true);

        $dashboardLocations = Location::query()
            ->where('status', 'aktif')
            ->whereHas('chargers', $availableChargers)
            ->withCount(['chargers as available_chargers_count' => $availableChargers])
            ->with(['chargers' => static fn ($query) => $availableChargers($query)
                ->select('id_charger', 'id_location', 'connector_type', 'max_power_kw', 'price_per_kwh')
                ->orderByDesc('max_power_kw')])
            ->orderBy('nama_lokasi')
            ->get();

        // Daftar lokasi untuk formulir laporan pengguna
        $reportLocations = Location::query()
            ->where('status', 'aktif')
            ->whereHas('chargers', $availableChargers)
            ->with(['chargers' => fn ($query) => $query
                ->select('id_charger', 'id_location', 'device_number', 'connector_type')])
            ->orderBy('nama_lokasi')
            ->get();

        $vehicles = $user->vehicles()->orderBy('id_vehicle')->get();

        $vehicle = $vehicles->firstWhere(
            'id_vehicle',
            $request->session()->get('selected_vehicle_id')
        ) ?? $vehicles->first();

        if ($vehicle) {
            $request->session()->put('selected_vehicle_id', $vehicle->id_vehicle);
        }

        $ongoingSession = ChargingSession::where('user_id', $user->id_user)
            ->where('status', 'ongoing')
            ->latest('start_time')
            ->first();

        return view('dashboard', compact(
            'dashboardLocations',
            'vehicles',
            'vehicle',
            'ongoingSession',
            'reportLocations'
        ));
    }
}
