<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunPenerimaan;
use App\Services\TahunPenerimaanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunPenerimaanController extends Controller
{
    public function __construct(private TahunPenerimaanService $service) {}

    public function index(): View
    {
        $tahunPenerimaan = TahunPenerimaan::latest()->get();

        return view('admin.tahun-penerimaan.index', compact('tahunPenerimaan'));
    }

    public function create(): View
    {
        return view('admin.tahun-penerimaan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun' => ['required', 'string', 'max:9', 'unique:tahun_penerimaan,tahun'],
            'label' => ['required', 'string', 'max:100'],
        ]);

        $this->service->create($data);

        return redirect()->route('admin.tahun-penerimaan.index')
            ->with('success', 'Tahun penerimaan berhasil ditambahkan.');
    }

    public function edit(TahunPenerimaan $tahunPenerimaan): View
    {
        return view('admin.tahun-penerimaan.edit', compact('tahunPenerimaan'));
    }

    public function update(Request $request, TahunPenerimaan $tahunPenerimaan): RedirectResponse
    {
        $data = $request->validate([
            'tahun' => ['required', 'string', 'max:9', 'unique:tahun_penerimaan,tahun,' . $tahunPenerimaan->id],
            'label' => ['required', 'string', 'max:100'],
        ]);

        $this->service->update($tahunPenerimaan, $data);

        return redirect()->route('admin.tahun-penerimaan.index')
            ->with('success', 'Tahun penerimaan berhasil diperbarui.');
    }

    public function destroy(TahunPenerimaan $tahunPenerimaan): RedirectResponse
    {
        try {
            $this->service->delete($tahunPenerimaan);

            return redirect()->route('admin.tahun-penerimaan.index')
                ->with('success', 'Tahun penerimaan berhasil dihapus.');
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.tahun-penerimaan.index')
                ->with('error', $e->getMessage());
        }
    }

    public function activate(TahunPenerimaan $tahunPenerimaan): RedirectResponse
    {
        $this->service->activate($tahunPenerimaan);

        return redirect()->route('admin.tahun-penerimaan.index')
            ->with('success', 'Tahun penerimaan berhasil diaktifkan.');
    }
}
