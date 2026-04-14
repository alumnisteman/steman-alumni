<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in → go to login
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Admin.');
        }

        $user = auth()->user();

        // Editor role → allowed into admin panel (content management only)
        if ($user->role === 'editor') {
            return $next($request);
        }

        // Admin role → allowed into admin panel
        if ($user->role === 'admin') {
            return $next($request);
        }

        return abort(403, 'Akses ditolak.');
    }
}
