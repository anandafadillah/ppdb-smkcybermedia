<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPenerimaan::where('is_active', true)->first();

        $baseQuery = fn() => Peserta::query()
            ->when($tahunAktif, fn($q) => $q->where('peserta.tahun_penerimaan_id', $tahunAktif->id));

        $totalPeserta = $baseQuery()->count();

        $kpi = [
            'total'         => $totalPeserta,
            'terverifikasi' => $baseQuery()->where('status_verifikasi', 'terverifikasi')->count(),
            'lolos'         => $baseQuery()->where('status_hasil', 'lolos')->count(),
            'daftar_ulang'  => $baseQuery()->where('status_daftar_ulang', 'sudah')->count(),
        ];

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
        $perVerifikasi = $baseQuery()
            ->select('status_verifikasi as key', DB::raw('COUNT(*) as total'))
            ->groupBy('status_verifikasi')
            ->get()
            ->map(fn($r) => ['label' => $labelVerifikasi[$r->key] ?? $r->key, 'total' => (int) $r->total])
            ->values();

        $labelHasil = [
            'belum'       => 'Belum Diproses',
            'lolos'       => 'Lolos',
            'tidak_lolos' => 'Tidak Lolos',
            'cadangan'    => 'Cadangan',
        ];
        $perHasil = $baseQuery()
            ->select('status_hasil as key', DB::raw('COUNT(*) as total'))
            ->groupBy('status_hasil')
            ->get()
            ->map(fn($r) => ['label' => $labelHasil[$r->key] ?? $r->key, 'total' => (int) $r->total])
            ->values();

        $pengumuman = Pengumuman::published()->latest('tanggal_publish')->take(5)->get();

        return view('admin.dashboard', compact(
            'tahunAktif', 'totalPeserta', 'kpi',
            'perJalur', 'perJurusan', 'perVerifikasi', 'perHasil',
            'pengumuman',
        ));
    }
}
