<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk mengakses halaman ini.');
        }

        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // SECURITY: Never fall through to $next() on failure — always deny
        return abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
    }
}
