<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Services\FormConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeluargaController extends Controller
{
    public function __construct(
        private FormConfigService $formConfigService,
    ) {}

    private function getTahunAktif(): ?TahunPenerimaan
    {
        return TahunPenerimaan::where('is_active', true)->first();
    }

    private function getPeserta(): ?Peserta
    {
        $tahun = $this->getTahunAktif();
        if (!$tahun) {
            return null;
        }

        return Peserta::where('user_id', Auth::id())
            ->where('tahun_penerimaan_id', $tahun->id)
            ->with('jalur', 'dataAyah', 'dataIbu', 'dataWali', 'dataAlamat', 'dataKip')
            ->first();
    }

    public function index()
    {
        $tahun   = $this->getTahunAktif();
        $peserta = $this->getPeserta();
        $config  = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;

        return view('peserta.keluarga.index', compact('peserta', 'config'));
    }

    public function store(Request $request)
    {
        $tahun   = $this->getTahunAktif();
        $peserta = $this->getPeserta();

        if (!$peserta) {
            return back()->with('error', 'Tidak ada tahun penerimaan aktif.');
        }

        $config = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;

        $rules = [
            'nama_ayah'                  => 'nullable|string|max:255',
            'nik_ayah'                   => 'nullable|string|max:16',
            'tahun_lahir_ayah'           => 'nullable|integer|min:1900|max:' . date('Y'),
            'pendidikan_ayah'            => 'nullable|string|max:50',
            'pekerjaan_ayah'             => 'nullable|string|max:100',
            'penghasilan_ayah'           => 'nullable|string|max:50',
            'ketidakmampuan_khusus_ayah' => 'nullable|string|max:100',
            'nama_ibu'                   => 'nullable|string|max:255',
            'nik_ibu'                    => 'nullable|string|max:16',
            'tahun_lahir_ibu'            => 'nullable|integer|min:1900|max:' . date('Y'),
            'pendidikan_ibu'             => 'nullable|string|max:50',
            'pekerjaan_ibu'              => 'nullable|string|max:100',
            'penghasilan_ibu'            => 'nullable|string|max:50',
            'ketidakmampuan_khusus_ibu'  => 'nullable|string|max:100',
            'nama_wali'                  => 'nullable|string|max:255',
            'nik_wali'                   => 'nullable|string|max:16',
            'tahun_lahir_wali'           => 'nullable|integer|min:1900|max:' . date('Y'),
            'pekerjaan_wali'             => 'nullable|string|max:100',
            'penghasilan_wali'           => 'nullable|string|max:50',
            'rt'                         => 'nullable|string|max:3',
            'rw'                         => 'nullable|string|max:3',
            'kelurahan'                  => 'nullable|string|max:100',
            'kecamatan'                  => 'nullable|string|max:100',
            'kota'                       => 'nullable|string|max:100',
            'latitude'                   => 'nullable|numeric',
            'longitude'                  => 'nullable|numeric',
            'jarak_tempat_tinggal'       => 'nullable|integer|min:0',
            'no_kip'                     => 'nullable|string|max:30',
            'no_kps_pkh'                 => 'nullable|string|max:30',
            'nama_di_kip'                => 'nullable|string|max:255',
            'terima_kip'                 => 'nullable|boolean',
        ];

        if ($config) {
            // Ayah
            if (!$config->isFieldActive('ayah_nik'))            unset($rules['nik_ayah']);
            if (!$config->isFieldActive('ayah_tahun_lahir'))    unset($rules['tahun_lahir_ayah']);
            if (!$config->isFieldActive('ayah_pendidikan'))     unset($rules['pendidikan_ayah']);
            if (!$config->isFieldActive('ayah_pekerjaan'))      unset($rules['pekerjaan_ayah']);
            if (!$config->isFieldActive('ayah_penghasilan'))    unset($rules['penghasilan_ayah']);
            if (!$config->isFieldActive('ayah_ketidakmampuan')) unset($rules['ketidakmampuan_khusus_ayah']);

            // Ibu
            if (!$config->isFieldActive('ibu_nik'))            unset($rules['nik_ibu']);
            if (!$config->isFieldActive('ibu_tahun_lahir'))    unset($rules['tahun_lahir_ibu']);
            if (!$config->isFieldActive('ibu_pendidikan'))     unset($rules['pendidikan_ibu']);
            if (!$config->isFieldActive('ibu_pekerjaan'))      unset($rules['pekerjaan_ibu']);
            if (!$config->isFieldActive('ibu_penghasilan'))    unset($rules['penghasilan_ibu']);
            if (!$config->isFieldActive('ibu_ketidakmampuan')) unset($rules['ketidakmampuan_khusus_ibu']);

            // Wali — seluruh section
            if (!$config->isFieldActive('data_wali')) {
                unset($rules['nama_wali'], $rules['nik_wali'], $rules['tahun_lahir_wali'],
                      $rules['pekerjaan_wali'], $rules['penghasilan_wali']);
            } else {
                if (!$config->isFieldActive('wali_nik'))         unset($rules['nik_wali']);
                if (!$config->isFieldActive('wali_tahun_lahir')) unset($rules['tahun_lahir_wali']);
                if (!$config->isFieldActive('wali_pekerjaan'))   unset($rules['pekerjaan_wali']);
                if (!$config->isFieldActive('wali_penghasilan')) unset($rules['penghasilan_wali']);
            }

            // Alamat
            if (!$config->isFieldActive('alamat_koordinat')) {
                unset($rules['latitude'], $rules['longitude']);
            }
            if (!$config->isFieldActive('alamat_jarak')) unset($rules['jarak_tempat_tinggal']);

            // KIP — seluruh section
            if (!$config->isFieldActive('data_kip')) {
                unset($rules['no_kip'], $rules['no_kps_pkh'], $rules['nama_di_kip'], $rules['terima_kip']);
            } else {
                if (!$config->isFieldActive('kip_no_kip'))      unset($rules['no_kip']);
                if (!$config->isFieldActive('kip_no_kps_pkh'))  unset($rules['no_kps_pkh']);
                if (!$config->isFieldActive('kip_nama_di_kip')) unset($rules['nama_di_kip']);
                if (!$config->isFieldActive('kip_terima'))      unset($rules['terima_kip']);
            }
        }

        $validated = $request->validate($rules);

        $peserta->dataAyah()->updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nama'                 => $validated['nama_ayah'] ?? null,
                'nik'                  => $validated['nik_ayah'] ?? null,
                'tahun_lahir'          => $validated['tahun_lahir_ayah'] ?? null,
                'pendidikan'           => $validated['pendidikan_ayah'] ?? null,
                'pekerjaan'            => $validated['pekerjaan_ayah'] ?? null,
                'penghasilan'          => $validated['penghasilan_ayah'] ?? null,
                'ketidakmampuan_khusus' => $validated['ketidakmampuan_khusus_ayah'] ?? null,
            ]
        );

        $peserta->dataIbu()->updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nama'                 => $validated['nama_ibu'] ?? null,
                'nik'                  => $validated['nik_ibu'] ?? null,
                'tahun_lahir'          => $validated['tahun_lahir_ibu'] ?? null,
                'pendidikan'           => $validated['pendidikan_ibu'] ?? null,
                'pekerjaan'            => $validated['pekerjaan_ibu'] ?? null,
                'penghasilan'          => $validated['penghasilan_ibu'] ?? null,
                'ketidakmampuan_khusus' => $validated['ketidakmampuan_khusus_ibu'] ?? null,
            ]
        );

        $waliAktif = !$config || $config->isFieldActive('data_wali');
        if ($waliAktif && !empty($validated['nama_wali'])) {
            $peserta->dataWali()->updateOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'nama'        => $validated['nama_wali'],
                    'nik'         => $validated['nik_wali'] ?? null,
                    'tahun_lahir' => $validated['tahun_lahir_wali'] ?? null,
                    'pekerjaan'   => $validated['pekerjaan_wali'] ?? null,
                    'penghasilan' => $validated['penghasilan_wali'] ?? null,
                ]
            );
        }

        $peserta->dataAlamat()->updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'rt'                   => $validated['rt'] ?? null,
                'rw'                   => $validated['rw'] ?? null,
                'kelurahan'            => $validated['kelurahan'] ?? null,
                'kecamatan'            => $validated['kecamatan'] ?? null,
                'kota'                 => $validated['kota'] ?? null,
                'latitude'             => $validated['latitude'] ?? null,
                'longitude'            => $validated['longitude'] ?? null,
                'jarak_tempat_tinggal' => $validated['jarak_tempat_tinggal'] ?? null,
            ]
        );

        $kipAktif = !$config || $config->isFieldActive('data_kip');
        if ($kipAktif && $peserta->jalur?->isAfirmasi()) {
            $peserta->dataKip()->updateOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'no_kip'      => $validated['no_kip'] ?? null,
                    'no_kps_pkh'  => $validated['no_kps_pkh'] ?? null,
                    'nama_di_kip' => $validated['nama_di_kip'] ?? null,
                    'terima_kip'  => $validated['terima_kip'] ?? false,
                ]
            );
        }

        return redirect()->route('peserta.formulir.index')
            ->with('success', 'Data keluarga berhasil disimpan.')
            ->with('active_tab', 'data-keluarga');
    }
}
