<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\AsalSekolah;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\Setting;
use App\Models\TahunPenerimaan;
use App\Services\FormConfigService;
use App\Services\PendaftaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormulirController extends Controller
{
    public function __construct(
        private PendaftaranService $service,
        private FormConfigService $formConfigService,
    ) {}

    private function getTahunAktif(): ?TahunPenerimaan
    {
        return TahunPenerimaan::where('is_active', true)->first();
    }

    private function getOrCreatePeserta(): ?Peserta
    {
        $tahun = $this->getTahunAktif();
        if (!$tahun) return null;

        return Peserta::firstOrCreate(
            ['user_id' => Auth::id(), 'tahun_penerimaan_id' => $tahun->id],
            ['status_formulir' => 'draft', 'status_verifikasi' => 'belum_diverifikasi',
             'status_hasil' => 'belum', 'status_daftar_ulang' => 'belum']
        );
    }

    public function index()
    {
        $tahun   = $this->getTahunAktif();
        $peserta = $this->getOrCreatePeserta();
        $config  = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;

        if ($peserta) {
            $peserta->load('jalur', 'dataDiri', 'dataAyah', 'dataIbu', 'dataWali', 'dataAlamat', 'dataKip');
        }

        $jurusan     = Jurusan::orderBy('nama')->get();
        $jalur       = $peserta?->tahunPenerimaan
            ? JalurPendaftaran::where('tahun_penerimaan_id', $peserta->tahun_penerimaan_id)
                ->where('is_active', true)->orderBy('nama')->get()
            : collect();
        $asalSekolah = AsalSekolah::orderBy('nama')->get();
        $keterangan  = Setting::get('keterangan_formulir', '');
        $activeTab   = session('active_tab', 'data-diri');

        return view('peserta.formulir.index', compact(
            'peserta', 'jurusan', 'jalur', 'asalSekolah', 'activeTab', 'config', 'keterangan'
        ));
    }

    public function store(Request $request)
    {
        $tahun   = $this->getTahunAktif();
        $peserta = $this->getOrCreatePeserta();

        if (!$peserta) {
            return back()->with('error', 'Tidak ada tahun penerimaan aktif.');
        }

        if ($peserta->sudahSubmit()) {
            return redirect('/peserta/formulir')->with('error', 'Formulir sudah disubmit dan tidak dapat diubah.');
        }

        $config = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;

        $rules = [
            'jalur_id'            => 'required|exists:jalur_pendaftaran,id',
            'jurusan_id'          => 'required|exists:jurusan,id',
            'nama_lengkap'        => 'required|string|max:255',
            'jenis_kelamin'       => 'required|in:L,P',
            'tempat_lahir'        => 'nullable|string|max:100',
            'tanggal_lahir'       => 'nullable|date',
            'agama'               => 'nullable|string|max:50',
            'no_hp'               => 'nullable|string|max:20',
            'tinggi_badan'        => 'nullable|integer|min:50|max:250',
            'berat_badan'         => 'nullable|integer|min:10|max:200',
            'jumlah_saudara'      => 'nullable|integer|min:0|max:20',
            'asal_sekolah_id'     => 'nullable|exists:asal_sekolah,id',
            'asal_sekolah_custom' => 'nullable|string|max:255',
        ];

        // Field yang nonaktif tidak divalidasi
        if ($config) {
            if (!$config->isFieldActive('diri_tempat_lahir'))  unset($rules['tempat_lahir']);
            if (!$config->isFieldActive('diri_tanggal_lahir')) unset($rules['tanggal_lahir']);
            if (!$config->isFieldActive('diri_agama'))         unset($rules['agama']);
            if (!$config->isFieldActive('diri_no_hp'))         unset($rules['no_hp']);
            if (!$config->isFieldActive('diri_tinggi_badan'))  unset($rules['tinggi_badan']);
            if (!$config->isFieldActive('diri_berat_badan'))   unset($rules['berat_badan']);
            if (!$config->isFieldActive('diri_jumlah_saudara'))unset($rules['jumlah_saudara']);
            if (!$config->isFieldActive('diri_asal_sekolah')) {
                unset($rules['asal_sekolah_id']);
                unset($rules['asal_sekolah_custom']);
            }
        }

        $validated = $request->validate($rules);

        $peserta->update([
            'jalur_id'   => $validated['jalur_id'],
            'jurusan_id' => $validated['jurusan_id'],
        ]);

        $peserta->dataDiri()->updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nama_lengkap'        => $validated['nama_lengkap'],
                'jenis_kelamin'       => $validated['jenis_kelamin'],
                'tempat_lahir'        => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir'       => $validated['tanggal_lahir'] ?? null,
                'agama'               => $validated['agama'] ?? null,
                'no_hp'               => $validated['no_hp'] ?? null,
                'tinggi_badan'        => $validated['tinggi_badan'] ?? null,
                'berat_badan'         => $validated['berat_badan'] ?? null,
                'jumlah_saudara'      => $validated['jumlah_saudara'] ?? null,
                'asal_sekolah_id'     => $validated['asal_sekolah_id'] ?? null,
                'asal_sekolah_custom' => $validated['asal_sekolah_custom'] ?? null,
            ]
        );

        $this->service->generateNomorPendaftaran($peserta->fresh(), 'daring');

        // Kunci form config saat peserta pertama submit
        if ($config) {
            $this->formConfigService->lock($config);
        }

        return redirect('/peserta/formulir')->with('success', 'Formulir berhasil disubmit. Nomor pendaftaran Anda: ' . $peserta->fresh()->no_pendaftaran);
    }
}
