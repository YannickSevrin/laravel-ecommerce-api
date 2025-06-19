<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // List of used categories
        $categories = [
            'Clothing',
            'Childhood',
            'Self',
            'Share',
        ];

        // Create or retrieve categories
        $categoryMap = [];
        foreach ($categories as $name) {
            $category = Category::firstOrCreate(['name' => $name]);
            $categoryMap[$name] = $category->id;
        }

        // Products to insert
        $products = [
            [
                'name' => "The right to innovate more easily",
                'description' => "In discussion for a long time. Yellow season to lose.",
                'price' => 165.49,
                'category_id' => $categoryMap['Clothing'],
                'image' => 'https://placeimg.com/640/480/any',
            ],
            [
                'name' => "The comfort to innovate more easily",
                'description' => "Disappear everyone, extend happy sum.",
                'price' => 86.14,
                'category_id' => $categoryMap['Childhood'],
                'image' => 'https://dummyimage.com/640x480',
            ],
            [
                'name' => "The right to change more easily",
                'description' => "Call blood weapon. Late four wish prison.",
                'price' => 75.01,
                'category_id' => $categoryMap['Self'],
                'image' => 'https://www.lorempixel.com/640/480',
            ],
            [
                'name' => "The freedom to evolve in its purest form",
                'description' => "Fixed say path which also interrupts game.",
                'price' => 96.47,
                'category_id' => $categoryMap['Share'],
                'image' => 'https://placeimg.com/640/480/any',
            ],
            [
                'name' => "The art of achieving your goals in complete peace of mind",
                'description' => "Game noise knee trace class answer deserve.",
                'price' => 61.03,
                'category_id' => $categoryMap['Childhood'],
                'image' => 'https://dummyimage.com/640x480',
            ],
        ];

        foreach ($products as $data) {
            Product::create($data);
        }
    }
}
