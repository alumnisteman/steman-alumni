<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthRedirectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an admin can access the alumni directory.
     */
    public function test_admin_can_access_alumni_directory(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get('/alumni');

        $response->assertStatus(200);
        $response->assertViewIs('alumni.index');
    }

    /**
     * Test that an admin can access the alumni network map.
     */
    public function test_admin_can_access_alumni_network(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get('/alumni/network');

        $response->assertStatus(200);
        $response->assertViewIs('network.index');
    }

    /**
     * Test that an authenticated user is redirected away from the login page.
     */
    public function test_authenticated_user_redirected_from_login(): void
    {
        $user = User::factory()->create([
            'role' => 'alumni',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->get('/login');

        // Should redirect to alumni dashboard
        $response->assertRedirect('/alumni/dashboard');
    }

    /**
     * Test that an authenticated admin is redirected away from the login page.
     */
    public function test_authenticated_admin_redirected_from_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get('/login');

        // Should redirect to admin dashboard
        $response->assertRedirect('/admin/dashboard');
    }
}
