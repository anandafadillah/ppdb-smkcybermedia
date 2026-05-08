<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaDataDiri extends Model
{
    use HasFactory;

    protected $table = 'peserta_data_diri';

    protected $fillable = [
        'peserta_id', 'nama_lengkap', 'nisn', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'agama', 'no_hp',
        'tinggi_badan', 'berat_badan', 'jumlah_saudara',
        'asal_sekolah_id', 'asal_sekolah_custom',
    ];

    protected $casts = ['tanggal_lahir' => 'date'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function asalSekolah()
    {
        return $this->belongsTo(AsalSekolah::class, 'asal_sekolah_id');
    }
}
