<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\TahunPenerimaan;
use App\Services\SeleksiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeleksiController extends Controller
{
    public function __construct(private SeleksiService $seleksiService) {}

    public function index(Request $request): View
    {
        $tahunAktif  = TahunPenerimaan::where('is_active', true)->first();
        $jurusanList = Jurusan::orderBy('nama')->get();
        $jalurList   = $tahunAktif
            ? JalurPendaftaran::where('tahun_penerimaan_id', $tahunAktif->id)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get()
            : collect();

        $jurusanId = $request->integer('jurusan_id') ?: $jurusanList->first()?->id;
        $jalurId   = $request->integer('jalur_id')   ?: $jalurList->first()?->id;

        $ranking         = collect();
        $kuota           = 0;
        $batasCadangan   = 0;
        $selectedJurusan = null;
        $selectedJalur   = null;

        if ($tahunAktif && $jurusanId && $jalurId) {
            $selectedJurusan = Jurusan::find($jurusanId);
            $selectedJalur   = JalurPendaftaran::find($jalurId);

            if ($selectedJurusan && $selectedJalur) {
                $kuota         = (int) floor($selectedJurusan->kapasitas * $selectedJalur->persentase_kuota / 100);
                $batasCadangan = $kuota + (int) ceil($kuota * 0.1);
                $ranking       = $this->seleksiService->rekomendasiHasil($jurusanId, $jalurId, $tahunAktif->id);
            }
        }

        return view('admin.seleksi.index', compact(
            'tahunAktif', 'jurusanList', 'jalurList',
            'jurusanId', 'jalurId', 'ranking',
            'kuota', 'batasCadangan', 'selectedJurusan', 'selectedJalur'
        ));
    }

    public function hitungSemua(): RedirectResponse
    {
        $tahunAktif = TahunPenerimaan::where('is_active', true)->firstOrFail();
        $count      = $this->seleksiService->hitungSkorSemua($tahunAktif->id);

        return back()->with('success', "Perhitungan selesai. {$count} peserta berhasil dihitung skornya.");
    }

    public function terapkanHasil(Request $request): RedirectResponse
    {
        $request->validate([
            'jurusan_id' => ['required', 'exists:jurusan,id'],
            'jalur_id'   => ['required', 'exists:jalur_pendaftaran,id'],
        ]);

        $tahunAktif  = TahunPenerimaan::where('is_active', true)->firstOrFail();
        $rekomendasi = $this->seleksiService->rekomendasiHasil(
            $request->integer('jurusan_id'),
            $request->integer('jalur_id'),
            $tahunAktif->id
        );

        foreach ($rekomendasi as $skor) {
            $skor->peserta->updateQuietly(['status_hasil' => $skor->rekomendasi_status]);
        }

        return back()->with('success', "Status hasil {$rekomendasi->count()} peserta berhasil diterapkan.");
    }
}
