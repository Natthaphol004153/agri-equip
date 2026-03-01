<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_list()
    {
        Customer::factory()->count(30)->create();

        $response = $this->getJson('/api/admin/customers?per_page=10');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'current_page', 'last_page', 'per_page', 'total']);

        $this->assertCount(10, $response->json('data'));
    }

    public function test_store_validates_input_and_creates()
    {
        $payload = [
            'name' => 'Somchai',
            'phone' => '0812345678',
            'customer_type' => 'individual',
            'password' => '1234'
        ];

        $resp = $this->postJson('/api/admin/customers', $payload);
        $resp->assertStatus(201)->assertJsonFragment(['name' => 'Somchai']);

        $this->assertDatabaseHas('customers', ['name' => 'Somchai']);
    }

    public function test_store_returns_validation_errors()
    {
        $resp = $this->postJson('/api/admin/customers', []);
        $resp->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }
}
