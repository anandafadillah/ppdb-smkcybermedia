<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaBerkas extends Model
{
    use HasFactory;
    protected $table = 'peserta_berkas';

    protected $fillable = [
        'peserta_id', 'tipe_berkas', 'file_path', 'mime_type', 'keterangan',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public static function tipeList(): array
    {
        return [
            'foto_3x4'       => 'Foto 3x4',
            'nilai_rapor'    => 'Nilai Rapor',
            'akta_kelahiran' => 'Akta Kelahiran',
            'kartu_keluarga' => 'Kartu Keluarga',
            'ktp_orangtua'   => 'KTP Orang Tua',
            'sktm'           => 'SKTM',
            'kartu_pkh'      => 'Kartu PKH/KPS/KIP',
            'berkas_lainnya' => 'Berkas Lainnya',
        ];
    }

    public static function tipeFoto(): array
    {
        return ['foto_3x4'];
    }

    public static function tipeDokumen(): array
    {
        return ['nilai_rapor', 'akta_kelahiran', 'kartu_keluarga', 'ktp_orangtua', 'sktm', 'kartu_pkh', 'berkas_lainnya'];
    }
}
