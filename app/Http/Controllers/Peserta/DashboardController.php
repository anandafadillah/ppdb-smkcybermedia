<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tahun   = TahunPenerimaan::where('is_active', true)->first();
        $peserta = $tahun
            ? Peserta::where('user_id', Auth::id())
                ->where('tahun_penerimaan_id', $tahun->id)
                ->with('dataDiri', 'jalur', 'jurusan', 'berkas', 'nilai')
                ->first()
            : null;

        $pengumuman = Pengumuman::published()->latest('tanggal_publish')->take(5)->get();

        return view('peserta.dashboard', compact('peserta', 'pengumuman'));
    }
}
