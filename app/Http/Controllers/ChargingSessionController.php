<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChargingSession;
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
        $energyConsumed = $request->input('energy_consumed', 5.0); 
        $totalCost = $energyConsumed * ($session->charger->price_per_kwh ?? 0);

        $session->update([
            'end_time'            => $endTime,
            'energy_consumed_kwh' => $energyConsumed,
            'total_cost'          => $totalCost,
            'status'              => 'completed',
        ]);

        // 1. Otomatis buat data notifikasi untuk ditampilkan di halaman Notifications web
        // (Sesuaikan nama kolom tabel notifikasi kamu, misal: user_id, title, message, is_read)
        \App\Models\Notification::create([
            'user_id' => $session->user_id,
            'title'   => 'Sesi Pengisian Daya Selesai',
            'message' => 'Sesi charging pada ' . ($session->charger->name ?? 'Charger') . ' telah selesai. Total tagihan: Rp ' . number_format($totalCost, 0, ',', '.'),
            'is_read' => false,
        ]);
    }

    return redirect()->route('charging.session', $id)
                     ->with('success', 'Sesi pengisian daya berhasil dihentikan!');
}
    /**
     * Menampilkan halaman pembayaran untuk sesi charging.
     */
    public function paymentView($id)
    {
        $session = ChargingSession::with(['charger', 'user', 'vehicle'])->findOrFail($id);

        // Menyesuaikan dengan file view 'payment.blade.php' yang ada di folder resources/views/
        return view('payment', compact('session'));
    }

    /**
     * Memproses pembayaran sesi charging.
     */
   public function pay(Request $request, $id)
{
    $session = ChargingSession::with(['user', 'charger'])->findOrFail($id);

    $session->update(['status' => 'paid']);

    // 2. Kirim email invoice ke Gmail pengguna jika emailnya tersedia
    if ($session->user && $session->user->email) {
        Mail::to($session->user->email)->send(new SendChargingInvoiceMail($session));
    }

    // Buat juga notifikasi web bahwa pembayaran sukses
    \App\Models\Notification::create([
        'user_id' => $session->user_id,
        'title'   => 'Pembayaran Berhasil',
        'message' => 'Pembayaran untuk sesi charging # ' . $id . ' telah berhasil dikonfirmasi. Invoice telah dikirim ke Gmail Anda.',
        'is_read' => false,
    ]);

    return redirect()->route('charging.session', $id)
                     ->with('success', 'Pembayaran berhasil diproses dan invoice dikirim ke Gmail!');

}
}
