<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\User;

class PointController extends Controller
{
    /**
     * Tampilkan halaman penukaran poin
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('points.index', compact('user'));
    }

    /**
     * Proses penukaran poin menjadi saldo
     */
    public function redeem(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'points' => 'required|integer|min:1',
        ], [
            'points.required' => 'Jumlah poin harus ditentukan.',
            'points.min' => 'Penukaran minimal 1 poin.',
        ]);

        $pointsToRedeem = (int) $request->points;

        // 2. Ambil data User terbaru dari Database
        /** @var User $user */
        $user = User::find(Auth::id());

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // 3. Deteksi otomatis nama kolom poin di database ('poin' atau 'points')
        $pointColumn = Schema::hasColumn('users', 'poin') ? 'poin' : 'points';
        $currentPoints = (int) ($user->{$pointColumn} ?? 0);

        // 4. Periksa kecukupan poin
        if ($currentPoints < $pointsToRedeem) {
            return redirect()->back()->with('error', "Poin tidak cukup. Poin Anda: {$currentPoints}, Dibutuhkan: {$pointsToRedeem}");
        }

        // 5. Hitung saldo baru (1 Poin = Rp 1.000)
        $saldoAdded = $pointsToRedeem * 1000;

        // 6. Potong poin, tambah saldo, dan catat transaksi finansial secara atomik
        DB::transaction(function () use ($user, $pointColumn, $currentPoints, $pointsToRedeem, $saldoAdded) {
            $user->{$pointColumn} = $currentPoints - $pointsToRedeem;
            $user->increment('saldo', $saldoAdded);

            Transaction::create([
                'id_user' => $user->id_user ?? $user->id,
                'session_id' => null,
                'invoice_number' => 'REWARD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
                'payment_method' => 'e-wallet',
                'amount' => $saldoAdded,
                'type' => 'reward',
                'description' => "Penukaran {$pointsToRedeem} Poin Reward menjadi saldo dompet.",
                'status' => 'success',
                'paid_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', "Berhasil menukarkan {$pointsToRedeem} Poin menjadi Saldo Rp " . number_format($saldoAdded, 0, ',', '.') . "!");
    }
}