<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPenerimaan::where('is_active', true)->first();

        $baseQuery = fn() => Peserta::query()
            ->when($tahunAktif, fn($q) => $q->where('peserta.tahun_penerimaan_id', $tahunAktif->id));

        $perJalur = $baseQuery()
            ->join('jalur_pendaftaran', 'peserta.jalur_id', '=', 'jalur_pendaftaran.id')
            ->select('jalur_pendaftaran.nama as label', DB::raw('COUNT(*) as total'))
            ->groupBy('peserta.jalur_id', 'jalur_pendaftaran.nama')
            ->get();

        $perJurusan = $baseQuery()
            ->join('jurusan', 'peserta.jurusan_id', '=', 'jurusan.id')
            ->select('jurusan.nama as label', DB::raw('COUNT(*) as total'))
            ->groupBy('peserta.jurusan_id', 'jurusan.nama')
            ->get();

        $labelVerifikasi = [
            'belum_diverifikasi' => 'Belum Diverifikasi',
            'terverifikasi'      => 'Terverifikasi',
            'ditolak'            => 'Ditolak',
        ];
        $perStatusVerifikasi = $baseQuery()
            ->select('status_verifikasi as label', DB::raw('COUNT(*) as total'))
            ->groupBy('status_verifikasi')
            ->get()
            ->map(fn($item) => [
                'label' => $labelVerifikasi[$item->label] ?? $item->label,
                'total' => (int) $item->total,
            ])
            ->values();

        $labelHasil = [
            'belum'       => 'Belum Diproses',
            'lolos'       => 'Lolos',
            'tidak_lolos' => 'Tidak Lolos',
            'cadangan'    => 'Cadangan',
        ];
        $perStatusHasil = $baseQuery()
            ->select('status_hasil as label', DB::raw('COUNT(*) as total'))
            ->groupBy('status_hasil')
            ->get()
            ->map(fn($item) => [
                'label' => $labelHasil[$item->label] ?? $item->label,
                'total' => (int) $item->total,
            ])
            ->values();

        $totalPeserta = $baseQuery()->count();

        return view('admin.statistik.index', compact(
            'tahunAktif',
            'perJalur',
            'perJurusan',
            'perStatusVerifikasi',
            'perStatusHasil',
            'totalPeserta',
        ));
    }
}
