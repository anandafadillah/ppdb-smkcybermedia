<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\Peserta;
use App\Models\PesertaBerkas;
use App\Models\Setting;
use App\Models\TahunPenerimaan;
use App\Services\FormConfigService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class FormulirPdfController extends Controller
{
    public function __construct(private FormConfigService $formConfigService) {}

    public function downloadMine()
    {
        $tahun = TahunPenerimaan::where('is_active', true)->first();
        if (!$tahun) {
            abort(404, 'Tidak ada tahun penerimaan aktif.');
        }

        $peserta = Peserta::where('user_id', Auth::id())
            ->where('tahun_penerimaan_id', $tahun->id)
            ->first();

        if (!$peserta) {
            abort(404, 'Data pendaftaran tidak ditemukan.');
        }

        if ($peserta->status_verifikasi !== 'terverifikasi') {
            abort(403, 'Formulir hanya dapat diunduh setelah diverifikasi oleh panitia.');
        }

        return $this->buildPdf($peserta);
    }

    public function download(Peserta $peserta)
    {
        return $this->buildPdf($peserta);
    }

    private function buildPdf(Peserta $peserta)
    {
        $peserta->load([
            'jalur', 'jurusan', 'tahunPenerimaan',
            'dataDiri.asalSekolah', 'dataAyah', 'dataIbu', 'dataWali',
            'dataAlamat', 'dataKip', 'nilai.mataPelajaran', 'berkas',
        ]);

        $tahun = $peserta->tahunPenerimaan;
        $c     = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;

        // Susun baris nilai rapor
        $nilaiRows = collect();
        $mataPelajaran = MataPelajaran::aktif()->orderBy('nama')->get();
        $nilaiByMapelSemester = [];
        foreach ($peserta->nilai as $n) {
            $nilaiByMapelSemester[$n->mata_pelajaran_id][$n->semester] = $n->nilai;
        }
        foreach ($mataPelajaran as $mapel) {
            $semesters = $nilaiByMapelSemester[$mapel->id] ?? [];
            $vals = array_values(array_filter($semesters, fn($v) => $v !== null && $v !== ''));
            $nilaiRows->push([
                'mapel'     => $mapel->nama,
                'semesters' => $semesters,
                'rata'      => count($vals) > 0 ? array_sum($vals) / count($vals) : null,
            ]);
        }

        // Susun baris berkas
        $berkasByTipe = $peserta->berkas->keyBy('tipe_berkas');
        $berkasRows = collect();
        $tipeToConfig = [
            'foto_3x4'       => 'berkas_foto',
            'akta_kelahiran' => 'berkas_akta',
            'kartu_keluarga' => 'berkas_kk',
            'ktp_orangtua'   => 'berkas_ktp_ortu',
            'sktm'           => 'berkas_sktm',
            'kartu_pkh'      => 'berkas_pkh',
            'berkas_lainnya' => 'berkas_lainnya',
            'nilai_rapor'    => 'nilai_rapor',
        ];
        foreach (PesertaBerkas::tipeList() as $tipe => $label) {
            $configKey = $tipeToConfig[$tipe] ?? null;
            if ($c && $configKey && !$c->isFieldActive($configKey)) {
                continue;
            }
            $existing = $berkasByTipe->get($tipe);
            $berkasRows->push([
                'label'      => $label,
                'ada'        => (bool) $existing,
                'keterangan' => $existing?->keterangan,
            ]);
        }

        $namaSekolah = Setting::get('nama_sekolah', 'SMK Cyber Media Jakarta');
        $logoPath    = Setting::get('logo', null);

        $data = compact('peserta', 'c', 'nilaiRows', 'berkasRows', 'namaSekolah', 'logoPath');

        $filename = 'formulir-' . ($peserta->no_pendaftaran ?? 'peserta-' . $peserta->id) . '.pdf';

        return Pdf::loadView('pdf.formulir-peserta', $data)
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }
}
