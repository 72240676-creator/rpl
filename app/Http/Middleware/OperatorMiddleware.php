<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OperatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->peran === 'operator') {
            return $next($request);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Anda tidak memiliki akses ke halaman operator.');
    }
}
