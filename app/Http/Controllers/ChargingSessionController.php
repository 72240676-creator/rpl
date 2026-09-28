<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ChargingSession;
use App\Models\Charger;
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
            'charger_id' => 'required|exists:chargers,id',
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

        // Cari charger berdasarkan primary key "id"
        $charger = Charger::where('id', $request->charger_id)->first();

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
        $session->charger_id = $charger->id;
        $session->vehicle_id = $vehicle->id_vehicle ?? $vehicle->id;
        $session->start_time = now();
        $session->end_time = null;
        $session->energy_consumed_kwh = 0;
        $session->total_cost = 0;
        $session->status = 'ongoing';
        $session->save();

        return redirect()
            ->route('charging.session', $session->id)
            ->with('success', 'Pengisian daya berhasil dimulai!');
    }

    /**
     * Menampilkan halaman monitoring charging.
     */
    public function show(ChargingSession $session)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        // Pastikan session milik user yang login
        if ($session->user_id != $userId) {
            abort(403);
        }

        // Cari charger berdasarkan kolom "id"
        $charger = Charger::where('id', $session->charger_id)->first()
            ?? $session->charger;

        return view('chargingsession', compact('session', 'charger'));
    }

    /**
     * Menghentikan charging session dan menghitung tagihan.
     */
    public function stop(ChargingSession $session)
    {
        $userId = Auth::user()->id_user ?? Auth::id();

        // Pastikan session milik user yang login
        if ($session->user_id != $userId) {
            abort(403);
        }

        // Jika sudah selesai
        if (strtolower($session->status) !== 'ongoing' && !is_null($session->end_time)) {
            return redirect()
                ->route('charging.payment.view', $session->id)
                ->with('success', 'Charging session sudah selesai. Silakan lakukan pembayaran.');
        }

        $endTime = now();

        // Hitung durasi
        $durationSeconds = $session->start_time->diffInSeconds($endTime);
        $durationHours = $durationSeconds / 3600;

        // Ambil charger
        $charger = Charger::findOrFail($session->charger_id);

        // Hitung energi
        $energyConsumed = max(
            0.01,
            $durationHours * $charger->max_power_kw
        );

        // Hitung biaya
        $totalCost = round(
            $energyConsumed * $charger->price_per_kwh
        );

        // Update session
        $session->update([
            'end_time' => $endTime,
            'energy_consumed_kwh' => round($energyConsumed, 3),
            'total_cost' => $totalCost,
            'status' => 'completed',
        ]);

        // Arahkan ke halaman pembayaran
        return redirect()
            ->route('charging.payment.view', $session->id)
            ->with(
                'success',
                'Pengisian daya dihentikan. Silakan lakukan pembayaran.'
            );
    }

    /**
     * Proses pembayaran menggunakan saldo E-Wallet.
     */
    public function pay(ChargingSession $session)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        // Pastikan session milik user yang login
        if ($session->user_id != $userId) {
            return back()->with(
                'error',
                'Gagal: Sesi ini milik user lain.'
            );
        }

        // Jika sudah dibayar
        if ($session->status === 'paid') {
            return redirect()
                ->route('charging.invoice', $session->id)
                ->with(
                    'info',
                    'Tagihan ini sudah dibayar sebelumnya.'
                );
        }

        // Ambil saldo dan total biaya
        $saldoUser = (float) $user->saldo;
        $totalCost = (float) $session->total_cost;

        // Cek saldo
        if ($saldoUser < $totalCost) {
            return back()->with(
                'error',
                'Saldo tidak mencukupi! Saldo Anda: Rp ' .
                number_format($saldoUser, 0, ',', '.') .
                ', Tagihan: Rp ' .
                number_format($totalCost, 0, ',', '.')
            );
        }

        // Jalankan pembayaran dalam database transaction
        DB::beginTransaction();

        try {
            // Potong saldo
            $user->saldo = $saldoUser - $totalCost;
            $user->save();

            // Ubah status session menjadi paid
            $session->status = 'paid';
            $session->save();

            // Buat nomor invoice
            $invoiceNumber = 'INV-CHG-' .
                date('Ymd') . '-' .
                str_pad($session->id, 5, '0', STR_PAD_LEFT);

            // Buat transaksi pembayaran
            Transaction::create([
                'session_id' => $session->id,
                'invoice_number' => $invoiceNumber,
                'payment_method' => 'e-wallet',
                'amount' => $totalCost,
                'type' => 'payment',
                'status' => 'success',
                'paid_at' => now(),
            ]);

            DB::commit();

            // Setelah pembayaran berhasil, langsung menuju Invoice
            return redirect()
                ->route('charging.invoice', $session->id)
                ->with(
                    'success',
                    'Pembayaran sebesar Rp ' .
                    number_format($totalCost, 0, ',', '.') .
                    ' berhasil!'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Gagal Database: ' . $e->getMessage()
            );
        }
    }

    /**
     * Menampilkan halaman Review Pembayaran.
     */
    public function paymentView(ChargingSession $session)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_user ?? $user->id;

        // Pastikan session milik user yang login
        if ($session->user_id != $userId) {
            abort(403);
        }

        // Jika sudah dibayar
        if ($session->status === 'paid') {
            return redirect()
                ->route('charging.invoice', $session->id)
                ->with(
                    'info',
                    'Sesi ini sudah dibayar.'
                );
        }

        // Ambil charger berdasarkan id
        $charger = Charger::findOrFail($session->charger_id);

        return view(
            'payment',
            compact('session', 'charger')
        );
    }
}