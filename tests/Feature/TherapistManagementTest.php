<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class TherapistManagementTest extends TestCase
{
    public function test_therapists_route_renders_directory_and_commission_details(): void
    {
        $response = $this->actingAs((new User)->forceFill(['name' => 'Administrator', 'username' => 'admin', 'role' => 'admin']))->get('/therapists');

        $response->assertOk()
            ->assertViewIs('therapists')
            ->assertSee('Therapist & Commission')
            ->assertSee('Therapist A')
            ->assertSee('Therapist H')
            ->assertSee('Total Commission This Week')
            ->assertSee('Available')
            ->assertSee('In Service')
            ->assertSee('Off Duty')
            ->assertSee('Schedule')
            ->assertSee('Completed Services')
            ->assertSee('Add Therapist');
    }
}
