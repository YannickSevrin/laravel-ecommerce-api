<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            foreach (['shipping', 'billing'] as $type) {
                Address::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'address' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'postal_code' => fake()->postcode(),
                    'country' => fake()->country(),
                ]);
            }
        }
    }
}
