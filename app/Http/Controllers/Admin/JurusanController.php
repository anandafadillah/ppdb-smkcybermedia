<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusan = Jurusan::orderBy('kode')->get();

        return view('admin.jurusan.index', compact('jurusan'));
    }

    public function create()
    {
        return view('admin.jurusan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'      => ['required', 'string', 'max:10', 'unique:jurusan,kode'],
            'nama'      => ['required', 'string', 'max:255'],
            'kapasitas' => ['nullable', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Jurusan::create($data);

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $data = $request->validate([
            'kode'      => ['required', 'string', 'max:10', 'unique:jurusan,kode,' . $jurusan->id],
            'nama'      => ['required', 'string', 'max:255'],
            'kapasitas' => ['nullable', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $jurusan->update($data);

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        if ($jurusan->peserta()->exists()) {
            return redirect()->route('admin.jurusan.index')
                ->with('error', 'Jurusan tidak dapat dihapus karena sudah dipilih oleh peserta.');
        }

        $jurusan->delete();

        return redirect()->route('admin.jurusan.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
