<?php

namespace App\Services;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Models\Peserta;
use App\Models\PesertaSkor;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SeleksiService
{
    public function hitungSkor(Peserta $peserta): ?PesertaSkor
    {
        $peserta->load(['dataDiri', 'nilai', 'tahunPenerimaan']);

        if (! $peserta->dataDiri?->tanggal_lahir) {
            return null;
        }

        $bobotNilai = (int) Setting::get('seleksi_bobot_nilai', 70);
        $bobotUmur  = (int) Setting::get('seleksi_bobot_umur', 30);
        $umurMin    = (int) Setting::get('seleksi_umur_min', 15);
        $umurMax    = (int) Setting::get('seleksi_umur_max', 21);

        $skorNilai = $this->hitungSkorNilai($peserta->nilai);

        // Hitung umur per 1 Juli tahun penerimaan (standar PPDB)
        $tahun     = $peserta->tahunPenerimaan?->tahun ?? now()->year;
        $referensi = Carbon::createFromDate($tahun, 7, 1);
        $umur      = (int) $peserta->dataDiri->tanggal_lahir->diffInYears($referensi);

        $skorUmur  = $this->hitungSkorUmur($umur, $umurMin, $umurMax);
        $skorTotal = ($skorNilai * $bobotNilai / 100) + ($skorUmur * $bobotUmur / 100);

        $skor = PesertaSkor::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'jurusan_id'           => $peserta->jurusan_id,
                'jalur_id'             => $peserta->jalur_id,
                'skor_nilai'           => round($skorNilai, 2),
                'skor_umur'            => round($skorUmur, 2),
                'skor_total'           => round($skorTotal, 2),
                'bobot_nilai_snapshot' => $bobotNilai,
                'bobot_umur_snapshot'  => $bobotUmur,
                'umur_saat_dihitung'   => $umur,
                'calculated_at'        => now(),
            ]
        );

        // Perbarui ranking di kelompok jurusan+jalur ini
        if ($peserta->jurusan_id && $peserta->jalur_id) {
            $this->generateRanking(
                $peserta->jurusan_id,
                $peserta->jalur_id,
                $peserta->tahun_penerimaan_id
            );
        }

        return $skor;
    }

    public function hitungSkorSemua(int $tahunPenerimaanId): int
    {
        $pesertaList = Peserta::where('tahun_penerimaan_id', $tahunPenerimaanId)
            ->where('status_formulir', 'submitted')
            ->get();

        $count = 0;
        foreach ($pesertaList as $peserta) {
            // Load ulang tanpa trigger generateRanking per peserta supaya efisien
            $peserta->load(['dataDiri', 'nilai', 'tahunPenerimaan']);

            if (! $peserta->dataDiri?->tanggal_lahir) {
                continue;
            }

            $bobotNilai = (int) Setting::get('seleksi_bobot_nilai', 70);
            $bobotUmur  = (int) Setting::get('seleksi_bobot_umur', 30);
            $umurMin    = (int) Setting::get('seleksi_umur_min', 15);
            $umurMax    = (int) Setting::get('seleksi_umur_max', 21);

            $skorNilai = $this->hitungSkorNilai($peserta->nilai);
            $tahun     = $peserta->tahunPenerimaan?->tahun ?? now()->year;
            $referensi = Carbon::createFromDate($tahun, 7, 1);
            $umur      = (int) $peserta->dataDiri->tanggal_lahir->diffInYears($referensi);
            $skorUmur  = $this->hitungSkorUmur($umur, $umurMin, $umurMax);
            $skorTotal = ($skorNilai * $bobotNilai / 100) + ($skorUmur * $bobotUmur / 100);

            PesertaSkor::updateOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'jurusan_id'           => $peserta->jurusan_id,
                    'jalur_id'             => $peserta->jalur_id,
                    'skor_nilai'           => round($skorNilai, 2),
                    'skor_umur'            => round($skorUmur, 2),
                    'skor_total'           => round($skorTotal, 2),
                    'bobot_nilai_snapshot' => $bobotNilai,
                    'bobot_umur_snapshot'  => $bobotUmur,
                    'umur_saat_dihitung'   => $umur,
                    'calculated_at'        => now(),
                ]
            );

            $count++;
        }

        // Generate ranking untuk semua kombinasi jurusan+jalur dalam tahun ini
        $kombinasi = PesertaSkor::whereHas('peserta', fn ($q) =>
            $q->where('tahun_penerimaan_id', $tahunPenerimaanId)
        )
        ->whereNotNull('jurusan_id')
        ->whereNotNull('jalur_id')
        ->select('jurusan_id', 'jalur_id')
        ->distinct()
        ->get();

        foreach ($kombinasi as $combo) {
            $this->generateRanking($combo->jurusan_id, $combo->jalur_id, $tahunPenerimaanId);
        }

        return $count;
    }

    public function generateRanking(int $jurusanId, int $jalurId, int $tahunPenerimaanId): void
    {
        $skors = PesertaSkor::where('jurusan_id', $jurusanId)
            ->where('jalur_id', $jalurId)
            ->whereHas('peserta', fn ($q) =>
                $q->where('tahun_penerimaan_id', $tahunPenerimaanId)
                  ->where('status_formulir', 'submitted')
            )
            ->orderByDesc('skor_total')
            ->get();

        foreach ($skors as $index => $skor) {
            $skor->update(['ranking' => $index + 1]);
        }
    }

    public function rekomendasiHasil(int $jurusanId, int $jalurId, int $tahunPenerimaanId): Collection
    {
        $jurusan = Jurusan::findOrFail($jurusanId);
        $jalur   = JalurPendaftaran::findOrFail($jalurId);

        $kuota   = (int) floor($jurusan->kapasitas * $jalur->persentase_kuota / 100);
        // Cadangan maksimal 10% dari kuota
        $batasCadangan = $kuota + (int) ceil($kuota * 0.1);

        return PesertaSkor::with('peserta.dataDiri')
            ->where('jurusan_id', $jurusanId)
            ->where('jalur_id', $jalurId)
            ->whereHas('peserta', fn ($q) =>
                $q->where('tahun_penerimaan_id', $tahunPenerimaanId)
                  ->where('status_formulir', 'submitted')
            )
            ->orderByDesc('skor_total')
            ->get()
            ->values()
            ->map(function ($skor, $index) use ($kuota, $batasCadangan) {
                $rank = $index + 1;
                $skor->rekomendasi_status = match (true) {
                    $rank <= $kuota         => 'lolos',
                    $rank <= $batasCadangan => 'cadangan',
                    default                 => 'tidak_lolos',
                };
                $skor->kuota_posisi = $rank;
                return $skor;
            });
    }

    private function hitungSkorNilai(Collection $nilaiCollection): float
    {
        $aktifIds = MataPelajaran::aktif()->pluck('id')->all();

        $filtered = $nilaiCollection->filter(
            fn ($n) => in_array($n->mata_pelajaran_id, $aktifIds) && $n->nilai !== null
        );

        if ($filtered->isEmpty()) {
            return 0.0;
        }

        // Avg per mata pelajaran (semua semester), lalu avg antar mapel
        $avgPerMapel = $filtered
            ->groupBy('mata_pelajaran_id')
            ->map(fn ($rows) => $rows->avg('nilai'));

        return (float) $avgPerMapel->avg();
    }

    private function hitungSkorUmur(int $umur, int $umurMin, int $umurMax): float
    {
        if ($umurMax <= $umurMin) {
            return 0.0;
        }

        $skor = (($umur - $umurMin) / ($umurMax - $umurMin)) * 100;

        return (float) max(0.0, min(100.0, $skor));
    }
}
