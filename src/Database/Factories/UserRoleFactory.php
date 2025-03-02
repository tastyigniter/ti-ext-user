<?php

namespace Igniter\User\Database\Factories;

use Igniter\Flame\Database\Factories\Factory;

class UserRoleFactory extends Factory
{
    protected $model = \Igniter\User\Models\UserRole::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->slug(2),
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->paragraph(),
            'permissions' => [$this->faker->numberBetween(1, 99)],
        ];
    }
}
