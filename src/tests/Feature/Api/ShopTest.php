<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\Category;
use Tests\TestCase;

class ShopTest extends TestCase
{
    public function test_can_get_products_list()
    {
        $category = $this->createCategory();
        $products = Product::factory(5)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'category_id',
                        'image',
                        'created_at',
                        'updated_at',
                        'category' => ['id', 'name']
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ],
                'links'
            ]);

        $this->assertEquals(5, $response->json('meta.total'));
    }

    public function test_can_get_single_product()
    {
        $product = $this->createProduct();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'category_id',
                    'image',
                    'created_at',
                    'updated_at',
                    'category' => ['id', 'name']
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_product()
    {
        $response = $this->getJson('/api/products/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }

    public function test_can_filter_products_by_category()
    {
        $category1 = $this->createCategory();
        $category2 = $this->createCategory();
        
        Product::factory(3)->create(['category_id' => $category1->id]);
        Product::factory(2)->create(['category_id' => $category2->id]);

        $response = $this->getJson("/api/products?category={$category1->id}");

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('meta.total'));
    }

    public function test_can_search_products_by_name()
    {
        $category = $this->createCategory();
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'iPhone 15'
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Samsung Galaxy'
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'iPhone 14'
        ]);

        $response = $this->getJson('/api/products?search=iPhone');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.total'));
    }

    public function test_can_sort_products_by_price()
    {
        $category = $this->createCategory();
        Product::factory()->create(['category_id' => $category->id, 'price' => 100]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 50]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 200]);

        // Sort ascending
        $response = $this->getJson('/api/products?sort=price&direction=asc');
        $response->assertStatus(200);
        $prices = collect($response->json('data'))->pluck('price');
        $this->assertEquals([50, 100, 200], $prices->toArray());

        // Sort descending
        $response = $this->getJson('/api/products?sort=price&direction=desc');
        $response->assertStatus(200);
        $prices = collect($response->json('data'))->pluck('price');
        $this->assertEquals([200, 100, 50], $prices->toArray());
    }

    public function test_can_get_categories_list()
    {
        $categories = Category::factory(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                        'products_count'
                    ]
                ]
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_categories_include_products_count()
    {
        $category = $this->createCategory();
        Product::factory(5)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
        $categoryData = collect($response->json('data'))->first();
        $this->assertEquals(5, $categoryData['products_count']);
    }

    public function test_products_pagination_works()
    {
        $category = $this->createCategory();
        Product::factory(25)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ],
                'links' => ['next', 'prev']
            ]);

        $this->assertEquals(25, $response->json('meta.total'));
        $this->assertEquals(12, $response->json('meta.per_page'));
        $this->assertEquals(3, $response->json('meta.last_page'));
    }

    public function test_can_filter_by_price_range()
    {
        $category = $this->createCategory();
        Product::factory()->create(['category_id' => $category->id, 'price' => 50]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 100]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 150]);
        Product::factory()->create(['category_id' => $category->id, 'price' => 200]);

        $response = $this->getJson('/api/products?min_price=75&max_price=175');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.total'));
        
        $prices = collect($response->json('data'))->pluck('price');
        $this->assertTrue($prices->every(fn($price) => $price >= 75 && $price <= 175));
    }
} 