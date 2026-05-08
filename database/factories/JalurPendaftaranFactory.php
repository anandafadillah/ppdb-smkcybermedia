<?php

namespace Database\Factories;

use App\Models\TahunPenerimaan;
use Illuminate\Database\Eloquent\Factories\Factory;

class JalurPendaftaranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tahun_penerimaan_id' => TahunPenerimaan::factory(),
            'nama'                => fake()->randomElement(['Afirmasi', 'Pindah Sekolah']),
            'deskripsi'           => fake()->optional()->sentence(),
            'is_active'           => true,
            'persentase_kuota'    => 0,
            'kode_awal_daring'    => strtoupper(fake()->lexify('???')) . '-D',
            'kode_awal_luring'    => strtoupper(fake()->lexify('???')) . '-L',
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
