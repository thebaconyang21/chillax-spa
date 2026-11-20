<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    public function test_customers_route_renders_profiles_and_history_panel(): void
    {
        $response = $this->get('/customers');

        $response->assertOk()
            ->assertViewIs('customers')
            ->assertSee('Customer Management')
            ->assertSee('Maria Santos')
            ->assertSee('0917 123 4567')
            ->assertSee('Returning Customer')
            ->assertSee('New Customer')
            ->assertSee('Appointment History')
            ->assertSee('Swedish Massage (60 mins)')
            ->assertSee('Add Customer');
    }
}
