<?php

namespace Tests\Feature\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifikasiTest extends TestCase
{
    use RefreshDatabase;

    private function makePeserta(): array
    {
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();
        $peserta = Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
            'status_formulir'     => 'submitted',
            'status_verifikasi'   => 'belum_diverifikasi',
            'status_hasil'        => 'belum',
            'status_daftar_ulang' => 'belum',
        ]);
        return compact('peserta', 'user');
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Halaman detail peserta
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_detail_peserta(): void
    {
        $admin            = User::factory()->admin()->create();
        ['peserta' => $p] = $this->makePeserta();

        $response = $this->actingAs($admin)->get("/admin/peserta/{$p->id}");

        $response->assertOk();
        $response->assertSee('belum_diverifikasi');
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Update status verifikasi
    // ──────────────────────────────────────────────

    public function test_admin_dapat_ubah_status_verifikasi(): void
    {
        $admin            = User::factory()->admin()->create();
        ['peserta' => $p] = $this->makePeserta();

        $response = $this->actingAs($admin)
            ->patch("/admin/peserta/{$p->id}/verifikasi", ['status_verifikasi' => 'terverifikasi']);

        $response->assertRedirect();
        $this->assertDatabaseHas('peserta', ['id' => $p->id, 'status_verifikasi' => 'terverifikasi']);
    }

    public function test_panitia_dapat_ubah_status_verifikasi(): void
    {
        $panitia          = User::factory()->panitia()->create();
        ['peserta' => $p] = $this->makePeserta();

        $response = $this->actingAs($panitia)
            ->patch("/panitia/peserta/{$p->id}/verifikasi", ['status_verifikasi' => 'ditolak']);

        $response->assertRedirect();
        $this->assertDatabaseHas('peserta', ['id' => $p->id, 'status_verifikasi' => 'ditolak']);
    }

    public function test_status_verifikasi_tidak_valid_ditolak(): void
    {
        $admin            = User::factory()->admin()->create();
        ['peserta' => $p] = $this->makePeserta();

        $response = $this->actingAs($admin)
            ->patch("/admin/peserta/{$p->id}/verifikasi", ['status_verifikasi' => 'sembarang']);

        $response->assertSessionHasErrors('status_verifikasi');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Status verifikasi berubah → upload terkunci
    // ──────────────────────────────────────────────

    public function test_peserta_terkunci_setelah_verifikasi_berubah(): void
    {
        $admin            = User::factory()->admin()->create();
        ['peserta' => $p] = $this->makePeserta();

        $this->actingAs($admin)
            ->patch("/admin/peserta/{$p->id}/verifikasi", ['status_verifikasi' => 'terverifikasi']);

        $this->assertTrue($p->fresh()->uploadTerkunci());
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Update status hasil
    // ──────────────────────────────────────────────

    public function test_admin_dapat_ubah_status_hasil(): void
    {
        $admin            = User::factory()->admin()->create();
        ['peserta' => $p] = $this->makePeserta();

        $response = $this->actingAs($admin)
            ->patch("/admin/peserta/{$p->id}/hasil", ['status_hasil' => 'lolos']);

        $response->assertRedirect();
        $this->assertDatabaseHas('peserta', ['id' => $p->id, 'status_hasil' => 'lolos']);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Update status daftar ulang
    // ──────────────────────────────────────────────

    public function test_admin_dapat_ubah_status_daftar_ulang(): void
    {
        $admin            = User::factory()->admin()->create();
        ['peserta' => $p] = $this->makePeserta();

        $response = $this->actingAs($admin)
            ->patch("/admin/peserta/{$p->id}/daftar-ulang", ['status_daftar_ulang' => 'sudah']);

        $response->assertRedirect();
        $this->assertDatabaseHas('peserta', ['id' => $p->id, 'status_daftar_ulang' => 'sudah']);
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Peserta tidak bisa ubah status
    // ──────────────────────────────────────────────

    public function test_peserta_tidak_bisa_ubah_status_verifikasi(): void
    {
        ['peserta' => $p, 'user' => $u] = $this->makePeserta();

        $this->actingAs($u)
            ->patch("/admin/peserta/{$p->id}/verifikasi", ['status_verifikasi' => 'terverifikasi'])
            ->assertForbidden();
    }
}
