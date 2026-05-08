<?php

namespace Tests\Feature\Peserta;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BerkasTest extends TestCase
{
    use RefreshDatabase;

    private function setupPeserta(string $statusVerifikasi = 'belum_diverifikasi'): array
    {
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'is_active'           => true,
        ]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();
        $peserta = Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
            'status_formulir'     => 'submitted',
            'status_verifikasi'   => $statusVerifikasi,
        ]);

        return compact('user', 'peserta');
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Akses halaman berkas
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_melihat_halaman_berkas(): void
    {
        Storage::fake('local');
        ['user' => $user] = $this->setupPeserta();

        $response = $this->actingAs($user)->get('/peserta/berkas');

        $response->assertOk();
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Upload foto 3x4
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_upload_foto_3x4(): void
    {
        Storage::fake('local');
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();

        $file = UploadedFile::fake()->image('foto.jpg', 300, 400);

        $response = $this->actingAs($user)->post('/peserta/berkas', [
            'tipe_berkas' => 'foto_3x4',
            'file'        => $file,
        ]);

        $response->assertRedirect('/peserta/berkas');
        $this->assertDatabaseHas('peserta_berkas', [
            'peserta_id'  => $peserta->id,
            'tipe_berkas' => 'foto_3x4',
        ]);

        $berkas = \App\Models\PesertaBerkas::where('peserta_id', $peserta->id)->first();
        Storage::disk('local')->assertExists($berkas->file_path);
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Upload PDF berkas dokumen
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_upload_berkas_pdf(): void
    {
        Storage::fake('local');
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();

        $file = UploadedFile::fake()->create('rapor.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post('/peserta/berkas', [
            'tipe_berkas' => 'nilai_rapor',
            'file'        => $file,
        ]);

        $response->assertRedirect('/peserta/berkas');
        $this->assertDatabaseHas('peserta_berkas', [
            'peserta_id'  => $peserta->id,
            'tipe_berkas' => 'nilai_rapor',
        ]);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Validasi MIME type
    // ──────────────────────────────────────────────

    public function test_foto_3x4_hanya_terima_gambar(): void
    {
        Storage::fake('local');
        ['user' => $user] = $this->setupPeserta();

        $file = UploadedFile::fake()->create('foto.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/peserta/berkas', [
            'tipe_berkas' => 'foto_3x4',
            'file'        => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_berkas_dokumen_hanya_terima_pdf(): void
    {
        Storage::fake('local');
        ['user' => $user] = $this->setupPeserta();

        $file = UploadedFile::fake()->image('doc.jpg');

        $response = $this->actingAs($user)->post('/peserta/berkas', [
            'tipe_berkas' => 'nilai_rapor',
            'file'        => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Upload terkunci saat status_verifikasi != belum_diverifikasi
    // ──────────────────────────────────────────────

    public function test_upload_terkunci_saat_sudah_diverifikasi(): void
    {
        Storage::fake('local');
        ['user' => $user] = $this->setupPeserta(statusVerifikasi: 'terverifikasi');

        $file = UploadedFile::fake()->image('foto.jpg');

        $response = $this->actingAs($user)->post('/peserta/berkas', [
            'tipe_berkas' => 'foto_3x4',
            'file'        => $file,
        ]);

        $response->assertRedirect('/peserta/berkas');
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('peserta_berkas', 0);
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Re-upload gantikan file lama
    // ──────────────────────────────────────────────

    public function test_re_upload_menggantikan_file_lama(): void
    {
        Storage::fake('local');
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();

        $file1 = UploadedFile::fake()->image('foto1.jpg');
        $this->actingAs($user)->post('/peserta/berkas', [
            'tipe_berkas' => 'foto_3x4',
            'file'        => $file1,
        ]);

        $file2 = UploadedFile::fake()->image('foto2.jpg');
        $this->actingAs($user)->post('/peserta/berkas', [
            'tipe_berkas' => 'foto_3x4',
            'file'        => $file2,
        ]);

        $this->assertDatabaseCount('peserta_berkas', 1);
    }

    // ──────────────────────────────────────────────
    // Siklus 7: Keterangan tersimpan
    // ──────────────────────────────────────────────

    public function test_keterangan_berkas_tersimpan(): void
    {
        Storage::fake('local');
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();

        $file = UploadedFile::fake()->create('lainnya.pdf', 100, 'application/pdf');

        $this->actingAs($user)->post('/peserta/berkas', [
            'tipe_berkas' => 'berkas_lainnya',
            'file'        => $file,
            'keterangan'  => 'Surat keterangan domisili',
        ]);

        $this->assertDatabaseHas('peserta_berkas', [
            'peserta_id'  => $peserta->id,
            'keterangan'  => 'Surat keterangan domisili',
        ]);
    }
}
