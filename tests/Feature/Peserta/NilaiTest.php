<?php

namespace Tests\Feature\Peserta;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NilaiTest extends TestCase
{
    use RefreshDatabase;

    private function setupPeserta(string $statusVerifikasi = 'belum_diverifikasi'): array
    {
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'is_active'           => true,
        ]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();
        $peserta = Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
            'status_formulir'     => 'submitted',
            'status_verifikasi'   => $statusVerifikasi,
        ]);

        return compact('user', 'peserta');
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Akses halaman nilai
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_melihat_halaman_nilai_rapor(): void
    {
        ['user' => $user] = $this->setupPeserta();
        MataPelajaran::factory()->create(['nama' => 'Matematika']);

        $response = $this->actingAs($user)->get('/peserta/nilai');

        $response->assertOk();
        $response->assertSee('Matematika');
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Submit nilai tersimpan
    // ──────────────────────────────────────────────

    public function test_submit_menyimpan_nilai_per_mapel_per_semester(): void
    {
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();
        $mapel = MataPelajaran::factory()->create();

        $this->actingAs($user)->post('/peserta/nilai', [
            'nilai' => [
                $mapel->id => [
                    1 => '85.50',
                    2 => '90.00',
                ],
            ],
        ]);

        $this->assertDatabaseHas('peserta_nilai', [
            'peserta_id'       => $peserta->id,
            'mata_pelajaran_id' => $mapel->id,
            'semester'         => 1,
            'nilai'            => '85.50',
        ]);
        $this->assertDatabaseHas('peserta_nilai', [
            'peserta_id'       => $peserta->id,
            'mata_pelajaran_id' => $mapel->id,
            'semester'         => 2,
            'nilai'            => '90.00',
        ]);
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Simpan sebagian (partial save OK)
    // ──────────────────────────────────────────────

    public function test_nilai_kosong_tidak_tersimpan(): void
    {
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();
        $mapel = MataPelajaran::factory()->create();

        $this->actingAs($user)->post('/peserta/nilai', [
            'nilai' => [
                $mapel->id => [
                    1 => '',
                    2 => '80.00',
                ],
            ],
        ]);

        $this->assertDatabaseCount('peserta_nilai', 1);
        $this->assertDatabaseHas('peserta_nilai', ['semester' => 2, 'nilai' => '80.00']);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Input terkunci saat terverifikasi
    // ──────────────────────────────────────────────

    public function test_input_nilai_terkunci_saat_terverifikasi(): void
    {
        ['user' => $user] = $this->setupPeserta(statusVerifikasi: 'terverifikasi');
        $mapel = MataPelajaran::factory()->create();

        $response = $this->actingAs($user)->post('/peserta/nilai', [
            'nilai' => [$mapel->id => [1 => '85.00']],
        ]);

        $response->assertRedirect('/peserta/nilai');
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('peserta_nilai', 0);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Re-submit update nilai (idempotent)
    // ──────────────────────────────────────────────

    public function test_submit_kedua_update_nilai_bukan_duplikat(): void
    {
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();
        $mapel = MataPelajaran::factory()->create();

        $this->actingAs($user)->post('/peserta/nilai', [
            'nilai' => [$mapel->id => [1 => '70.00']],
        ]);
        $this->actingAs($user)->post('/peserta/nilai', [
            'nilai' => [$mapel->id => [1 => '85.00']],
        ]);

        $this->assertDatabaseCount('peserta_nilai', 1);
        $this->assertDatabaseHas('peserta_nilai', ['nilai' => '85.00']);
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Hanya mapel aktif yang tampil
    // ──────────────────────────────────────────────

    public function test_hanya_mapel_aktif_yang_ditampilkan(): void
    {
        ['user' => $user] = $this->setupPeserta();
        MataPelajaran::factory()->create(['nama' => 'Aktif']);
        MataPelajaran::factory()->nonaktif()->create(['nama' => 'Nonaktif']);

        $response = $this->actingAs($user)->get('/peserta/nilai');

        $response->assertSee('Aktif');
        $response->assertDontSee('Nonaktif');
    }
}
