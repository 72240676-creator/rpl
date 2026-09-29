<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; 

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan perannya adalah admin menggunakan Facade Auth
        if (Auth::check() && Auth::user()->peran === 'admin') {
            return $next($request);
        }

        // Jika bukan admin, kembalikan ke halaman awal dengan pesan error
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman Admin.');
    }
}