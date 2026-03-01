<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_via_web()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'username' => 'admin001',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login.submit'), [
            'username' => 'admin001',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_customer_can_login_via_web()
    {
        $customer = Customer::create([
            'name' => 'Farm Owner', 
            'phone' => '0822222222',
            'password' => Hash::make('1234')
        ]);

        $response = $this->post(route('customer.login.submit'), [
            'phone' => '0822222222',
            'password' => '1234',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($customer, 'customer');
    }

    public function test_staff_can_login_via_api()
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'username' => 'staff001',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'staff001',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'user']);
    }

    public function test_invalid_login_returns_error()
    {
        $response = $this->post(route('login.submit'), [
            'username' => 'nonexistent',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }
}
