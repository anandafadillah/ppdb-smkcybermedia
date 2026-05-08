<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AsalSekolahFactory extends Factory
{
    public function definition(): array
    {
        return [
            'npsn'      => $this->faker->unique()->numerify('########'),
            'nama'      => 'SMP ' . $this->faker->company(),
            'alamat'    => $this->faker->address(),
            'kelurahan' => $this->faker->word(),
            'kecamatan' => $this->faker->word(),
            'status'    => $this->faker->randomElement(['negeri', 'swasta']),
        ];
    }
}
