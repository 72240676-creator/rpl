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
}