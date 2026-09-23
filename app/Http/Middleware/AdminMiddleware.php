<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan perannya adalah admin
        if (auth()->check() && auth()->user()->peran === 'admin') {
            return $next($request);
        }

        // Jika bukan admin, kembalikan ke halaman awal dengan pesan error
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman Admin.');
    }
}