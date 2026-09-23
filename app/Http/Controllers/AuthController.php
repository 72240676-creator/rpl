<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->status_akun === 'nonaktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda sedang dinonaktifkan.',
                ])->onlyInput('email');
            }

            if (Auth::user()->peran === 'admin') {
                return redirect()->route('admin.stations.index');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'nomor_telepon' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'password' => Hash::make($validated['password']),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}