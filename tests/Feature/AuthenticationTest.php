<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_with_username_and_password(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_returns_error_and_preserves_username(): void
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'cashier',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors([
                'username' => 'Invalid username or password.',
            ])
            ->assertSessionHasInput('username', 'cashier');
    }

    public function test_cashier_is_forbidden_from_admin_only_page(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this->actingAs($cashier)->get('/reports');

        $response->assertForbidden();
        $response->assertSee('Access Denied');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
