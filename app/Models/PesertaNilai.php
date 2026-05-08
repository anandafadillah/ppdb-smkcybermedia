<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaNilai extends Model
{
    use HasFactory;

    protected $table = 'peserta_nilai';

    protected $fillable = ['peserta_id', 'mata_pelajaran_id', 'semester', 'nilai'];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
}
