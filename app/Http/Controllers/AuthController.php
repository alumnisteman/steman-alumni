<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Major;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(\App\Services\AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin() {
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
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Login',
                'description' => 'User logged in to the system.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            $request->session()->regenerate();
            if (Auth::user()->role === 'admin') return redirect()->intended('/admin/dashboard');
            return redirect()->intended('/alumni/dashboard');
        }

        ActivityLog::create([
            'user_id' => null,
            'action' => 'Login Failed',
            'description' => 'Failed login attempt for email: ' . $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        Log::warning('Login failed for: ' . $request->email);
        return back()->withErrors(['email' => 'Kredensial tidak cocok.'])->withInput($request->only('email'));
    }

    public function showRegister() { 
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
                'tahun_lulus' => 'nullable|integer',
                'jurusan' => 'nullable|string',
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
                'tahun_lulus' => $data['tahun_lulus'] ?? null,
                'jurusan' => $data['jurusan'] ?? null,
            ]);
            Log::info('User created: ID ' . $user->id);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Register',
                'description' => 'New alumni registered: ' . $user->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            Auth::login($user);
            return redirect('/alumni/dashboard');
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage())->withInput();
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Logout',
                'description' => 'User logged out.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
