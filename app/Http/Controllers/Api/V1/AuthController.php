<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Resources\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        // FIX HIGH: Rate limiting untuk API login (cegah brute force)
        $rateLimitKey = 'api_login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            return response()->json([
                'message' => 'Terlalu banyak percobaan login. Silakan tunggu beberapa saat.'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($this->authService->login($request->only('email', 'password'))) {
            $user = Auth::user();

            // FIX CRITICAL: Hapus hardcoded fallback token — jika Sanctum tidak tersedia,
            // kembalikan error yang jelas daripada token palsu yang bisa disalahgunakan
            if (!method_exists($user, 'createToken')) {
                return response()->json([
                    'message' => 'Server tidak mendukung token otentikasi. Hubungi administrator.'
                ], 503);
            }

            $token = $this->authService->generateApiToken($user);

            RateLimiter::clear($rateLimitKey);

            return new AuthResource([
                'user'  => $user,
                'token' => $token,
            ]);
        }

        RateLimiter::hit($rateLimitKey, 60);
        return response()->json(['message' => 'Kredensial tidak valid.'], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            // FIX MEDIUM: Password minimal 8 karakter sesuai standar keamanan
            'password'              => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data           = $request->only('name', 'email', 'password');
        $data['role']   = 'alumni';

        $user = $this->authService->register($data);

        // FIX CRITICAL: Tidak ada fallback token palsu
        if (!method_exists($user, 'createToken')) {
            return response()->json([
                'message' => 'Akun berhasil dibuat, namun server tidak mendukung token otentikasi. Hubungi administrator.'
            ], 503);
        }

        $token = $this->authService->generateApiToken($user);

        return new AuthResource([
            'user'  => $user,
            'token' => $token,
        ]);
    }
}
