<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChargingSession;
use App\Notifications\ChargingFinishedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendChargingInvoiceMail;

class ChargingSessionController extends Controller
{
    /**
     * Menampilkan detail sesi charging.
     */
    public function show($id)
    {
        $session = ChargingSession::with(['charger', 'user', 'vehicle'])->findOrFail($id);

        return view('chargingsession', compact('session'));
    }

    /**
     * Menghentikan sesi pengisian daya (Stop Charging).
     */
   public function stop(Request $request, $id)
{
    $session = ChargingSession::with(['charger', 'user'])->findOrFail($id);

    if ($session->status === 'ongoing') {

        $endTime = now();

        $durationHours = $session->start_time->diffInSeconds($endTime) / 3600;

        $energyConsumed = $durationHours * (float) $session->charger->max_power_kw;

        $totalCost = $energyConsumed * (float) $session->charger->price_per_kwh;

        // Potong saldo
        $session->user->saldo -= $totalCost;
        $session->user->save();

        // Simpan hasil charging
        $session->update([
            'end_time' => $endTime,
            'energy_consumed_kwh' => $energyConsumed,
            'total_cost' => $totalCost,
            'status' => 'completed',
        ]);

        // Charger tersedia lagi
        $session->charger->update([
            'status' => 'tersedia',
        ]);
    }

    return redirect()
        ->route('charging.session', $id)
        ->with('success', 'Charging selesai. Saldo berhasil dipotong.');
}

    /**
     * Menampilkan halaman pembayaran untuk sesi charging.
     */
    public function paymentView($id)
    {
        $session = ChargingSession::with(['charger', 'user', 'vehicle'])->findOrFail($id);

        return view('payment', compact('session'));
    }

    /**
     * Memproses pembayaran sesi charging.
     */
    public function pay(Request $request, $id)
    {
        $session = ChargingSession::with(['user', 'charger'])->findOrFail($id);

        $session->update(['status' => 'paid']);

        // Kirim email invoice ke Gmail pengguna jika emailnya tersedia
        if ($session->user && $session->user->email) {
            Mail::to($session->user->email)->send(
                new SendChargingInvoiceMail($session)
            );
        }

        // Buat notifikasi web bahwa pembayaran sukses
        \App\Models\Notification::create([
            'user_id' => $session->user_id,
            'title'   => 'Pembayaran Berhasil',
            'message' => 'Pembayaran untuk sesi charging # ' . $id . ' telah berhasil dikonfirmasi. Invoice telah dikirim ke Gmail Anda.',
            'is_read' => false,
        ]);

        return redirect()->route('charging.session', $id)
                         ->with('success', 'Pembayaran berhasil diproses dan invoice dikirim ke Gmail!');
    }
        /**
     * Menampilkan riwayat charging user.
     */
    public function history()
    {
        $user = Auth::user();

        $histories = ChargingSession::where('user_id', $user->id_user)
            ->whereIn('status', ['completed', 'paid'])
            ->orderBy('end_time', 'desc')
            ->get();

        return view('riwayat', compact('histories'));
    }
}