<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'role'],
                'token'
            ])
            ->assertJson([
                'user' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'role' => 'customer'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);
    }

    public function test_registration_requires_valid_data()
    {
        // Test missing name
        $response = $this->postJson('/api/auth/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Test invalid email
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test password confirmation mismatch
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different'
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'role'],
                'token'
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    public function test_authenticated_user_can_get_profile()
    {
        $user = $this->createUser();
        $headers = $this->getAuthHeaders($user);

        $response = $this->getJson('/api/auth/user', $headers);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'role']
            ])
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    public function test_user_can_logout()
    {
        $user = $this->createUser();
        $headers = $this->getAuthHeaders($user);

        $response = $this->postJson('/api/auth/logout', [], $headers);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout successful']);
    }
} 