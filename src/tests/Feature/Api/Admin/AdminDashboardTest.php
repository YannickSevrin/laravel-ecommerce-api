<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_admin_can_access_dashboard()
    {
        $admin = $this->createAdmin();

        $response = $this->getJson('/api/admin/dashboard', $this->getAuthHeaders($admin));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'products' => [
                        'count',
                        'recent' => [
                            '*' => ['id', 'name', 'price', 'category_id']
                        ]
                    ],
                    'categories' => [
                        'count',
                        'list' => [
                            '*' => ['id', 'name', 'products_count']
                        ]
                    ],
                    'users' => [
                        'count',
                        'customers',
                        'admins',
                        'recent' => [
                            '*' => ['id', 'name', 'email', 'created_at']
                        ]
                    ],
                    'orders' => [
                        'count',
                        'pending',
                        'paid',
                        'shipped',
                        'canceled',
                        'recent' => [
                            '*' => ['id', 'user_id', 'total', 'status', 'user', 'items']
                        ]
                    ],
                    'sales' => [
                        'total',
                        'monthly',
                        'daily'
                    ]
                ]
            ]);
    }

    public function test_customer_cannot_access_dashboard()
    {
        $customer = $this->createUser('customer');

        $response = $this->getJson('/api/admin/dashboard', $this->getAuthHeaders($customer));

        $response->assertStatus(403)
            ->assertJson(['message' => 'Access denied. Admin role required.']);
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(401);
    }

    public function test_dashboard_shows_correct_statistics()
    {
        $admin = $this->createAdmin();
        
        // Create test data
        $category = $this->createCategory();
        Product::factory(5)->create(['category_id' => $category->id]);
        $users = collect([
            $this->createUser('customer'),
            $this->createUser('customer'),
            $this->createAdmin()
        ]);
        
        // Create orders with different statuses
        Order::factory()->create(['status' => 'pending', 'total' => 100]);
        Order::factory()->create(['status' => 'paid', 'total' => 200]);
        Order::factory()->create(['status' => 'shipped', 'total' => 150]);
        Order::factory()->create(['status' => 'canceled', 'total' => 50]);

        $response = $this->getJson('/api/admin/dashboard', $this->getAuthHeaders($admin));

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Check counts
        $this->assertEquals(5, $data['products']['count']);
        $this->assertEquals(1, $data['categories']['count']);
        $this->assertEquals(4, $data['users']['count']); // 3 created + 1 admin
        $this->assertEquals(2, $data['users']['customers']); // 2 customers
        $this->assertEquals(2, $data['users']['admins']); // 2 admins (1 created + 1 in test)
        $this->assertEquals(4, $data['orders']['count']);
        
        // Check order status counts
        $this->assertEquals(1, $data['orders']['pending']);
        $this->assertEquals(1, $data['orders']['paid']);
        $this->assertEquals(1, $data['orders']['shipped']);
        $this->assertEquals(1, $data['orders']['canceled']);
        
        // Check sales total
        $this->assertEquals(500.0, $data['sales']['total']); // 100 + 200 + 150 + 50
    }

    public function test_dashboard_limits_recent_items()
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();
        
        // Create more than 5 products (dashboard should limit to 5)
        Product::factory(10)->create(['category_id' => $category->id]);
        
        // Create more than 5 users (dashboard should limit to 5)
        for ($i = 0; $i < 10; $i++) {
            $this->createUser('customer');
        }
        
        // Create more than 10 orders (dashboard should limit to 10)
        Order::factory(15)->create();

        $response = $this->getJson('/api/admin/dashboard', $this->getAuthHeaders($admin));

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Check limits
        $this->assertLessThanOrEqual(5, count($data['products']['recent']));
        $this->assertLessThanOrEqual(5, count($data['users']['recent']));
        $this->assertLessThanOrEqual(10, count($data['orders']['recent']));
    }

    public function test_dashboard_categories_include_product_count()
    {
        $admin = $this->createAdmin();
        $category1 = $this->createCategory();
        $category2 = $this->createCategory();
        
        // Create products for categories
        Product::factory(3)->create(['category_id' => $category1->id]);
        Product::factory(2)->create(['category_id' => $category2->id]);

        $response = $this->getJson('/api/admin/dashboard', $this->getAuthHeaders($admin));

        $response->assertStatus(200);

        $categories = $response->json('data.categories.list');
        
        // Find our categories in the response
        $cat1Data = collect($categories)->firstWhere('id', $category1->id);
        $cat2Data = collect($categories)->firstWhere('id', $category2->id);
        
        $this->assertEquals(3, $cat1Data['products_count']);
        $this->assertEquals(2, $cat2Data['products_count']);
    }

    public function test_dashboard_orders_include_user_and_items()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/admin/dashboard', $this->getAuthHeaders($admin));

        $response->assertStatus(200);

        $orders = $response->json('data.orders.recent');
        $orderData = collect($orders)->firstWhere('id', $order->id);
        
        $this->assertNotNull($orderData);
        $this->assertArrayHasKey('user', $orderData);
        $this->assertArrayHasKey('items', $orderData);
        $this->assertEquals($user->id, $orderData['user']['id']);
    }
} 