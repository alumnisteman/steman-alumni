<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use App\Jobs\LogActivity;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(\App\Services\AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin() {
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->dashboardUrl());
        }
        
        try {
            // Prevent regeneration if session already has an answer (fixes Service Worker/Double-fetch issues)
            if (!session()->has('captcha_answer')) {
                $num1 = rand(1, 10);
                $num2 = rand(1, 10);
                $ans = $num1 + $num2;
                
                session(['captcha_answer' => $ans]);
                session(['captcha_question' => "$num1 + $num2"]);
                
                // Immediate verification of session write
                if (session('captcha_answer') != $ans) {
                    Log::error('Session write failed in showLogin. Check session driver/permissions.');
                }
            }

            $captcha_question = session('captcha_question');

            // FIX MEDIUM: Jika session gagal (captcha_question null), tolak — jangan fallback ke '5 + 5'
            // Fallback hardcoded memungkinkan bot menebak jawaban captcha (selalu 10)
            if (!$captcha_question) {
                Log::error('Session captcha gagal ditulis di showLogin. Periksa driver/permission session.');
                return redirect()->route('login')->with('error', 'Sesi tidak tersedia. Silakan refresh halaman.');
            }

            return view('auth.login', compact('captcha_question'));
        } catch (\Exception $e) {
            Log::error('Critical Login View Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
    
    public function login(Request $request)
    {
        if ($request->filled('hp_field')) {
            Log::warning('Bot detected on login: ' . $request->ip());
            return back()->with('error', 'Akses ditolak.');
        }

        Log::info('Login attempt for: ' . $request->email);

        // --- Production Hardening: Anti-Brute Force Rate Limiting ---
        if (RateLimiter::tooManyAttempts('login:' . $request->ip(), 15)) {
            Log::warning('Login throttled for IP: ' . $request->ip());
            return back()->with('error', 'Terlalu banyak percobaan login. Silakan tunggu 60 detik.');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'captcha' => ['required', 'numeric', function ($attribute, $value, $fail) {
                $sessionValue = session('captcha_answer');
                if ($value != $sessionValue) {
                    $fail('Jawaban Captcha salah.');
                }
            }],
        ]);
        unset($credentials['captcha']);

        if ($this->authService->login($credentials)) {
            $user = Auth::user();
            $host = $request->getHost();
            $adminSubdomain = 'admin.' . parse_url(config('app.url'), PHP_URL_HOST);

            // Special Guard: Alumni cannot login via Admin Subdomain
            if ($host === $adminSubdomain && !in_array($user->role, ['admin', 'editor'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Alumni tidak diperbolehkan masuk melalui Panel Admin. Silakan gunakan portal utama.');
            }

            Log::info('Login success for: ' . $request->email);
            
            LogActivity::dispatch(
                Auth::id(),
                'Login',
                'User logged in to the system.',
                $request->ip(),
                $request->header('User-Agent')
            );

            $request->session()->regenerate();
            return redirect()->intended(Auth::user()->dashboardUrl());
        }

        LogActivity::dispatch(
            null,
            'Login Failed',
            'Failed login attempt for email: ' . $request->email,
            $request->ip(),
            $request->header('User-Agent')
        );

        Log::warning('Login failed for: ' . $request->email);
        // FIX HIGH: Gunakan key yang sama dengan tooManyAttempts() agar rate limiter bekerja
        RateLimiter::hit('login:' . $request->ip(), 60);
        return back()->withErrors(['email' => 'Kredensial tidak cocok.'])->withInput($request->only('email'));
    }

    public function showRegister() { 
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->dashboardUrl());
        }
        try {
            $majors = Major::orderBy('name')->get(); 
            
            if (!session()->has('captcha_answer')) {
                $num1 = rand(1, 10);
                $num2 = rand(1, 10);
                session(['captcha_answer' => $num1 + $num2]);
                session(['captcha_question' => "$num1 + $num2"]);
            }

            $captcha_question = session('captcha_question');

            // FIX MEDIUM: Tolak jika session captcha tidak tersedia — jangan fallback ke '5 + 5'
            if (!$captcha_question) {
                Log::error('Session captcha gagal ditulis di showRegister. Periksa driver/permission session.');
                return redirect()->route('register')->with('error', 'Sesi tidak tersedia. Silakan refresh halaman.');
            }

            return view('auth.register', compact('majors', 'captcha_question'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Register view error: ' . $e->getMessage());
            return redirect()->route('register')->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function register(Request $request)
    {
        if ($request->filled('hp_field')) {
            Log::warning('Bot detected on register: ' . $request->ip());
            return back()->with('error', 'Akses ditolak.');
        }

        Log::info('Register attempt: ' . $request->email);
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'nisn' => 'nullable|string',
                'graduation_year' => 'nullable|integer',
                'major' => 'nullable|string',
                'captcha' => ['required', 'numeric', function ($attribute, $value, $fail) {
                    if ($value != session('captcha_answer')) {
                        $fail('Jawaban Captcha salah.');
                    }
                }],
            ]);
            Log::info('Validation passed for: ' . $request->email);

            $user = $this->authService->register([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'alumni',
                'nisn' => $data['nisn'] ?? null,
                'graduation_year' => $data['graduation_year'] ?? null,
                'major' => $data['major'] ?? null,
            ]);
            Log::info('User created: ID ' . $user->id);

            LogActivity::dispatch(
                $user->id,
                'Register',
                'New alumni registered: ' . $user->name,
                $request->ip(),
                $request->header('User-Agent')
            );

            // Auto-follow batch mates for community engagement
            try { \App\Jobs\AutoFollowBatchMates::dispatch($user->id); } catch (\Throwable $e) { Log::warning('AutoFollowBatchMates dispatch failed: ' . $e->getMessage()); }

            Auth::login($user);
            return redirect()->intended($user->dashboardUrl());
        } catch (\Exception $e) {
            // FIX HIGH: Jangan bocorkan pesan exception ke user — log saja di server
            Log::error('Registration error: ' . $e->getMessage());
            return back()->with('error', 'Pendaftaran gagal karena masalah server. Silakan coba lagi atau hubungi admin.')->withInput();
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogActivity::dispatch(
                Auth::id(),
                'Logout',
                'User logged out.',
                $request->ip(),
                $request->header('User-Agent')
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function qrLogin(string $token)
    {
        $user = \App\Models\User::where('qr_login_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Token QR tidak valid atau sudah kadaluarsa.');
        }

        if ($user->qr_token_expires_at && $user->qr_token_expires_at->isPast()) {
            return redirect('/login')->with('error', 'Token QR sudah kadaluarsa. Silakan minta kartu QR baru.');
        }

        if (!$user->is_active) {
            return redirect('/login')->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi Admin.');
        }
        
        if ($user->status !== 'approved') {
            return redirect('/login')->with('error', 'Akun Anda belum disetujui atau sedang dalam peninjauan.');
        }

        // Perform Login
        \Illuminate\Support\Facades\Auth::login($user);

        \App\Jobs\LogActivity::dispatch(
            $user->id,
            'QR Login',
            'User logged in via Magic QR Card.',
            request()->ip(),
            request()->header('User-Agent')
        );

        \Illuminate\Support\Facades\Log::info('QR Login success for: ' . $user->email);

        request()->session()->regenerate();
        return redirect()->intended($user->dashboardUrl())->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }
}
