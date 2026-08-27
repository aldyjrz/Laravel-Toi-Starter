<?php

declare(strict_types=1);

namespace Aldytoi\LaravelToi\Tests\Models;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'Password123!',
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }
}
