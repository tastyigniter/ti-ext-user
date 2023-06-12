<?php

namespace Igniter\User\Database\Factories;

use Igniter\Flame\Database\Factories\Factory;

class CustomerGroupFactory extends Factory
{
    protected $model = \Igniter\User\Models\CustomerGroup::class;

    public function definition(): array
    {
        return [
            'group_name' => $this->faker->sentence(2),
            'description' => $this->faker->paragraph(),
            'approval' => $this->faker->boolean(),
        ];
    }
}
