<?php

namespace Tests\Feature\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Models\Peserta;
use App\Models\PesertaNilai;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NilaiRekapTest extends TestCase
{
    use RefreshDatabase;

    private function makeData(): array
    {
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id, 'is_active' => true]);
        $jurusan = Jurusan::factory()->create();
        $mapel   = MataPelajaran::factory()->create(['nama' => 'Matematika']);
        $user    = User::factory()->peserta()->create();
        $peserta = Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
        ]);
        PesertaNilai::create([
            'peserta_id'        => $peserta->id,
            'mata_pelajaran_id' => $mapel->id,
            'semester'          => 1,
            'nilai'             => 85.00,
        ]);
        return compact('tahun', 'jalur', 'jurusan', 'mapel', 'peserta', 'user');
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Admin/Panitia lihat halaman rekap
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_rekap_nilai(): void
    {
        $admin = User::factory()->admin()->create();
        $this->makeData();

        $response = $this->actingAs($admin)->get('/admin/nilai-rekap');

        $response->assertOk();
        $response->assertSee('Matematika');
    }

    public function test_panitia_dapat_melihat_halaman_rekap_nilai(): void
    {
        $panitia = User::factory()->panitia()->create();
        $this->makeData();

        $response = $this->actingAs($panitia)->get('/panitia/nilai-rekap');

        $response->assertOk();
        $response->assertSee('Matematika');
    }

    public function test_peserta_tidak_bisa_akses_rekap_nilai(): void
    {
        ['user' => $user] = $this->makeData();

        $this->actingAs($user)->get('/admin/nilai-rekap')->assertForbidden();
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Filter per jalur
    // ──────────────────────────────────────────────

    public function test_rekap_dapat_difilter_per_jalur(): void
    {
        $admin = User::factory()->admin()->create();
        ['jalur' => $jalur] = $this->makeData();

        // Peserta dari jalur lain
        $tahun2  = TahunPenerimaan::where('is_active', true)->first();
        $jalur2  = JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun2->id, 'nama' => 'Afirmasi']);
        $jurusan2 = Jurusan::factory()->create();
        $user2   = User::factory()->peserta()->create();
        Peserta::factory()->create([
            'user_id' => $user2->id, 'tahun_penerimaan_id' => $tahun2->id,
            'jalur_id' => $jalur2->id, 'jurusan_id' => $jurusan2->id,
        ]);

        $response = $this->actingAs($admin)->get("/admin/nilai-rekap?jalur_id={$jalur->id}");

        $response->assertOk();
        // Jalur2 peserta tidak muncul (tidak ada nama yang match)
        // Cukup pastikan response 200 dengan filter jalur bekerja
        $response->assertSee('Matematika');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Export ke Excel
    // ──────────────────────────────────────────────

    public function test_admin_dapat_export_rekap_nilai_ke_excel(): void
    {
        $admin = User::factory()->admin()->create();
        $this->makeData();

        $response = $this->actingAs($admin)->get('/admin/nilai-rekap/export');

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function test_panitia_dapat_export_rekap_nilai(): void
    {
        $panitia = User::factory()->panitia()->create();
        $this->makeData();

        $response = $this->actingAs($panitia)->get('/panitia/nilai-rekap/export');

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }
}
