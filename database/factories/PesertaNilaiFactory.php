<?php

namespace Database\Factories;

use App\Models\MataPelajaran;
use App\Models\Peserta;
use Illuminate\Database\Eloquent\Factories\Factory;

class PesertaNilaiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'peserta_id'        => Peserta::factory(),
            'mata_pelajaran_id' => MataPelajaran::factory(),
            'semester'          => fake()->numberBetween(1, 5),
            'nilai'             => fake()->numberBetween(60, 100),
        ];
    }
}
