<?php

namespace Tests\Feature\Peserta;

use App\Models\AsalSekolah;
use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormulirTest extends TestCase
{
    use RefreshDatabase;

    private function tahunAktif(): TahunPenerimaan
    {
        return TahunPenerimaan::factory()->create(['is_active' => true]);
    }

    private function jalurAktif(TahunPenerimaan $tahun): JalurPendaftaran
    {
        return JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'is_active'           => true,
            'kode_awal_daring'    => 'AFR-D',
        ]);
    }

    private function validPayload(JalurPendaftaran $jalur, Jurusan $jurusan): array
    {
        return [
            'jalur_id'      => $jalur->id,
            'jurusan_id'    => $jurusan->id,
            'nama_lengkap'  => 'Budi Santoso',
            'jenis_kelamin' => 'L',
        ];
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Submit formulir → data tersimpan
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_submit_formulir_dan_data_diri_tersimpan(): void
    {
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalurAktif($tahun);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();

        $response = $this->actingAs($user)->post('/peserta/formulir', $this->validPayload($jalur, $jurusan));

        $response->assertRedirect('/peserta/formulir');
        $this->assertDatabaseHas('peserta_data_diri', ['nama_lengkap' => 'Budi Santoso']);
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Nomor pendaftaran digenerate dengan format benar
    // ──────────────────────────────────────────────

    public function test_submit_menghasilkan_nomor_pendaftaran_dengan_format_kode_sequence(): void
    {
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalurAktif($tahun); // kode_awal_daring = 'AFR-D'
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();

        $this->actingAs($user)->post('/peserta/formulir', $this->validPayload($jalur, $jurusan));

        $peserta = \App\Models\Peserta::where('user_id', $user->id)->first();
        $this->assertMatchesRegularExpression('/^AFR-D-\d{4}$/', $peserta->no_pendaftaran);
        $this->assertEquals('AFR-D-0001', $peserta->no_pendaftaran);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Sequence increment untuk peserta ke-2
    // ──────────────────────────────────────────────

    public function test_peserta_kedua_mendapat_sequence_berikutnya(): void
    {
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalurAktif($tahun);
        $jurusan = Jurusan::factory()->create();

        $user1 = User::factory()->peserta()->create();
        $user2 = User::factory()->peserta()->create();

        $this->actingAs($user1)->post('/peserta/formulir', $this->validPayload($jalur, $jurusan));
        $this->actingAs($user2)->post('/peserta/formulir', $this->validPayload($jalur, $jurusan));

        $p1 = \App\Models\Peserta::where('user_id', $user1->id)->first();
        $p2 = \App\Models\Peserta::where('user_id', $user2->id)->first();

        $this->assertEquals('AFR-D-0001', $p1->no_pendaftaran);
        $this->assertEquals('AFR-D-0002', $p2->no_pendaftaran);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Formulir terkunci setelah submit
    // ──────────────────────────────────────────────

    public function test_status_formulir_menjadi_submitted_setelah_submit(): void
    {
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalurAktif($tahun);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();

        $this->actingAs($user)->post('/peserta/formulir', $this->validPayload($jalur, $jurusan));

        $peserta = \App\Models\Peserta::where('user_id', $user->id)->first();
        $this->assertEquals('submitted', $peserta->status_formulir);
    }

    public function test_peserta_yang_sudah_submit_tidak_bisa_submit_lagi(): void
    {
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalurAktif($tahun);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();

        $this->actingAs($user)->post('/peserta/formulir', $this->validPayload($jalur, $jurusan));

        // Submit kedua kali
        $response = $this->actingAs($user)->post('/peserta/formulir', $this->validPayload($jalur, $jurusan));

        $response->assertRedirect('/peserta/formulir');
        $response->assertSessionHas('error');
        // Sequence tidak bertambah (masih 0001)
        $peserta = \App\Models\Peserta::where('user_id', $user->id)->first();
        $this->assertEquals('AFR-D-0001', $peserta->no_pendaftaran);
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Dashboard tampilkan status formulir
    // ──────────────────────────────────────────────

    public function test_dashboard_peserta_menampilkan_status_belum_diisi(): void
    {
        $user = User::factory()->peserta()->create();

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('Belum diisi');
    }

    public function test_dashboard_peserta_menampilkan_nomor_pendaftaran_setelah_submit(): void
    {
        $tahun   = $this->tahunAktif();
        $jalur   = $this->jalurAktif($tahun);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();

        $this->actingAs($user)->post('/peserta/formulir', $this->validPayload($jalur, $jurusan));

        $response = $this->actingAs($user)->get('/peserta/dashboard');

        $response->assertOk();
        $response->assertSee('AFR-D-0001');
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Peserta akses halaman formulir
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_melihat_halaman_formulir_data_diri(): void
    {
        $tahun  = $this->tahunAktif();
        $jalur  = $this->jalurAktif($tahun);
        Jurusan::factory()->create(['nama' => 'TJKT']);

        $peserta = User::factory()->peserta()->create();

        $response = $this->actingAs($peserta)->get('/peserta/formulir');

        $response->assertOk();
        $response->assertSee('TJKT');
    }
}
