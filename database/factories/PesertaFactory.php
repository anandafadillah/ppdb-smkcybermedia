<?php

namespace Database\Factories;

use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PesertaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'             => User::factory()->peserta(),
            'tahun_penerimaan_id' => TahunPenerimaan::factory(),
            'jalur_id'            => null,
            'jurusan_id'          => null,
            'no_pendaftaran'      => null,
            'status_formulir'     => 'draft',
            'status_verifikasi'   => 'belum_diverifikasi',
            'status_hasil'        => 'belum',
            'status_daftar_ulang' => 'belum',
        ];
    }
}
