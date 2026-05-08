<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaDataAlamat extends Model
{
    protected $table = 'peserta_data_alamat';

    protected $fillable = [
        'peserta_id', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota',
        'latitude', 'longitude', 'jarak_tempat_tinggal',
    ];
}
