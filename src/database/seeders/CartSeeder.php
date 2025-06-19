<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        foreach ($users as $user) {
            foreach (range(1, rand(1, 3)) as $_) {
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $products->random()->id,
                    'quantity' => rand(1, 4),
                ]);
            }
        }
    }
}
