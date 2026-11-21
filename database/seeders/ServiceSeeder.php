<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['service_name' => 'Swedish Massage', 'duration' => 60, 'happy_hour_price' => 300, 'regular_price' => 350],
            ['service_name' => 'Therapeutic Massage', 'duration' => 90, 'happy_hour_price' => null, 'regular_price' => 500],
            ['service_name' => 'Foot Massage', 'duration' => 60, 'happy_hour_price' => null, 'regular_price' => 400],
            ['service_name' => 'Combo (Swedish Massage + 1 Add-on)', 'duration' => 60, 'happy_hour_price' => 400, 'regular_price' => null],
        ] as $service) {
            Service::updateOrCreate(
                ['service_name' => $service['service_name']],
                $service,
            );
        }
    }
}
