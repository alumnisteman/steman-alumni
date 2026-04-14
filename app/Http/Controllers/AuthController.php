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
            $num1 = rand(1, 10);
            $num2 = rand(1, 10);
            session(['captcha_answer' => $num1 + $num2]);
            $captcha_question = "$num1 + $num2";
            return view('auth.login', compact('captcha_question'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Login view error: ' . $e->getMessage());
            return view('auth.login', ['captcha_question' => '5 + 5']); // Fallback
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
                if ($value != session('captcha_answer')) {
                    $fail('Jawaban Captcha salah.');
                }
            }],
        ]);
        unset($credentials['captcha']);

        if ($this->authService->login($credentials)) {
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
        RateLimiter::hit($request->ip(), 60);
        return back()->withErrors(['email' => 'Kredensial tidak cocok.'])->withInput($request->only('email'));
    }

    public function showRegister() { 
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->dashboardUrl());
        }
        try {
            $majors = Major::orderBy('name')->get(); 
            $num1 = rand(1, 10);
            $num2 = rand(1, 10);
            session(['captcha_answer' => $num1 + $num2]);
            $captcha_question = "$num1 + $num2";
            return view('auth.register', compact('majors', 'captcha_question')); 
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Register view error: ' . $e->getMessage());
            return view('auth.register', [
                'majors' => collect(), 
                'captcha_question' => '5 + 5'
            ]);
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
                'password' => 'required|string|min:4|confirmed',
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

            Auth::login($user);
            return redirect()->intended($user->dashboardUrl());
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage())->withInput();
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
}
