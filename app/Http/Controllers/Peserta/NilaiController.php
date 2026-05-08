<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\Peserta;
use App\Models\PesertaNilai;
use App\Models\TahunPenerimaan;
use App\Services\FormConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function __construct(private FormConfigService $formConfigService) {}

    private function getPeserta(): ?Peserta
    {
        $tahun = TahunPenerimaan::where('is_active', true)->first();
        if (!$tahun) {
            return null;
        }

        return Peserta::where('user_id', Auth::id())
            ->where('tahun_penerimaan_id', $tahun->id)
            ->first();
    }

    public function index()
    {
        $tahun         = TahunPenerimaan::where('is_active', true)->first();
        $peserta       = $this->getPeserta();
        $config        = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;
        $mataPelajaran = MataPelajaran::aktif()->orderBy('nama')->get();

        $nilaiByMapelSemester = [];
        if ($peserta) {
            $peserta->load('nilai.mataPelajaran');
            foreach ($peserta->nilai as $n) {
                $nilaiByMapelSemester[$n->mata_pelajaran_id][$n->semester] = $n->nilai;
            }
        }

        return view('peserta.nilai.index', compact('peserta', 'mataPelajaran', 'nilaiByMapelSemester', 'config'));
    }

    public function store(Request $request)
    {
        $peserta = $this->getPeserta();

        if (!$peserta) {
            return back()->with('error', 'Tidak ada tahun penerimaan aktif.');
        }

        if ($peserta->uploadTerkunci()) {
            return redirect('/peserta/nilai')->with('error', 'Input nilai terkunci. Data sedang dalam proses verifikasi.');
        }

        $nilaiInput = $request->input('nilai', []);

        $mapelAktifIds = MataPelajaran::aktif()->pluck('id')->toArray();

        foreach ($nilaiInput as $mapelId => $semesters) {
            if (!in_array($mapelId, $mapelAktifIds)) {
                continue;
            }

            foreach ($semesters as $semester => $nilai) {
                if ($nilai === null || $nilai === '') {
                    continue;
                }

                $nilaiFloat = (float) $nilai;
                if ($nilaiFloat < 0 || $nilaiFloat > 100) {
                    continue;
                }

                PesertaNilai::updateOrCreate(
                    [
                        'peserta_id'        => $peserta->id,
                        'mata_pelajaran_id' => $mapelId,
                        'semester'          => (int) $semester,
                    ],
                    ['nilai' => $nilaiFloat]
                );
            }
        }

        return redirect('/peserta/nilai')->with('success', 'Nilai rapor berhasil disimpan.');
    }
}
