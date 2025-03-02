<?php

declare(strict_types=1);

namespace Igniter\User\Database\Factories;

use Igniter\User\Models\CustomerGroup;
use Override;
use Igniter\Flame\Database\Factories\Factory;

class CustomerGroupFactory extends Factory
{
    protected $model = CustomerGroup::class;

    #[Override]
    public function definition(): array
    {
        return [
            'group_name' => $this->faker->sentence(2),
            'description' => $this->faker->paragraph(),
            'approval' => $this->faker->boolean(),
        ];
    }
}
