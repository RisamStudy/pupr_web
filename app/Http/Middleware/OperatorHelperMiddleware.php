<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OperatorHelperMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah pengguna adalah operator atau helper
        if (Auth::check() && (Auth::user()->isOperator() || Auth::user()->isHelper())) {
            return $next($request);
        }

        // Jika bukan admin, redirect ke halaman yang sesuai
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
