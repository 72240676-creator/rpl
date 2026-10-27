<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
        $currentSaldo = (int) ($user->saldo ?? 0);

        // 6. Potong poin & Tambah saldo
        $user->{$pointColumn} = $currentPoints - $pointsToRedeem;
        $user->saldo = $currentSaldo + $saldoAdded;

        // 7. Simpan perubahan ke Database
        $user->save();

        return redirect()->back()->with('success', "Berhasil menukarkan {$pointsToRedeem} Poin menjadi Saldo Rp " . number_format($saldoAdded, 0, ',', '.') . "!");
    }
}