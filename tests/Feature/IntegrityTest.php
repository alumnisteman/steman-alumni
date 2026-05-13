<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Session;

class IntegrityTest extends TestCase
{
    /**
     * Test that the login page is accessible and captcha persists.
     */
    public function test_login_page_integrity(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Keamanan'); // Check for Captcha label

        // Get the captcha answer from session
        $ans1 = session('captcha_answer');
        $this->assertNotNull($ans1, 'Captcha answer should be set in session');

        // Hit the login page again (simulating double-fetch or refresh)
        $response2 = $this->get('/login');
        $response2->assertStatus(200);
        
        $ans2 = session('captcha_answer');
        $this->assertEquals($ans1, $ans2, 'Captcha answer should NOT change on subsequent GET requests');
    }

    /**
     * Test that critical public assets are accessible.
     */
    public function test_public_assets_accessibility(): void
    {
        // Check if common folders in storage are redirecting/accessible correctly
        // Note: we can't easily check actual files without knowing their names,
        // but we can check if the /storage/ base is not 403 Forbidden.
        $response = $this->get('/storage/');
        // It might be 404 (if directory listing is off) but should NOT be 403
        $this->assertNotEquals(403, $response->getStatusCode(), 'Storage directory should not be Forbidden');
    }

    /**
     * Test database connectivity.
     */
    public function test_database_integrity(): void
    {
        $this->assertTrue(\App\Models\User::exists(), 'User table should have data');
    }
}
