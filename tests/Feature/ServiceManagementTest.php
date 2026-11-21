<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    public function test_services_route_renders_service_pricing_and_addons(): void
    {
        $response = $this->actingAs((new User)->forceFill(['name' => 'Administrator', 'username' => 'admin', 'role' => 'admin']))->get('/services');

        $response->assertOk()
            ->assertViewIs('services')
            ->assertSee('Service Management')
            ->assertSee('Swedish Massage')
            ->assertSee('Therapeutic Massage')
            ->assertSee('Happy Hour:')
            ->assertSee('11:00 AM - 4:00 PM')
            ->assertSee('Ventosa')
            ->assertSee('30 Minutes Massage')
            ->assertSee('Add Service');
    }
}
