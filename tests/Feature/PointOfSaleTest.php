<?php

namespace Tests\Feature;

use Tests\TestCase;

class PointOfSaleTest extends TestCase
{
    public function test_pos_route_renders_service_picker_and_payment_summary(): void
    {
        $response = $this->get('/pos');

        $response->assertOk()
            ->assertViewIs('pos')
            ->assertSee('Point of Sale')
            ->assertSee('Maria Santos')
            ->assertSee('Swedish Massage')
            ->assertSee('Ventosa')
            ->assertSee('Happy Hour: 11:00 AM - 4:00 PM')
            ->assertSee('Order Summary')
            ->assertSee('E-cash')
            ->assertSee('Process Payment')
            ->assertSee('Print Receipt');
    }
}
