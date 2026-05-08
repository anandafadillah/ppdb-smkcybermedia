<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Siklus 1: Admin login dengan email
    // ──────────────────────────────────────────────

    public function test_admin_dapat_login_dengan_email_dan_redirect_ke_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email'    => 'admin@smk.test',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'identifier' => 'admin@smk.test',
            'password'   => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    // ──────────────────────────────────────────────
    // Siklus 2: Panitia login dengan email
    // ──────────────────────────────────────────────

    public function test_panitia_dapat_login_dengan_email_dan_redirect_ke_panitia_dashboard(): void
    {
        $panitia = User::factory()->panitia()->create([
            'email'    => 'panitia@smk.test',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'identifier' => 'panitia@smk.test',
            'password'   => 'password',
        ]);

        $response->assertRedirect('/panitia/dashboard');
        $this->assertAuthenticatedAs($panitia);
    }

    // ──────────────────────────────────────────────
    // Siklus 3: Peserta login dengan NISN
    // ──────────────────────────────────────────────

    public function test_peserta_dapat_login_dengan_nisn_dan_redirect_ke_peserta_dashboard(): void
    {
        $peserta = User::factory()->peserta()->create([
            'nisn'     => '1234567890',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'identifier' => '1234567890',
            'password'   => 'password',
        ]);

        $response->assertRedirect('/peserta/dashboard');
        $this->assertAuthenticatedAs($peserta);
    }

    // ──────────────────────────────────────────────
    // Siklus 4: Kredensial salah
    // ──────────────────────────────────────────────

    public function test_login_dengan_password_salah_menampilkan_pesan_error(): void
    {
        User::factory()->admin()->create([
            'email'    => 'admin@smk.test',
            'password' => bcrypt('password-benar'),
        ]);

        $response = $this->post('/login', [
            'identifier' => 'admin@smk.test',
            'password'   => 'password-salah',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    // ──────────────────────────────────────────────
    // Siklus 5: Akun nonaktif ditolak
    // ──────────────────────────────────────────────

    public function test_akun_nonaktif_tidak_dapat_login(): void
    {
        User::factory()->admin()->create([
            'email'     => 'nonaktif@smk.test',
            'password'  => bcrypt('password'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'identifier' => 'nonaktif@smk.test',
            'password'   => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    // ──────────────────────────────────────────────
    // Siklus 6: Logout
    // ──────────────────────────────────────────────

    public function test_pengguna_yang_login_dapat_logout_dan_diarahkan_ke_halaman_login(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    // ──────────────────────────────────────────────
    // Siklus 7–9: Middleware role
    // ──────────────────────────────────────────────

    public function test_peserta_tidak_bisa_akses_halaman_admin(): void
    {
        $peserta = User::factory()->peserta()->create();

        $response = $this->actingAs($peserta)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_admin_tidak_bisa_akses_halaman_panitia(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/panitia/dashboard');

        $response->assertForbidden();
    }

    public function test_panitia_tidak_bisa_akses_halaman_peserta(): void
    {
        $panitia = User::factory()->panitia()->create();

        $response = $this->actingAs($panitia)->get('/peserta/dashboard');

        $response->assertForbidden();
    }
}
