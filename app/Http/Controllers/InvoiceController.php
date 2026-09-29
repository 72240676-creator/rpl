<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use App\Models\Transaction;
use App\Notifications\ChargingFinishedNotification; // Integrasi kelas notifikasi
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class InvoiceController extends Controller
{
    /**
     * Menampilkan halaman Invoice & Struk Digital
     */
    public function show(ChargingSession $session)
    {
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;
        $sessionId = $session->getKey(); // Mengambil primary key session secara aman

        // Memastikan invoice hanya bisa dilihat oleh pemilik sesi
        if ($session->user_id != $userId) {
            abort(403);
        }

        // Mengambil transaksi yang berhasil untuk sesi ini
        $transaction = Transaction::where('session_id', $sessionId)
            ->where('status', 'success')
            ->firstOrFail();

        // Mengambil data charger
        $charger = $session->charger;

        // Opsional: Kirim/trigger notifikasi email saat invoice berhasil diakses/dibuka (jika diperlukan)
        // $user->notify(new ChargingFinishedNotification($session));

        return view('invoice', compact(
            'session',
            'transaction',
            'charger'
        ));
    }

    /**
     * Download Invoice dalam bentuk PDF & Kirim Notifikasi Email
     */
    public function downloadPdf(ChargingSession $session)
    {
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;
        $sessionId = $session->getKey(); // Mengambil primary key session secara aman

        // Memastikan invoice hanya bisa di-download oleh pemilik sesi
        if ($session->user_id != $userId) {
            abort(403);
        }

        // Mengambil transaksi yang berhasil
        $transaction = Transaction::where('session_id', $sessionId)
            ->where('status', 'success')
            ->firstOrFail();

        // Mengambil data charger
        $charger = $session->charger;

        // Mengirimkan notifikasi email ke user saat invoice/struk diunduh
        try {
            $user->notify(new ChargingFinishedNotification($session));
        } catch (\Exception $e) {
            // Tangkap error jika konfigurasi mail belum aktif, agar proses download PDF tetap berjalan lancar
        }

        // Membuat PDF dari view invoicepdf
        $pdf = Pdf::loadView('invoicepdf', compact(
            'session',
            'transaction',
            'charger'
        ));

        // Langsung download file PDF
        return $pdf->download(
            'invoice-session-' . $sessionId . '.pdf'
        );
    }
}