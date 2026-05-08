<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomorPendaftaranSequence extends Model
{
    protected $table = 'nomor_pendaftaran_sequence';

    protected $fillable = ['jalur_id', 'tahun_penerimaan_id', 'mode', 'last_sequence'];
}
