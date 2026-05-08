<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaDataAyah extends Model
{
    protected $table = 'peserta_data_ayah';

    protected $fillable = [
        'peserta_id', 'nama', 'nik', 'tahun_lahir',
        'pendidikan', 'pekerjaan', 'penghasilan', 'ketidakmampuan_khusus',
    ];
}
