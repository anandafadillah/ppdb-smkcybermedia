<?php

namespace Tests\Feature\Peserta;

use App\Models\JalurPendaftaran;
use App\Models\Jurusan;
use App\Models\Peserta;
use App\Models\TahunPenerimaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeluargaTest extends TestCase
{
    use RefreshDatabase;

    private function setupPeserta(bool $afirmasi = false): array
    {
        $tahun   = TahunPenerimaan::factory()->create(['is_active' => true]);
        $jalur   = JalurPendaftaran::factory()->create([
            'tahun_penerimaan_id' => $tahun->id,
            'is_active'           => true,
            'nama'                => $afirmasi ? 'Afirmasi' : 'Reguler',
            'kode_awal_daring'    => $afirmasi ? 'AFR-D' : 'REG-D',
        ]);
        $jurusan = Jurusan::factory()->create();
        $user    = User::factory()->peserta()->create();
        $peserta = Peserta::factory()->create([
            'user_id'             => $user->id,
            'tahun_penerimaan_id' => $tahun->id,
            'jalur_id'            => $jalur->id,
            'jurusan_id'          => $jurusan->id,
            'status_formulir'     => 'submitted',
        ]);

        return compact('user', 'peserta', 'jalur');
    }

    private function validPayload(): array
    {
        return [
            'nama_ayah'          => 'Budi Santoso',
            'nik_ayah'           => '3201010101800001',
            'tahun_lahir_ayah'   => '1980',
            'pendidikan_ayah'    => 'S1',
            'pekerjaan_ayah'     => 'PNS',
            'penghasilan_ayah'   => '5000000',
            'nama_ibu'           => 'Siti Aminah',
            'nik_ibu'            => '3201010101820002',
            'tahun_lahir_ibu'    => '1982',
            'pendidikan_ibu'     => 'SMA',
            'pekerjaan_ibu'      => 'IRT',
            'penghasilan_ibu'    => '0',
            'rt'                 => '001',
            'rw'                 => '002',
            'kelurahan'          => 'Cibubur',
            'kecamatan'          => 'Ciracas',
            'kota'               => 'Jakarta Timur',
        ];
    }

    // ──────────────────────────────────────────────
    // Siklus 1: Akses halaman data keluarga
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_melihat_halaman_data_keluarga(): void
    {
        ['user' => $user] = $this->setupPeserta();

        $response = $this->actingAs($user)->get('/peserta/keluarga');

        $response->assertOk();
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Submit data ayah tersimpan
    // ──────────────────────────────────────────────

    public function test_submit_menyimpan_data_ayah(): void
    {
        ['user' => $user] = $this->setupPeserta();

        $this->actingAs($user)->post('/peserta/keluarga', $this->validPayload());

        $this->assertDatabaseHas('peserta_data_ayah', ['nama' => 'Budi Santoso']);
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Submit data ibu tersimpan
    // ──────────────────────────────────────────────

    public function test_submit_menyimpan_data_ibu(): void
    {
        ['user' => $user] = $this->setupPeserta();

        $this->actingAs($user)->post('/peserta/keluarga', $this->validPayload());

        $this->assertDatabaseHas('peserta_data_ibu', ['nama' => 'Siti Aminah']);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Submit data alamat tersimpan
    // ──────────────────────────────────────────────

    public function test_submit_menyimpan_data_alamat(): void
    {
        ['user' => $user] = $this->setupPeserta();

        $this->actingAs($user)->post('/peserta/keluarga', $this->validPayload());

        $this->assertDatabaseHas('peserta_data_alamat', ['kelurahan' => 'Cibubur', 'kota' => 'Jakarta Timur']);
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Data KIP tersimpan untuk jalur Afirmasi
    // ──────────────────────────────────────────────

    public function test_data_kip_tersimpan_untuk_jalur_afirmasi(): void
    {
        ['user' => $user] = $this->setupPeserta(afirmasi: true);

        $payload = array_merge($this->validPayload(), [
            'no_kip'      => '1234567890123456',
            'no_kps_pkh'  => 'PKH-001',
            'nama_di_kip' => 'Budi Santoso Jr',
        ]);

        $this->actingAs($user)->post('/peserta/keluarga', $payload);

        $this->assertDatabaseHas('peserta_data_kip', ['no_kip' => '1234567890123456']);
    }

    public function test_data_kip_tidak_tersimpan_untuk_jalur_non_afirmasi(): void
    {
        ['user' => $user] = $this->setupPeserta(afirmasi: false);

        $payload = array_merge($this->validPayload(), [
            'no_kip' => '1234567890123456',
        ]);

        $this->actingAs($user)->post('/peserta/keluarga', $payload);

        $this->assertDatabaseMissing('peserta_data_kip', ['no_kip' => '1234567890123456']);
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Data wali opsional
    // ──────────────────────────────────────────────

    public function test_data_wali_tersimpan_jika_diisi(): void
    {
        ['user' => $user] = $this->setupPeserta();

        $payload = array_merge($this->validPayload(), [
            'nama_wali'     => 'Paman Budi',
            'pekerjaan_wali' => 'Wiraswasta',
        ]);

        $this->actingAs($user)->post('/peserta/keluarga', $payload);

        $this->assertDatabaseHas('peserta_data_wali', ['nama' => 'Paman Budi']);
    }

    public function test_data_wali_tidak_disimpan_jika_kosong(): void
    {
        ['user' => $user] = $this->setupPeserta();

        $this->actingAs($user)->post('/peserta/keluarga', $this->validPayload());

        $this->assertDatabaseCount('peserta_data_wali', 0);
    }

    // ──────────────────────────────────────────────
    // Siklus 7: Re-submit update data (idempotent)
    // ──────────────────────────────────────────────

    public function test_submit_kedua_update_data_ayah_bukan_duplikat(): void
    {
        ['user' => $user] = $this->setupPeserta();

        $this->actingAs($user)->post('/peserta/keluarga', $this->validPayload());

        $updated = array_merge($this->validPayload(), ['nama_ayah' => 'Budi Revisi']);
        $this->actingAs($user)->post('/peserta/keluarga', $updated);

        $this->assertDatabaseCount('peserta_data_ayah', 1);
        $this->assertDatabaseHas('peserta_data_ayah', ['nama' => 'Budi Revisi']);
    }
}
