<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'user_id',
        'tahun_penerimaan_id',
        'jalur_id',
        'jurusan_id',
        'no_pendaftaran',
        'status_formulir',
        'status_verifikasi',
        'status_hasil',
        'status_daftar_ulang',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function jalur()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_id');
    }

    public function dataDiri()
    {
        return $this->hasOne(PesertaDataDiri::class);
    }

    public function dataAyah()
    {
        return $this->hasOne(PesertaDataAyah::class);
    }

    public function dataIbu()
    {
        return $this->hasOne(PesertaDataIbu::class);
    }

    public function dataWali()
    {
        return $this->hasOne(PesertaDataWali::class);
    }

    public function dataAlamat()
    {
        return $this->hasOne(PesertaDataAlamat::class);
    }

    public function dataKip()
    {
        return $this->hasOne(PesertaDataKip::class);
    }

    public function berkas()
    {
        return $this->hasMany(PesertaBerkas::class);
    }

    public function nilai()
    {
        return $this->hasMany(PesertaNilai::class);
    }

    public function uploadTerkunci(): bool
    {
        return $this->status_verifikasi !== 'belum_diverifikasi';
    }

    public function sudahSubmit(): bool
    {
        return $this->status_formulir === 'submitted';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunPenerimaan()
    {
        return $this->belongsTo(TahunPenerimaan::class, 'tahun_penerimaan_id');
    }
}
