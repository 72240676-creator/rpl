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
use Illuminate\Validation\ValidationException;

class ChargingSessionController extends Controller
{
    /**
     * Memulai charging session.
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'charger_id' => 'required|integer|exists:chargers,id_charger',
            'vehicle_id' => 'required|integer',
            'reserved_amount' => 'required|integer|min:10000|max:5000000',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = $user->id_user ?? $user->id;

        $charger = Charger::findOrFail($validated['charger_id']);
        if ((float) $charger->price_per_kwh <= 0) {
            throw ValidationException::withMessages([
                'charger_id' => 'Tarif charger belum valid. Silakan pilih charger lain atau hubungi operator.',
            ]);
        }

        $vehicle = $user->vehicles()
            ->where('id_vehicle', $validated['vehicle_id'])
            ->first();

        if (!$vehicle) {
            return back()
                ->withErrors(['vehicle_id' => 'Kendaraan yang dipilih tidak terdaftar pada akun Anda.'])
                ->withInput();
        }

        $reservedAmount = (int) $validated['reserved_amount'];
        $session = DB::transaction(function () use ($userId, $charger, $vehicle, $reservedAmount) {
            $lockedUser = User::whereKey($userId)->lockForUpdate()->firstOrFail();
            $ongoingSession = ChargingSession::where('user_id', $userId)
                ->where('status', 'ongoing')
                ->first();

            if ($ongoingSession) {
                return $ongoingSession;
            }

            if ((float) $lockedUser->saldo < $reservedAmount) {
                throw ValidationException::withMessages([
                    'reserved_amount' => 'Saldo tidak mencukupi untuk menahan batas biaya ini. Silakan top up atau pilih batas yang lebih rendah.',
                ]);
            }

            $startSoc = (int) ($vehicle->current_battery_soc ?? 25);
            if ($startSoc >= 100) {
                $startSoc = 28;
                $vehicle->update(['current_battery_soc' => 28]);
            }

            $lockedUser->decrement('saldo', $reservedAmount);

            $session = ChargingSession::create([
                'user_id' => $userId,
                'charger_id' => $charger->id_charger,
                'vehicle_id' => $vehicle->id_vehicle,
                'start_battery_soc' => $startSoc,
                'end_battery_soc' => null,
                'start_time' => now(),
                'end_time' => null,
                'energy_consumed_kwh' => 0,
                'total_cost' => 0,
                'reserved_amount' => $reservedAmount,
                'status' => 'ongoing',
            ]);

            Transaction::create([
                'id_user' => $userId,
                'session_id' => $session->id,
                'invoice_number' => 'HOLD-' . $session->id,
                'payment_method' => 'e-wallet',
                'amount' => $reservedAmount,
                'type' => 'payment',
                'description' => 'Reservasi dana untuk sesi pengisian.',
                'status' => 'pending',
            ]);

            return $session;
        });

        if (!$session->wasRecentlyCreated) {
            return redirect()
                ->route('charging.session', $session->id)
                ->with('error', 'Anda masih memiliki sesi pengisian yang sedang berjalan.');
        }

        $request->session()->put('selected_vehicle_id', $vehicle->id_vehicle);

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

        $session->loadMissing(['charger.location', 'vehicle', 'user']);
        $charger = $session->charger ?? Charger::where('id_charger', $session->charger_id)->first();
        $vehicle = $session->vehicle ?? ($user ? $user->vehicles()->first() : null);

        return view(
            'chargingsession',
            compact('session', 'charger', 'vehicle')
        );
    }

    /**
     * Menghentikan charging.
     */
    public function stop(
        Request $request,
        ChargingSession $session
    ) {
        $validated = $request->validate([
            'energy_consumed_kwh' => 'sometimes|numeric|min:0',
            'total_cost' => 'sometimes|numeric|min:0',
            'end_battery_soc' => 'sometimes|integer|between:0,100',
        ]);

        $userId = Auth::user()->id_user ?? Auth::id();

        if ($session->user_id != $userId) {
            abort(403);
        }

        $result = DB::transaction(function () use ($session, $validated) {
            $lockedSession = ChargingSession::whereKey($session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (strtolower($lockedSession->status) !== 'ongoing') {
                return [
                    'session' => $lockedSession,
                    'newly_completed' => false,
                    'prepaid' => (float) ($lockedSession->reserved_amount ?? 0) > 0,
                    'earned_points' => 0,
                ];
            }

            $lockedSession->loadMissing(['vehicle', 'user']);
            $charger = Charger::where('id_charger', $lockedSession->charger_id)->first()
                ?? $lockedSession->charger;
            $endTime = now();
            $durationHours = $lockedSession->start_time->diffInSeconds($endTime) / 3600;
            $reportedEnergy = (float) ($validated['energy_consumed_kwh'] ?? 0);
            $energyConsumed = $reportedEnergy > 0
                ? $reportedEnergy
                : $durationHours * (float) ($charger->max_power_kw ?? 0);
            $tariff = (float) ($charger->price_per_kwh ?? 0);
            $reservedAmount = (float) ($lockedSession->reserved_amount ?? 0);
            $prepaid = $reservedAmount > 0;

            if ($prepaid) {
                if ($tariff <= 0) {
                    throw ValidationException::withMessages([
                        'session' => 'Tarif charger tidak valid; sesi belum dapat diselesaikan.',
                    ]);
                }

                $energyConsumed = floor(min($energyConsumed, $reservedAmount / $tariff) * 1000) / 1000;
                $totalCost = min($reservedAmount, round($energyConsumed * $tariff));
            } else {
                $totalCost = !empty($validated['total_cost']) && (float) $validated['total_cost'] > 0
                    ? round((float) $validated['total_cost'])
                    : round($energyConsumed * $tariff);
                $energyConsumed = round(max(0.01, $energyConsumed), 3);
                $totalCost = round(max(0, $totalCost));
            }

            $energyConsumed = round(max(0, $energyConsumed), 3);
            $batteryCapacity = (float) ($lockedSession->vehicle->battery_capacity_kwh ?? 60.0);
            $startSoc = (int) ($lockedSession->start_battery_soc ?? 25);
            $finalSoc = $prepaid || empty($validated['end_battery_soc'])
                ? min(100, round($startSoc + (($energyConsumed / max(1, $batteryCapacity)) * 100)))
                : min(100, (int) $validated['end_battery_soc']);

            $lockedSession->update([
                'end_time' => $endTime,
                'end_battery_soc' => $finalSoc,
                'energy_consumed_kwh' => $energyConsumed,
                'total_cost' => $totalCost,
                'status' => $prepaid ? 'paid' : 'completed',
            ]);

            if ($lockedSession->vehicle) {
                $lockedSession->vehicle->update(['current_battery_soc' => $finalSoc]);
            }

            if ($charger) {
                $charger->update(['status' => 'tersedia']);
            }

            $earnedPoints = 0;
            if ($prepaid) {
                $user = User::whereKey($lockedSession->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();
                $refund = max(0, (int) $reservedAmount - (int) $totalCost);

                if ($refund > 0) {
                    $user->increment('saldo', $refund);
                }

                $earnedPoints = (int) floor($totalCost / 10000);
                if ($earnedPoints > 0) {
                    $user->increment('points', $earnedPoints);
                }

                $holdTransaction = Transaction::where('session_id', $lockedSession->id)
                    ->where('invoice_number', 'HOLD-' . $lockedSession->id)
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->firstOrFail();
                $paidAt = now();

                $holdTransaction->update([
                    'invoice_number' => 'INV-' . $lockedSession->id . '-' . $paidAt->format('YmdHis'),
                    'amount' => $totalCost,
                    'description' => 'Pembayaran dari saldo yang ditahan saat sesi dimulai.',
                    'status' => 'success',
                    'paid_at' => $paidAt,
                ]);

                if ($refund > 0) {
                    Transaction::create([
                        'id_user' => $user->id_user,
                        'session_id' => $lockedSession->id,
                        'invoice_number' => 'REFUND-' . $lockedSession->id,
                        'payment_method' => 'e-wallet',
                        'amount' => $refund,
                        'type' => 'refund',
                        'description' => 'Pengembalian sisa dana reservasi charging.',
                        'status' => 'success',
                        'paid_at' => $paidAt,
                    ]);
                }
            }

            return [
                'session' => $lockedSession,
                'newly_completed' => true,
                'prepaid' => $prepaid,
                'earned_points' => $earnedPoints,
            ];
        });

        $session = $result['session'];
        if ($result['newly_completed'] && $session->user) {
            try {
                $session->user->notify(new ChargingFinishedNotification($session));
            } catch (\Exception $e) {
                report($e);
            }

            if ($result['prepaid'] && $session->user->email) {
                try {
                    Mail::to($session->user->email)->send(new SendChargingInvoiceMail($session));
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }

        if ($result['prepaid'] || $session->status === 'paid') {
            return redirect()
                ->route('charging.invoice', $session->id)
                ->with('success', 'Pengisian selesai. Biaya aktual telah dipotong dan sisa dana reservasi dikembalikan.');
        }

        return redirect()
            ->route('charging.payment.view', $session->id)
            ->with('success', 'Pengisian daya dihentikan. Silakan lanjutkan pembayaran.');
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

        $histories = ChargingSession::with([
                'charger.location',
                'vehicle',
                'transactions' => fn ($query) => $query->where('status', 'success')->latest('paid_at'),
            ])
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'paid'])
            ->orderBy('end_time', 'desc')
            ->get();

        return view(
            'riwayat',
            compact('histories')
        );
    }
}