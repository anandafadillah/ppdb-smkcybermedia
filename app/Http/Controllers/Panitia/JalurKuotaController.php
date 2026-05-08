<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\JalurPendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JalurKuotaController extends Controller
{
    public function update(Request $request, JalurPendaftaran $jalurPendaftaran): RedirectResponse
    {
        $request->validate([
            'persentase_kuota' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $totalLain = JalurPendaftaran::where('tahun_penerimaan_id', $jalurPendaftaran->tahun_penerimaan_id)
            ->where('id', '!=', $jalurPendaftaran->id)
            ->sum('persentase_kuota');

        $jalurPendaftaran->update(['persentase_kuota' => $request->persentase_kuota]);

        $redirect = redirect()->route('panitia.dashboard');

        if (($totalLain + $request->persentase_kuota) > 100) {
            return $redirect->with('warning', 'Total persentase kuota semua jalur melebihi 100%. Harap sesuaikan kembali.');
        }

        return $redirect->with('success', 'Persentase kuota berhasil diperbarui.');
    }
}
