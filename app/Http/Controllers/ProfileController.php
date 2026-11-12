<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Memanggil relasi vehicles yang sudah kita atur di Model User
        $vehicles = $user->vehicles; 
        
        return view('profile.index', compact('user', 'vehicles'));
    }

    public function storeVehicle(Request $request)
    {
        $request->validate([
            'merek' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'nomor_polisi' => 'required|string|max:15',
            'tipe_konektor' => 'required|string',
        ]);

        Vehicle::create([
            'id_user' => Auth::user()->id_user, // Mengambil id_user yang sedang login secara spesifik
            'merek' => $request->merek,
            'model' => $request->model,
            'nomor_polisi' => strtoupper($request->nomor_polisi),
            'tipe_konektor' => $request->tipe_konektor,
        ]);

        return back()->with('success', 'Kendaraan listrik berhasil didaftarkan!');
    }

    public function selectVehicle(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|integer',
        ]);

        $vehicle = Auth::user()->vehicles()
            ->where('id_vehicle', $validated['vehicle_id'])
            ->firstOrFail();

        $request->session()->put('selected_vehicle_id', $vehicle->id_vehicle);

        return redirect()->route('dashboard')->with(
            'success',
            'Kendaraan aktif diubah ke ' . $vehicle->merek . ' ' . $vehicle->model . '.'
        );
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'nama' => $request->nama,
            'nomor_telepon' => $request->nomor_telepon,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Informasi profil berhasil diperbarui!');
    }

    /**
     * Simulasi berkendara: mengurangi daya baterai mobil ke 20% (untuk kebutuhan demo / pengujian).
     */
    public function discharge(Vehicle $vehicle)
    {
        $userId = Auth::user()->id_user ?? Auth::id();
        if ($vehicle->id_user != $userId) {
            abort(403);
        }

        $vehicle->update(['current_battery_soc' => 20]);

        return back()->with('success', 'Simulasi berkendara berhasil! Baterai mobil kini tersisa 20% (siap untuk pengisian ulang).');
    }
}