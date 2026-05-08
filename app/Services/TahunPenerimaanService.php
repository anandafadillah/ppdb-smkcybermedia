<?php

namespace App\Services;

use App\Models\TahunPenerimaan;
use Illuminate\Support\Facades\DB;

class TahunPenerimaanService
{
    public function create(array $data): TahunPenerimaan
    {
        return TahunPenerimaan::create([
            'tahun'     => $data['tahun'],
            'label'     => $data['label'],
            'is_active' => false,
        ]);
    }

    public function update(TahunPenerimaan $tahun, array $data): TahunPenerimaan
    {
        $tahun->update([
            'tahun' => $data['tahun'],
            'label' => $data['label'],
        ]);

        return $tahun->fresh();
    }

    public function delete(TahunPenerimaan $tahun): void
    {
        if ($tahun->hasPeserta()) {
            throw new \RuntimeException('Tahun penerimaan ini sudah memiliki data peserta dan tidak dapat dihapus.');
        }

        $tahun->delete();
    }

    public function activate(TahunPenerimaan $tahun): void
    {
        DB::transaction(function () use ($tahun) {
            TahunPenerimaan::where('id', '!=', $tahun->id)->update(['is_active' => false]);
            $tahun->update(['is_active' => true]);
        });
    }
}
