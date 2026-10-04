<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use App\Models\Charger;
use App\Notifications\ChargingFinishedNotification;
use Illuminate\Http\Request;

class ChargingSessionController extends Controller
{
    /**
     * Memulai charging session.
     */
    public function start(Request $request)
    {
        $request->validate([
            'charger_id' => 'required|integer',
        ]);

        $user = auth()->user();

        // Pastikan user sudah login
        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah user masih punya session yang sedang berjalan
        $ongoingSession = ChargingSession::where(
            'user_id',
            $user->id_user
        )
            ->where('status', 'ongoing')
            ->first();

        if ($ongoingSession) {
            return redirect()
                ->route(
                    'charging.session',
                    $ongoingSession->id
                )
                ->with(
                    'error',
                    'Anda masih memiliki sesi pengisian yang sedang berjalan.'
                );
        }

        // Ambil charger yang dipilih
        // Primary key Charger = id_charger
        $charger = Charger::findOrFail(
            $request->charger_id
        );

        // Ambil kendaraan pertama milik user
        $vehicle = $user->vehicles()->first();

        if (!$vehicle) {
            return back()->with(
                'error',
                'Anda belum memiliki kendaraan.'
            );
        }

        // Buat charging session baru
        $session = ChargingSession::create([
            'user_id' => $user->id_user,

            // PENTING:
            // tabel chargers menggunakan id_charger
            'charger_id' => $charger->id_charger,

            'vehicle_id' => $vehicle->id_vehicle,

            'start_time' => now(),

            'end_time' => null,

            'energy_consumed_kwh' => 0,

            'total_cost' => 0,

            'status' => 'ongoing',
        ]);

        return redirect()
            ->route(
                'charging.session',
                $session->id
            )
            ->with(
                'success',
                'Pengisian daya berhasil dimulai.'
            );
    }

    /**
     * Menampilkan charging session yang sedang berjalan.
     */
    public function show(ChargingSession $session)
    {
        return view(
            'chargingsession',
            compact('session')
        );
    }

    /**
     * Menghentikan charging session.
     */
    public function stop(
        Request $request,
        ChargingSession $session
    ) {
        /*
         * Ambil nilai terakhir dari Live Charging.
         *
         * Nilai ini dikirim dari chargingsession.blade.php
         * melalui:
         * - energy_consumed
         * - total_cost
         */
        $energyConsumed = (float) $request->input(
            'energy_consumed',
            $session->energy_consumed_kwh ?? 0
        );

        $totalCost = (float) $request->input(
            'total_cost',
            $session->total_cost ?? 0
        );

        // Pastikan nilainya tidak negatif
        $energyConsumed = max(
            0,
            $energyConsumed
        );

        $totalCost = max(
            0,
            $totalCost
        );

        // Pembulatan sesuai tipe kolom database
        $energyConsumed = round(
            $energyConsumed,
            3
        );

        $totalCost = round(
            $totalCost
        );

        // Update charging session
        $session->update([
            'end_time' => now(),

            'energy_consumed_kwh' =>
                $energyConsumed,

            'total_cost' =>
                $totalCost,

            'status' =>
                'completed',
        ]);

        // Kirim notifikasi charging selesai
        $user = auth()->user();

        if ($user) {
            $user->notify(
                new ChargingFinishedNotification(
                    'charging_finished',
                    $session
                )
            );
        }

        return redirect()
            ->route(
                'charging.session',
                $session->id
            )
            ->with(
                'success',
                'Pengisian daya selesai dan notifikasi telah dikirim.'
            );
    }
}