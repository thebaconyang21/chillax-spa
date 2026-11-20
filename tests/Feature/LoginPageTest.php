<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_login_route_renders_standalone_login_form(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('Chillax Spa')
            ->assertSee('Username')
            ->assertSee('Password')
            ->assertSee('Log In')
            ->assertDontSee('Main navigation');
    }
}
