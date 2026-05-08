<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengumumanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'         => User::factory()->admin(),
            'judul'           => fake()->sentence(4),
            'isi'             => '<p>' . fake()->paragraph() . '</p>',
            'status'          => 'draft',
            'tanggal_publish' => now()->toDateString(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }
}
