<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MataPelajaranFactory extends Factory
{
    public function definition(): array
    {
        $mapel = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS', 'PKn', 'Seni Budaya', 'PJOK'];

        return [
            'nama'      => $this->faker->unique()->randomElement($mapel),
            'is_active' => true,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(['is_active' => false]);
    }
}
