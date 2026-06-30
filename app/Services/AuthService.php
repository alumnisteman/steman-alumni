<?php

namespace App\Services;

use App\Mail\AdminNewUserMail;
use App\Mail\WelcomeRegisterMail;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function login(array $credentials, bool $remember = false): bool
    {
        $credentials['is_active'] = true;
        $credentials['status'] = 'approved';
        return Auth::attempt($credentials, $remember);
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['status'] = 'approved';
        $data['is_active'] = true;
        $data['auto_approved'] = true;
        $user = $this->authRepository->create($data);

        try {
            Mail::to($user->email)->send(new WelcomeRegisterMail($user));
        } catch (\Throwable $e) {
            Log::warning('Welcome email gagal dikirim ke ' . $user->email . ': ' . $e->getMessage());
        }

        try {
            $adminEmails = \App\Models\User::where('role', 'admin')
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new AdminNewUserMail($user));
            }
        } catch (\Throwable $e) {
            Log::warning('Admin new-user notification gagal: ' . $e->getMessage());
        }

        return $user;
    }

    /**
     * Generate an API Token for the user using Sanctum.
     *
     * @param \App\Models\User $user
     * @param string $tokenName
     * @return string
     */
    public function generateApiToken($user, $tokenName = 'auth_token')
    {
        // Require Laravel Sanctum usage
        return $user->createToken($tokenName)->plainTextToken;
    }
}
