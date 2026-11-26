<?php

namespace Database\Seeders;

use App\Models\Therapist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TherapistScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $firstScheduleDate = Carbon::today();

        Therapist::query()->each(function (Therapist $therapist) use ($firstScheduleDate): void {
            for ($day = 0; $day < 14; $day++) {
                $therapist->schedules()->firstOrCreate(
                    ['schedule_date' => $firstScheduleDate->copy()->addDays($day)->toDateString()],
                    ['start_time' => '11:00:00', 'end_time' => '23:00:00'],
                );
            }
        });
    }
}
