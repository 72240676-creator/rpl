<?php

namespace App\Http\Controllers;

use App\Models\Charger;
use App\Models\LaporanPengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LaporanPenggunaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_location' => ['required', 'exists:locations,id_location'],
            'id_charger' => ['nullable', 'exists:chargers,id_charger'],
            'jenis_masalah' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string', 'max:2000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Pastikan charger sesuai dengan lokasi yang dipilih
        if (!empty($validated['id_charger'])) {
            $charger = Charger::where('id_charger', $validated['id_charger'])
                ->where('id_location', $validated['id_location'])
                ->first();

            if (!$charger) {
                throw ValidationException::withMessages([
                    'id_charger' => 'Charger tidak sesuai dengan lokasi SPKLU yang dipilih.',
                ]);
            }
        }

        $foto = null;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')
                ->store('laporan-pengguna', 'public');
        }

        LaporanPengguna::create([
            'id_user' => $request->user()->id_user,
            'id_location' => $validated['id_location'],
            'id_charger' => $validated['id_charger'] ?? null,
            'jenis_masalah' => $validated['jenis_masalah'],
            'deskripsi' => $validated['deskripsi'],
            'foto' => $foto,
            'status' => 'menunggu',
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Laporan berhasil dikirim. Terima kasih atas informasinya!');
    }
}