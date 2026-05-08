<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'      => fake()->name(),
            'nisn'      => null,
            'email'     => fake()->unique()->safeEmail(),
            'password'  => static::$password ??= Hash::make('password'),
            'role'      => 'peserta',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'nisn'  => null,
            'email' => fake()->unique()->safeEmail(),
            'role'  => 'admin',
        ]);
    }

    public function panitia(): static
    {
        return $this->state(fn () => [
            'nisn'  => null,
            'email' => fake()->unique()->safeEmail(),
            'role'  => 'panitia',
        ]);
    }

    public function peserta(): static
    {
        return $this->state(fn () => [
            'nisn'  => fake()->unique()->numerify('##########'),
            'email' => null,
            'role'  => 'peserta',
        ]);
    }
}
