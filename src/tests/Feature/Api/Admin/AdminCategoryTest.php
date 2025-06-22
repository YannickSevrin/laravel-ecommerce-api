<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Category;
use App\Models\Product;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    public function test_admin_can_get_categories_list()
    {
        $admin = $this->createAdmin();
        Category::factory(5)->create();

        $response = $this->getJson('/api/admin/categories', $this->getAuthHeaders($admin));

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
                ],
                'meta',
                'links'
            ]);
    }

    public function test_customer_cannot_access_admin_categories()
    {
        $customer = $this->createUser('customer');

        $response = $this->getJson('/api/admin/categories', $this->getAuthHeaders($customer));

        $response->assertStatus(403)
            ->assertJson(['message' => 'Access denied. Admin role required.']);
    }

    public function test_admin_can_create_category()
    {
        $admin = $this->createAdmin();

        $categoryData = [
            'name' => 'Electronics'
        ];

        $response = $this->postJson('/api/admin/categories', $categoryData, $this->getAuthHeaders($admin));

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Category created successfully',
                'data' => [
                    'name' => 'Electronics'
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics'
        ]);
    }

    public function test_category_creation_validates_required_fields()
    {
        $admin = $this->createAdmin();

        $response = $this->postJson('/api/admin/categories', [], $this->getAuthHeaders($admin));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_category_creation_validates_unique_name()
    {
        $admin = $this->createAdmin();
        Category::factory()->create(['name' => 'Electronics']);

        $categoryData = [
            'name' => 'Electronics' // Duplicate name
        ];

        $response = $this->postJson('/api/admin/categories', $categoryData, $this->getAuthHeaders($admin));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_admin_can_get_single_category()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();

        $response = $this->getJson("/api/admin/categories/{$category->id}", $this->getAuthHeaders($admin));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                    'products_count'
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name
                ]
            ]);
    }

    public function test_admin_can_update_category()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory(['name' => 'Old Name']);

        $updateData = [
            'name' => 'Updated Category Name'
        ];

        $response = $this->putJson("/api/admin/categories/{$category->id}", $updateData, $this->getAuthHeaders($admin));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category updated successfully',
                'data' => [
                    'name' => 'Updated Category Name'
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name'
        ]);
    }

    public function test_category_update_validates_unique_name()
    {
        $admin = $this->createAdmin();
        $category1 = $this->createCategory(['name' => 'Electronics']);
        $category2 = $this->createCategory(['name' => 'Clothing']);

        $updateData = [
            'name' => 'Electronics' // Already exists
        ];

        $response = $this->putJson("/api/admin/categories/{$category2->id}", $updateData, $this->getAuthHeaders($admin));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_category_update_allows_same_name()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory(['name' => 'Electronics']);

        $updateData = [
            'name' => 'Electronics' // Same name
        ];

        $response = $this->putJson("/api/admin/categories/{$category->id}", $updateData, $this->getAuthHeaders($admin));

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_category()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();

        $response = $this->deleteJson("/api/admin/categories/{$category->id}", [], $this->getAuthHeaders($admin));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Category deleted successfully']);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_products()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->deleteJson("/api/admin/categories/{$category->id}", [], $this->getAuthHeaders($admin));

        $response->assertStatus(400)
            ->assertJson(['message' => 'Cannot delete category with products']);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_non_existent_category()
    {
        $admin = $this->createAdmin();

        $response = $this->deleteJson('/api/admin/categories/999', [], $this->getAuthHeaders($admin));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Category not found']);
    }

    public function test_categories_include_products_count()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();
        Product::factory(3)->create(['category_id' => $category->id]);

        $response = $this->getJson("/api/admin/categories/{$category->id}", $this->getAuthHeaders($admin));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'products_count' => 3
                ]
            ]);
    }

    public function test_categories_list_is_paginated()
    {
        $admin = $this->createAdmin();
        Category::factory(25)->create();

        $response = $this->getJson('/api/admin/categories', $this->getAuthHeaders($admin));

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

    public function test_admin_can_search_categories()
    {
        $admin = $this->createAdmin();
        Category::factory()->create(['name' => 'Electronics']);
        Category::factory()->create(['name' => 'Electronic Accessories']);
        Category::factory()->create(['name' => 'Clothing']);

        $response = $this->getJson('/api/admin/categories?search=Electronic', $this->getAuthHeaders($admin));

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.total'));
    }

    public function test_categories_are_ordered_by_name()
    {
        $admin = $this->createAdmin();
        Category::factory()->create(['name' => 'Zebra']);
        Category::factory()->create(['name' => 'Alpha']);
        Category::factory()->create(['name' => 'Beta']);

        $response = $this->getJson('/api/admin/categories', $this->getAuthHeaders($admin));

        $response->assertStatus(200);
        
        $categories = $response->json('data');
        $names = collect($categories)->pluck('name')->toArray();
        
        $this->assertEquals(['Alpha', 'Beta', 'Zebra'], $names);
    }
} 