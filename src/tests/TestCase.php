<?php

namespace Tests;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Create a test user
     */
    protected function createUser($role = 'customer', $attributes = [])
    {
        return User::factory()->create(array_merge($attributes, [
            'role' => $role,
        ]));
    }

    /**
     * Create an admin user
     */
    protected function createAdmin($attributes = [])
    {
        return $this->createUser('admin', $attributes);
    }

    /**
     * Get authenticated headers for a user
     */
    protected function getAuthHeaders(User $user)
    {
        $token = $user->createToken('test-token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    /**
     * Create a test product
     */
    protected function createProduct($attributes = [])
    {
        $category = Category::factory()->create();
        return Product::factory()->create(array_merge([
            'category_id' => $category->id,
        ], $attributes));
    }

    /**
     * Create a test category
     */
    protected function createCategory($attributes = [])
    {
        return Category::factory()->create($attributes);
    }
}
