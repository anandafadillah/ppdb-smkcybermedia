<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaDataWali extends Model
{
    protected $table = 'peserta_data_wali';

    protected $fillable = [
        'peserta_id', 'nama', 'nik', 'tahun_lahir', 'pekerjaan', 'penghasilan',
    ];
}
