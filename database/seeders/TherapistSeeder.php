<?php

namespace Database\Seeders;

use App\Models\Therapist;
use Illuminate\Database\Seeder;

class TherapistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range('A', 'H') as $letter) {
            Therapist::updateOrCreate(
                ['therapist_name' => "Therapist {$letter}"],
                [
                    'contact_number' => '09'.str_pad((string) ord($letter), 9, '0', STR_PAD_LEFT),
                    'availability_status' => 'available',
                ],
            );
        }
    }
}
