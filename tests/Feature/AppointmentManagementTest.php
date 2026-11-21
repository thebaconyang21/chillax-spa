<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AppointmentManagementTest extends TestCase
{
    public function test_appointment_route_renders_management_page_with_sample_details(): void
    {
        $response = $this->actingAs((new User)->forceFill(['name' => 'Cashier', 'username' => 'cashier', 'role' => 'cashier']))->get('/appointments');

        $response->assertOk()
            ->assertViewIs('appointments')
            ->assertSee('Appointment Management')
            ->assertSee('Maria Santos')
            ->assertSee('0917 123 4567')
            ->assertSee('In Service')
            ->assertSee('Proceed to POS')
            ->assertSee('Upcoming Reminder');
    }
}
