<?php

namespace Tests\Feature\Api;

use App\Models\Cart;
use App\Models\Product;
use Tests\TestCase;

class CartTest extends TestCase
{
    public function test_authenticated_user_can_get_cart()
    {
        $user = $this->createUser();
        $product = $this->createProduct();
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->getJson('/api/cart', $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'product_id',
                        'quantity',
                        'created_at',
                        'updated_at',
                        'product' => [
                            'id',
                            'name',
                            'price',
                            'image'
                        ]
                    ]
                ],
                'total',
                'count'
            ]);
    }

    public function test_unauthenticated_user_cannot_access_cart()
    {
        $response = $this->getJson('/api/cart');

        $response->assertStatus(401);
    }

    public function test_user_can_add_product_to_cart()
    {
        $user = $this->createUser();
        $product = $this->createProduct();

        $response = $this->postJson(
            "/api/cart/add/{$product->id}",
            ['quantity' => 3],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product added to cart']);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3
        ]);
    }

    public function test_adding_existing_product_updates_quantity()
    {
        $user = $this->createUser();
        $product = $this->createProduct();

        // Add product first time
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // Add same product again
        $response = $this->postJson(
            "/api/cart/add/{$product->id}",
            ['quantity' => 3],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5 // 2 + 3
        ]);
    }

    public function test_cannot_add_non_existent_product_to_cart()
    {
        $user = $this->createUser();

        $response = $this->postJson(
            '/api/cart/add/999',
            ['quantity' => 1],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }

    public function test_user_can_update_cart_item_quantity()
    {
        $user = $this->createUser();
        $product = $this->createProduct();
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->putJson(
            "/api/cart/update/{$product->id}",
            ['quantity' => 5],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(200)
            ->assertJson(['message' => 'Cart updated']);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5
        ]);
    }

    public function test_updating_with_zero_quantity_removes_item()
    {
        $user = $this->createUser();
        $product = $this->createProduct();
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->putJson(
            "/api/cart/update/{$product->id}",
            ['quantity' => 0],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(200)
            ->assertJson(['message' => 'Item removed from cart']);

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    public function test_user_can_remove_product_from_cart()
    {
        $user = $this->createUser();
        $product = $this->createProduct();
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->deleteJson(
            "/api/cart/remove/{$product->id}",
            [],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(200)
            ->assertJson(['message' => 'Product removed from cart']);

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    public function test_removing_non_existent_cart_item_returns_404()
    {
        $user = $this->createUser();
        $product = $this->createProduct();

        $response = $this->deleteJson(
            "/api/cart/remove/{$product->id}",
            [],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(404)
            ->assertJson(['message' => 'Item not found in cart']);
    }

    public function test_user_can_clear_entire_cart()
    {
        $user = $this->createUser();
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();
        
        Cart::create(['user_id' => $user->id, 'product_id' => $product1->id, 'quantity' => 2]);
        Cart::create(['user_id' => $user->id, 'product_id' => $product2->id, 'quantity' => 3]);

        $response = $this->deleteJson('/api/cart/clear', [], $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Cart cleared']);

        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
    }

    public function test_cart_total_calculation_is_correct()
    {
        $user = $this->createUser();
        $product1 = $this->createProduct(['price' => 100]);
        $product2 = $this->createProduct(['price' => 50]);
        
        Cart::create(['user_id' => $user->id, 'product_id' => $product1->id, 'quantity' => 2]);
        Cart::create(['user_id' => $user->id, 'product_id' => $product2->id, 'quantity' => 3]);

        $response = $this->getJson('/api/cart', $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJson([
                'total' => 350.0, // (100 * 2) + (50 * 3)
                'count' => 5 // 2 + 3
            ]);
    }

    public function test_cart_validation_requires_positive_quantity()
    {
        $user = $this->createUser();
        $product = $this->createProduct();

        $response = $this->postJson(
            "/api/cart/add/{$product->id}",
            ['quantity' => -1],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_cart_validation_requires_integer_quantity()
    {
        $user = $this->createUser();
        $product = $this->createProduct();

        $response = $this->postJson(
            "/api/cart/add/{$product->id}",
            ['quantity' => 'invalid'],
            $this->getAuthHeaders($user)
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }
} 