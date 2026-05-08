<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsalSekolah;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Models\User;
use App\Services\FormConfigService;
use App\Services\PendaftaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Rap2hpoutre\FastExcel\FastExcel;

class PesertaController extends Controller
{
    public function __construct(
        private PendaftaranService $service,
        private FormConfigService $formConfigService,
    ) {}

    public function index(Request $request)
    {
        $tahun = TahunPenerimaan::where('is_active', true)->first();

        $query = Peserta::with(['user', 'dataDiri', 'jalur', 'jurusan'])
            ->when($tahun, fn ($q) => $q->where('tahun_penerimaan_id', $tahun->id));

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('dataDiri', fn ($dq) => $dq->where('nama_lengkap', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn ($uq) => $uq->where('nisn', 'like', "%{$search}%"))
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%");
            });
        }

        if ($jalurId = $request->input('jalur_id')) {
            $query->where('jalur_id', $jalurId);
        }

        if ($statusVerifikasi = $request->input('status_verifikasi')) {
            $query->where('status_verifikasi', $statusVerifikasi);
        }

        if ($statusHasil = $request->input('status_hasil')) {
            $query->where('status_hasil', $statusHasil);
        }

        $pesertaList = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $jalurList   = $tahun ? JalurPendaftaran::where('tahun_penerimaan_id', $tahun->id)->get() : collect();
        $jurusanList = Jurusan::orderBy('nama')->get();

        return view('admin.peserta.index', compact('pesertaList', 'jalurList', 'jurusanList'));
    }

    public function create()
    {
        $tahun   = TahunPenerimaan::where('is_active', true)->first();
        $jalur   = $tahun ? JalurPendaftaran::where('tahun_penerimaan_id', $tahun->id)->where('is_active', true)->get() : collect();
        $jurusan = Jurusan::orderBy('nama')->get();

        return view('admin.peserta.create', compact('jalur', 'jurusan'));
    }

    public function store(Request $request)
    {
        $tahun = TahunPenerimaan::where('is_active', true)->first();
        if (!$tahun) {
            return back()->with('error', 'Tidak ada tahun penerimaan aktif.');
        }

        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'nisn'       => 'required|digits:10|unique:users,nisn',
            'password'   => 'required|string|min:8',
            'jalur_id'   => 'required|exists:jalur_pendaftaran,id',
            'jurusan_id' => 'required|exists:jurusan,id',
        ]);

        $user = User::create([
            'name'      => $validated['nama'],
            'nisn'      => $validated['nisn'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'peserta',
            'is_active' => true,
        ]);

        $peserta = Peserta::create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $validated['jalur_id'],
            'jurusan_id'          => $validated['jurusan_id'],
            'status_formulir'     => 'draft',
            'status_verifikasi'   => 'belum_diverifikasi',
            'status_hasil'        => 'belum',
            'status_daftar_ulang' => 'belum',
        ]);

        $this->service->generateNomorPendaftaran($peserta->fresh()->load('jalur'), 'luring');

        return redirect($this->redirectBase($request) . '/peserta')
            ->with('success', "Peserta {$validated['nama']} berhasil ditambahkan.");
    }

    public function edit(Request $request, Peserta $peserta)
    {
        $tahun   = TahunPenerimaan::where('is_active', true)->first();
        $jalur   = $tahun ? JalurPendaftaran::where('tahun_penerimaan_id', $tahun->id)->where('is_active', true)->get() : collect();
        $jurusan = Jurusan::orderBy('nama')->get();
        $asalSekolah = AsalSekolah::orderBy('nama')->get();
        $config  = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;

        $peserta->load('jalur', 'dataDiri', 'dataAyah', 'dataIbu', 'dataWali', 'dataAlamat', 'dataKip');

        return view('admin.peserta.edit', compact('peserta', 'jalur', 'jurusan', 'asalSekolah', 'config'));
    }

    public function update(Request $request, Peserta $peserta)
    {
        $tahun  = TahunPenerimaan::where('is_active', true)->first();
        $config = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;

        $rules = [
            // Inti
            'jalur_id'            => 'required|exists:jalur_pendaftaran,id',
            'jurusan_id'          => 'required|exists:jurusan,id',
            // Data diri
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
            // Ayah
            'nama_ayah'                  => 'nullable|string|max:255',
            'nik_ayah'                   => 'nullable|string|max:16',
            'tahun_lahir_ayah'           => 'nullable|integer|min:1900|max:' . date('Y'),
            'pendidikan_ayah'            => 'nullable|string|max:50',
            'pekerjaan_ayah'             => 'nullable|string|max:100',
            'penghasilan_ayah'           => 'nullable|string|max:50',
            'ketidakmampuan_khusus_ayah' => 'nullable|string|max:100',
            // Ibu
            'nama_ibu'                   => 'nullable|string|max:255',
            'nik_ibu'                    => 'nullable|string|max:16',
            'tahun_lahir_ibu'            => 'nullable|integer|min:1900|max:' . date('Y'),
            'pendidikan_ibu'             => 'nullable|string|max:50',
            'pekerjaan_ibu'              => 'nullable|string|max:100',
            'penghasilan_ibu'            => 'nullable|string|max:50',
            'ketidakmampuan_khusus_ibu'  => 'nullable|string|max:100',
            // Wali
            'nama_wali'                  => 'nullable|string|max:255',
            'nik_wali'                   => 'nullable|string|max:16',
            'tahun_lahir_wali'           => 'nullable|integer|min:1900|max:' . date('Y'),
            'pekerjaan_wali'             => 'nullable|string|max:100',
            'penghasilan_wali'           => 'nullable|string|max:50',
            // Alamat
            'rt'                         => 'nullable|string|max:3',
            'rw'                         => 'nullable|string|max:3',
            'kelurahan'                  => 'nullable|string|max:100',
            'kecamatan'                  => 'nullable|string|max:100',
            'kota'                       => 'nullable|string|max:100',
            'latitude'                   => 'nullable|numeric',
            'longitude'                  => 'nullable|numeric',
            'jarak_tempat_tinggal'       => 'nullable|integer|min:0',
            // KIP
            'no_kip'                     => 'nullable|string|max:30',
            'no_kps_pkh'                 => 'nullable|string|max:30',
            'nama_di_kip'                => 'nullable|string|max:255',
            'terima_kip'                 => 'nullable|boolean',
        ];

        if ($config) {
            // Data diri
            if (!$config->isFieldActive('diri_tempat_lahir'))   unset($rules['tempat_lahir']);
            if (!$config->isFieldActive('diri_tanggal_lahir'))  unset($rules['tanggal_lahir']);
            if (!$config->isFieldActive('diri_agama'))          unset($rules['agama']);
            if (!$config->isFieldActive('diri_no_hp'))          unset($rules['no_hp']);
            if (!$config->isFieldActive('diri_tinggi_badan'))   unset($rules['tinggi_badan']);
            if (!$config->isFieldActive('diri_berat_badan'))    unset($rules['berat_badan']);
            if (!$config->isFieldActive('diri_jumlah_saudara')) unset($rules['jumlah_saudara']);
            if (!$config->isFieldActive('diri_asal_sekolah')) {
                unset($rules['asal_sekolah_id'], $rules['asal_sekolah_custom']);
            }
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
            // Wali
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
            if (!$config->isFieldActive('alamat_koordinat')) unset($rules['latitude'], $rules['longitude']);
            if (!$config->isFieldActive('alamat_jarak'))     unset($rules['jarak_tempat_tinggal']);
            // KIP
            if (!$config->isFieldActive('data_kip')) {
                unset($rules['no_kip'], $rules['no_kps_pkh'], $rules['nama_di_kip'], $rules['terima_kip']);
            } else {
                if (!$config->isFieldActive('kip_no_kip'))      unset($rules['no_kip']);
                if (!$config->isFieldActive('kip_no_kps_pkh'))  unset($rules['no_kps_pkh']);
                if (!$config->isFieldActive('kip_nama_di_kip')) unset($rules['nama_di_kip']);
                if (!$config->isFieldActive('kip_terima'))      unset($rules['terima_kip']);
            }
        }

        $v = $request->validate($rules);

        $peserta->update([
            'jalur_id'   => $v['jalur_id'],
            'jurusan_id' => $v['jurusan_id'],
        ]);

        $peserta->dataDiri()->updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nama_lengkap'        => $v['nama_lengkap'],
                'jenis_kelamin'       => $v['jenis_kelamin'],
                'tempat_lahir'        => $v['tempat_lahir'] ?? null,
                'tanggal_lahir'       => $v['tanggal_lahir'] ?? null,
                'agama'               => $v['agama'] ?? null,
                'no_hp'               => $v['no_hp'] ?? null,
                'tinggi_badan'        => $v['tinggi_badan'] ?? null,
                'berat_badan'         => $v['berat_badan'] ?? null,
                'jumlah_saudara'      => $v['jumlah_saudara'] ?? null,
                'asal_sekolah_id'     => $v['asal_sekolah_id'] ?? null,
                'asal_sekolah_custom' => $v['asal_sekolah_custom'] ?? null,
            ]
        );

        $peserta->dataAyah()->updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nama'                  => $v['nama_ayah'] ?? null,
                'nik'                   => $v['nik_ayah'] ?? null,
                'tahun_lahir'           => $v['tahun_lahir_ayah'] ?? null,
                'pendidikan'            => $v['pendidikan_ayah'] ?? null,
                'pekerjaan'             => $v['pekerjaan_ayah'] ?? null,
                'penghasilan'           => $v['penghasilan_ayah'] ?? null,
                'ketidakmampuan_khusus' => $v['ketidakmampuan_khusus_ayah'] ?? null,
            ]
        );

        $peserta->dataIbu()->updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nama'                  => $v['nama_ibu'] ?? null,
                'nik'                   => $v['nik_ibu'] ?? null,
                'tahun_lahir'           => $v['tahun_lahir_ibu'] ?? null,
                'pendidikan'            => $v['pendidikan_ibu'] ?? null,
                'pekerjaan'             => $v['pekerjaan_ibu'] ?? null,
                'penghasilan'           => $v['penghasilan_ibu'] ?? null,
                'ketidakmampuan_khusus' => $v['ketidakmampuan_khusus_ibu'] ?? null,
            ]
        );

        $waliAktif = !$config || $config->isFieldActive('data_wali');
        if ($waliAktif && !empty($v['nama_wali'])) {
            $peserta->dataWali()->updateOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'nama'        => $v['nama_wali'],
                    'nik'         => $v['nik_wali'] ?? null,
                    'tahun_lahir' => $v['tahun_lahir_wali'] ?? null,
                    'pekerjaan'   => $v['pekerjaan_wali'] ?? null,
                    'penghasilan' => $v['penghasilan_wali'] ?? null,
                ]
            );
        }

        $peserta->dataAlamat()->updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'rt'                   => $v['rt'] ?? null,
                'rw'                   => $v['rw'] ?? null,
                'kelurahan'            => $v['kelurahan'] ?? null,
                'kecamatan'            => $v['kecamatan'] ?? null,
                'kota'                 => $v['kota'] ?? null,
                'latitude'             => $v['latitude'] ?? null,
                'longitude'            => $v['longitude'] ?? null,
                'jarak_tempat_tinggal' => $v['jarak_tempat_tinggal'] ?? null,
            ]
        );

        $kipAktif = !$config || $config->isFieldActive('data_kip');
        if ($kipAktif && $peserta->fresh()->load('jalur')->jalur?->isAfirmasi()) {
            $peserta->dataKip()->updateOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'no_kip'      => $v['no_kip'] ?? null,
                    'no_kps_pkh'  => $v['no_kps_pkh'] ?? null,
                    'nama_di_kip' => $v['nama_di_kip'] ?? null,
                    'terima_kip'  => $v['terima_kip'] ?? false,
                ]
            );
        }

        return redirect(route($this->routePrefix($request) . '.peserta.show', $peserta))
            ->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function show(Request $request, Peserta $peserta)
    {
        $peserta->load(['user', 'dataDiri', 'dataAyah', 'dataIbu', 'dataWali', 'dataAlamat', 'dataKip', 'jalur', 'jurusan', 'berkas', 'nilai.mataPelajaran']);
        return view('admin.peserta.show', compact('peserta'));
    }

    public function updateVerifikasi(Request $request, Peserta $peserta)
    {
        $validated = $request->validate([
            'status_verifikasi' => 'required|in:belum_diverifikasi,terverifikasi,ditolak',
        ]);
        $peserta->update($validated);
        return back()->with('success', 'Status verifikasi diperbarui.');
    }

    public function updateHasil(Request $request, Peserta $peserta)
    {
        $validated = $request->validate([
            'status_hasil' => 'required|in:belum,lolos,tidak_lolos,cadangan',
        ]);
        $peserta->update($validated);
        return back()->with('success', 'Status hasil diperbarui.');
    }

    public function updateDaftarUlang(Request $request, Peserta $peserta)
    {
        $validated = $request->validate([
            'status_daftar_ulang' => 'required|in:belum,sudah',
        ]);
        $peserta->update($validated);
        return back()->with('success', 'Status daftar ulang diperbarui.');
    }

    public function resetPassword(Request $request, Peserta $peserta)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $peserta->user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password peserta berhasil direset.');
    }

    public function destroy(Request $request, Peserta $peserta)
    {
        $peserta->delete();

        return redirect($this->redirectBase($request) . '/peserta')
            ->with('success', 'Data peserta berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $tahun = TahunPenerimaan::where('is_active', true)->first();

        $data = Peserta::with(['user', 'dataDiri', 'jalur', 'jurusan'])
            ->when($tahun, fn ($q) => $q->where('tahun_penerimaan_id', $tahun->id))
            ->orderBy('no_pendaftaran')
            ->get()
            ->map(fn ($p) => [
                'No Pendaftaran'   => $p->no_pendaftaran ?? '-',
                'Nama Lengkap'     => $p->dataDiri?->nama_lengkap ?? $p->user?->name ?? '-',
                'NISN'             => $p->user?->nisn ?? '-',
                'Jalur'            => $p->jalur?->nama ?? '-',
                'Jurusan'          => $p->jurusan?->nama ?? '-',
                'Status Verifikasi' => $p->status_verifikasi,
                'Status Hasil'     => $p->status_hasil,
                'Daftar Ulang'     => $p->status_daftar_ulang,
            ]);

        return (new FastExcel($data))->download('peserta.xlsx');
    }

    private function redirectBase(Request $request): string
    {
        return str_starts_with($request->path(), 'panitia') ? '/panitia' : '/admin';
    }

    private function routePrefix(Request $request): string
    {
        return str_starts_with($request->path(), 'panitia') ? 'panitia' : 'admin';
    }
}
