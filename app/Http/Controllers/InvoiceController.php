<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use App\Models\Transaction;
use App\Notifications\ChargingFinishedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Menampilkan halaman Invoice / Struk Digital.
     */
    public function show(ChargingSession $session)
    {
        $user = Auth::user();

        $userId = $user->id_user ?? $user->id;

        /*
         * Pastikan invoice hanya dapat dilihat
         * oleh pemilik charging session.
         */
        if ($session->user_id != $userId) {
            abort(403);
        }

        /*
         * Ambil transaksi yang sudah berhasil.
         */
        $transaction = Transaction::where(
            'session_id',
            $session->getKey()
        )
            ->where(
                'status',
                'success'
            )
            ->firstOrFail();

        /*
         * Ambil data charger.
         */
        $charger = $session->charger;

        return view(
            'invoice',
            compact(
                'session',
                'transaction',
                'charger'
            )
        );
    }

    /**
     * Download invoice dalam bentuk PDF.
     */
    public function downloadPdf(ChargingSession $session)
    {
        $user = Auth::user();

        $userId = $user->id_user ?? $user->id;

        /*
         * Pastikan invoice hanya bisa di-download
         * oleh pemilik charging session.
         */
        if ($session->user_id != $userId) {
            abort(403);
        }

        /*
         * Ambil transaksi yang berhasil.
         */
        $transaction = Transaction::where(
            'session_id',
            $session->getKey()
        )
            ->where(
                'status',
                'success'
            )
            ->firstOrFail();

        /*
         * Ambil data charger.
         */
        $charger = $session->charger;

        /*
         * Notifikasi menggunakan constructor
         * yang sesuai dengan branch Bagas:
         *
         * ChargingFinishedNotification(
         *     'charging_finished',
         *     $session
         * )
         */
        try {

            if ($user) {

                $user->notify(
                    new ChargingFinishedNotification(
                        'charging_finished',
                        $session
                    )
                );
            }

        } catch (\Exception $e) {

            /*
             * Jangan menggagalkan proses download PDF
             * jika notifikasi gagal.
             */
        }

        /*
         * Buat PDF dari view invoicepdf.
         */
        $pdf = Pdf::loadView(
            'invoicepdf',
            compact(
                'session',
                'transaction',
                'charger'
            )
        );

        /*
         * Download PDF.
         */
        return $pdf->download(
            'invoice-session-' .
            $session->getKey() .
            '.pdf'
        );
    }
}