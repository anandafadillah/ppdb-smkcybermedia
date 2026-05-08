<?php

namespace App\Services;

use App\Models\NomorPendaftaranSequence;
use App\Models\Peserta;
use Illuminate\Support\Facades\DB;

class PendaftaranService
{
    public function generateNomorPendaftaran(Peserta $peserta, string $mode = 'daring'): string
    {
        return DB::transaction(function () use ($peserta, $mode) {
            $jalur = $peserta->jalur;

            $seq = NomorPendaftaranSequence::lockForUpdate()
                ->where('jalur_id', $jalur->id)
                ->where('tahun_penerimaan_id', $peserta->tahun_penerimaan_id)
                ->where('mode', $mode)
                ->first();

            if ($seq) {
                $seq->increment('last_sequence');
                $seq->refresh();
            } else {
                $seq = NomorPendaftaranSequence::create([
                    'jalur_id'            => $jalur->id,
                    'tahun_penerimaan_id' => $peserta->tahun_penerimaan_id,
                    'mode'                => $mode,
                    'last_sequence'       => 1,
                ]);
            }

            $kode = $mode === 'daring' ? $jalur->kode_awal_daring : $jalur->kode_awal_luring;
            $no   = $kode . '-' . str_pad($seq->last_sequence, 4, '0', STR_PAD_LEFT);

            $peserta->update([
                'no_pendaftaran'  => $no,
                'status_formulir' => 'submitted',
            ]);

            return $no;
        });
    }
}
