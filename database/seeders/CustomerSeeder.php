<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['customer_name' => 'Maria Santos', 'contact_number' => '09171234567'],
            ['customer_name' => 'John Rivera', 'contact_number' => '09175550142'],
            ['customer_name' => 'Angela Cruz', 'contact_number' => '09204447621'],
            ['customer_name' => 'Patricia Gomez', 'contact_number' => '09163339081'],
            ['customer_name' => 'Miguel Bautista', 'contact_number' => '09178881234'],
        ] as $customer) {
            Customer::updateOrCreate(
                ['customer_name' => $customer['customer_name']],
                $customer,
            );
        }
    }
}
