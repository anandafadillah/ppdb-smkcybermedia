<?php

namespace Tests\Feature\Peserta;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Models\Pengumuman;
use App\Models\Peserta;
use App\Models\PesertaBerkas;
use App\Models\PesertaNilai;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function setupPeserta(array $attributes = []): array
    {
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'is_active'           => true,
        ]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();
        $peserta = Peserta::factory()->create(array_merge([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
            'status_formulir'     => 'submitted',
            'no_pendaftaran'      => 'REG-0001',
        ], $attributes));

        return compact('user', 'peserta');
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Peserta tanpa data → CTA formulir
    // ──────────────────────────────────────────────

    public function test_peserta_tanpa_data_melihat_cta_isi_formulir(): void
    {
        TahunPenerimaan::factory()->create(['is_active' => true]);
        $user = User::factory()->peserta()->create();

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Isi Formulir');
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Peserta sudah submit → nomor pendaftaran tampil
    // ──────────────────────────────────────────────

    public function test_peserta_sudah_submit_melihat_nomor_pendaftaran(): void
    {
        ['user' => $user] = $this->setupPeserta(['no_pendaftaran' => 'REG-0042']);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('REG-0042');
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Status verifikasi tampil dengan label
    // ──────────────────────────────────────────────

    public function test_dashboard_menampilkan_label_status_verifikasi(): void
    {
        ['user' => $user] = $this->setupPeserta(['status_verifikasi' => 'terverifikasi']);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Terverifikasi');
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Status hasil seleksi tampil dengan label
    // ──────────────────────────────────────────────

    public function test_dashboard_menampilkan_label_status_hasil_seleksi(): void
    {
        ['user' => $user] = $this->setupPeserta(['status_hasil' => 'lolos']);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Lolos');
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Jumlah berkas terupload tampil
    // ──────────────────────────────────────────────

    public function test_dashboard_menampilkan_jumlah_berkas_terupload(): void
    {
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();

        PesertaBerkas::factory()->create([
            'peserta_id'  => $peserta->id,
            'tipe_berkas' => 'foto_3x4',
        ]);
        PesertaBerkas::factory()->create([
            'peserta_id'  => $peserta->id,
            'tipe_berkas' => 'akta_kelahiran',
        ]);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('2 / ' . count(PesertaBerkas::tipeList()));
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Status nilai tampil (sudah diisi)
    // ──────────────────────────────────────────────

    public function test_dashboard_menampilkan_status_nilai_sudah_diisi(): void
    {
        ['user' => $user, 'peserta' => $peserta] = $this->setupPeserta();
        $mapel = MataPelajaran::factory()->create(['is_active' => true]);
        PesertaNilai::factory()->create([
            'peserta_id'        => $peserta->id,
            'mata_pelajaran_id' => $mapel->id,
        ]);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Sudah Diisi');
    }

    // ──────────────────────────────────────────────
    // Siklus 7: Pengumuman published tampil
    // ──────────────────────────────────────────────

    public function test_pengumuman_published_tampil_di_dashboard(): void
    {
        ['user' => $user] = $this->setupPeserta();
        Pengumuman::factory()->published()->create(['judul' => 'Pengumuman Penting Masuk']);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Pengumuman Penting Masuk');
    }

    // ──────────────────────────────────────────────
    // Siklus 8: Status Daftar Ulang tampil
    // ──────────────────────────────────────────────

    public function test_dashboard_menampilkan_status_daftar_ulang_belum(): void
    {
        ['user' => $user] = $this->setupPeserta(['status_daftar_ulang' => 'belum']);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Belum Daftar Ulang');
    }

    public function test_dashboard_menampilkan_status_daftar_ulang_sudah(): void
    {
        ['user' => $user] = $this->setupPeserta(['status_daftar_ulang' => 'sudah']);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Sudah Daftar Ulang');
    }

    public function test_dashboard_tampilkan_peringatan_daftar_ulang_jika_lolos_belum_daftar(): void
    {
        ['user' => $user] = $this->setupPeserta([
            'status_hasil'        => 'lolos',
            'status_daftar_ulang' => 'belum',
        ]);

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Segera lakukan daftar ulang');
    }
}
