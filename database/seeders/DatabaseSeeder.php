<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@chillaxspa.test'],
            [
                'name' => 'Spa Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
        );

        User::updateOrCreate(
            ['email' => 'cashier@chillaxspa.test'],
            [
                'name' => 'Jane Dela Cruz',
                'username' => 'cashier',
                'password' => Hash::make('cashier123'),
                'role' => 'cashier',
            ],
        );

        $this->call([
            ServiceSeeder::class,
            AddonSeeder::class,
            TherapistSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
