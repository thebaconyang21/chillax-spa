<?php

namespace Tests\Feature;

use App\Models\Therapist;
use Database\Seeders\TherapistScheduleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TherapistScheduleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_fourteen_daily_schedules_for_each_therapist_and_is_idempotent(): void
    {
        $this->travelTo('2026-11-27 08:00:00');
        $therapists = [
            Therapist::create(['therapist_name' => 'Therapist One']),
            Therapist::create(['therapist_name' => 'Therapist Two']),
        ];
        $seeder = app(TherapistScheduleSeeder::class);

        $seeder->run();
        $seeder->run();

        $this->assertDatabaseCount('therapist_schedules', 28);
        $this->assertDatabaseHas('therapist_schedules', [
            'therapist_id' => $therapists[0]->id,
            'schedule_date' => '2026-11-27',
            'start_time' => '11:00:00',
            'end_time' => '23:00:00',
        ]);
        $this->assertDatabaseHas('therapist_schedules', [
            'therapist_id' => $therapists[1]->id,
            'schedule_date' => '2026-12-10',
            'start_time' => '11:00:00',
            'end_time' => '23:00:00',
        ]);
    }
}
