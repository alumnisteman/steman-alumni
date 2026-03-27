<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            if (!\Illuminate\Support\Facades\Auth::check()) {
                return redirect()->route('login');
            }

            if (in_array(\Illuminate\Support\Facades\Auth::user()->role, $roles)) {
                return $next($request);
            }

            return redirect()->route('login')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('RoleMiddleware Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Sistem sedang sibuk. Silakan coba lagi.');
        }
    }
}
