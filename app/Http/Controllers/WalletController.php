<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('topup', [
            'user' => $user,
            'recentTopUps' => Transaction::where('id_user', $user->id_user)
                ->where('type', 'topup')
                ->latest()
                ->paginate(5),
            'topupReference' => (string) Str::uuid(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nominal' => ['required', 'integer', 'min:10000', 'max:5000000'],
            'topup_reference' => ['required', 'uuid'],
        ]);

        $userId = Auth::user()->id_user;
        $invoiceNumber = 'TOPUP-' . $validated['topup_reference'];
        $amount = (int) $validated['nominal'];

        $created = DB::transaction(function () use ($userId, $invoiceNumber, $amount) {
            $user = User::whereKey($userId)->lockForUpdate()->firstOrFail();
            $topUp = Transaction::firstOrCreate(
                ['invoice_number' => $invoiceNumber],
                [
                    'id_user' => $userId,
                    'session_id' => null,
                    'payment_method' => 'e-wallet',
                    'amount' => $amount,
                    'type' => 'topup',
                    'description' => 'Penambahan saldo dompet.',
                    'status' => 'success',
                    'paid_at' => now(),
                ]
            );

            if (!$topUp->wasRecentlyCreated) {
                if ((int) $topUp->id_user !== (int) $userId || $topUp->type !== 'topup') {
                    throw ValidationException::withMessages([
                        'topup_reference' => 'Referensi transaksi tidak valid. Muat ulang halaman dan coba kembali.',
                    ]);
                }

                return false;
            }

            $user->increment('saldo', $amount);

            return true;
        });

        return redirect()
            ->route('topup.index')
            ->with('success', $created
                ? 'Saldo berhasil ditambahkan sebesar Rp ' . number_format($amount, 0, ',', '.') . '.'
                : 'Transaksi ini sudah diproses sebelumnya.');
    }
}
