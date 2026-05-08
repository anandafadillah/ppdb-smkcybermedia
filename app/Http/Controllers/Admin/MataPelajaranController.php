<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mataPelajaran = MataPelajaran::orderBy('nama')->get();

        return view('admin.mata-pelajaran.index', compact('mataPelajaran'));
    }

    public function create()
    {
        return view('admin.mata-pelajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:mata_pelajaran,nama',
        ]);

        MataPelajaran::create($validated);

        return redirect('/admin/mata-pelajaran')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata-pelajaran.edit', compact('mataPelajaran'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:mata_pelajaran,nama,' . $mataPelajaran->id,
        ]);

        $mataPelajaran->update($validated);

        return redirect('/admin/mata-pelajaran')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        if ($mataPelajaran->pesertaNilai()->exists()) {
            return redirect('/admin/mata-pelajaran')
                ->with('error', 'Mata pelajaran tidak dapat dihapus karena sudah memiliki data nilai peserta.');
        }

        $mataPelajaran->delete();

        return redirect('/admin/mata-pelajaran')->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function toggleAktif(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->update(['is_active' => !$mataPelajaran->is_active]);

        return redirect('/admin/mata-pelajaran')->with('success', 'Status mata pelajaran diperbarui.');
    }
}
