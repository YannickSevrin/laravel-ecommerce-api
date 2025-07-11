<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->pluck('id');

        foreach (range(1, 10) as $i) {
            Order::create([
                'user_id' => $customers->random(),
                'total' => fake()->randomFloat(2, 20, 300),
                'status' => fake()->randomElement(['pending', 'paid', 'shipped']),
                'created_at' => now()->subDays(rand(1, 365)),
            ]);
        }
    }
}
