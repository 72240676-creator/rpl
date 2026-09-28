<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Menampilkan halaman Invoice & Struk Digital
     */
    public function show(ChargingSession $session)
    {
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        // Memastikan invoice hanya bisa dilihat oleh pemilik sesi
        if ($session->user_id != $userId) {
            abort(403);
        }

        // Mengambil transaksi yang berhasil untuk sesi ini
        $transaction = Transaction::where('session_id', $session->id)
            ->where('status', 'success')
            ->firstOrFail();

        // Mengambil data charger
        $charger = $session->charger;

        return view('invoice', compact(
            'session',
            'transaction',
            'charger'
        ));
    }

    /**
     * Download Invoice dalam bentuk PDF
     */
    public function downloadPdf(ChargingSession $session)
    {
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        // Memastikan invoice hanya bisa di-download oleh pemilik sesi
        if ($session->user_id != $userId) {
            abort(403);
        }

        // Mengambil transaksi yang berhasil
        $transaction = Transaction::where('session_id', $session->id)
            ->where('status', 'success')
            ->firstOrFail();

        // Mengambil data charger
        $charger = $session->charger;

        // Membuat PDF dari view invoicepdf
        $pdf = Pdf::loadView('invoicepdf', compact(
            'session',
            'transaction',
            'charger'
        ));

        // Langsung download file PDF
        return $pdf->download(
            'invoice-session-' . $session->id . '.pdf'
        );
    }
}