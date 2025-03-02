<?php

namespace Igniter\User\Database\Factories;

use DateTimeInterface;
use Igniter\Flame\Database\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = \Igniter\User\Models\User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'username' => str_slug($this->faker->userName()),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'activated_at' => $this->faker->dateTime()->format(DateTimeInterface::ATOM),
            'is_activated' => $this->faker->boolean(),
            'super_user' => $this->faker->boolean(),
            'status' => $this->faker->boolean(),
        ];
    }
}
