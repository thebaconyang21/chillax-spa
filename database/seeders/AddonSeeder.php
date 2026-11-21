<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['addon_name' => 'Ventosa', 'price' => 150],
            ['addon_name' => 'Hot Stone', 'price' => 150],
            ['addon_name' => 'Ear Candle', 'price' => 150],
            ['addon_name' => '30 Minutes Massage', 'price' => 200],
        ] as $addon) {
            Addon::updateOrCreate(
                ['addon_name' => $addon['addon_name']],
                $addon,
            );
        }
    }
}
