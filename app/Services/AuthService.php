<?php

namespace App\Services;

use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        $data['status'] = 'pending'; // Require admin approval
        return $this->authRepository->create($data);
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
