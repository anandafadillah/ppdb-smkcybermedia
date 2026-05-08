<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\PesertaBerkas;
use App\Models\TahunPenerimaan;
use App\Services\FormConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BerkasController extends Controller
{
    public function __construct(private FormConfigService $formConfigService) {}

    // Mapping: tipe_berkas key → FormConfig field key
    private const TIPE_TO_CONFIG = [
        'foto_3x4'       => 'berkas_foto',
        'akta_kelahiran' => 'berkas_akta',
        'kartu_keluarga' => 'berkas_kk',
        'ktp_orangtua'   => 'berkas_ktp_ortu',
        'sktm'           => 'berkas_sktm',
        'kartu_pkh'      => 'berkas_pkh',
        'berkas_lainnya' => 'berkas_lainnya',
        'nilai_rapor'    => 'nilai_rapor',
    ];

    private function getPeserta(): ?Peserta
    {
        $tahun = TahunPenerimaan::where('is_active', true)->first();
        if (!$tahun) {
            return null;
        }

        return Peserta::where('user_id', Auth::id())
            ->where('tahun_penerimaan_id', $tahun->id)
            ->with('berkas')
            ->first();
    }

    public function index()
    {
        $tahun   = TahunPenerimaan::where('is_active', true)->first();
        $peserta = $this->getPeserta();
        $config  = $tahun ? $this->formConfigService->getOrCreate($tahun) : null;

        // Filter tipeList berdasarkan config yang aktif
        $allTipe = PesertaBerkas::tipeList();
        $tipeList = collect($allTipe)->filter(function ($label, $tipe) use ($config) {
            if (!$config) return true;
            $configKey = self::TIPE_TO_CONFIG[$tipe] ?? null;
            return $configKey ? $config->isFieldActive($configKey) : true;
        })->all();

        $berkasByTipe = $peserta
            ? $peserta->berkas->keyBy('tipe_berkas')
            : collect();

        return view('peserta.berkas.index', compact('peserta', 'tipeList', 'berkasByTipe'));
    }

    public function store(Request $request)
    {
        $peserta = $this->getPeserta();

        if (!$peserta) {
            return back()->with('error', 'Tidak ada tahun penerimaan aktif.');
        }

        if ($peserta->uploadTerkunci()) {
            return redirect('/peserta/berkas')->with('error', 'Upload berkas tidak dapat dilakukan. Berkas sedang dalam proses verifikasi.');
        }

        $tipe      = $request->input('tipe_berkas');
        $isFoto    = in_array($tipe, PesertaBerkas::tipeFoto());
        $mimeRule  = $isFoto ? 'mimes:jpg,jpeg,png' : 'mimes:pdf';

        $validated = $request->validate([
            'tipe_berkas' => 'required|in:' . implode(',', array_keys(PesertaBerkas::tipeList())),
            'file'        => ['required', 'file', 'max:2048', $mimeRule],
            'keterangan'  => 'nullable|string|max:500',
        ]);

        $existing = $peserta->berkas()->where('tipe_berkas', $tipe)->first();

        if ($existing && $existing->file_path) {
            Storage::disk('local')->delete($existing->file_path);
        }

        $ext      = $validated['file']->getClientOriginalExtension();
        $path     = $validated['file']->storeAs(
            "berkas/{$peserta->id}",
            "{$tipe}.{$ext}",
            'local'
        );

        $peserta->berkas()->updateOrCreate(
            ['peserta_id' => $peserta->id, 'tipe_berkas' => $tipe],
            [
                'file_path'  => $path,
                'mime_type'  => $validated['file']->getMimeType(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]
        );

        return redirect('/peserta/berkas')->with('success', 'Berkas berhasil diupload.');
    }
}
