<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JalurPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'jalur_pendaftaran';

    protected $fillable = [
        'tahun_penerimaan_id',
        'nama',
        'deskripsi',
        'is_active',
        'persentase_kuota',
        'kode_awal_daring',
        'kode_awal_luring',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'persentase_kuota' => 'integer',
    ];

    public function tahunPenerimaan(): BelongsTo
    {
        return $this->belongsTo(TahunPenerimaan::class, 'tahun_penerimaan_id');
    }

    public function isAfirmasi(): bool
    {
        return str_contains(strtolower($this->nama), 'afirmasi');
    }
}
