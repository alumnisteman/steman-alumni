<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AlumniMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Explicit role checking for Alumni features
        if (\Illuminate\Support\Facades\Auth::check() && in_array(\Illuminate\Support\Facades\Auth::user()->role, ['alumni', 'admin', 'editor'])) {
            return $next($request);
        }

        // Redirect to login only if auth/role fails
        return redirect()->route('login')->with('error', 'Akses terbatas. Silakan login sebagai Alumni.');
    }
}
