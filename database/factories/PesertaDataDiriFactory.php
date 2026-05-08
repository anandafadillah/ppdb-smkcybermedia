<?php

namespace Database\Factories;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Factories\Factory;

class PesertaDataDiriFactory extends Factory
{
    public function definition(): array
    {
        return [
            'peserta_id'    => Peserta::factory(),
            'nama_lengkap'  => fake()->name(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
        ];
    }
}
