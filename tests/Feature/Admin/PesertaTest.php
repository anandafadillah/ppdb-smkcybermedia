<?php

namespace Tests\Feature\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\PesertaDataDiri;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesertaTest extends TestCase
{
    use RefreshDatabase;

    private function tahunAktif(): TahunPenerimaan
    {
        return TahunPenerimaan::factory()->create(['is_active' => true]);
    }

    private function jalur(TahunPenerimaan $tahun, array $attr = []): JalurPendaftaran
    {
        return JalurPendaftaran::factory()->create(array_merge([
            'tahun_penerimaan_id' => $tahun->id,
            'is_active'           => true,
            'kode_awal_daring'    => 'REG-D',
            'kode_awal_luring'    => 'REG-L',
        ], $attr));
    }

    private function pesertaDenganNama(TahunPenerimaan $tahun, JalurPendaftaran $jalur, string $nama): Peserta
    {
        $user    = User::factory()->peserta()->create();
        $jurusan = Jurusan::factory()->create();
        $peserta = Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
            'status_formulir'     => 'submitted',
        ]);
        PesertaDataDiri::factory()->create([
            'peserta_id'   => $peserta->id,
            'nama_lengkap' => $nama,
        ]);
        return $peserta;
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Admin/Panitia lihat list peserta
    // ──────────────────────────────────────────────

    public function test_admin_dapat_melihat_list_peserta(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = $this->tahunAktif();
        $jalur = $this->jalur($tahun);
        $this->pesertaDenganNama($tahun, $jalur, 'Budi Santoso');

        $response = $this->actingAs($admin)->get('/admin/peserta');

        $response->assertOk();
        $response->assertSee('Budi Santoso');
    }

    public function test_panitia_dapat_melihat_list_peserta(): void
    {
        $panitia = User::factory()->panitia()->create();
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalur($tahun);
        $this->pesertaDenganNama($tahun, $jalur, 'Siti Aminah');

        $response = $this->actingAs($panitia)->get('/panitia/peserta');

        $response->assertOk();
        $response->assertSee('Siti Aminah');
    }

    public function test_peserta_tidak_bisa_akses_manajemen_peserta(): void
    {
        $user = User::factory()->peserta()->create();

        $this->actingAs($user)->get('/admin/peserta')->assertForbidden();
        $this->actingAs($user)->get('/panitia/peserta')->assertForbidden();
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Search peserta
    // ──────────────────────────────────────────────

    public function test_list_dapat_dicari_berdasarkan_nama(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = $this->tahunAktif();
        $jalur = $this->jalur($tahun);
        $this->pesertaDenganNama($tahun, $jalur, 'Budi Santoso');
        $this->pesertaDenganNama($tahun, $jalur, 'Andi Prasetyo');

        $response = $this->actingAs($admin)->get('/admin/peserta?search=Budi');

        $response->assertSee('Budi Santoso');
        $response->assertDontSee('Andi Prasetyo');
    }

    public function test_list_dapat_dicari_berdasarkan_nisn(): void
    {
        $admin   = User::factory()->admin()->create();
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalur($tahun);
        $user1   = User::factory()->peserta()->create(['nisn' => '1234567890']);
        $user2   = User::factory()->peserta()->create(['nisn' => '9876543210']);
        $jurusan = Jurusan::factory()->create();

        $p1 = Peserta::factory()->create([
            'user_id' => $user1->id, 'tahun_penerimaan_id' => $tahun->id,
            'jalur_id' => $jalur->id, 'jurusan_id' => $jurusan->id,
        ]);
        $p2 = Peserta::factory()->create([
            'user_id' => $user2->id, 'tahun_penerimaan_id' => $tahun->id,
            'jalur_id' => $jalur->id, 'jurusan_id' => $jurusan->id,
        ]);
        PesertaDataDiri::factory()->create(['peserta_id' => $p1->id, 'nama_lengkap' => 'User Satu']);
        PesertaDataDiri::factory()->create(['peserta_id' => $p2->id, 'nama_lengkap' => 'User Dua']);

        $response = $this->actingAs($admin)->get('/admin/peserta?search=1234567890');

        $response->assertSee('1234567890');
        $response->assertDontSee('9876543210');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Filter peserta
    // ──────────────────────────────────────────────

    public function test_list_dapat_difilter_berdasarkan_jalur(): void
    {
        $admin   = User::factory()->admin()->create();
        $tahun   = $this->tahunAktif();
        $jalur1  = $this->jalur($tahun, ['nama' => 'Reguler']);
        $jalur2  = $this->jalur($tahun, ['nama' => 'Afirmasi']);

        $this->pesertaDenganNama($tahun, $jalur1, 'Peserta Reguler');
        $this->pesertaDenganNama($tahun, $jalur2, 'Peserta Afirmasi');

        $response = $this->actingAs($admin)->get("/admin/peserta?jalur_id={$jalur1->id}");

        $response->assertSee('Peserta Reguler');
        $response->assertDontSee('Peserta Afirmasi');
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Hapus peserta
    // ──────────────────────────────────────────────

    public function test_admin_dapat_menghapus_peserta(): void
    {
        $admin   = User::factory()->admin()->create();
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalur($tahun);
        $peserta = $this->pesertaDenganNama($tahun, $jalur, 'Peserta Hapus');

        $response = $this->actingAs($admin)->delete("/admin/peserta/{$peserta->id}");

        $response->assertRedirect('/admin/peserta');
        $this->assertDatabaseMissing('peserta', ['id' => $peserta->id]);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Tambah peserta manual (luring)
    // ──────────────────────────────────────────────

    public function test_admin_dapat_tambah_peserta_manual_luring(): void
    {
        $admin   = User::factory()->admin()->create();
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalur($tahun);
        $jurusan = Jurusan::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/peserta', [
            'nama'      => 'Peserta Luring',
            'nisn'      => '1111111111',
            'password'  => 'password123',
            'jalur_id'  => $jalur->id,
            'jurusan_id' => $jurusan->id,
        ]);

        $response->assertRedirect('/admin/peserta');
        $this->assertDatabaseHas('users', ['nisn' => '1111111111', 'role' => 'peserta']);

        $user    = User::where('nisn', '1111111111')->first();
        $peserta = Peserta::where('user_id', $user->id)->first();
        $this->assertNotNull($peserta);
        $this->assertMatchesRegularExpression('/^REG-L-\d{4}$/', $peserta->no_pendaftaran);
        $this->assertEquals('submitted', $peserta->status_formulir);
    }

    public function test_nisn_duplikat_ditolak_saat_tambah_manual(): void
    {
        $admin   = User::factory()->admin()->create();
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalur($tahun);
        $jurusan = Jurusan::factory()->create();

        User::factory()->peserta()->create(['nisn' => '2222222222']);

        $response = $this->actingAs($admin)->post('/admin/peserta', [
            'nama'      => 'Duplikat',
            'nisn'      => '2222222222',
            'password'  => 'password123',
            'jalur_id'  => $jalur->id,
            'jurusan_id' => $jurusan->id,
        ]);

        $response->assertSessionHasErrors('nisn');
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Export Excel
    // ──────────────────────────────────────────────

    public function test_admin_dapat_export_peserta_ke_excel(): void
    {
        $admin = User::factory()->admin()->create();
        $tahun = $this->tahunAktif();
        $jalur = $this->jalur($tahun);
        $this->pesertaDenganNama($tahun, $jalur, 'Ekspor Test');

        $response = $this->actingAs($admin)->get('/admin/peserta/export');

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function test_admin_dapat_reset_password_peserta(): void
    {
        $admin  = User::factory()->admin()->create();
        $tahun  = $this->tahunAktif();
        $jalur  = $this->jalur($tahun);
        $peserta = $this->pesertaDenganNama($tahun, $jalur, 'Reset Test');

        $response = $this->actingAs($admin)->patch("/admin/peserta/{$peserta->id}/reset-password", [
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $peserta->user->fresh()->password));
    }

    public function test_reset_password_peserta_gagal_jika_konfirmasi_tidak_cocok(): void
    {
        $admin  = User::factory()->admin()->create();
        $tahun  = $this->tahunAktif();
        $jalur  = $this->jalur($tahun);
        $peserta = $this->pesertaDenganNama($tahun, $jalur, 'Reset Gagal');

        $response = $this->actingAs($admin)->patch("/admin/peserta/{$peserta->id}/reset-password", [
            'password'              => 'newpassword123',
            'password_confirmation' => 'berbeda999',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_panitia_dapat_reset_password_peserta(): void
    {
        $panitia = User::factory()->panitia()->create();
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalur($tahun);
        $peserta = $this->pesertaDenganNama($tahun, $jalur, 'Reset Panitia');

        $response = $this->actingAs($panitia)->patch("/panitia/peserta/{$peserta->id}/reset-password", [
            'password'              => 'resetbypanitia',
            'password_confirmation' => 'resetbypanitia',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('resetbypanitia', $peserta->user->fresh()->password));
    }
}
