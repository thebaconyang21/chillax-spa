<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    public function test_reports_route_renders_report_tabs_and_tables(): void
    {
        $response = $this->actingAs((new User)->forceFill(['name' => 'Administrator', 'username' => 'admin', 'role' => 'admin']))->get('/reports');

        $response->assertOk()
            ->assertViewIs('reports')
            ->assertSee('Reports')
            ->assertSee('Export')
            ->assertSee('Daily')
            ->assertSee('Weekly')
            ->assertSee('Monthly')
            ->assertSee('Yearly')
            ->assertSee('Total Sales')
            ->assertSee('Total Therapist Commission')
            ->assertSee('Net Income for the Spa')
            ->assertSee('Top Services')
            ->assertSee('Therapist Commission')
            ->assertSee('sales-chart');
    }
}
