<?php

namespace Tests\Feature\Admin;

use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahunPenerimaanTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────
    // Siklus 1: Admin dapat melihat daftar tahun penerimaan
    // ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_tahun_penerimaan(): void
    {
        $admin = User::factory()->admin()->create();
        TahunPenerimaan::factory()->create(['tahun' => '2024/2025', 'label' => 'TA 2024/2025']);
        TahunPenerimaan::factory()->create(['tahun' => '2025/2026', 'label' => 'TA 2025/2026']);

        $response = $this->actingAs($admin)->get('/admin/tahun-penerimaan');

        $response->assertOk();
        $response->assertSee('TA 2024/2025');
        $response->assertSee('TA 2025/2026');
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 2: Admin dapat membuat tahun penerimaan baru
    // ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_membuat_tahun_penerimaan_baru(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/tahun-penerimaan', [
            'tahun' => '2025/2026',
            'label' => 'TA 2025/2026',
        ]);

        $response->assertRedirect(route('admin.tahun-penerimaan.index'));
        $this->assertDatabaseHas('tahun_penerimaan', [
            'tahun' => '2025/2026',
            'label' => 'TA 2025/2026',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 3: Admin dapat mengedit tahun penerimaan
    // ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_mengedit_tahun_penerimaan(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = TahunPenerimaan::factory()->create(['tahun' => '2024/2025', 'label' => 'TA 2024/2025']);

        $response = $this->actingAs($admin)->put("/admin/tahun-penerimaan/{$tahun->id}", [
            'tahun' => '2025/2026',
            'label' => 'TA 2025/2026 Diperbarui',
        ]);

        $response->assertRedirect(route('admin.tahun-penerimaan.index'));
        $this->assertDatabaseHas('tahun_penerimaan', [
            'id'    => $tahun->id,
            'tahun' => '2025/2026',
            'label' => 'TA 2025/2026 Diperbarui',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 4: Admin dapat menghapus tahun penerimaan tanpa peserta
    // ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_menghapus_tahun_penerimaan_tanpa_peserta(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = TahunPenerimaan::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/tahun-penerimaan/{$tahun->id}");

        $response->assertRedirect(route('admin.tahun-penerimaan.index'));
        $this->assertDatabaseMissing('tahun_penerimaan', ['id' => $tahun->id]);
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 5: Hapus ditolak jika sudah ada peserta
    // ──────────────────────────────────────────────────────────────

    public function test_tahun_penerimaan_dengan_peserta_tidak_bisa_dihapus(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = TahunPenerimaan::factory()->create();
        Peserta::factory()->create(['tahun_penerimaan_id' => $tahun->id]);

        $response = $this->actingAs($admin)->delete("/admin/tahun-penerimaan/{$tahun->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('tahun_penerimaan', ['id' => $tahun->id]);
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 6: Admin dapat mengaktifkan tahun penerimaan
    // ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_mengaktifkan_tahun_penerimaan(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = TahunPenerimaan::factory()->create(['is_active' => false]);

        $response = $this->actingAs($admin)->patch("/admin/tahun-penerimaan/{$tahun->id}/activate");

        $response->assertRedirect(route('admin.tahun-penerimaan.index'));
        $this->assertDatabaseHas('tahun_penerimaan', [
            'id'        => $tahun->id,
            'is_active' => true,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 7: Mengaktifkan A otomatis menonaktifkan yang lain
    // ──────────────────────────────────────────────────────────────

    public function test_mengaktifkan_satu_tahun_penerimaan_menonaktifkan_yang_lain(): void
    {
        $admin   = User::factory()->admin()->create();
        $tahunA  = TahunPenerimaan::factory()->create(['is_active' => true]);
        $tahunB  = TahunPenerimaan::factory()->create(['is_active' => false]);
        $tahunC  = TahunPenerimaan::factory()->create(['is_active' => false]);

        $this->actingAs($admin)->patch("/admin/tahun-penerimaan/{$tahunB->id}/activate");

        $this->assertDatabaseHas('tahun_penerimaan', ['id' => $tahunA->id, 'is_active' => false]);
        $this->assertDatabaseHas('tahun_penerimaan', ['id' => $tahunB->id, 'is_active' => true]);
        $this->assertDatabaseHas('tahun_penerimaan', ['id' => $tahunC->id, 'is_active' => false]);
    }
}
