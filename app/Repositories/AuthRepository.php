<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }
}
