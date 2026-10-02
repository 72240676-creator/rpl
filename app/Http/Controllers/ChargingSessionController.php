<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\ChargingSession;
use App\Models\Transaction;
use App\Mail\SendChargingInvoiceMail;

class ChargingSessionController extends Controller
{
    /**
     * Menampilkan detail sesi charging.
     */
    public function show($id)
    {
        $session = ChargingSession::with([
            'charger',
            'user',
            'vehicle'
        ])->findOrFail($id);

        return view(
            'chargingsession',
            compact('session')
        );
    }

    /**
     * Menghentikan sesi pengisian daya (Stop Charging).
     */
    public function stop(Request $request, $id)
    {
        $session = ChargingSession::with([
            'charger',
            'user'
        ])->findOrFail($id);

        if ($session->status === 'ongoing') {

            // Waktu charging selesai
            $endTime = now();

            /*
             * Mengambil nilai terakhir yang ditampilkan
             * oleh Live Charging.
             *
             * Nilai ini dikirim dari chargingsession.blade.php.
             */
            $energyConsumed = (float) $request->input(
                'energy_consumed',
                0
            );

            $totalCost = (float) $request->input(
                'total_cost',
                0
            );

            // Pastikan nilai tidak negatif
            $energyConsumed = max(
                0,
                $energyConsumed
            );

            $totalCost = max(
                0,
                $totalCost
            );

            // Pembulatan
            $energyConsumed = round(
                $energyConsumed,
                3
            );

            $totalCost = round(
                $totalCost
            );

            // Update data charging session
            $session->update([
                'end_time' => $endTime,
                'energy_consumed_kwh' => $energyConsumed,
                'total_cost' => $totalCost,
                'status' => 'completed',
            ]);

            // Buat notifikasi bahwa charging selesai
            \App\Models\Notification::create([
                'user_id' => $session->user_id,

                'title' =>
                    'Sesi Pengisian Daya Selesai',

                'message' =>
                    'Sesi charging pada ' .
                    ($session->charger->name ?? 'Charger') .
                    ' telah selesai. Total tagihan: Rp ' .
                    number_format(
                        $totalCost,
                        0,
                        ',',
                        '.'
                    ),

                'is_read' => false,
            ]);
        }

        return redirect()
            ->route(
                'charging.session',
                $id
            )
            ->with(
                'success',
                'Sesi pengisian daya berhasil dihentikan!'
            );
    }

    /**
     * Menampilkan halaman pembayaran untuk sesi charging.
     */
    public function paymentView($id)
    {
        $session = ChargingSession::with([
            'charger',
            'user',
            'vehicle'
        ])->findOrFail($id);

        return view(
            'payment',
            compact('session')
        );
    }

    /**
     * Memproses pembayaran sesi charging.
     */
    public function pay(Request $request, $id)
    {
        $session = ChargingSession::with([
            'user',
            'charger'
        ])->findOrFail($id);

        $user = $session->user;

        // Pastikan user ditemukan
        if (!$user) {
            return back()->with(
                'error',
                'Data pengguna tidak ditemukan.'
            );
        }

        /*
         * Jika sudah dibayar sebelumnya,
         * langsung arahkan ke invoice.
         */
        if ($session->status === 'paid') {

            return redirect()
                ->route(
                    'charging.invoice',
                    $session->id
                )
                ->with(
                    'info',
                    'Tagihan ini sudah dibayar sebelumnya.'
                );
        }

        // Ambil saldo user
        $saldoUser = (float) $user->saldo;

        // Ambil total biaya dari charging session
        $totalCost = (float) $session->total_cost;

        // Pastikan saldo mencukupi
        if ($saldoUser < $totalCost) {

            return back()->with(
                'error',
                'Saldo tidak mencukupi! Saldo Anda: Rp ' .
                number_format(
                    $saldoUser,
                    0,
                    ',',
                    '.'
                ) .
                ', Tagihan: Rp ' .
                number_format(
                    $totalCost,
                    0,
                    ',',
                    '.'
                )
            );
        }

        /*
         * Proses pembayaran dalam database transaction
         * agar saldo, session, dan transaction
         * berhasil disimpan secara bersamaan.
         */
        DB::beginTransaction();

        try {

            // ==========================================
            // 1. POTONG SALDO USER
            // ==========================================

            $user->saldo =
                $saldoUser - $totalCost;

            $user->save();


            // ==========================================
            // 2. UBAH STATUS CHARGING
            // ==========================================

            $session->update([
                'status' => 'paid',
            ]);


            // ==========================================
            // 3. BUAT NOMOR INVOICE
            // ==========================================

            $invoiceNumber =
                'INV-CHG-' .
                now()->format('Ymd') .
                '-' .
                str_pad(
                    $session->id,
                    5,
                    '0',
                    STR_PAD_LEFT
                );


            // ==========================================
            // 4. BUAT TRANSACTION
            // ==========================================

            $transaction = Transaction::create([

                'session_id' =>
                    $session->id,

                'invoice_number' =>
                    $invoiceNumber,

                'payment_method' =>
                    'e-wallet',

                'amount' =>
                    $totalCost,

                'type' =>
                    'payment',

                'status' =>
                    'success',

                'paid_at' =>
                    now(),

            ]);


            // Commit semua perubahan database
            DB::commit();


            // ==========================================
            // 5. KIRIM EMAIL INVOICE
            // ==========================================

            if (
                $user->email
            ) {

                try {

                    Mail::to(
                        $user->email
                    )->send(
                        new SendChargingInvoiceMail(
                            $session
                        )
                    );

                } catch (\Exception $mailException) {

                    /*
                     * Jika email gagal dikirim,
                     * pembayaran tetap dianggap berhasil.
                     */
                }
            }


            // ==========================================
            // 6. NOTIFIKASI PEMBAYARAN
            // ==========================================

            \App\Models\Notification::create([

                'user_id' =>
                    $session->user_id,

                'title' =>
                    'Pembayaran Berhasil',

                'message' =>
                    'Pembayaran untuk sesi charging #' .
                    $session->id .
                    ' sebesar Rp ' .
                    number_format(
                        $totalCost,
                        0,
                        ',',
                        '.'
                    ) .
                    ' berhasil. Invoice ' .
                    $invoiceNumber .
                    ' telah dibuat.',

                'is_read' =>
                    false,
            ]);


            // ==========================================
            // 7. ARAHKAN KE INVOICE
            // ==========================================

            return redirect()
                ->route(
                    'charging.invoice',
                    $session->id
                )
                ->with(
                    'success',
                    'Pembayaran berhasil! Invoice berhasil dibuat.'
                );

        } catch (\Exception $e) {

            // Batalkan perubahan database
            DB::rollBack();

            return back()->with(
                'error',
                'Pembayaran gagal: ' .
                $e->getMessage()
            );
        }
    }
}