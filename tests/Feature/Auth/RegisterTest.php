<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Siklus 1: Registrasi data valid
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_registrasi_dengan_data_valid_dan_langsung_masuk_ke_dashboard(): void
    {
        $response = $this->post('/daftar', [
            'nama'                  => 'Budi Santoso',
            'nisn'                  => '1234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/peserta/dashboard');

        $this->assertDatabaseHas('users', [
            'name'      => 'Budi Santoso',
            'nisn'      => '1234567890',
            'role'      => 'peserta',
            'is_active' => true,
        ]);

        $this->assertAuthenticated();
    }

    // ──────────────────────────────────────────────
    // Siklus 2: NISN sudah terdaftar
    // ──────────────────────────────────────────────

    public function test_nisn_yang_sudah_terdaftar_ditolak_dengan_pesan_error(): void
    {
        User::factory()->peserta()->create(['nisn' => '1234567890']);

        $response = $this->post('/daftar', [
            'nama'                  => 'Peserta Lain',
            'nisn'                  => '1234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('nisn');
        $this->assertGuest();
    }

    // ──────────────────────────────────────────────
    // Siklus 3: NISN tidak valid
    // ──────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('nisnTidakValidProvider')]
    public function test_nisn_yang_tidak_valid_ditolak(string $nisn): void
    {
        $response = $this->post('/daftar', [
            'nama'                  => 'Budi Santoso',
            'nisn'                  => $nisn,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('nisn');
        $this->assertGuest();
    }

    public static function nisnTidakValidProvider(): array
    {
        return [
            'kurang dari 10 digit' => ['123456789'],
            'lebih dari 10 digit'  => ['12345678901'],
            'mengandung huruf'     => ['123456789a'],
            'kosong'               => [''],
        ];
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Password terlalu pendek
    // ──────────────────────────────────────────────

    public function test_password_kurang_dari_8_karakter_ditolak(): void
    {
        $response = $this->post('/daftar', [
            'nama'                  => 'Budi Santoso',
            'nisn'                  => '1234567890',
            'password'              => 'tujuh7',
            'password_confirmation' => 'tujuh7',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Konfirmasi password tidak cocok
    // ──────────────────────────────────────────────

    public function test_password_konfirmasi_tidak_cocok_ditolak(): void
    {
        $response = $this->post('/daftar', [
            'nama'                  => 'Budi Santoso',
            'nisn'                  => '1234567890',
            'password'              => 'password123',
            'password_confirmation' => 'passwordXXX',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Halaman /daftar bisa diakses publik
    // ──────────────────────────────────────────────

    public function test_halaman_daftar_dapat_diakses_tanpa_login(): void
    {
        $response = $this->get('/daftar');

        $response->assertOk();
        $response->assertSee('Pendaftaran Peserta Baru');
    }
}
