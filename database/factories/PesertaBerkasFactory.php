<?php

namespace Database\Factories;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Factories\Factory;

class PesertaBerkasFactory extends Factory
{
    public function definition(): array
    {
        $tipe = fake()->randomElement(['foto_3x4', 'nilai_rapor', 'akta_kelahiran', 'kartu_keluarga', 'ktp_orangtua']);

        return [
            'peserta_id'  => Peserta::factory(),
            'tipe_berkas' => $tipe,
            'file_path'   => 'berkas/1/' . $tipe . '.pdf',
            'mime_type'   => 'application/pdf',
            'keterangan'  => null,
        ];
    }
}
