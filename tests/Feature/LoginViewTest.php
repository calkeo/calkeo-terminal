<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginViewTest extends TestCase
{
    /**
     * Test that the login view displays the correct app name and version.
     *
     * @return void
     */
    public function test_login_view_displays_version_and_name()
    {
        // Test the login route directly
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee(config('app.name'));
        $response->assertSee(config('app.version'));
    }
}
