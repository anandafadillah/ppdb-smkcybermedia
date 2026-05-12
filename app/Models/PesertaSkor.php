<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaSkor extends Model
{
    protected $table = 'peserta_skor';

    protected $fillable = [
        'peserta_id',
        'jurusan_id',
        'jalur_id',
        'skor_nilai',
        'skor_umur',
        'skor_total',
        'bobot_nilai_snapshot',
        'bobot_umur_snapshot',
        'umur_saat_dihitung',
        'ranking',
        'calculated_at',
    ];

    protected $casts = [
        'skor_nilai'    => 'float',
        'skor_umur'     => 'float',
        'skor_total'    => 'float',
        'calculated_at' => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function jalur()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_id');
    }
}
