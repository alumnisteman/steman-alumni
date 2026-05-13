<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // 1. Kick out if deactivated
            if (!$user->is_active || $user->status !== 'approved') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/login')->with('error', 'Akun Anda dinonaktifkan atau belum disetujui. Silakan hubungi Admin.');
            }

            // 2. Update last_active_at (Throttled to every 5 minutes to avoid DB overhead)
            if (!$user->last_active_at || \Carbon\Carbon::parse($user->last_active_at)->diffInMinutes(now()) >= 5) {
                $user->update(['last_active_at' => now()]);
            }
        }

        return $next($request);
    }
}
