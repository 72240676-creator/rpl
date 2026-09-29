<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ChargingSession;
use App\Models\Charger;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ChargingSessionController extends Controller
 {
    /**
     * Memulai charging session.
     */
   public function start(Request $request)
    {
        $request->validate([
            'charger_id' => 'required',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        // Cek apakah user masih mempunyai charging session yang berjalan
        $ongoingSession = ChargingSession::where('user_id', $userId)
            ->whereIn('status', ['ongoing', 'ONGOING'])
            ->whereNull('end_time')
            ->first();

        if ($ongoingSession) {
            return redirect()
                ->route('charging.session', $ongoingSession->id)
                ->with('error', 'Anda masih memiliki sesi pengisian yang sedang berjalan.');
        }

        // Cari charger murni menggunakan id_charger
        $charger = Charger::where('id_charger', $request->charger_id)->first();

        if (!$charger) {
            return back()->with('error', 'Unit charger tidak ditemukan.');
        }

        // Ambil kendaraan pertama milik user
        $vehicle = $user->vehicles()->first();

        if (!$vehicle) {
            return back()->with('error', 'Anda belum memiliki kendaraan.');
        }

        // Buat charging session baru
        $session = new ChargingSession();
        $session->user_id = $userId;
        $session->charger_id = $charger->id_charger;
        $session->vehicle_id = $vehicle->id_vehicle ?? $vehicle->id;
        $session->start_time = now();
        $session->end_time = null;
        $session->energy_consumed_kwh = 0;
        $session->total_cost = 0;
        $session->status = 'ongoing';
        $session->save();

        return redirect()->route('charging.session', $session->id);
    }
    /**
     * Menampilkan halaman monitoring charging.
     */
    public function show(ChargingSession $session)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        if ($session->user_id != $userId) {
            abort(403);
        }

        // Ambil charger hanya berdasarkan id_charger atau lewat relasi Eloquent
        $charger = Charger::where('id_charger', $session->charger_id)->first() ?? $session->charger;

        return view('chargingsession', compact('session', 'charger'));
    }
    /**
     * Menghentikan charging session (menghitung tagihan).
     */
    public function stop(ChargingSession $session)
    {
        $userId = Auth::user()->id_user ?? Auth::id();

        if ($session->user_id != $userId) {
            abort(403);
        }

        // Jika sudah selesai sebelumnya, langsung arahkan ke halaman pembayaran
        if (strtolower($session->status) !== 'ongoing' && !is_null($session->end_time)) {
            return redirect()
                ->route('charging.payment.view', $session->id)
                ->with('success', 'Charging session sudah selesai. Silakan lakukan pembayaran.');
        }

        $endTime = now();
        $durationSeconds = $session->start_time->diffInSeconds($endTime);
        $charger = Charger::findOrFail($session->charger_id);

        // Perhitungan energi (kWh) & biaya
        $durationHours = $durationSeconds / 3600;
        $energyConsumed = max(0.01, $durationHours * $charger->max_power_kw);
        $totalCost = round($energyConsumed * $charger->price_per_kwh);

        // Update session menjadi selesai
        $session->update([
            'end_time' => $endTime,
            'energy_consumed_kwh' => round($energyConsumed, 3),
            'total_cost' => $totalCost,
            'status' => 'completed',
        ]);

        // 🚀 Ubah tujuan redirect ke halaman/form rincian pembayaran
        return redirect()
            ->route('charging.payment.view', $session->id)
            ->with('success', 'Pengisian daya dihentikan. Silakan lakukan pembayaran.');
    }

    /**
     * Proses eksekusi pembayaran saldo E-Wallet.
     */
   public function pay(ChargingSession $session)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        // 1. Cek Kepemilikan Sesi Charging
        if ($session->user_id != $userId) {
            return back()->with('error', 'Gagal: Sesi ini milik user lain.');
        }

        // 2. Cek Jika Status Tagihan Sudah Lunas
        if ($session->status === 'paid') {
            return redirect()->route('dashboard')->with('info', 'Tagihan ini sudah dibayar sebelumnya.');
        }

        // 3. Konversi dan Cek Saldo User
        $saldoUser = (float) $user->saldo;
        $totalCost = (float) $session->total_cost;

        if ($saldoUser < $totalCost) {
            return back()->with('error', 'Saldo tidak mencukupi! Saldo Anda: Rp ' . number_format($saldoUser, 0, ',', '.') . ', Tagihan: Rp ' . number_format($totalCost, 0, ',', '.'));
        }

        // 4. Eksekusi Pembayaran menggunakan DB Transaction
        DB::beginTransaction();
        try {
            // Potong Saldo User
            $user->saldo = $saldoUser - $totalCost;
            $user->save();

            // Update Status Sesi Pengisian menjadi paid
            $session->status = 'paid';
            $session->save();

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Pembayaran sebesar Rp ' . number_format($totalCost, 0, ',', '.') . ' berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Database: ' . $e->getMessage());
        }
    }
    /**
     * Menampilkan halaman Review Pembayaran.
     */
    public function paymentView(ChargingSession $session)
    {
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        if ($session->user_id != $userId) {
            abort(403);
        }

        if ($session->status === 'paid') {
            return redirect()
                ->route('charging.session', $session->id)
                ->with('error', 'Sesi ini sudah dibayar.');
        }

        $charger = Charger::findOrFail($session->charger_id);

        return view('payment', compact('session', 'charger'));
    }
}