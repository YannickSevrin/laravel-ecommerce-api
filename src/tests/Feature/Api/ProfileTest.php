<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ProfileTest extends TestCase
{
    public function test_authenticated_user_can_get_profile()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $response = $this->getJson('/api/profile', $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'email_verified_at',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    public function test_user_can_update_profile()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $updateData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com'
        ];

        $response = $this->putJson('/api/profile', $updateData, $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profile updated successfully',
                'data' => [
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com'
        ]);
    }

    public function test_user_can_update_password()
    {
        $user = $this->createUser([
            'password' => Hash::make('oldpassword')
        ]);

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->putJson('/api/profile', $updateData, $this->getAuthHeaders($user));

        $response->assertStatus(200);

        // Verify password was updated
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_password_update_requires_current_password()
    {
        $user = $this->createUser([
            'password' => Hash::make('oldpassword')
        ]);

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->putJson('/api/profile', $updateData, $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_password_update_validates_current_password()
    {
        $user = $this->createUser([
            'password' => Hash::make('oldpassword')
        ]);

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->putJson('/api/profile', $updateData, $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_profile_update_validates_email_uniqueness()
    {
        $user1 = $this->createUser(['email' => 'user1@example.com']);
        $user2 = $this->createUser(['email' => 'user2@example.com']);

        $updateData = [
            'name' => $user1->name,
            'email' => 'user2@example.com' // Already taken
        ];

        $response = $this->putJson('/api/profile', $updateData, $this->getAuthHeaders($user1));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_profile_update_allows_same_email()
    {
        $user = $this->createUser(['email' => 'john@example.com']);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'john@example.com' // Same email
        ];

        $response = $this->putJson('/api/profile', $updateData, $this->getAuthHeaders($user));

        $response->assertStatus(200);
    }

    public function test_profile_update_requires_valid_data()
    {
        $user = $this->createUser();

        // Test invalid email
        $response = $this->putJson('/api/profile', [
            'name' => 'Valid Name',
            'email' => 'invalid-email'
        ], $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test empty name
        $response = $this->putJson('/api/profile', [
            'name' => '',
            'email' => 'valid@example.com'
        ], $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_delete_profile()
    {
        $user = $this->createUser();

        $response = $this->deleteJson('/api/profile', [], $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Profile deleted successfully']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_profile_deletion_removes_related_data()
    {
        $user = $this->createUser();
        $product = $this->createProduct();
        
        // Add some related data
        \App\Models\Cart::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);
        \App\Models\Address::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson('/api/profile', [], $this->getAuthHeaders($user));

        $response->assertStatus(200);

        // Check that related data is also removed
        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('addresses', ['user_id' => $user->id]);
    }

    public function test_password_confirmation_must_match()
    {
        $user = $this->createUser([
            'password' => Hash::make('oldpassword')
        ]);

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword'
        ];

        $response = $this->putJson('/api/profile', $updateData, $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_password_must_meet_minimum_length()
    {
        $user = $this->createUser([
            'password' => Hash::make('oldpassword')
        ]);

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'oldpassword',
            'password' => '123',
            'password_confirmation' => '123'
        ];

        $response = $this->putJson('/api/profile', $updateData, $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
} 