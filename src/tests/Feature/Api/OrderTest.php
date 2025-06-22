<?php

namespace Tests\Feature\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_user_can_create_order_from_cart()
    {
        $user = $this->createUser();
        $product1 = $this->createProduct(['price' => 100]);
        $product2 = $this->createProduct(['price' => 50]);
        
        // Add items to cart
        Cart::create(['user_id' => $user->id, 'product_id' => $product1->id, 'quantity' => 2]);
        Cart::create(['user_id' => $user->id, 'product_id' => $product2->id, 'quantity' => 1]);

        // Create address
        $address = Address::factory()->create(['user_id' => $user->id]);

        $orderData = [
            'address_id' => $address->id,
            'payment_method' => 'credit_card'
        ];

        $response = $this->postJson('/api/orders', $orderData, $this->getAuthHeaders($user));

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'order' => [
                    'id',
                    'user_id',
                    'total',
                    'status',
                    'created_at',
                    'items' => [
                        '*' => [
                            'product_id',
                            'quantity',
                            'price'
                        ]
                    ]
                ]
            ]);

        // Check order was created
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => 250.0, // (100 * 2) + (50 * 1)
            'status' => 'pending'
        ]);

        // Check cart was cleared
        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
    }

    public function test_cannot_create_order_with_empty_cart()
    {
        $user = $this->createUser();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $orderData = [
            'address_id' => $address->id,
            'payment_method' => 'credit_card'
        ];

        $response = $this->postJson('/api/orders', $orderData, $this->getAuthHeaders($user));

        $response->assertStatus(400)
            ->assertJson(['message' => 'Cart is empty']);
    }

    public function test_cannot_create_order_with_invalid_address()
    {
        $user = $this->createUser();
        $product = $this->createProduct();
        Cart::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        $orderData = [
            'address_id' => 999,
            'payment_method' => 'credit_card'
        ];

        $response = $this->postJson('/api/orders', $orderData, $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address_id']);
    }

    public function test_cannot_use_another_users_address()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $product = $this->createProduct();
        
        Cart::create(['user_id' => $user1->id, 'product_id' => $product->id, 'quantity' => 1]);
        $address = Address::factory()->create(['user_id' => $user2->id]);

        $orderData = [
            'address_id' => $address->id,
            'payment_method' => 'credit_card'
        ];

        $response = $this->postJson('/api/orders', $orderData, $this->getAuthHeaders($user1));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address_id']);
    }

    public function test_user_can_get_their_orders()
    {
        $user = $this->createUser();
        $otherUser = $this->createUser();
        
        // Create orders for the user
        $order1 = Order::factory()->create(['user_id' => $user->id]);
        $order2 = Order::factory()->create(['user_id' => $user->id]);
        
        // Create order for another user (should not appear)
        Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/orders', $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'total',
                        'status',
                        'created_at',
                        'updated_at',
                        'items' => [
                            '*' => [
                                'id',
                                'product_id',
                                'quantity',
                                'price',
                                'product' => ['id', 'name', 'image']
                            ]
                        ]
                    ]
                ],
                'meta',
                'links'
            ]);

        // Should only return user's orders
        $orderIds = collect($response->json('data'))->pluck('id');
        $this->assertContains($order1->id, $orderIds);
        $this->assertContains($order2->id, $orderIds);
        $this->assertEquals(2, count($orderIds));
    }

    public function test_user_can_get_single_order()
    {
        $user = $this->createUser();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/orders/{$order->id}", $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'total',
                    'status',
                    'created_at',
                    'updated_at',
                    'items',
                    'address'
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $order->id,
                    'user_id' => $user->id
                ]
            ]);
    }

    public function test_user_cannot_access_another_users_order()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $order = Order::factory()->create(['user_id' => $user2->id]);

        $response = $this->getJson("/api/orders/{$order->id}", $this->getAuthHeaders($user1));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Order not found']);
    }

    public function test_order_creation_requires_authentication()
    {
        $response = $this->postJson('/api/orders', [
            'address_id' => 1,
            'payment_method' => 'credit_card'
        ]);

        $response->assertStatus(401);
    }

    public function test_order_creation_validates_payment_method()
    {
        $user = $this->createUser();
        $product = $this->createProduct();
        $address = Address::factory()->create(['user_id' => $user->id]);
        
        Cart::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        $orderData = [
            'address_id' => $address->id,
            'payment_method' => 'invalid_method'
        ];

        $response = $this->postJson('/api/orders', $orderData, $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_method']);
    }

    public function test_order_includes_correct_items_and_prices()
    {
        $user = $this->createUser();
        $product1 = $this->createProduct(['price' => 100, 'name' => 'Product 1']);
        $product2 = $this->createProduct(['price' => 50, 'name' => 'Product 2']);
        $address = Address::factory()->create(['user_id' => $user->id]);
        
        Cart::create(['user_id' => $user->id, 'product_id' => $product1->id, 'quantity' => 2]);
        Cart::create(['user_id' => $user->id, 'product_id' => $product2->id, 'quantity' => 3]);

        $orderData = [
            'address_id' => $address->id,
            'payment_method' => 'credit_card'
        ];

        $response = $this->postJson('/api/orders', $orderData, $this->getAuthHeaders($user));

        $response->assertStatus(201);

        $order = Order::where('user_id', $user->id)->first();
        
        // Check order items
        $this->assertEquals(2, $order->items->count());
        
        $item1 = $order->items->where('product_id', $product1->id)->first();
        $this->assertEquals(2, $item1->quantity);
        $this->assertEquals(100, $item1->price);
        
        $item2 = $order->items->where('product_id', $product2->id)->first();
        $this->assertEquals(3, $item2->quantity);
        $this->assertEquals(50, $item2->price);
        
        // Check total
        $this->assertEquals(350, $order->total); // (100 * 2) + (50 * 3)
    }

    public function test_orders_are_paginated()
    {
        $user = $this->createUser();
        
        // Create 15 orders
        Order::factory(15)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders', $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertEquals(15, $response->json('meta.total'));
        $this->assertEquals(10, $response->json('meta.per_page'));
    }
} 