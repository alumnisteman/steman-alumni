<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AlumniMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'alumni') {
                return $next($request);
            }
            return redirect()->route('login')->with('error', 'Silakan login sebagai Alumni.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AlumniMiddleware Error: ' . $e->getMessage());
            // Fail safe: redirect to login if DB/Auth fails
            return redirect()->route('login')->with('error', 'Sistem sedang sibuk, silakan coba lagi nanti.');
        }
    }
}
