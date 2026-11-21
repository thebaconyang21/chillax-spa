<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_users_route_renders_management_filters_and_modals(): void
    {
        $response = $this->get('/users');

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
