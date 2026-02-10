<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
            $phone = $this->faker->numerify('08#-###-####');
            $cleanPhone = str_replace('-', '', $phone);
            $password = substr($cleanPhone,-4);
        return [
            'customer_code' => 'CUST-' . $this->faker->unique()->numerify('###'),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'customer_type' => $this->faker->randomElement(['individual','farm']),
            'address' => $this->faker->address(),
            'password' => Hash::make($password),
        ];
    }
}
