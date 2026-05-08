<?php

namespace Tests\Feature\Admin;

use App\Models\JalurPendaftaran;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JalurPendaftaranTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Siklus 1: Admin membuat jalur baru
    // ──────────────────────────────────────────────

    public function test_admin_dapat_membuat_jalur_pendaftaran_baru(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = TahunPenerimaan::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->post('/admin/jalur-pendaftaran', [
            'tahun_penerimaan_id' => $tahun->id,
            'nama'                => 'Afirmasi',
            'deskripsi'           => 'Jalur untuk keluarga tidak mampu',
            'kode_awal_daring'    => 'AFR-D',
            'kode_awal_luring'    => 'AFR-L',
        ]);

        $response->assertRedirect('/admin/jalur-pendaftaran');
        $this->assertDatabaseHas('jalur_pendaftaran', [
            'tahun_penerimaan_id' => $tahun->id,
            'nama'                => 'Afirmasi',
            'is_active'           => true,
        ]);
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Admin melihat list jalur
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_list_jalur_per_tahun_penerimaan_aktif(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = TahunPenerimaan::factory()->create(['is_active' => true]);

        JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id, 'nama' => 'Afirmasi']);
        JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id, 'nama' => 'Pindah Sekolah']);

        $tahunLain = TahunPenerimaan::factory()->create(['is_active' => false]);
        JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahunLain->id, 'nama' => 'Tidak Tampil']);

        $response = $this->actingAs($admin)->get('/admin/jalur-pendaftaran');

        $response->assertOk();
        $response->assertSee('Afirmasi');
        $response->assertSee('Pindah Sekolah');
        $response->assertDontSee('Tidak Tampil');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Admin mengedit jalur
    // ──────────────────────────────────────────────

    public function test_admin_dapat_mengedit_jalur_pendaftaran(): void
    {
        $admin = User::factory()->admin()->create();
        $jalur = JalurPendaftaran::factory()->create(['nama' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put("/admin/jalur-pendaftaran/{$jalur->id}", [
            'nama'             => 'Afirmasi',
            'deskripsi'        => 'Deskripsi baru',
            'kode_awal_daring' => 'AFR-D',
            'kode_awal_luring' => 'AFR-L',
        ]);

        $response->assertRedirect('/admin/jalur-pendaftaran');
        $this->assertDatabaseHas('jalur_pendaftaran', [
            'id'   => $jalur->id,
            'nama' => 'Afirmasi',
        ]);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Admin menghapus jalur
    // ──────────────────────────────────────────────

    public function test_admin_dapat_menghapus_jalur_pendaftaran(): void
    {
        $admin = User::factory()->admin()->create();
        $jalur = JalurPendaftaran::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/jalur-pendaftaran/{$jalur->id}");

        $response->assertRedirect('/admin/jalur-pendaftaran');
        $this->assertDatabaseMissing('jalur_pendaftaran', ['id' => $jalur->id]);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Toggle aktif/nonaktif
    // ──────────────────────────────────────────────

    public function test_admin_dapat_menonaktifkan_jalur_yang_sedang_aktif(): void
    {
        $admin = User::factory()->admin()->create();
        $jalur = JalurPendaftaran::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->patch("/admin/jalur-pendaftaran/{$jalur->id}/toggle-aktif");

        $response->assertRedirect('/admin/jalur-pendaftaran');
        $this->assertDatabaseHas('jalur_pendaftaran', ['id' => $jalur->id, 'is_active' => false]);
    }

    public function test_admin_dapat_mengaktifkan_jalur_yang_nonaktif(): void
    {
        $admin = User::factory()->admin()->create();
        $jalur = JalurPendaftaran::factory()->nonaktif()->create();

        $this->actingAs($admin)->patch("/admin/jalur-pendaftaran/{$jalur->id}/toggle-aktif");

        $this->assertDatabaseHas('jalur_pendaftaran', ['id' => $jalur->id, 'is_active' => true]);
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Panitia update persentase kuota
    // ──────────────────────────────────────────────

    public function test_panitia_dapat_mengatur_persentase_kuota_jalur(): void
    {
        $panitia = User::factory()->panitia()->create();
        $jalur   = JalurPendaftaran::factory()->create(['persentase_kuota' => 0]);

        $response = $this->actingAs($panitia)
            ->patch("/panitia/jalur-pendaftaran/{$jalur->id}/kuota", [
                'persentase_kuota' => 60,
            ]);

        $response->assertRedirect('/panitia/dashboard');
        $this->assertDatabaseHas('jalur_pendaftaran', [
            'id'               => $jalur->id,
            'persentase_kuota' => 60,
        ]);
    }

    // ──────────────────────────────────────────────
    // Siklus 7: Warning jika total kuota > 100%
    // ──────────────────────────────────────────────

    public function test_update_kuota_yang_membuat_total_melebihi_100_persen_menampilkan_warning(): void
    {
        $panitia = User::factory()->panitia()->create();
        $tahun   = TahunPenerimaan::factory()->create();

        JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'persentase_kuota'    => 70,
        ]);
        $jalurTarget = JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'persentase_kuota'    => 0,
        ]);

        $response = $this->actingAs($panitia)
            ->patch("/panitia/jalur-pendaftaran/{$jalurTarget->id}/kuota", [
                'persentase_kuota' => 50,
            ]);

        // Tetap tersimpan (soft quota — tidak diblokir)
        $this->assertDatabaseHas('jalur_pendaftaran', [
            'id'               => $jalurTarget->id,
            'persentase_kuota' => 50,
        ]);
        // Tapi ada session warning
        $response->assertSessionHas('warning');
    }

    // ──────────────────────────────────────────────
    // Siklus 8: Hanya jalur aktif yang bisa diambil untuk formulir peserta
    // ──────────────────────────────────────────────

    public function test_hanya_jalur_aktif_yang_muncul_di_daftar_jalur_aktif(): void
    {
        $tahun = TahunPenerimaan::factory()->create(['is_active' => true]);

        $aktif   = JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id, 'is_active' => true]);
        $nonaktif = JalurPendaftaran::factory()->nonaktif()->create(['tahun_penerimaan_id' => $tahun->id]);

        $jalurAktif = JalurPendaftaran::where('tahun_penerimaan_id', $tahun->id)
            ->where('is_active', true)
            ->get();

        $this->assertCount(1, $jalurAktif);
        $this->assertTrue($jalurAktif->contains($aktif));
        $this->assertFalse($jalurAktif->contains($nonaktif));
    }

    // ──────────────────────────────────────────────
    // Siklus 9: Panitia tidak bisa create/delete jalur
    // ──────────────────────────────────────────────

    public function test_panitia_tidak_bisa_membuat_jalur_pendaftaran(): void
    {
        $panitia = User::factory()->panitia()->create();
        $tahun   = TahunPenerimaan::factory()->create();

        $response = $this->actingAs($panitia)->post('/admin/jalur-pendaftaran', [
            'tahun_penerimaan_id' => $tahun->id,
            'nama'                => 'Jalur Baru',
        ]);

        $response->assertForbidden();
    }

    public function test_panitia_tidak_bisa_menghapus_jalur_pendaftaran(): void
    {
        $panitia = User::factory()->panitia()->create();
        $jalur   = JalurPendaftaran::factory()->create();

        $response = $this->actingAs($panitia)->delete("/admin/jalur-pendaftaran/{$jalur->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('jalur_pendaftaran', ['id' => $jalur->id]);
    }
}
