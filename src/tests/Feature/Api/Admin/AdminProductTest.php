<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Product;
use App\Models\Category;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminProductTest extends TestCase
{
    public function test_admin_can_get_products_list()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();
        Product::factory(5)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/admin/products', $this->getAuthHeaders($admin));

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
                'meta',
                'links'
            ]);
    }

    public function test_customer_cannot_access_admin_products()
    {
        $customer = $this->createUser('customer');

        $response = $this->getJson('/api/admin/products', $this->getAuthHeaders($customer));

        $response->assertStatus(403)
            ->assertJson(['message' => 'Access denied. Admin role required.']);
    }

    public function test_admin_can_create_product()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $category->id
        ];

        $response = $this->postJson('/api/admin/products', $productData, $this->getAuthHeaders($admin));

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Product created successfully',
                'data' => [
                    'name' => 'Test Product',
                    'description' => 'Test Description',
                    'price' => 99.99,
                    'category_id' => $category->id
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'category_id' => $category->id
        ]);
    }

    public function test_admin_can_create_product_with_image()
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $category = $this->createCategory();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg')
        ];

        $response = $this->postJson('/api/admin/products', $productData, $this->getAuthHeaders($admin));

        $response->assertStatus(201);

        $product = Product::where('name', 'Test Product')->first();
        $this->assertNotNull($product->image);
        
        // Check that file was stored
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_product_creation_validates_required_fields()
    {
        $admin = $this->createAdmin();

        $response = $this->postJson('/api/admin/products', [], $this->getAuthHeaders($admin));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'price', 'category_id']);
    }

    public function test_product_creation_validates_price_format()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 'invalid-price',
            'category_id' => $category->id
        ];

        $response = $this->postJson('/api/admin/products', $productData, $this->getAuthHeaders($admin));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_product_creation_validates_category_exists()
    {
        $admin = $this->createAdmin();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => 999 // Non-existent category
        ];

        $response = $this->postJson('/api/admin/products', $productData, $this->getAuthHeaders($admin));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

    public function test_admin_can_get_single_product()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();

        $response = $this->getJson("/api/admin/products/{$product->id}", $this->getAuthHeaders($admin));

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

    public function test_admin_can_update_product()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();
        $newCategory = $this->createCategory();

        $updateData = [
            'name' => 'Updated Product Name',
            'description' => 'Updated Description',
            'price' => 149.99,
            'category_id' => $newCategory->id
        ];

        $response = $this->putJson("/api/admin/products/{$product->id}", $updateData, $this->getAuthHeaders($admin));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product updated successfully',
                'data' => [
                    'name' => 'Updated Product Name',
                    'price' => 149.99,
                    'category_id' => $newCategory->id
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 149.99
        ]);
    }

    public function test_admin_can_update_product_image()
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $product = $this->createProduct(['image' => 'old-image.jpg']);

        $updateData = [
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'category_id' => $product->category_id,
            'image' => UploadedFile::fake()->image('new-product.jpg')
        ];

        $response = $this->putJson("/api/admin/products/{$product->id}", $updateData, $this->getAuthHeaders($admin));

        $response->assertStatus(200);

        $product->refresh();
        $this->assertNotEquals('old-image.jpg', $product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_admin_can_delete_product()
    {
        $admin = $this->createAdmin();
        $product = $this->createProduct();

        $response = $this->deleteJson("/api/admin/products/{$product->id}", [], $this->getAuthHeaders($admin));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_deleting_product_removes_image_file()
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        
        // Create a product with an image
        $category = $this->createCategory();
        $imagePath = 'products/test-image.jpg';
        Storage::disk('public')->put($imagePath, 'fake-image-content');
        
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'image' => $imagePath
        ]);

        $response = $this->deleteJson("/api/admin/products/{$product->id}", [], $this->getAuthHeaders($admin));

        $response->assertStatus(200);
        
        // Check that the image file was deleted
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_admin_cannot_delete_non_existent_product()
    {
        $admin = $this->createAdmin();

        $response = $this->deleteJson('/api/admin/products/999', [], $this->getAuthHeaders($admin));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }

    public function test_admin_products_are_paginated()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();
        Product::factory(25)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/admin/products', $this->getAuthHeaders($admin));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertEquals(25, $response->json('meta.total'));
    }

    public function test_admin_can_filter_products_by_category()
    {
        $admin = $this->createAdmin();
        $category1 = $this->createCategory();
        $category2 = $this->createCategory();
        
        Product::factory(3)->create(['category_id' => $category1->id]);
        Product::factory(2)->create(['category_id' => $category2->id]);

        $response = $this->getJson("/api/admin/products?category={$category1->id}", $this->getAuthHeaders($admin));

        $response->assertStatus(200);
        $this->assertEquals(3, $response->json('meta.total'));
    }

    public function test_admin_can_search_products()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();
        
        Product::factory()->create(['category_id' => $category->id, 'name' => 'iPhone 15']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Samsung Galaxy']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'iPhone 14']);

        $response = $this->getJson('/api/admin/products?search=iPhone', $this->getAuthHeaders($admin));

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.total'));
    }

    public function test_image_validation_accepts_valid_formats()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();

        $validFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        foreach ($validFormats as $format) {
            Storage::fake('public');
            
            $productData = [
                'name' => "Test Product {$format}",
                'description' => 'Test Description',
                'price' => 99.99,
                'category_id' => $category->id,
                'image' => UploadedFile::fake()->image("product.{$format}")
            ];

            $response = $this->postJson('/api/admin/products', $productData, $this->getAuthHeaders($admin));

            $response->assertStatus(201, "Failed to upload {$format} format");
        }
    }

    public function test_image_validation_rejects_invalid_files()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->create('document.pdf', 1000)
        ];

        $response = $this->postJson('/api/admin/products', $productData, $this->getAuthHeaders($admin));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }
} 