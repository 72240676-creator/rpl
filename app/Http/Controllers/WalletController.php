<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        return view('topup'); // Menampilkan halaman top up
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nominal' => 'required|numeric|min:10000',
            'metode_pembayaran' => 'required|string'
        ]);

        // Ambil data user yang sedang login
        $user = User::find(Auth::id());
        
        // Tambahkan saldo (Simulasi pembayaran sukses)
        $user->saldo = $user->saldo + $request->nominal;
        $user->save(); // Simpan ke database

        return redirect()->route('dashboard')->with('success', 'Top Up sebesar Rp' . number_format($request->nominal, 0, ',', '.') . ' menggunakan ' . strtoupper($request->metode_pembayaran) . ' berhasil!');
    }
    public function processTopUp(Request $request)
    {
        // Validasi nominal input (minimal 10.000)
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required',
        ], [
            'amount.min' => 'Nominal top up minimal adalah Rp 10.000.',
            'amount.required' => 'Silakan masukkan atau pilih nominal top up.',
        ]);

        $user = Auth::user();

        // Tambahkan nominal yang diketik ke saldo user
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->saldo += $request->nominal;
        $user->save();

        return redirect()->back()->with('success', 'Top up sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil!');
    }
}