<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TahunPenerimaanFactory extends Factory
{
    public function definition(): array
    {
        $tahun = (int) fake()->year();

        return [
            'tahun'     => $tahun . '/' . ($tahun + 1),
            'label'     => 'TA ' . $tahun . '/' . ($tahun + 1),
            'is_active' => false,
        ];
    }
}
