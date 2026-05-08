<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaDataKip extends Model
{
    protected $table = 'peserta_data_kip';

    protected $fillable = [
        'peserta_id', 'no_kip', 'no_kps_pkh', 'nama_di_kip', 'terima_kip',
    ];

    protected $casts = ['terima_kip' => 'boolean'];
}
