<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsalSekolah;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class AsalSekolahController extends Controller
{
    public function index(Request $request)
    {
        $query = AsalSekolah::query()->orderBy('nama');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%");
            });
        }

        $asalSekolah = $query->paginate(20)->withQueryString();

        return view('admin.asal-sekolah.index', compact('asalSekolah'));
    }

    public function create()
    {
        return view('admin.asal-sekolah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'npsn'      => 'required|string|max:8|unique:asal_sekolah,npsn',
            'nama'      => 'required|string|max:255',
            'alamat'    => 'nullable|string',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'status'    => 'required|in:negeri,swasta',
        ]);

        AsalSekolah::create($validated);

        return redirect('/admin/asal-sekolah')->with('success', 'Asal sekolah berhasil ditambahkan.');
    }

    public function edit(AsalSekolah $asalSekolah)
    {
        return view('admin.asal-sekolah.edit', compact('asalSekolah'));
    }

    public function update(Request $request, AsalSekolah $asalSekolah)
    {
        $validated = $request->validate([
            'npsn'      => 'required|string|max:8|unique:asal_sekolah,npsn,' . $asalSekolah->id,
            'nama'      => 'required|string|max:255',
            'alamat'    => 'nullable|string',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'status'    => 'required|in:negeri,swasta',
        ]);

        $asalSekolah->update($validated);

        return redirect('/admin/asal-sekolah')->with('success', 'Asal sekolah berhasil diperbarui.');
    }

    public function destroy(AsalSekolah $asalSekolah)
    {
        $asalSekolah->delete();

        return redirect('/admin/asal-sekolah')->with('success', 'Asal sekolah berhasil dihapus.');
    }

    public function export()
    {
        $data = AsalSekolah::orderBy('nama')->get()->map(fn ($s) => [
            'NPSN'         => $s->npsn,
            'Nama Sekolah' => $s->nama,
            'Alamat'       => $s->alamat ?? '',
            'Kelurahan'    => $s->kelurahan ?? '',
            'Kecamatan'    => $s->kecamatan ?? '',
            'Status'       => $s->status,
        ]);

        return (new FastExcel($data))->download('asal_sekolah.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);

        $path = $request->file('file')->getPathname();

        $rows = (new FastExcel)->import($path);
        $first = $rows->first() ?? [];
        $required = ['NPSN', 'Nama Sekolah', 'Status'];
        foreach ($required as $col) {
            if (!array_key_exists($col, $first)) {
                return back()->withErrors(['file' => "Kolom '$col' tidak ditemukan di file Excel."]);
            }
        }

        $errors   = [];
        $imported = 0;

        (new FastExcel)->import($path, function ($line) use (&$errors, &$imported) {
            $npsn = trim($line['NPSN'] ?? '');
            $nama = trim($line['Nama Sekolah'] ?? '');
            $status = strtolower(trim($line['Status'] ?? ''));

            if (!$npsn || !$nama || !$status) {
                $errors[] = "NPSN '$npsn': field wajib (NPSN/Nama Sekolah/Status) kosong.";
                return null;
            }
            if (!in_array($status, ['negeri', 'swasta'])) {
                $errors[] = "NPSN '$npsn': status harus 'negeri' atau 'swasta'.";
                return null;
            }

            try {
                AsalSekolah::updateOrCreate(
                    ['npsn' => $npsn],
                    [
                        'nama'      => $nama,
                        'alamat'    => $line['Alamat'] ?? null,
                        'kelurahan' => $line['Kelurahan'] ?? null,
                        'kecamatan' => $line['Kecamatan'] ?? null,
                        'status'    => $status,
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "NPSN '$npsn': " . $e->getMessage();
            }

            return null;
        });

        $msg = "$imported data berhasil diimpor.";
        if ($errors) {
            $msg .= ' ' . count($errors) . ' baris bermasalah.';
            return redirect('/admin/asal-sekolah')->with('warning', $msg)->with('import_errors', $errors);
        }

        return redirect('/admin/asal-sekolah')->with('success', $msg);
    }
}
