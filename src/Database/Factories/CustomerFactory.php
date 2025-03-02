<?php

namespace Igniter\User\Database\Factories;

use Igniter\Flame\Database\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = \Igniter\User\Models\Customer::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'telephone' => $this->faker->phoneNumber(),
            'newsletter' => $this->faker->boolean(),
            'address_id' => $this->faker->numberBetween(1, 9999),
            'customer_group_id' => $this->faker->numberBetween(1, 9999),
            'status' => '1',
            'is_activated' => $this->faker->boolean(),
            'ip_address' => $this->faker->ipv6(),
        ];
    }
}
