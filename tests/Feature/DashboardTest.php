<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_dashboard_route_renders_overview_appointments_availability_and_actions(): void
    {
        $response = $this->actingAs((new User)->forceFill(['name' => 'Cashier', 'username' => 'cashier', 'role' => 'cashier']))->get('/dashboard');

        $response->assertOk()
            ->assertViewIs('dashboard')
            ->assertSee('Welcome back, Jane Dela Cruz')
            ->assertSee("Today's Sales")
            ->assertSee("Today's Appointments")
            ->assertSee('Available Therapists')
            ->assertSee('Pending Appointments')
            ->assertSee('Upcoming Appointments')
            ->assertSee('Therapist Availability')
            ->assertSee('Weekly Sales')
            ->assertSee('New Appointment')
            ->assertSee('Open POS')
            ->assertSee('Add Customer');
    }
}
