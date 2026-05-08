<?php

namespace Tests\Feature\Admin;

use App\Models\FormConfig;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormConfigTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────
    // Siklus 1: Admin dapat melihat halaman konfigurasi formulir
    // ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_konfigurasi_formulir(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = TahunPenerimaan::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/admin/tahun-penerimaan/{$tahun->id}/form-config");

        $response->assertOk();
        $response->assertSee('Data Wali');
        $response->assertSee('Nilai Rapor');
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 2: Admin dapat menyimpan konfigurasi field
    // ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_menyimpan_konfigurasi_field_formulir(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = TahunPenerimaan::factory()->create();

        $response = $this->actingAs($admin)
            ->put("/admin/tahun-penerimaan/{$tahun->id}/form-config", [
                'fields' => ['data_wali', 'nilai_rapor'],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $config = FormConfig::where('tahun_penerimaan_id', $tahun->id)->first();
        $this->assertTrue($config->isFieldActive('data_wali'));
        $this->assertTrue($config->isFieldActive('nilai_rapor'));
        $this->assertFalse($config->isFieldActive('data_kip'));
        $this->assertFalse($config->isFieldActive('berkas_foto'));
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 3: Panitia juga dapat mengakses konfigurasi formulir
    // ──────────────────────────────────────────────────────────────

    public function test_panitia_dapat_melihat_dan_menyimpan_konfigurasi_formulir(): void
    {
        $panitia = User::factory()->panitia()->create();
        $tahun   = TahunPenerimaan::factory()->create();

        $getResponse = $this->actingAs($panitia)
            ->get("/panitia/tahun-penerimaan/{$tahun->id}/form-config");
        $getResponse->assertOk();

        $putResponse = $this->actingAs($panitia)
            ->put("/panitia/tahun-penerimaan/{$tahun->id}/form-config", [
                'fields' => ['berkas_kk', 'berkas_akta'],
            ]);
        $putResponse->assertRedirect();
        $putResponse->assertSessionHas('success');

        $config = FormConfig::where('tahun_penerimaan_id', $tahun->id)->first();
        $this->assertTrue($config->isFieldActive('berkas_kk'));
        $this->assertFalse($config->isFieldActive('data_wali'));
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 4: Konfigurasi terkunci tidak bisa diubah
    // ──────────────────────────────────────────────────────────────

    public function test_update_konfigurasi_terkunci_mengembalikan_pesan_error(): void
    {
        $admin  = User::factory()->admin()->create();
        $tahun  = TahunPenerimaan::factory()->create();
        $config = FormConfig::create([
            'tahun_penerimaan_id' => $tahun->id,
            'field_configs'       => array_fill_keys(array_keys(FormConfig::AVAILABLE_FIELDS), true),
            'is_locked'           => true,
        ]);

        $response = $this->actingAs($admin)
            ->put("/admin/tahun-penerimaan/{$tahun->id}/form-config", [
                'fields' => ['data_wali'],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // field_configs tidak berubah
        $this->assertTrue($config->fresh()->isFieldActive('data_kip'));
        $this->assertTrue($config->fresh()->isFieldActive('berkas_foto'));
    }

    // ──────────────────────────────────────────────────────────────
    // Siklus 5: isFieldActive() membaca status field dari config
    // ──────────────────────────────────────────────────────────────

    public function test_is_field_active_membaca_status_field_dari_konfigurasi(): void
    {
        $tahun = TahunPenerimaan::factory()->create();
        $config = FormConfig::create([
            'tahun_penerimaan_id' => $tahun->id,
            'field_configs'       => [
                'data_wali'        => true,
                'data_kip'         => false,
                'alamat_koordinat' => false,
                'nilai_rapor'      => true,
                'berkas_foto'      => true,
                'berkas_akta'      => false,
                'berkas_kk'        => true,
                'berkas_ktp_ortu'  => false,
                'berkas_sktm'      => false,
                'berkas_pkh'       => false,
                'berkas_lainnya'   => true,
            ],
            'is_locked' => false,
        ]);

        $this->assertTrue($config->isFieldActive('data_wali'));
        $this->assertFalse($config->isFieldActive('data_kip'));
        $this->assertFalse($config->isFieldActive('alamat_koordinat'));
        $this->assertTrue($config->isFieldActive('nilai_rapor'));
        $this->assertFalse($config->isFieldActive('berkas_akta'));
    }
}
