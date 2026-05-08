<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = ['nama', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function pesertaNilai()
    {
        return $this->hasMany(PesertaNilai::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
