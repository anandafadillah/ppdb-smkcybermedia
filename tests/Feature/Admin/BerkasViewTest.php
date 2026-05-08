<?php

namespace Tests\Feature\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\PesertaBerkas;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BerkasViewTest extends TestCase
{
    use RefreshDatabase;

    private function makeBerkas(): array
    {
        Storage::fake('local');

        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();
        $peserta = Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
        ]);

        $file = UploadedFile::fake()->image('foto.jpg');
        $path = $file->storeAs("berkas/{$peserta->id}", 'foto_3x4.jpg', 'local');

        $berkas = PesertaBerkas::create([
            'peserta_id'  => $peserta->id,
            'tipe_berkas' => 'foto_3x4',
            'file_path'   => $path,
            'mime_type'   => 'image/jpeg',
        ]);

        return compact('peserta', 'berkas', 'user');
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Berkas tampil di detail peserta
    // ──────────────────────────────────────────────

    public function test_berkas_peserta_tampil_di_halaman_detail(): void
    {
        $admin = User::factory()->admin()->create();
        ['peserta' => $p] = $this->makeBerkas();

        $response = $this->actingAs($admin)->get("/admin/peserta/{$p->id}");

        $response->assertOk();
        $response->assertSee('Foto 3x4');
        $response->assertSee('Download');
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Admin download berkas
    // ──────────────────────────────────────────────

    public function test_admin_dapat_download_berkas_peserta(): void
    {
        $admin = User::factory()->admin()->create();
        ['berkas' => $berkas] = $this->makeBerkas();

        $response = $this->actingAs($admin)->get("/admin/berkas/{$berkas->id}/download");

        $response->assertOk();
        $response->assertHeader('Content-Type');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Panitia download berkas
    // ──────────────────────────────────────────────

    public function test_panitia_dapat_download_berkas_peserta(): void
    {
        $panitia = User::factory()->panitia()->create();
        ['berkas' => $berkas] = $this->makeBerkas();

        $response = $this->actingAs($panitia)->get("/panitia/berkas/{$berkas->id}/download");

        $response->assertOk();
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Peserta tidak bisa download via admin route
    // ──────────────────────────────────────────────

    public function test_peserta_tidak_bisa_download_via_admin_route(): void
    {
        ['user' => $user, 'berkas' => $berkas] = $this->makeBerkas();

        $this->actingAs($user)
            ->get("/admin/berkas/{$berkas->id}/download")
            ->assertForbidden();
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Berkas belum diupload → tampil "Belum diupload"
    // ──────────────────────────────────────────────

    public function test_berkas_yang_belum_diupload_ditandai_belum(): void
    {
        Storage::fake('local');
        $admin   = User::factory()->admin()->create();
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();
        $peserta = Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
        ]);

        $response = $this->actingAs($admin)->get("/admin/peserta/{$peserta->id}");

        $response->assertOk();
        $response->assertSee('Belum diupload');
    }
}
