<?php

namespace Tests\Feature\Api;

use App\Models\Address;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function test_user_can_get_their_addresses()
    {
        $user = $this->createUser();
        $otherUser = $this->createUser();
        
        // Create addresses for the user
        $address1 = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);
        $address2 = Address::factory()->create(['user_id' => $user->id, 'is_default' => false]);
        
        // Create address for another user (should not appear)
        Address::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/profile/addresses', $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'name',
                        'street',
                        'city',
                        'state',
                        'postal_code',
                        'country',
                        'phone',
                        'is_default',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        $addressIds = collect($response->json('data'))->pluck('id');
        $this->assertContains($address1->id, $addressIds);
        $this->assertContains($address2->id, $addressIds);
        $this->assertEquals(2, count($addressIds));
    }

    public function test_unauthenticated_user_cannot_access_addresses()
    {
        $response = $this->getJson('/api/profile/addresses');

        $response->assertStatus(401);
    }

    public function test_user_can_create_address()
    {
        $user = $this->createUser();

        $addressData = [
            'name' => 'John Doe',
            'street' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'phone' => '+1234567890'
        ];

        $response = $this->postJson('/api/profile/addresses', $addressData, $this->getAuthHeaders($user));

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Address created successfully',
                'data' => [
                    'name' => 'John Doe',
                    'street' => '123 Main Street',
                    'city' => 'New York',
                    'user_id' => $user->id
                ]
            ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'name' => 'John Doe',
            'street' => '123 Main Street'
        ]);
    }

    public function test_first_address_becomes_default_automatically()
    {
        $user = $this->createUser();

        $addressData = [
            'name' => 'John Doe',
            'street' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'phone' => '+1234567890'
        ];

        $response = $this->postJson('/api/profile/addresses', $addressData, $this->getAuthHeaders($user));

        $response->assertStatus(201);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'is_default' => true
        ]);
    }

    public function test_address_creation_requires_valid_data()
    {
        $user = $this->createUser();

        // Test missing required fields
        $response = $this->postJson('/api/profile/addresses', [], $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'street', 'city', 'state', 'postal_code', 'country']);

        // Test invalid phone format
        $response = $this->postJson('/api/profile/addresses', [
            'name' => 'John Doe',
            'street' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'phone' => 'invalid-phone'
        ], $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_user_can_update_their_address()
    {
        $user = $this->createUser();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'name' => 'Updated Name',
            'street' => 'Updated Street',
            'city' => 'Updated City',
            'state' => 'UC',
            'postal_code' => '99999',
            'country' => 'Updated Country',
            'phone' => '+9876543210'
        ];

        $response = $this->putJson("/api/profile/addresses/{$address->id}", $updateData, $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Address updated successfully',
                'data' => [
                    'name' => 'Updated Name',
                    'street' => 'Updated Street'
                ]
            ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'name' => 'Updated Name',
            'street' => 'Updated Street'
        ]);
    }

    public function test_user_cannot_update_another_users_address()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $address = Address::factory()->create(['user_id' => $user2->id]);

        $updateData = [
            'name' => 'Updated Name',
            'street' => 'Updated Street',
            'city' => 'Updated City',
            'state' => 'UC',
            'postal_code' => '99999',
            'country' => 'Updated Country'
        ];

        $response = $this->putJson("/api/profile/addresses/{$address->id}", $updateData, $this->getAuthHeaders($user1));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Address not found']);
    }

    public function test_user_can_delete_their_address()
    {
        $user = $this->createUser();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/profile/addresses/{$address->id}", [], $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Address deleted successfully']);

        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_user_cannot_delete_another_users_address()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $address = Address::factory()->create(['user_id' => $user2->id]);

        $response = $this->deleteJson("/api/profile/addresses/{$address->id}", [], $this->getAuthHeaders($user1));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Address not found']);
    }

    public function test_user_can_set_default_address()
    {
        $user = $this->createUser();
        $address1 = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);
        $address2 = Address::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        $response = $this->postJson("/api/profile/addresses/{$address2->id}/default", [], $this->getAuthHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Default address updated']);

        // Check that address2 is now default
        $this->assertDatabaseHas('addresses', [
            'id' => $address2->id,
            'is_default' => true
        ]);

        // Check that address1 is no longer default
        $this->assertDatabaseHas('addresses', [
            'id' => $address1->id,
            'is_default' => false
        ]);
    }

    public function test_user_cannot_set_another_users_address_as_default()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $address = Address::factory()->create(['user_id' => $user2->id]);

        $response = $this->postJson("/api/profile/addresses/{$address->id}/default", [], $this->getAuthHeaders($user1));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Address not found']);
    }

    public function test_deleting_default_address_sets_another_as_default()
    {
        $user = $this->createUser();
        $defaultAddress = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);
        $otherAddress = Address::factory()->create(['user_id' => $user->id, 'is_default' => false]);

        $response = $this->deleteJson("/api/profile/addresses/{$defaultAddress->id}", [], $this->getAuthHeaders($user));

        $response->assertStatus(200);

        // Check that the other address became default
        $this->assertDatabaseHas('addresses', [
            'id' => $otherAddress->id,
            'is_default' => true
        ]);
    }

    public function test_address_validation_rules()
    {
        $user = $this->createUser();

        // Test postal code validation
        $response = $this->postJson('/api/profile/addresses', [
            'name' => 'John Doe',
            'street' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '', // Empty postal code
            'country' => 'USA'
        ], $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['postal_code']);

        // Test country validation
        $response = $this->postJson('/api/profile/addresses', [
            'name' => 'John Doe',
            'street' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => '' // Empty country
        ], $this->getAuthHeaders($user));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['country']);
    }

    public function test_addresses_are_returned_with_default_first()
    {
        $user = $this->createUser();
        $regularAddress = Address::factory()->create(['user_id' => $user->id, 'is_default' => false]);
        $defaultAddress = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);

        $response = $this->getJson('/api/profile/addresses', $this->getAuthHeaders($user));

        $response->assertStatus(200);

        $addresses = $response->json('data');
        // Default address should be first
        $this->assertTrue($addresses[0]['is_default']);
        $this->assertEquals($defaultAddress->id, $addresses[0]['id']);
    }
} 