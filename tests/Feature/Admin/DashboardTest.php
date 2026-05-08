<?php

namespace Tests\Feature\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_tidak_error_saat_ada_peserta_dengan_jalur(): void
    {
        $admin  = User::factory()->admin()->create();
        $tahun  = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur  = JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'is_active'           => true,
        ]);
        $jurusan = Jurusan::factory()->create();
        $user   = User::factory()->peserta()->create();

        Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
    }

    public function test_admin_statistik_tidak_error_saat_ada_peserta_dengan_jalur(): void
    {
        $admin   = User::factory()->admin()->create();
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id, 'is_active' => true]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();

        Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/statistik');
        $response->assertOk();
    }

    public function test_panitia_statistik_tidak_error_saat_ada_peserta_dengan_jalur(): void
    {
        $panitia = User::factory()->panitia()->create();
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create(['tahun_penerimaan_id' => $tahun->id, 'is_active' => true]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();

        Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
        ]);

        $response = $this->actingAs($panitia)->get('/panitia/statistik');
        $response->assertOk();
    }

    public function test_panitia_dashboard_tidak_error_saat_ada_peserta_dengan_jalur(): void
    {
        $panitia = User::factory()->panitia()->create();
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'is_active'           => true,
        ]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();

        Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
        ]);

        $response = $this->actingAs($panitia)->get('/panitia/dashboard');

        $response->assertOk();
    }
}
