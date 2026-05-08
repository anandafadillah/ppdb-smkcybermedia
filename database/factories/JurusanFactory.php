<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JurusanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode'      => strtoupper(fake()->unique()->lexify('????')),
            'nama'      => fake()->words(3, true),
            'kapasitas' => fake()->optional()->numberBetween(20, 50),
            'deskripsi' => fake()->optional()->sentence(),
        ];
    }
}
