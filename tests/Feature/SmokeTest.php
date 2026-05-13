<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    /**
     * Test if the homepage loads successfully.
     */
    public function test_homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $response.assertStatus(200);
    }

    /**
     * Test if the alumni directory is accessible to guests.
     */
    public function test_alumni_directory_is_accessible(): void
    {
        $response = $this->get('/alumni');
        $response.assertStatus(200);
    }

    /**
     * Test if the login page is accessible.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response.assertStatus(200);
    }

    /**
     * Test if the ranking page (risky polymorphic area) is accessible.
     */
    public function test_ranking_page_is_accessible(): void
    {
        $response = $this->get('/ranking');
        $response.assertStatus(200);
    }

    /**
     * Test if the mobile menu exists in the layout.
     */
    public function test_mobile_menu_exists_in_html(): void
    {
        $response = $this->get('/');
        $response.assertSee('id="mobileMenu"', false);
        $response.assertSee('data-bs-target="#mobileMenu"', false);
    }
}
