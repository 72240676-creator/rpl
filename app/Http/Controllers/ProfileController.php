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
}