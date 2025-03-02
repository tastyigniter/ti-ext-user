<?php

declare(strict_types=1);

namespace Igniter\User\Database\Factories;

use Igniter\User\Models\UserRole;
use Override;
use Igniter\Flame\Database\Factories\Factory;

class UserRoleFactory extends Factory
{
    protected $model = UserRole::class;

    #[Override]
    public function definition(): array
    {
        return [
            'code' => $this->faker->slug(2),
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->paragraph(),
            'permissions' => [
                'Admin.Dashboard' => 1,
                'Admin.Users' => 1,
            ],
        ];
    }
}
