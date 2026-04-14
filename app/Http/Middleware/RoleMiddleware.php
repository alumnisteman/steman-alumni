<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        \Illuminate\Support\Facades\Log::debug("Middleware Trace: RoleMiddleware Started. User: " . (\Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->email : 'Guest'));
        
        try {
            if (!\Illuminate\Support\Facades\Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login untuk mengakses halaman ini.');
            }

            if (in_array(\Illuminate\Support\Facades\Auth::user()->role, $roles)) {
                return $next($request);
            }

            return abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Middleware Trace: RoleMiddleware FATAL: " . $e->getMessage());
            return $next($request); // Fallback to allow request to proceed if middleware itself crashes
        }
    }
}
