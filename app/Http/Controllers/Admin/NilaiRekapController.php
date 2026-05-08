<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class NilaiRekapController extends Controller
{
    private function queryPeserta(Request $request)
    {
        $tahun = TahunPenerimaan::where('is_active', true)->first();

        $query = Peserta::with(['user', 'dataDiri', 'jalur', 'jurusan', 'nilai.mataPelajaran'])
            ->when($tahun, fn ($q) => $q->where('tahun_penerimaan_id', $tahun->id));

        if ($jalurId = $request->input('jalur_id')) {
            $query->where('jalur_id', $jalurId);
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $tahun       = TahunPenerimaan::where('is_active', true)->first();
        $pesertaList = $this->queryPeserta($request)->get();
        $mapelList   = MataPelajaran::aktif()->orderBy('nama')->get();
        $jalurList   = $tahun ? JalurPendaftaran::where('tahun_penerimaan_id', $tahun->id)->get() : collect();
        $jurusanList = Jurusan::orderBy('nama')->get();

        return view('admin.nilai-rekap.index', compact('pesertaList', 'mapelList', 'jalurList', 'jurusanList'));
    }

    public function export(Request $request)
    {
        $mapelList   = MataPelajaran::aktif()->orderBy('nama')->get();
        $pesertaList = $this->queryPeserta($request)->get();

        $data = $pesertaList->map(function ($p) use ($mapelList) {
            $row = [
                'No Pendaftaran' => $p->no_pendaftaran ?? '-',
                'Nama Lengkap'   => $p->dataDiri?->nama_lengkap ?? $p->user?->name ?? '-',
                'Jalur'          => $p->jalur?->nama ?? '-',
                'Jurusan'        => $p->jurusan?->nama ?? '-',
            ];

            $nilaiMap = $p->nilai->groupBy('mata_pelajaran_id');

            foreach ($mapelList as $mapel) {
                $nilaiMapel = $nilaiMap->get($mapel->id, collect());
                for ($s = 1; $s <= 5; $s++) {
                    $n = $nilaiMapel->firstWhere('semester', $s);
                    $row["{$mapel->nama} Sem{$s}"] = $n?->nilai ?? '';
                }
            }

            return $row;
        });

        return (new FastExcel($data))->download('rekap_nilai.xlsx');
    }
}
