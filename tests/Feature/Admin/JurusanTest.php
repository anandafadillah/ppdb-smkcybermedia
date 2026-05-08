<?php

namespace Tests\Feature\Admin;

use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurusanTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Siklus 1: Admin melihat daftar jurusan
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_jurusan(): void
    {
        $admin = User::factory()->admin()->create();
        Jurusan::factory()->create(['kode' => 'TJKT', 'nama' => 'Teknik Jaringan Komputer dan Telekomunikasi']);

        $response = $this->actingAs($admin)->get('/admin/jurusan');

        $response->assertOk();
        $response->assertSee('TJKT');
        $response->assertSee('Teknik Jaringan Komputer dan Telekomunikasi');
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Admin membuat jurusan baru
    // ──────────────────────────────────────────────

    public function test_admin_dapat_membuat_jurusan_baru(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/jurusan', [
            'kode'      => 'MPLB',
            'nama'      => 'Manajemen Perkantoran dan Layanan Bisnis',
            'kapasitas' => 36,
            'deskripsi' => 'Jurusan MPLB',
        ]);

        $response->assertRedirect('/admin/jurusan');
        $this->assertDatabaseHas('jurusan', [
            'kode' => 'MPLB',
            'nama' => 'Manajemen Perkantoran dan Layanan Bisnis',
        ]);
    }

    // ──────────────────────────────────────────────
    // Siklus 3a: Validasi field wajib
    // ──────────────────────────────────────────────

    public function test_kode_dan_nama_wajib_diisi_saat_membuat_jurusan(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/jurusan', []);

        $response->assertSessionHasErrors(['kode', 'nama']);
    }

    // ──────────────────────────────────────────────
    // Siklus 3b: Validasi kode unik saat create
    // ──────────────────────────────────────────────

    public function test_kode_jurusan_harus_unik_saat_membuat(): void
    {
        $admin = User::factory()->admin()->create();
        Jurusan::factory()->create(['kode' => 'DKV']);

        $response = $this->actingAs($admin)->post('/admin/jurusan', [
            'kode' => 'DKV',
            'nama' => 'Nama Lain',
        ]);

        $response->assertSessionHasErrors('kode');
        $this->assertDatabaseCount('jurusan', 1);
    }

    // ──────────────────────────────────────────────
    // Siklus 4a: Admin mengedit jurusan
    // ──────────────────────────────────────────────

    public function test_admin_dapat_mengedit_jurusan(): void
    {
        $admin   = User::factory()->admin()->create();
        $jurusan = Jurusan::factory()->create(['kode' => 'PG', 'nama' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put("/admin/jurusan/{$jurusan->id}", [
            'kode'      => 'PG',
            'nama'      => 'Pengembangan Perangkat Lunak dan Gim',
            'kapasitas' => 40,
        ]);

        $response->assertRedirect('/admin/jurusan');
        $this->assertDatabaseHas('jurusan', [
            'id'  => $jurusan->id,
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);
    }

    // ──────────────────────────────────────────────
    // Siklus 4b: Kode yang sama saat edit tidak dianggap duplikat
    // ──────────────────────────────────────────────

    public function test_update_kode_jurusan_sendiri_tidak_menghasilkan_error_duplikat(): void
    {
        $admin   = User::factory()->admin()->create();
        $jurusan = Jurusan::factory()->create(['kode' => 'TJKT', 'nama' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put("/admin/jurusan/{$jurusan->id}", [
            'kode' => 'TJKT',
            'nama' => 'Nama Baru',
        ]);

        $response->assertRedirect('/admin/jurusan');
        $response->assertSessionHasNoErrors();
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Admin menghapus jurusan yang aman
    // ──────────────────────────────────────────────

    public function test_admin_dapat_menghapus_jurusan_yang_belum_dipilih_peserta(): void
    {
        $admin   = User::factory()->admin()->create();
        $jurusan = Jurusan::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/jurusan/{$jurusan->id}");

        $response->assertRedirect('/admin/jurusan');
        $this->assertDatabaseMissing('jurusan', ['id' => $jurusan->id]);
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Tidak bisa hapus jurusan yang sudah dipilih peserta
    // ──────────────────────────────────────────────

    public function test_admin_tidak_bisa_menghapus_jurusan_yang_sudah_dipilih_peserta(): void
    {
        $admin        = User::factory()->admin()->create();
        $jurusan      = Jurusan::factory()->create();
        $tahun        = TahunPenerimaan::factory()->create();
        $pesertaUser  = User::factory()->peserta()->create();

        Peserta::factory()->create([
            'user_id'              => $pesertaUser->id,
            'tahun_penerimaan_id'  => $tahun->id,
            'jurusan_id'           => $jurusan->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/jurusan/{$jurusan->id}");

        $response->assertRedirect('/admin/jurusan');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('jurusan', ['id' => $jurusan->id]);
    }

    // ──────────────────────────────────────────────
    // Siklus 7: Role bukan admin mendapat 403
    // ──────────────────────────────────────────────

    public function test_panitia_tidak_bisa_akses_manajemen_jurusan(): void
    {
        $panitia = User::factory()->panitia()->create();

        $response = $this->actingAs($panitia)->get('/admin/jurusan');

        $response->assertForbidden();
    }

    public function test_peserta_tidak_bisa_akses_manajemen_jurusan(): void
    {
        $peserta = User::factory()->peserta()->create();

        $response = $this->actingAs($peserta)->get('/admin/jurusan');

        $response->assertForbidden();
    }
}
