<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Charger;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        $locations = Location::with('chargers')->latest()->get();
        return view('admin.stations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi'     => 'required|string|max:255',
            'alamat'          => 'required|string',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
            'jam_operasional' => 'required|string',
            'fasilitas'       => 'nullable|string',
            'status'          => 'required|in:aktif,tutup_sementara,penuh,perawatan',
        ]);

        Location::create($request->all());

        return redirect()->back()->with('success', 'Stasiun SPKLU berhasil ditambahkan!');
    }

    public function update(Request $request, $id_location)
    {
        $location = Location::findOrFail($id_location);
        
        $request->validate([
            'nama_lokasi'     => 'required|string|max:255',
            'alamat'          => 'required|string',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
            'jam_operasional' => 'required|string',
            'fasilitas'       => 'nullable|string',
            'status'          => 'required|in:aktif,tutup_sementara,penuh,perawatan',
        ]);

        $location->update($request->all());

        return redirect()->back()->with('success', 'Data stasiun berhasil diperbarui!');
    }

    public function destroy($id_location)
    {
        Location::findOrFail($id_location)->delete();
        return redirect()->back()->with('success', 'Stasiun berhasil dihapus!');
    }

    public function storeCharger(Request $request, $id_location)
    {
        $request->validate([
            'device_number'  => 'required|string|unique:chargers,device_number',
            'connector_type' => 'required|string',
            'max_power_kw'   => 'required|numeric',
            'price_per_kwh'  => 'required|numeric',
            'is_online'      => 'required|boolean',
            'status'         => 'required|in:tersedia,digunakan,rusak',
        ]);

        Charger::create([
            'id_location'    => $id_location,
            'device_number'  => $request->device_number,
            'connector_type' => $request->connector_type,
            'max_power_kw'   => $request->max_power_kw,
            'price_per_kwh'  => $request->price_per_kwh,
            'is_online'      => $request->is_online,
            'status'         => $request->status,
        ]);

        return redirect()->back()->with('success', 'Perangkat charger berhasil ditambahkan!');
    }

    public function destroyCharger($id_charger)
    {
        Charger::findOrFail($id_charger)->delete();
        return redirect()->back()->with('success', 'Charger berhasil dihapus!');
    }
}