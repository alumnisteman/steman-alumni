<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AuthService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthServiceTest extends TestCase
{
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = app(AuthService::class);
    }

    public function test_register_creates_user_with_pending_status()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->authService->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('pending', $user->status);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_login_returns_false_for_invalid_credentials()
    {
        $result = $this->authService->login([
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertFalse($result);
    }

    public function test_login_returns_false_for_inactive_user()
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
            'status' => 'approved',
        ]);

        $result = $this->authService->login([
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $this->assertFalse($result);
    }

    public function test_login_returns_false_for_unapproved_user()
    {
        $user = User::factory()->create([
            'email' => 'unapproved@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'status' => 'pending',
        ]);

        $result = $this->authService->login([
            'email' => 'unapproved@example.com',
            'password' => 'password123',
        ]);

        $this->assertFalse($result);
    }

    public function test_login_returns_true_for_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'valid@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'status' => 'approved',
        ]);

        $result = $this->authService->login([
            'email' => 'valid@example.com',
            'password' => 'password123',
        ]);

        $this->assertTrue($result);
    }
}
