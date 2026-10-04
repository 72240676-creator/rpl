<?php

namespace App\Http\Controllers;

use App\Mail\SendChargingInvoiceMail;
use App\Models\ChargingSession;
use App\Models\Charger;
use App\Models\Transaction;
use App\Notifications\ChargingFinishedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $user = auth()->user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah masih ada charging yang berjalan
        $ongoingSession = ChargingSession::where('user_id', $user->id_user)
            ->where('status', 'ongoing')
            ->first();

        if ($ongoingSession) {
            return redirect()
                ->route('charging.session', $ongoingSession->id)
                ->with(
                    'error',
                    'Anda masih memiliki sesi pengisian yang sedang berjalan.'
                );
        }

        $charger = Charger::findOrFail($request->charger_id);

        $vehicle = $user->vehicles()->first();

        if (!$vehicle) {
            return back()->with(
                'error',
                'Anda belum memiliki kendaraan.'
            );
        }

        // Buat charging session
        $session = ChargingSession::create([
            'user_id' => $user->id_user,
            'charger_id' => $charger->id_charger,
            'vehicle_id' => $vehicle->id_vehicle,
            'start_time' => now(),
            'end_time' => null,
            'energy_consumed_kwh' => 0,
            'total_cost' => 0,
            'status' => 'ongoing',
        ]);

        return redirect()
            ->route('charging.session', $session->id);
    }

    /**
     * Detail charging session.
     */
    public function show(ChargingSession $session)
    {
        return view(
            'chargingsession',
            compact('session')
        );
    }

    /**
     * Menghentikan charging.
     */
    public function stop(
        Request $request,
        ChargingSession $session
    ) {
        /*
         * HANYA jalankan jika status masih ongoing.
         *
         * Ini penting supaya refresh / klik dua kali
         * tidak membuat notifikasi duplikat.
         */
        if ($session->status === 'ongoing') {

            $endTime = now();

            // Hitung durasi charging
            $durationHours =
                $session->start_time->diffInSeconds($endTime) / 3600;

            // Hitung energi
            $energyConsumed =
                $durationHours *
                (float) $session->charger->max_power_kw;

            // Hitung biaya
            $totalCost =
                $energyConsumed *
                (float) $session->charger->price_per_kwh;

            $energyConsumed = round(
                max(0, $energyConsumed),
                3
            );

            $totalCost = round(
                max(0, $totalCost),
                2
            );

            /*
             * Simpan hasil charging.
             */
            $session->update([
                'end_time' => $endTime,
                'energy_consumed_kwh' => $energyConsumed,
                'total_cost' => $totalCost,
                'status' => 'completed',
            ]);

            /*
             * Charger tersedia kembali.
             */
            $session->charger->update([
                'status' => 'tersedia',
            ]);

            /*
             * =====================================================
             * NOTIFIKASI CHARGING SELESAI
             * =====================================================
             *
             * HANYA dibuat di sini.
             */
            if ($session->user) {
                try {
                    $session->user->notify(
                        new ChargingFinishedNotification(
                            'charging_finished',
                            $session
                        )
                    );
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }

        /*
         * Setelah selesai langsung ke pembayaran.
         */
        return redirect()
            ->route(
                'charging.payment.view',
                $session->id
            )
            ->with(
                'success',
                'Charging selesai. Silakan lanjutkan pembayaran.'
            );
    }

    /**
     * Halaman pembayaran.
     */
    public function paymentView($id)
    {
        $session = ChargingSession::with([
            'charger',
            'user',
            'vehicle',
        ])->findOrFail($id);

        /*
         * Kalau sudah dibayar,
         * jangan tampilkan halaman pembayaran lagi.
         */
        $transaction = Transaction::where(
            'session_id',
            $session->id
        )
            ->where('status', 'success')
            ->first();

        if (
            $session->status === 'paid' ||
            $transaction
        ) {
            return redirect()
                ->route(
                    'charging.invoice',
                    $session->id
                );
        }

        return view(
            'payment',
            compact('session')
        );
    }

    /**
     * Proses pembayaran.
     */
    public function pay(
        Request $request,
        $id
    ) {
        $session = ChargingSession::with([
            'charger',
            'user',
        ])->findOrFail($id);

        /*
         * Cek transaksi yang sudah berhasil.
         */
        $transaction = Transaction::where(
            'session_id',
            $session->id
        )
            ->where('status', 'success')
            ->first();

        /*
         * Kalau sudah pernah bayar,
         * langsung buka invoice.
         *
         * Jangan membuat transaksi,
         * email atau notifikasi lagi.
         */
        if ($transaction) {

            if ($session->status !== 'paid') {
                $session->update([
                    'status' => 'paid',
                ]);
            }

            return redirect()
                ->route(
                    'charging.invoice',
                    $session->id
                );
        }

        /*
         * =====================================================
         * BUAT TRANSAKSI
         * =====================================================
         */
        $transaction = Transaction::create([
            'session_id' => $session->id,

            'invoice_number' =>
                'INV-' .
                $session->id .
                '-' .
                now()->format('YmdHis'),

            'payment_method' => 'e-wallet',

            'amount' => $session->total_cost ?? 0,

            'type' => 'payment',

            'status' => 'success',

            'paid_at' => now(),
        ]);

        /*
         * Tandai session sebagai PAID.
         */
        $session->update([
            'status' => 'paid',
        ]);

        /*
         * =====================================================
         * KIRIM EMAIL INVOICE
         * =====================================================
         */
        if (
            $session->user &&
            $session->user->email
        ) {
            try {

                Mail::to(
                    $session->user->email
                )->send(
                    new SendChargingInvoiceMail(
                        $session
                    )
                );

            } catch (\Exception $e) {

                report($e);
            }
        }

        /*
         * =====================================================
         * NOTIFIKASI PEMBAYARAN BERHASIL
         * =====================================================
         *
         * Gunakan notification Laravel saja.
         *
         * Jangan Notification::create() lagi karena
         * bisa menyebabkan dua sistem notifikasi berjalan.
         */
        if ($session->user) {
            try {

                $session->user->notify(
                    new ChargingFinishedNotification(
                        'payment_success',
                        $transaction
                    )
                );

            } catch (\Exception $e) {

                report($e);
            }
        }

        /*
         * Buka invoice.
         */
        return redirect()
            ->route(
                'charging.invoice',
                $session->id
            )
            ->with(
                'success',
                'Pembayaran berhasil! Invoice telah dikirim ke Gmail Anda.'
            );
    }

    /**
     * Riwayat charging.
     */
    public function history()
    {
        $user = auth()->user();

        $histories = ChargingSession::where(
            'user_id',
            $user->id_user
        )
            ->whereIn(
                'status',
                [
                    'completed',
                    'paid',
                ]
            )
            ->orderBy(
                'end_time',
                'desc'
            )
            ->get();

        return view(
            'riwayat',
            compact('histories')
        );
    }
}