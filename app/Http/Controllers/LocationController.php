<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    // Menampilkan daftar lokasi di sisi pengemudi / pencarian (FR-02)
    public function index(Request $request)
    {
        $query = Location::query();

        // Fitur Filter berdasarkan status atau pencarian nama
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_lokasi', 'like', '%' . $request->search . '%')
                  ->orWhere('alamat', 'like', '%' . $request->search . '%');
        }

        $locations = $query->get();

        return view('locations.index', compact('locations'));
    }

    // Menampilkan panel khusus operator untuk mengelola lokasi (FR-03)
    public function operatorIndex()
    {
        $locations = Location::all();
        return view('operator.locations', compact('locations'));
    }

    // Menyimpan data lokasi baru oleh operator
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'jam_operasional' => 'required|string',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:aktif,tutup_sementara,penuh,dalam_perawatan',
        ]);

        Location::create($request->all());

        return back()->with('success', 'Stasiun SPKLU berhasil didaftarkan!');
    }

    // Mengubah status atau data lokasi
    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'status' => 'required|in:aktif,tutup_sementara,penuh,dalam_perawatan',
        ]);

        $location->update($request->all());

        return back()->with('success', 'Data lokasi SPKLU berhasil diperbarui!');
    }
}