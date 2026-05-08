<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'tahun_penerimaan';

    protected $fillable = ['tahun', 'label', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class);
    }

    public function hasPeserta(): bool
    {
        return $this->peserta()->exists();
    }
}
