<?php

namespace App\Http\Controllers;

use App\Models\FormConfig;
use App\Models\Setting;
use App\Models\TahunPenerimaan;
use App\Services\FormConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormConfigController extends Controller
{
    public function __construct(private FormConfigService $service) {}

    public function edit(TahunPenerimaan $tahunPenerimaan): View
    {
        $config       = $this->service->getOrCreate($tahunPenerimaan);
        $fieldGroups  = FormConfig::FIELD_GROUPS;
        $fixedFields  = FormConfig::FIXED_FIELDS;
        $keterangan   = Setting::get('keterangan_formulir', '');

        return view('form-config.edit', compact(
            'tahunPenerimaan', 'config', 'fieldGroups', 'fixedFields', 'keterangan'
        ));
    }

    public function update(Request $request, TahunPenerimaan $tahunPenerimaan): RedirectResponse
    {
        // Simpan keterangan formulir ke settings
        Setting::set('keterangan_formulir', $request->input('keterangan_formulir', ''));

        $config = $this->service->getOrCreate($tahunPenerimaan);

        try {
            $this->service->update($config, $request->input('fields', []));

            return back()->with('success', 'Konfigurasi formulir berhasil disimpan.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
