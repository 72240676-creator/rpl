<?php

namespace App\Http\Controllers;

use App\Mail\SendChargingInvoiceMail;
use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ChargingFinishedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = $user->id_user ?? $user->id;

        // Cek apakah masih ada charging yang berjalan
        $ongoingSession = ChargingSession::where('user_id', $userId)
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

        $charger = Charger::where('id_charger', $request->charger_id)->first() ?? Charger::findOrFail($request->charger_id);

        $vehicle = $user->vehicles()->first();

        if (!$vehicle) {
            return back()->with(
                'error',
                'Anda belum memiliki kendaraan.'
            );
        }

        // Buat charging session
        $session = ChargingSession::create([
            'user_id' => $userId,
            'charger_id' => $charger->id_charger ?? $charger->id,
            'vehicle_id' => $vehicle->id_vehicle ?? $vehicle->id,
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
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        if ($session->user_id != $userId) {
            abort(403);
        }

        $charger = Charger::where('id_charger', $session->charger_id)->first() ?? $session->charger;

        return view(
            'chargingsession',
            compact('session', 'charger')
        );
    }

    /**
     * Menghentikan charging.
     */
    public function stop(
        Request $request,
        ChargingSession $session
    ) {
        $userId = Auth::user()->id_user ?? Auth::id();

        if ($session->user_id != $userId) {
            abort(403);
        }

        /*
         * HANYA jalankan jika status masih ongoing.
         * Ini penting supaya refresh / klik dua kali tidak membuat notifikasi duplikat.
         */
        if (strtolower($session->status) === 'ongoing') {

            $endTime = now();

            // Hitung durasi charging
            $durationHours = $session->start_time->diffInSeconds($endTime) / 3600;

            $charger = Charger::where('id_charger', $session->charger_id)->first() ?? $session->charger;

            // Hitung energi
            $energyConsumed = $durationHours * (float) ($charger->max_power_kw ?? 0);

            // Hitung biaya
            $totalCost = $energyConsumed * (float) ($charger->price_per_kwh ?? 0);

            $energyConsumed = round(max(0.01, $energyConsumed), 3);
            $totalCost = round(max(0, $totalCost));

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
            if ($charger) {
                $charger->update([
                    'status' => 'tersedia',
                ]);
            }

            /*
             * NOTIFIKASI CHARGING SELESAI
             */
            if ($session->user) {
            try {
                $session->user->notify(
                new ChargingFinishedNotification($session)
        );
    } catch (\Exception $e) {
        report($e);
    }
}
        }

        /*
         * Setelah selesai langsung ke halaman pembayaran.
         */
        return redirect()
            ->route(
                'charging.payment.view',
                $session->id
            )
            ->with(
                'success',
                'Pengisian daya dihentikan. Silakan lanjutkan pembayaran.'
            );
    }

    /**
     * Halaman pembayaran.
     *
     * @param int $id
     */
    public function paymentView(int $id)
    {
        $session = ChargingSession::with([
            'charger',
            'user',
            'vehicle',
        ])->findOrFail($id);

        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        if ($session->user_id != $userId) {
            abort(403);
        }

        /*
         * Kalau sudah dibayar, langsung alihkan ke invoice.
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

        $charger = Charger::where('id_charger', $session->charger_id)->first() ?? $session->charger;

        return view(
            'payment',
            compact('session', 'charger')
        );
    }

    /**
     * Proses pembayaran.
     *
     * @param Request $request
     * @param int $id
     */
    public function pay(
        Request $request,
        int $id
    ) {
        $session = ChargingSession::with([
            'charger',
            'user',
        ])->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        if ($session->user_id != $userId) {
            return back()->with('error', 'Gagal: Sesi ini milik user lain.');
        }

        /*
         * Cek transaksi yang sudah berhasil.
         */
        $transaction = Transaction::where(
            'session_id',
            $session->id
        )
            ->where('status', 'success')
            ->first();

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

        // Cek Saldo User
        $saldoUser = (float) $user->saldo;
        $totalCost = (float) $session->total_cost;

        if ($saldoUser < $totalCost) {
            return back()->with('error', 'Saldo tidak mencukupi! Saldo Anda: Rp ' . number_format($saldoUser, 0, ',', '.') . ', Tagihan: Rp ' . number_format($totalCost, 0, ',', '.'));
        }

        DB::beginTransaction();
        try {
            // 1. Potong Saldo User
            $user->saldo = $saldoUser - $totalCost;

            // 2. Hitung & Tambah Poin User (1 Poin per Rp 10.000)
            $earnedPoints = (int) floor($totalCost / 10000);
            if ($earnedPoints > 0) {
                $user->points = ($user->points ?? 0) + $earnedPoints;
            }

            // SIMPAN PERUBAHAN SALDO & POIN KE DATABASE
            $user->save();

            // 3. Buat Transaksi
            $transaction = Transaction::create([
                'session_id' => $session->id,
                'invoice_number' => 'INV-' . $session->id . '-' . now()->format('YmdHis'),
                'payment_method' => 'e-wallet',
                'amount' => $totalCost,
                'type' => 'payment',
                'status' => 'success',
                'paid_at' => now(),
            ]);

            // 4. Tandai session sebagai PAID
            $session->update([
                'status' => 'paid',
            ]);

            DB::commit();

            // KIRIM EMAIL INVOICE
            if ($session->user && $session->user->email) {
                try {
                    Mail::to($session->user->email)->send(
                        new SendChargingInvoiceMail($session)
                    );
                } catch (\Exception $e) {
                    report($e);
                }
            }

            // NOTIFIKASI PEMBAYARAN BERHASIL
            if ($session->user) {
                try {
                    $session->user->notify(
                        new ChargingFinishedNotification($session)
                    );
                } catch (\Exception $e) {
                    report($e);
                }
            }

            $messageSuccess = 'Pembayaran berhasil!';
            if ($earnedPoints > 0) {
                $messageSuccess .= " Anda mendapatkan +{$earnedPoints} Poin!";
            }
            $messageSuccess .= ' Invoice telah dikirim ke Gmail Anda.';

            return redirect()
                ->route(
                    'charging.invoice',
                    $session->id
                )
                ->with(
                    'success',
                    $messageSuccess
                );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Database: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat charging.
     */
    public function history()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        $histories = ChargingSession::where('user_id', $userId)
            ->whereIn('status', ['completed', 'paid'])
            ->orderBy('end_time', 'desc')
            ->get();

        return view(
            'riwayat',
            compact('histories')
        );
    }
}