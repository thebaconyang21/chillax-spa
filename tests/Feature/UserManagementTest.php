<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_users_route_renders_management_filters_and_modals(): void
    {
        $response = $this->actingAs((new User)->forceFill(['name' => 'Administrator', 'username' => 'admin', 'role' => 'admin']))->get('/users');

        $response->assertOk()
            ->assertViewIs('users')
            ->assertSee('User Management')
            ->assertSee('Add User')
            ->assertSee('Administrator')
            ->assertSee('Cashier-Staff')
            ->assertSee('Jane Dela Cruz')
            ->assertSee('Full Name')
            ->assertSee('Confirm Password')
            ->assertSee('Delete User');
    }
}
