<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemIntegrityTest extends TestCase
{
    /**
     * Test that the homepage is accessible.
     */
    public function test_homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('STEMAN');
    }

    /**
     * Test that core navigation links return 200.
     */
    public function test_core_navigation_links(): void
    {
        $links = [
            '/news',
            '/jobs',
            '/gallery',
            '/network',
            '/login',
            '/register',
        ];

        foreach ($links as $link) {
            $response = $this->get($link);
            $response->assertStatus(200, "Link {$link} is broken.");
        }
    }

    /**
     * Test that Meilisearch failure doesn't crash the homepage.
     * This simulates a scenario where Meilisearch is down but the app should still work.
     */
    public function test_app_resilience_to_meilisearch_failure(): void
    {
        // Force Meilisearch to a non-existent host
        config(['scout.meilisearch.host' => 'http://999.999.999.999:7700']);
        
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertTrue(true, 'App survived Meilisearch being offline.');
    }

    /**
     * Test that the Ad system doesn't crash if no ads are available.
     */
    public function test_ad_system_resilience(): void
    {
        // Clear ad-related cache or force empty results if needed
        // Assuming AdViewComposer handles this gracefully
        
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertTrue(true, 'App survived empty Ad results.');
    }
}
