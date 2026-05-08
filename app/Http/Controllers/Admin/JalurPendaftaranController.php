<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JalurPendaftaran;
use App\Models\TahunPenerimaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JalurPendaftaranController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunPenerimaan::where('is_active', true)->first();
        $jalur      = $tahunAktif
            ? JalurPendaftaran::where('tahun_penerimaan_id', $tahunAktif->id)->orderBy('nama')->get()
            : collect();

        return view('admin.jalur-pendaftaran.index', compact('jalur', 'tahunAktif'));
    }

    public function create(): View
    {
        $tahunList = TahunPenerimaan::orderByDesc('tahun')->get();

        return view('admin.jalur-pendaftaran.create', compact('tahunList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun_penerimaan_id' => ['required', 'exists:tahun_penerimaan,id'],
            'nama'                => ['required', 'string', 'max:100'],
            'deskripsi'           => ['nullable', 'string'],
            'kode_awal_daring'    => ['nullable', 'string', 'max:20'],
            'kode_awal_luring'    => ['nullable', 'string', 'max:20'],
        ]);

        JalurPendaftaran::create($data + ['is_active' => true, 'persentase_kuota' => 0]);

        return redirect()->route('admin.jalur-pendaftaran.index')
            ->with('success', 'Jalur pendaftaran berhasil ditambahkan.');
    }

    public function edit(JalurPendaftaran $jalurPendaftaran): View
    {
        $tahunList = TahunPenerimaan::orderByDesc('tahun')->get();

        return view('admin.jalur-pendaftaran.edit', compact('jalurPendaftaran', 'tahunList'));
    }

    public function update(Request $request, JalurPendaftaran $jalurPendaftaran): RedirectResponse
    {
        $data = $request->validate([
            'nama'             => ['required', 'string', 'max:100'],
            'deskripsi'        => ['nullable', 'string'],
            'kode_awal_daring' => ['nullable', 'string', 'max:20'],
            'kode_awal_luring' => ['nullable', 'string', 'max:20'],
        ]);

        $jalurPendaftaran->update($data);

        return redirect()->route('admin.jalur-pendaftaran.index')
            ->with('success', 'Jalur pendaftaran berhasil diperbarui.');
    }

    public function toggleAktif(JalurPendaftaran $jalurPendaftaran): RedirectResponse
    {
        $jalurPendaftaran->update(['is_active' => !$jalurPendaftaran->is_active]);

        $status = $jalurPendaftaran->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.jalur-pendaftaran.index')
            ->with('success', "Jalur pendaftaran berhasil {$status}.");
    }

    public function destroy(JalurPendaftaran $jalurPendaftaran): RedirectResponse
    {
        $jalurPendaftaran->delete();

        return redirect()->route('admin.jalur-pendaftaran.index')
            ->with('success', 'Jalur pendaftaran berhasil dihapus.');
    }
}
