<?php

namespace Tests\Feature\Admin;

use App\Models\MataPelajaran;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MataPelajaranTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Siklus 2: Admin membuat mata pelajaran
    // ──────────────────────────────────────────────

    public function test_admin_dapat_membuat_mata_pelajaran_baru(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/mata-pelajaran', ['nama' => 'Matematika']);

        $response->assertRedirect('/admin/mata-pelajaran');
        $this->assertDatabaseHas('mata_pelajaran', ['nama' => 'Matematika', 'is_active' => true]);
    }

    public function test_nama_mata_pelajaran_harus_unik(): void
    {
        $admin = User::factory()->admin()->create();
        MataPelajaran::factory()->create(['nama' => 'Matematika']);

        $response = $this->actingAs($admin)->post('/admin/mata-pelajaran', ['nama' => 'Matematika']);

        $response->assertSessionHasErrors('nama');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Admin mengedit mata pelajaran
    // ──────────────────────────────────────────────

    public function test_admin_dapat_mengedit_mata_pelajaran(): void
    {
        $admin  = User::factory()->admin()->create();
        $mapel  = MataPelajaran::factory()->create(['nama' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put("/admin/mata-pelajaran/{$mapel->id}", ['nama' => 'Matematika']);

        $response->assertRedirect('/admin/mata-pelajaran');
        $this->assertDatabaseHas('mata_pelajaran', ['id' => $mapel->id, 'nama' => 'Matematika']);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Admin menghapus mata pelajaran
    // ──────────────────────────────────────────────

    public function test_admin_dapat_menghapus_mata_pelajaran_tanpa_nilai(): void
    {
        $admin = User::factory()->admin()->create();
        $mapel = MataPelajaran::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/mata-pelajaran/{$mapel->id}");

        $response->assertRedirect('/admin/mata-pelajaran');
        $this->assertDatabaseMissing('mata_pelajaran', ['id' => $mapel->id]);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Toggle aktif/nonaktif
    // ──────────────────────────────────────────────

    public function test_admin_dapat_menonaktifkan_mata_pelajaran(): void
    {
        $admin = User::factory()->admin()->create();
        $mapel = MataPelajaran::factory()->create(['is_active' => true]);

        $this->actingAs($admin)->patch("/admin/mata-pelajaran/{$mapel->id}/toggle-aktif");

        $this->assertDatabaseHas('mata_pelajaran', ['id' => $mapel->id, 'is_active' => false]);
    }

    public function test_admin_dapat_mengaktifkan_mata_pelajaran(): void
    {
        $admin = User::factory()->admin()->create();
        $mapel = MataPelajaran::factory()->nonaktif()->create();

        $this->actingAs($admin)->patch("/admin/mata-pelajaran/{$mapel->id}/toggle-aktif");

        $this->assertDatabaseHas('mata_pelajaran', ['id' => $mapel->id, 'is_active' => true]);
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Tidak bisa hapus jika ada nilai peserta
    // ──────────────────────────────────────────────

    public function test_mata_pelajaran_dengan_nilai_peserta_tidak_bisa_dihapus(): void
    {
        $admin  = User::factory()->admin()->create();
        $mapel  = MataPelajaran::factory()->create();
        $peserta = Peserta::factory()->create();

        \App\Models\PesertaNilai::create([
            'peserta_id'       => $peserta->id,
            'mata_pelajaran_id' => $mapel->id,
            'semester'         => 1,
            'nilai'            => 85,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/mata-pelajaran/{$mapel->id}");

        $response->assertRedirect('/admin/mata-pelajaran');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('mata_pelajaran', ['id' => $mapel->id]);
    }

    // ──────────────────────────────────────────────
    // Siklus 7: Hanya mapel aktif yang muncul (scope)
    // ──────────────────────────────────────────────

    public function test_hanya_mata_pelajaran_aktif_yang_bisa_diambil_via_scope(): void
    {
        $aktif   = MataPelajaran::factory()->create(['is_active' => true]);
        $nonaktif = MataPelajaran::factory()->nonaktif()->create();

        $hasil = MataPelajaran::aktif()->get();

        $this->assertCount(1, $hasil);
        $this->assertTrue($hasil->contains($aktif));
        $this->assertFalse($hasil->contains($nonaktif));
    }

    // ──────────────────────────────────────────────
    // Siklus 8: Panitia & peserta tidak bisa akses
    // ──────────────────────────────────────────────

    public function test_panitia_dan_peserta_tidak_bisa_akses_mata_pelajaran(): void
    {
        $panitia = User::factory()->panitia()->create();
        $peserta = User::factory()->peserta()->create();

        $this->actingAs($panitia)->get('/admin/mata-pelajaran')->assertForbidden();
        $this->actingAs($peserta)->get('/admin/mata-pelajaran')->assertForbidden();
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Admin melihat list mata pelajaran
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_list_mata_pelajaran(): void
    {
        $admin = User::factory()->admin()->create();
        MataPelajaran::factory()->create(['nama' => 'Matematika']);
        MataPelajaran::factory()->create(['nama' => 'Bahasa Indonesia']);

        $response = $this->actingAs($admin)->get('/admin/mata-pelajaran');

        $response->assertOk();
        $response->assertSee('Matematika');
        $response->assertSee('Bahasa Indonesia');
    }
}
